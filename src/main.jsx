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
