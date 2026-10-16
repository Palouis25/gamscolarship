import Seo from '../components/Seo.jsx';
import './StaticPage.css';

export default function About() {
  return (
    <>
      <Seo
        title="About | GamScholarship"
        description="GamScholarship helps students discover scholarship opportunities from around the world."
      />

      <section className="page-header">
        <div className="container">
          <h1>About GamScholarship</h1>
          <p>Making opportunity information easier to find.</p>
        </div>
      </section>

      <section className="section">
        <div className="container static-content">
          <h2>Our Mission</h2>
          <p>
            GamScholarship exists to make scholarship information easier to
            discover, understand, and access.
          </p>

          <h2>What We Focus On</h2>
          <ul>
            <li>Helping students discover scholarship opportunities</li>
            <li>Making eligibility and requirements easier to understand</li>
            <li>Hiding expired scholarships automatically</li>
            <li>Linking applicants to official application pages</li>
          </ul>

          <h2>A Growing Platform</h2>
          <p>
            GamScholarship collects scholarships from official sources and checks that each application link is live and points to a recognised official site before a listing is shown. These checks confirm the link, not every detail: always confirm the deadline and eligibility on the official page before you apply.
          </p>
        </div>
      </section>
    </>
  );
}
