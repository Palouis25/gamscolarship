import { parseDeadline, daysUntil } from '../../convex/lib/deadline';

/** Display info for a deadline. Only strict dates are formatted; descriptive
 *  deadlines are shown exactly as stored (never parsed with `new Date`). */
export function describeDeadline(deadline, { long = false } = {}) {
  const info = parseDeadline(deadline);
  if (info.kind !== 'date') return { label: info.text, note: '', tone: 'text', isDate: false, sortKey: null };
  const label = new Date(`${info.iso}T00:00:00Z`).toLocaleDateString('en-GB', {
    day: 'numeric',
    month: long ? 'long' : 'short',
    year: 'numeric',
    timeZone: 'UTC',
  });
  const days = daysUntil(deadline);
  let note = '';
  if (days === 0) note = 'Closes today';
  else if (days === 1) note = 'Closes tomorrow';
  else if (days <= 60) note = `${days} days left`;
  const tone = days <= 14 ? 'urgent' : days <= 45 ? 'soon' : 'normal';
  return { label, note, tone, isDate: true, sortKey: info.iso };
}

/** "Master's, PhD" -> ["Master's", "PhD"] */
export const splitList = (value) =>
  String(value ?? '').split(',').map((s) => s.trim()).filter(Boolean);

export function hostname(url) {
  try {
    return new URL(url).hostname.replace(/^www\./, '');
  } catch {
    return '';
  }
}

export function formatDate(iso) {
  const d = new Date(iso);
  return Number.isNaN(d.getTime())
    ? ''
    : d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC' });
}

/** Soonest real deadline first; descriptive deadlines after dated ones. */
export function byDeadline(a, b) {
  const ka = describeDeadline(a.deadline).sortKey;
  const kb = describeDeadline(b.deadline).sortKey;
  if (ka && kb) return ka.localeCompare(kb);
  if (ka) return -1;
  if (kb) return 1;
  return 0;
}
