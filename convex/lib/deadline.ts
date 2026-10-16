// Single source of truth for deadline handling, shared by the Convex backend
// (validation, expiry cron, public queries) and the React frontend.
//
// A deadline is stored as a string. Only a *strict* calendar date is treated
// as a date; anything else ("Varies by country", "Rolling") is descriptive
// text and is never parsed. (Never use `new Date(text)` on descriptive
// deadlines: V8 happily turns "foo April 2027" into a date.)

export type DeadlineInfo = { kind: 'date'; iso: string } | { kind: 'text'; text: string };

const ISO = /^(\d{4})-(\d{2})-(\d{2})$/;
const MONTHS: Record<string, number> = {
  jan: 1, january: 1, feb: 2, february: 2, mar: 3, march: 3, apr: 4, april: 4,
  may: 5, jun: 6, june: 6, jul: 7, july: 7, aug: 8, august: 8,
  sep: 9, sept: 9, september: 9, oct: 10, october: 10, nov: 11, november: 11, dec: 12, december: 12,
};

function toIso(y: number, m: number, d: number): string | null {
  const date = new Date(Date.UTC(y, m - 1, d));
  if (date.getUTCFullYear() !== y || date.getUTCMonth() !== m - 1 || date.getUTCDate() !== d) return null;
  return `${String(y).padStart(4, '0')}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
}

/** Parses ISO ("2026-11-03"), "3 November 2026" and "November 3, 2026". Otherwise returns text. */
export function parseDeadline(raw: string): DeadlineInfo {
  const text = (raw ?? '').trim();
  let m = ISO.exec(text);
  if (m) {
    const iso = toIso(+m[1], +m[2], +m[3]);
    if (iso) return { kind: 'date', iso };
  }
  m = /^(\d{1,2})(?:st|nd|rd|th)?\s+([A-Za-z]{3,9})\.?,?\s+(\d{4})$/.exec(text);
  if (m && MONTHS[m[2].toLowerCase()]) {
    const iso = toIso(+m[3], MONTHS[m[2].toLowerCase()], +m[1]);
    if (iso) return { kind: 'date', iso };
  }
  m = /^([A-Za-z]{3,9})\.?\s+(\d{1,2})(?:st|nd|rd|th)?,?\s+(\d{4})$/.exec(text);
  if (m && MONTHS[m[1].toLowerCase()]) {
    const iso = toIso(+m[3], MONTHS[m[1].toLowerCase()], +m[2]);
    if (iso) return { kind: 'date', iso };
  }
  return { kind: 'text', text };
}

export function todayIso(now: Date = new Date()): string {
  return now.toISOString().slice(0, 10);
}

/** True only when the deadline is a real date that is before `today`. */
export function isExpired(deadline: string, today: string = todayIso()): boolean {
  const info = parseDeadline(deadline);
  return info.kind === 'date' && info.iso < today;
}

/** Whole days from `today` to the deadline date; null for descriptive deadlines. */
export function daysUntil(deadline: string, today: string = todayIso()): number | null {
  const info = parseDeadline(deadline);
  if (info.kind !== 'date') return null;
  return Math.round((Date.parse(info.iso) - Date.parse(today)) / 86_400_000);
}
