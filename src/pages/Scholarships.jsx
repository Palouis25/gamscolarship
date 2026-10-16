import { useMemo, useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import { useQuery } from 'convex/react';
import { api } from '../../convex/_generated/api';
import Seo from '../components/Seo.jsx';
import ScholarshipCard from '../components/ScholarshipCard.jsx';
import { LoadingGrid, StatusBox } from '../components/StatusMessage.jsx';
import { byDeadline, splitList } from '../lib/format.js';
import './Scholarships.css';

const sortUnique = (arr) => Array.from(new Set(arr)).sort((a, b) => a.localeCompare(b));

const FILTERS = [
  { key: 'level', label: 'Degree level', all: 'All degree levels', multi: true },
  { key: 'country', label: 'Destination', all: 'All destinations', multi: false },
  { key: 'funding', label: 'Funding', all: 'All funding types', multi: false },
  { key: 'field', label: 'Field of study', all: 'All fields of study', multi: true },
];

const valuesOf = (s, f) => (f.multi ? splitList(s[f.key]) : [s[f.key]]);

export default function Scholarships() {
  const scholarships = useQuery(api.scholarships.list);
  const [searchParams, setSearchParams] = useSearchParams();

  const [query, setQuery] = useState(searchParams.get('q') || '');
  const [sort, setSort] = useState(searchParams.get('sort') || 'deadline');
  const [selected, setSelected] = useState(() =>
    Object.fromEntries(FILTERS.map((f) => [f.key, searchParams.get(f.key) || ''])),
  );

  const list = useMemo(() => scholarships ?? [], [scholarships]);

  const options = useMemo(
    () => Object.fromEntries(FILTERS.map((f) => [f.key, sortUnique(list.flatMap((s) => valuesOf(s, f)))])),
    [list],
  );

  useEffect(() => {
    const params = {};
    if (query) params.q = query;
    if (sort !== 'deadline') params.sort = sort;
    FILTERS.forEach((f) => selected[f.key] && (params[f.key] = selected[f.key]));
    setSearchParams(params, { replace: true });
  }, [query, sort, selected, setSearchParams]);

  const results = useMemo(() => {
    const q = query.trim().toLowerCase();
    const filtered = list.filter((s) => {
      if (q && ![s.title, s.organization, s.description, s.country, s.field, s.level].some((v) => v.toLowerCase().includes(q))) return false;
      return FILTERS.every((f) => !selected[f.key] || valuesOf(s, f).includes(selected[f.key]));
    });
    return sort === 'deadline' ? [...filtered].sort(byDeadline) : filtered;
  }, [list, query, sort, selected]);

  const anyFilter = query || FILTERS.some((f) => selected[f.key]);
  const clearFilters = () => {
    setQuery('');
    setSelected(Object.fromEntries(FILTERS.map((f) => [f.key, ''])));
  };

  const loading = scholarships === undefined;

  return (
    <>
      <Seo
        title="Scholarships | GamScholarship"
        description="Search scholarships by degree level, destination, funding and field of study. Every listing links to an official application page."
      />

      <section className="page-header">
        <div className="container">
          <h1>Scholarships</h1>
          <p>Browse open scholarships. Use search and filters to narrow the list.</p>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <div className="filters-bar" role="search">
            <div className="field field-search">
              <label htmlFor="q" className="visually-hidden">Search scholarships</label>
              <input
                id="q"
                type="search"
                className="filters-search"
                placeholder="Search by name, provider, country or field"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
              />
            </div>

            {FILTERS.map((f) => (
              <div className="field" key={f.key}>
                <label htmlFor={`f-${f.key}`} className="filter-label">{f.label}</label>
                <select
                  id={`f-${f.key}`}
                  value={selected[f.key]}
                  onChange={(e) => setSelected((prev) => ({ ...prev, [f.key]: e.target.value }))}
                >
                  <option value="">{f.all}</option>
                  {options[f.key].map((o) => (
                    <option key={o} value={o}>{o}</option>
                  ))}
                </select>
              </div>
            ))}

            <div className="field">
              <label htmlFor="sort" className="filter-label">Sort by</label>
              <select id="sort" value={sort} onChange={(e) => setSort(e.target.value)}>
                <option value="deadline">Deadline (soonest first)</option>
                <option value="recent">Recently added</option>
              </select>
            </div>
          </div>

          {loading ? (
            <LoadingGrid />
          ) : (
            <>
              <div className="results-row">
                <p className="results-count" aria-live="polite">
                  {results.length} scholarship{results.length === 1 ? '' : 's'} found
                </p>
                {anyFilter && (
                  <button type="button" className="btn btn-outline-dark btn-small" onClick={clearFilters}>
                    Clear all filters
                  </button>
                )}
              </div>

              {results.length > 0 ? (
                <div className="grid grid-3">
                  {results.map((s) => (
                    <ScholarshipCard key={s._id} scholarship={s} />
                  ))}
                </div>
              ) : list.length === 0 ? (
                <StatusBox title="No scholarships are published yet">
                  Listings appear here once they have been collected and their official links verified. Please check back soon.
                </StatusBox>
              ) : (
                <StatusBox
                  title="No scholarships match your search"
                  action={
                    <button type="button" className="btn btn-primary" onClick={clearFilters}>
                      Clear filters
                    </button>
                  }
                >
                  Try a broader search term, or remove a filter such as degree level or destination.
                </StatusBox>
              )}
            </>
          )}
        </div>
      </section>
    </>
  );
}
