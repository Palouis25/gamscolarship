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
}
