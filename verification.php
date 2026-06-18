<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/layout.php';

$extra_css = '
.verify-hero{background:var(--grad);color:#fff;padding:2.5rem 1.2rem}
.verify-hero .inner,.verify-wrap{max-width:980px;margin:0 auto}
.verify-hero h1{margin:0 0 .5rem;font-size:clamp(1.6rem,4vw,2.5rem);font-weight:800}
.verify-hero p{margin:0;opacity:.92;line-height:1.6}
.verify-wrap{padding:2rem 1.2rem;display:grid;gap:1rem}
.verify-panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.25rem}
.verify-panel h2{margin:0 0 .75rem;font-size:1.1rem;color:var(--primary)}
.verify-panel p{margin:.25rem 0;color:var(--muted);line-height:1.65}
.verify-list{margin:.5rem 0 0;padding-left:1.2rem;color:var(--muted);line-height:1.65}
.status-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:.8rem}
.status-card{border:1px solid var(--border);border-radius:10px;padding:.9rem;background:#f8fafc}
.status-card strong{display:block;margin-bottom:.3rem;color:var(--text)}
';

echo page_head(
    'Verification Policy - GamScholarship',
    'How GamScholarship checks scholarship sources, deadlines, and application links before recommending opportunities.',
    $extra_css
);
echo '<body>';
echo site_header('scholarships');
?>

<section class="verify-hero">
    <div class="inner">
        <h1>Scholarship Verification Policy</h1>
        <p>Our goal is to help Gambian students find real opportunities, avoid fake offers, and apply through official channels.</p>
    </div>
</section>

<main class="verify-wrap">
    <section class="verify-panel">
        <h2>What We Check</h2>
        <ul class="verify-list">
            <li>The opportunity links to an official university, government, foundation, or programme website.</li>
            <li>The deadline is listed on the official source whenever possible.</li>
            <li>The scholarship does not ask applicants to pay through personal bank accounts, agents, or unofficial WhatsApp numbers.</li>
            <li>The page clearly explains who can apply, what is funded, and where to submit the application.</li>
        </ul>
    </section>

    <section class="verify-panel">
        <h2>Status Labels</h2>
        <div class="status-grid">
            <div class="status-card">
                <strong>Official source checked</strong>
                The link points to an official or recognized source.
            </div>
            <div class="status-card">
                <strong>Deadline to confirm</strong>
                The programme is real, but the exact current deadline needs manual checking.
            </div>
            <div class="status-card">
                <strong>Closing soon</strong>
                The listed deadline is within the next 30 days.
            </div>
            <div class="status-card">
                <strong>Deadline may be closed</strong>
                The listed deadline appears to be in the past, so students should check the official page before applying.
            </div>
        </div>
    </section>

    <section class="verify-panel">
        <h2>Student Safety Rule</h2>
        <p>Always apply through the official scholarship, university, government, or foundation website. Do not pay anyone who promises guaranteed admission, guaranteed scholarships, or faster approval.</p>
    </section>
</main>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
