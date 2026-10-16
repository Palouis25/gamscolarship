import { USER_AGENT } from './linkCheck';

/** fetch with timeout + bounded retries for rate limits (429), 5xx and network
 *  errors. Returns the final Response (which may still be non-ok) or throws
 *  the last network error. */
export async function fetchWithRetry(
  url: string,
  init: RequestInit = {},
  opts: { tries?: number; timeoutMs?: number; fetchImpl?: typeof fetch; sleep?: (ms: number) => Promise<void> } = {}
): Promise<Response> {
  const tries = opts.tries ?? 3;
  const doFetch = opts.fetchImpl ?? fetch;
  const sleep = opts.sleep ?? ((ms: number) => new Promise((r) => setTimeout(r, ms)));
  let lastErr: unknown;
  for (let attempt = 0; attempt < tries; attempt++) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), opts.timeoutMs ?? 15_000);
    try {
      const res = await doFetch(url, { ...init, signal: controller.signal, headers: { 'User-Agent': USER_AGENT, ...(init.headers ?? {}) } });
      if ((res.status === 429 || res.status >= 500) && attempt < tries - 1) {
        const retryAfter = Number(res.headers.get('retry-after'));
        await sleep(Number.isFinite(retryAfter) && retryAfter > 0 ? Math.min(retryAfter, 30) * 1000 : 1000 * 2 ** attempt);
        continue;
      }
      return res;
    } catch (err) {
      lastErr = err;
      if (attempt < tries - 1) await sleep(1000 * 2 ** attempt);
    } finally {
      clearTimeout(timer);
    }
  }
  throw lastErr ?? new Error('request failed');
}
