import { Component } from 'react';
import { StatusBox } from './StatusMessage.jsx';

/** Catches render/query errors (Convex queries throw into React) so a backend
 *  problem shows a helpful message instead of a blank page. */
export default class ErrorBoundary extends Component {
  state = { error: null };

  static getDerivedStateFromError(error) {
    return { error };
  }

  componentDidCatch(error) {
    console.error('GamScholarship UI error:', error);
  }

  render() {
    if (!this.state.error) return this.props.children;
    return (
      <section className="section">
        <div className="container">
          <StatusBox
            tone="error"
            title="Something went wrong"
            action={
              <button type="button" className="btn btn-primary" onClick={() => window.location.reload()}>
                Reload page
              </button>
            }
          >
            We couldn&apos;t load this page. Please check your connection and try again.
          </StatusBox>
        </div>
      </section>
    );
  }
}
