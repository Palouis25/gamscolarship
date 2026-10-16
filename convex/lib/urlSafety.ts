// URL safety + "is this an official source?" checks. Pure functions: no
// network access, so they are unit-testable and usable inside mutations.

export type UrlIssue = { problem: string };

/** Hosts of well-known official scholarship providers (and the EU feed anchor).
 *  A host matches if it equals an entry or is a subdomain of one. Add a new
 *  provider here (a code change) to let its links pass automated verification;
 *  there is deliberately no manual approval queue. */
export const OFFICIAL_DOMAINS: string[] = [
  'chevening.org',
  'daad.de',
  'mastercardfdn.org',
  'cscuk.fcdo.gov.uk',
  'gatescambridge.org',
  'fulbrightonline.org',
  'erasmus-plus.ec.europa.eu',
  'eacea.ec.europa.eu',
  'turkiyeburslari.gov.tr',
  'dfat.gov.au',
  'studyinjapan.go.jp',
  'campuschina.org',
  'schwarzmanscholars.org',
  'sbfi.admin.ch',
];

const SHORTENERS = new Set([
  'bit.ly', 'tinyurl.com', 't.co', 'goo.gl', 'ow.ly', 'is.gd', 'buff.ly', 'rebrand.ly',
  'cutt.ly', 'shorturl.at', 'lnkd.in', 'tiny.cc', 'rb.gy', 'bl.ink',
]);

// TLDs / second-level labels that denote institutions.
const INSTITUTIONAL = /(^|\.)(gov|edu|mil|int)(\.[a-z]{2})?$|(^|\.)(ac|gov|edu|go)\.[a-z]{2}$|\.europa\.eu$/;

export function hostOf(url: string): string | null {
  try {
    return new URL(url).hostname.toLowerCase().replace(/\.$/, '');
  } catch {
    return null;
  }
}

export function hostMatches(host: string, domain: string): boolean {
  return host === domain || host.endsWith('.' + domain);
}

export function isOfficialHost(host: string): boolean {
  return OFFICIAL_DOMAINS.some((d) => hostMatches(host, d)) || INSTITUTIONAL.test(host);
}

/** Approximate registrable domain (no public-suffix list). Good enough to
 *  compare "same site" across www./subdomains. */
export function registrableDomain(host: string): string {
  const labels = host.split('.');
  if (labels.length <= 2) return host;
  const sld = labels[labels.length - 2];
  const tld = labels[labels.length - 1];
  const twoPart = tld.length === 2 && ['co', 'com', 'org', 'gov', 'ac', 'edu', 'net', 'go'].includes(sld);
  return labels.slice(twoPart ? -3 : -2).join('.');
}

export function sameSite(a: string, b: string): boolean {
  return registrableDomain(a) === registrableDomain(b);
}

const IPV4 = /^\d{1,3}(\.\d{1,3}){3}$/;

/** Returns blocking problems. Empty array = the URL itself looks safe to
 *  fetch and to show to students (this does NOT prove it is official). */
export function assessUrl(url: string): UrlIssue[] {
  const issues: UrlIssue[] = [];
  let parsed: URL;
  try {
    parsed = new URL(url);
  } catch {
    return [{ problem: 'Not a valid URL.' }];
  }
  if (parsed.protocol !== 'https:') issues.push({ problem: 'URL must use https.' });
  if (parsed.username || parsed.password) issues.push({ problem: 'URL contains embedded credentials.' });
  if (parsed.port && parsed.port !== '443') issues.push({ problem: 'URL uses a non-standard port.' });
  const host = parsed.hostname.toLowerCase();
  if (IPV4.test(host) || host.includes(':') || host.startsWith('[')) {
    issues.push({ problem: 'URL points to a raw IP address.' });
  }
  if (host === 'localhost' || host.endsWith('.local') || host.endsWith('.internal') || !host.includes('.')) {
    issues.push({ problem: 'URL points to a non-public host.' });
  }
  if (host.split('.').some((l) => l.startsWith('xn--'))) {
    issues.push({ problem: 'Host uses punycode (possible look-alike domain).' });
  }
  if (SHORTENERS.has(host)) issues.push({ problem: 'URL is a link shortener, not an official page.' });
  if (host.split('.').length > 5) issues.push({ problem: 'Host has an unusually deep subdomain chain.' });
  return issues;
}
