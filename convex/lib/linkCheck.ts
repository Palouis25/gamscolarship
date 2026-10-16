// Link verification. Network access goes through an injectable `fetchImpl`
// so the classification logic is testable without a network.

import { assessUrl, hostOf, isOfficialHost, sameSite } from './urlSafety';

export type LinkOutcome = 'ok' | 'dead' | 'blocked' | 'transient' | 'unsafe';
export type LinkResult = { url: string; outcome: LinkOutcome; detail: string };

export const USER_AGENT =
  'GamScholarshipLinkChecker/1.0 (+https://gamscolarship.online; info@gamscolarship.online)';

type FetchLike = (url: string, init?: any) => Promise<any>;

export async function checkUrl(
  url: string,
  opts: { fetchImpl?: FetchLike; timeoutMs?: number } = {}
): Promise<LinkResult> {
  const unsafe = assessUrl(url);
  if (unsafe.length > 0) {
    return { url, outcome: 'unsafe', detail: unsafe.map((i) => i.problem).join(' ') };
  }
  const doFetch: FetchLike = opts.fetchImpl ?? ((u, i) => fetch(u, i));
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), opts.timeoutMs ?? 10_000);
  try {
    const res = await doFetch(url, {
      method: 'GET',
      redirect: 'follow',
      signal: controller.signal,
      headers: { 'User-Agent': USER_AGENT, Accept: 'text/html,application/xhtml+xml,application/pdf;q=0.9,*/*;q=0.5' },
    });
    try {
      await res.body?.cancel?.(); // we only need the status, not the page body
    } catch {
      /* ignore */
    }

    const finalUrl: string = res.url || url;
    const finalIssues = assessUrl(finalUrl);
    if (finalIssues.length > 0) {
      return { url, outcome: 'unsafe', detail: `Redirects to an unsafe URL: ${finalIssues[0].problem}` };
    }
    const from = hostOf(url)!;
    const to = hostOf(finalUrl)!;
    if (!sameSite(from, to) && !isOfficialHost(to)) {
      return { url, outcome: 'unsafe', detail: `Redirects to an unrecognised domain (${to}).` };
    }

    const s: number = res.status;
    if (s >= 200 && s < 300) return { url, outcome: 'ok', detail: `HTTP ${s}` };
    if (s === 404 || s === 410) return { url, outcome: 'dead', detail: `HTTP ${s} (page not found)` };
    if (s === 429 || s === 408 || s >= 500) return { url, outcome: 'transient', detail: `HTTP ${s}` };
    if ([401, 403, 406, 451, 999].includes(s)) {
      return { url, outcome: 'blocked', detail: `HTTP ${s} (server refused automated access)` };
    }
    if (s >= 300 && s < 400) return { url, outcome: 'transient', detail: `HTTP ${s} (redirect not resolved)` };
    return { url, outcome: 'dead', detail: `HTTP ${s}` };
  } catch (err) {
    const aborted = (err as Error)?.name === 'AbortError';
    return { url, outcome: 'transient', detail: aborted ? 'Timed out' : `Network error: ${(err as Error)?.message ?? err}` };
  } finally {
    clearTimeout(timer);
  }
}

export type LinkVerdict =
  | { verdict: 'ok'; notes?: string }
  | { verdict: 'reject'; notes: string } // unsafe: never publish, final
  | { verdict: 'dead'; notes: string } // definitive: page gone
  | { verdict: 'transient'; notes: string }; // may recover on retry

/** Combines per-URL results into one verdict for a listing. A "blocked"
 *  response (403 etc.) proves the server is alive but declines bots; that is
 *  accepted only for hosts we already recognise as official. */
export function combineResults(results: LinkResult[]): LinkVerdict {
  const fmt = (r: LinkResult) => `${r.url}: ${r.detail}`;
  const unsafe = results.filter((r) => r.outcome === 'unsafe');
  if (unsafe.length) return { verdict: 'reject', notes: `Suspicious or invalid link. ${unsafe.map(fmt).join('; ')}` };
  const dead = results.filter((r) => r.outcome === 'dead');
  if (dead.length) return { verdict: 'dead', notes: `Link check failed. ${dead.map(fmt).join('; ')}` };
  const transient = results.filter((r) => r.outcome === 'transient');
  const blockedUntrusted = results.filter((r) => r.outcome === 'blocked' && !isOfficialHost(hostOf(r.url) ?? ''));
  const bad = [...transient, ...blockedUntrusted];
  if (bad.length) return { verdict: 'transient', notes: `Link check inconclusive. ${bad.map(fmt).join('; ')}` };
  return { verdict: 'ok' };
}

export async function checkLinks(urls: string[], opts?: Parameters<typeof checkUrl>[1]): Promise<LinkVerdict> {
  if (urls.length === 0) return { verdict: 'reject', notes: 'No application URL to check.' };
  return combineResults(await Promise.all(urls.map((u) => checkUrl(u, opts))));
}
