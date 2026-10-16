import { Link } from 'react-router-dom';
import { describeDeadline } from '../lib/format.js';

export default function ScholarshipCard({ scholarship }) {
  const { _id, title, organization, country, level, funding, deadline, description, isSample } = scholarship;
  const d = describeDeadline(deadline);

  return (
    <article className="card scholarship-card">
      <div className="scholarship-card-top">
        <span className="badge">{level}</span>
        <span className="badge badge-accent">{funding}</span>
        {isSample && <span className="badge badge-sample">Details not confirmed</span>}
      </div>

      <h3 className="scholarship-card-title">
        <Link to={`/scholarships/${_id}`} className="card-link">{title}</Link>
      </h3>
      <p className="scholarship-card-org">{organization} &middot; {country}</p>
      <p className="scholarship-card-desc">{description}</p>

      <div className="scholarship-card-footer">
        <span className="scholarship-card-deadline">
          <span className="deadline-label">Deadline</span>
          <strong>{d.label}</strong>
          {d.note && <span className={`deadline-chip deadline-${d.tone}`}>{d.note}</span>}
        </span>
        <span className="btn btn-outline-dark" aria-hidden="true">View details</span>
      </div>
    </article>
  );
}
