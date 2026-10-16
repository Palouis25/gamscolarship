
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { ConvexProvider, ConvexReactClient } from 'convex/react';
import App from './App.jsx';
import ErrorBoundary from './components/ErrorBoundary.jsx';
import './index.css';

const convexUrl = import.meta.env.VITE_CONVEX_URL?.trim();
const root = ReactDOM.createRoot(document.getElementById('root'));

if (!/^https:\/\/.+\.convex\.cloud\/?$/.test(convexUrl || '')) {
  root.render(
    <main className="container" style={{ padding: '64px 20px' }}>
      <h1>GamScholarship needs configuration</h1>
      <p>
        The Convex deployment URL is missing or invalid, so scholarship data
        cannot load.
      </p>
      <p>
        For local development, check <code>.env.local</code>. For the live site,
        configure <code>VITE_CONVEX_URL</code> in Vercel using your production
        Convex URL, then redeploy.
      </p>
    </main>,
  );
} else {
  const convex = new ConvexReactClient(convexUrl);

  root.render(
    <React.StrictMode>
      <ConvexProvider client={convex}>
        <BrowserRouter>
          <ErrorBoundary>
            <App />
          </ErrorBoundary>
        </BrowserRouter>
      </ConvexProvider>
    </React.StrictMode>,
  );
}
