import React from 'react'
import ReactDOM from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import { ConvexProvider, ConvexReactClient } from 'convex/react'
import App from './App.jsx'
import ErrorBoundary from './components/ErrorBoundary.jsx'
import './index.css'

const convexUrl = import.meta.env.VITE_CONVEX_URL;
const root = ReactDOM.createRoot(document.getElementById('root'));

if (!/^https:\/\/.+\.convex\.cloud\/?$/.test(convexUrl || '')) {
  // No usable backend URL: say so plainly instead of connecting to a made-up
  // placeholder deployment and spinning on "Loading…" forever.
  root.render(
    <main className="container" style={{ padding: '64px 20px' }}>
      <h1>GamScholarship is not configured</h1>
      <p>
        <code>VITE_CONVEX_URL</code> is missing or is not a Convex deployment URL
        (expected <code>https://&lt;name&gt;.convex.cloud</code>). Set it in{' '}
        <code>.env.local</code> for local work, or in the Vercel project&apos;s
        environment variables, then rebuild.
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
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { ConvexProvider, ConvexReactClient } from 'convex/react';
import App from './App.jsx';
import './index.css';

const convexUrl = import.meta.env.VITE_CONVEX_URL?.trim();
const root = ReactDOM.createRoot(document.getElementById('root'));

if (!convexUrl) {
  // Avoid initializing Convex with a fake URL, which causes a misleading
  // "Couldn't parse deployment name placeholder" error.
  root.render(
    <div
      role="alert"
      style={{
        maxWidth: '680px',
        margin: '12vh auto',
        padding: '24px',
        fontFamily: 'system-ui, sans-serif',
        lineHeight: 1.6,
      }}
    >
      <h1>GamScholarship needs configuration</h1>
      <p>The Convex deployment URL is missing, so scholarship data cannot load.</p>
      <p>
        For local development, run <code>npx convex dev</code> in the project
        folder and make sure it writes <code>VITE_CONVEX_URL</code> to{' '}
        <code>.env.local</code>.
      </p>
      <p>
        For the live site, add <code>VITE_CONVEX_URL</code> to the Vercel
        project’s Environment Variables using the correct production Convex
        deployment URL, then redeploy.
      </p>
    </div>,
  );
} else {
  const convex = new ConvexReactClient(convexUrl);

  root.render(
    <React.StrictMode>
      <ConvexProvider client={convex}>
        <BrowserRouter>
          <App />
        </BrowserRouter>
      </ConvexProvider>
    </React.StrictMode>,
  );
}
