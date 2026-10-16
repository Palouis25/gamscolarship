export function LoadingGrid({ count = 6, label = 'Loading scholarships' }) {
  return (
    <div role="status" aria-live="polite" aria-label={label}>
      <div className="grid grid-3" aria-hidden="true">
        {Array.from({ length: count }, (_, i) => (
          <div className="card skeleton-card" key={i}>
            <span className="skeleton skeleton-badge" />
            <span className="skeleton skeleton-line skeleton-title" />
            <span className="skeleton skeleton-line" />
            <span className="skeleton skeleton-line" />
          </div>
        ))}
      </div>
      <span className="visually-hidden">{label}…</span>
    </div>
  );
}

export function StatusBox({ title, children, tone = 'neutral', action }) {
  return (
    <div className={`status-box status-${tone}`} role={tone === 'error' ? 'alert' : undefined}>
      <h2>{title}</h2>
      {children && <p>{children}</p>}
      {action}
    </div>
  );
}
