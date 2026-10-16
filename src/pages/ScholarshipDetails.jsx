import { useParams, Link } from 'react-router-dom';
import { useQuery } from 'convex/react';
import { api } from '../../convex/_generated/api';
import Seo from '../components/Seo.jsx';
import { LoadingGrid, StatusBox } from '../components/StatusMessage.jsx';
import { describeDeadline, formatDate, hostname } from '../lib/format.js';
import './ScholarshipDetails.css';

function Section({ title, items }) {
  if (!items || items.length === 0) return null;
  return (
    <>
      <h2>{title}</h2>
      <ul>
        {items.map((item, i) => (
          <li key={`${i}-${item}`}>{item}</li>
        ))}
      </ul>
    </>
  );
}

export default function ScholarshipDetails() {
  const { id } = useParams();
  const scholarship = useQuery(api.scholarships.getById, { id });

  if (scholarship === undefined) {
    return (
      <section className="section">
        <div className="container"><LoadingGrid count={1} label="Loading scholarship" /></div>
      </section>
    );
  }

  if (scholarship === null) {
    return (
      <section className="section">
        <div className="container">
          <Seo title="Scholarship not available | GamScholarship" description="This scholarship is no longer listed." noIndex />
          <StatusBox
            title="This scholarship isn't available"
            action={<Link to="/scholarships" className="btn btn-primary">Browse scholarships</Link>}
          >
            It may have closed, been removed, or the link may be incorrect.
          </StatusBox>
        </div>
      </section>
    );
  }

  const { title, organization, country, level, funding, field, deadline, description, eligibility, requirements, benefits, applicationUrl, isSample, lastVerifiedAt } = scholarship;
  const d = describeDeadline(deadline, { long: true });
  const host = hostname(applicationUrl);
  const checked = lastVerifiedAt ? formatDate(lastVerifiedAt) : '';

  return (
    <>
      <Seo title={`${title} | GamScholarship`} description={description.slice(0, 160)} />

      <section className="page-header">
        <div className="container">
          <Link to="/scholarships" className="back-link">&larr; Back to scholarships</Link>
          <h1>{title}</h1>
          <p>{organization} &middot; {country}</p>
        </div>
      </section>

      <section className="section">
        <div className="container details-layout">
          <div className="details-main">
            {isSample && (
              <div className="sample-notice" role="note">
                The details below (especially the deadline) have not yet been confirmed against the
                official page. Please check the official site before you plan your application.
              </div>
            )}

            <h2>Overview</h2>
            <p>{description}</p>
            <Section title="Eligibility" items={eligibility} />
            <Section title="Requirements" items={requirements} />
            <Section title="Benefits" items={benefits} />
          </div>

          <aside className="details-sidebar card" aria-label="Scholarship summary">
            <dl className="details-facts">
              <dt>Application deadline</dt>
              <dd>
                {d.label}
                {d.note && <span className={`deadline-chip deadline-${d.tone}`}>{d.note}</span>}
              </dd>
              <dt>Degree level</dt>
              <dd>{level}</dd>
              <dt>Field of study</dt>
              <dd>{field}</dd>
              <dt>Funding</dt>
              <dd>{funding}</dd>
              <dt>Destination</dt>
              <dd>{country}</dd>
            </dl>
            <a href={applicationUrl} target="_blank" rel="noopener noreferrer" className="btn btn-accent btn-block">
              Go to official page<span className="visually-hidden"> (opens in a new tab)</span>
            </a>
            <p className="details-note">
              {host && <>Opens <strong>{host}</strong>. </>}
              {checked && <>Link last checked {checked}. </>}
              Always confirm the deadline and eligibility on the official page.
            </p>
          </aside>
        </div>
      </section>
    </>
  );
}
