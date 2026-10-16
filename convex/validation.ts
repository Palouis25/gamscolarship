// Automated, network-free validation run before a listing can be verified.
// (Link reachability is checked separately: see lib/linkCheck.ts.)

import { parseDeadline } from './lib/deadline';
import { assessUrl, hostOf, isOfficialHost } from './lib/urlSafety';
import type { NormalizedListing } from './sources/types';

export type ValidationIssue = { field: string; problem: string };

const JOB_WORDS = /\b(internships?|jobs?|vacanc(?:y|ies)|recruitment|hiring|careers?|competitions?|contests?|conferences?|workshops?|webinars?|exchange programmes?)\b/i;
const SCHOLARSHIP_WORDS = /\b(scholarships?|bursar(?:y|ies)|studentships?|scholars)\b/i;
const FUNDING_WORDS = /\b(scholarships?|bursar(?:y|ies)|studentships?|tuition|stipend|funded|funding|grants?|burslari)\b/i;

/** Scholarships only: rejects jobs, internships, competitions, bare fellowships. */
export function isScholarshipContent(input: Pick<NormalizedListing, 'title' | 'description'>): string | null {
  if (JOB_WORDS.test(input.title) && !SCHOLARSHIP_WORDS.test(input.title)) {
    return 'Title looks like a job, internship, competition or event, not a scholarship.';
  }
  if (/\bfellowships?\b/i.test(input.title) && !SCHOLARSHIP_WORDS.test(input.title)) {
    return 'Fellowships are out of scope; only scholarships are listed.';
  }
  if (!FUNDING_WORDS.test(`${input.title} ${input.description}`)) {
    return 'Nothing in the title or description indicates scholarship funding.';
  }
  return null;
}

export function runStaticChecks(input: NormalizedListing, today: string = new Date().toISOString().slice(0, 10)): ValidationIssue[] {
  const issues: ValidationIssue[] = [];
  const req = ['title', 'organization', 'country', 'description', 'applicationUrl', 'level', 'funding', 'field'] as const;
  for (const f of req) {
    if (typeof input[f] !== 'string' || !input[f].trim()) issues.push({ field: f, problem: 'Required field is missing or empty.' });
  }

  if (input.applicationUrl) {
    for (const i of assessUrl(input.applicationUrl)) issues.push({ field: 'applicationUrl', problem: i.problem });
    const host = hostOf(input.applicationUrl);
    if (host && !isOfficialHost(host) && input.urlTrust !== 'official_feed') {
      issues.push({
        field: 'applicationUrl',
        problem: `Host "${host}" is not a recognised official source (add its domain to OFFICIAL_DOMAINS in convex/lib/urlSafety.ts if it is genuine).`,
      });
    }
  }
  if (input.officialSourceUrl) {
    for (const i of assessUrl(input.officialSourceUrl)) issues.push({ field: 'officialSourceUrl', problem: i.problem });
  }

  const dl = typeof input.deadline === 'string' ? input.deadline.trim() : '';
  if (!dl) issues.push({ field: 'deadline', problem: 'Deadline is missing.' });
  else {
    const info = parseDeadline(dl);
    if (info.kind === 'date' && info.iso < today) issues.push({ field: 'deadline', problem: 'Deadline is already in the past.' });
  }

  if (input.description && input.description.trim().length < 40) {
    issues.push({ field: 'description', problem: 'Description looks too short to be a real, useful listing.' });
  }
  if (input.title && input.description) {
    const scope = isScholarshipContent(input);
    if (scope) issues.push({ field: 'title', problem: scope });
  }
  return issues;
}

/** Heuristic for formulaic template-farm listings ("... Program 133" by a generic "Foundation"). */
export function looksTemplated(input: Pick<NormalizedListing, 'title' | 'organization'>): boolean {
  const numberedTitle = /\b(program|programme|scholarship)\s*#?\d{2,4}\b/i.test(input.title);
  const genericOrg = /\b(foundation|network|initiative)\b/i.test(input.organization);
  return numberedTitle && genericOrg;
}
