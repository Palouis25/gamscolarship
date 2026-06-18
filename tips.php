<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/layout.php';

$extra_css = '
.page-hero{background:var(--grad);color:#fff;padding:2.5rem 1.2rem;text-align:center}
.page-hero h1{font-size:clamp(1.5rem,4vw,2.4rem);font-weight:800;margin:0 0 .5rem;letter-spacing:-.02em}
.page-hero p{opacity:.92;font-size:1rem;margin:0}
.tips-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem;margin-top:2rem}
.tip-card{background:var(--surface);border-radius:var(--radius);padding:1.5rem;box-shadow:var(--shadow);border:1px solid var(--border);border-top:4px solid var(--primary)}
.tip-card:nth-child(2){border-top-color:var(--secondary)}
.tip-card:nth-child(3){border-top-color:var(--success)}
.tip-card:nth-child(4){border-top-color:#7c3aed}
.tip-card:nth-child(5){border-top-color:#ea580c}
.tip-card:nth-child(6){border-top-color:#0284c7}
.tip-card:nth-child(7){border-top-color:#16a34a}
.tip-card:nth-child(8){border-top-color:#dc2626}
.tip-icon{width:42px;height:42px;border-radius:10px;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--primary);margin-bottom:.9rem}
.tip-card h3{margin:0 0 .6rem;font-size:1.05rem;font-weight:700}
.tip-card p{color:var(--muted);font-size:.9rem;line-height:1.65;margin:0}
.checklist{background:var(--surface);border-radius:var(--radius);padding:1.75rem;box-shadow:var(--shadow);border:1px solid var(--border);margin-top:2rem}
.checklist h2{margin:0 0 1.2rem;font-size:1.2rem}
.check-item{display:flex;align-items:flex-start;gap:.75rem;padding:.65rem 0;border-bottom:1px solid var(--border)}
.check-item:last-child{border-bottom:none}
.check-icon{flex-shrink:0;width:22px;height:22px;border-radius:50%;background:#d1fae5;display:flex;align-items:center;justify-content:center;font-size:.7rem;color:var(--success);margin-top:.1rem}
.check-text strong{display:block;font-size:.92rem;margin-bottom:.1rem}
.check-text span{color:var(--muted);font-size:.85rem}
.resources-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;margin-top:1.5rem}
.resource-card{background:var(--surface);border-radius:12px;padding:1.1rem;box-shadow:var(--shadow);border:1px solid var(--border);text-decoration:none;color:var(--text);display:flex;align-items:center;gap:.85rem;transition:transform .15s,box-shadow .15s}
.resource-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.12)}
.resource-icon{width:40px;height:40px;border-radius:10px;background:var(--grad);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0}
.resource-title{font-weight:600;font-size:.92rem}
.resource-desc{color:var(--muted);font-size:.8rem}
';

echo page_head('Scholarship Application Tips — GamScholarship', 'Expert tips and resources to help Gambian students win scholarships.', $extra_css);
echo '<body>';
echo site_header('tips');
?>

<div class="page-hero">
    <h1><i class="fas fa-lightbulb"></i> Scholarship Application Tips</h1>
    <p>Proven strategies to help Gambian students win scholarships and study abroad</p>
</div>

<main class="container" style="padding-top:2rem;padding-bottom:2rem">

    <!-- Ad -->
    <div style="text-align:center;margin-bottom:1.5rem">
        <?php echo adsense_unit(AD_SLOT_LEADERBOARD, 'horizontal'); ?>
    </div>

    <div class="tips-grid">
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-pen-fancy"></i></div>
            <h3>Write a Compelling Essay</h3>
            <p>Your personal statement is your chance to stand out. Tell a real story — your background, challenges overcome, and specific goals. Avoid generic phrases. Proofread at least three times, and ask a teacher or friend to review it too.</p>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-calendar-check"></i></div>
            <h3>Start Early — Very Early</h3>
            <p>Top scholarships like Chevening and Gates Cambridge open 8–12 months before the award date. Mark all deadlines on a calendar. Missing a deadline by one day means waiting a full year. Apply for 10+ scholarships simultaneously to improve your odds.</p>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-user-tie"></i></div>
            <h3>Get Strong Recommendation Letters</h3>
            <p>Choose referees who genuinely know your work — a professor who supervised your project, a supervisor who saw you lead. Give them at least 4 weeks notice, a copy of your CV, and notes on what you'd like them to highlight.</p>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-search-plus"></i></div>
            <h3>Understand Every Requirement</h3>
            <p>Read the eligibility criteria carefully before spending time on an application. Many scholarships require specific nationality, GPA, fields of study, or career goals. Don't waste effort on ones you don't qualify for.</p>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-shield-alt"></i></div>
            <h3>Avoid Scholarship Scams</h3>
            <p>Legitimate scholarships never ask for application fees. Only apply through official websites (gov.uk, chevening.org, daad.de, etc.). If a "scholarship" emails you unsolicited asking for a fee, it is a scam — report it.</p>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-language"></i></div>
            <h3>Prove Your English Proficiency</h3>
            <p>Most international scholarships require IELTS (6.5+) or TOEFL (90+). Register early — test centers in Gambia get booked out. Practice with free resources like British Council's LearnEnglish and IELTS.org sample tests.</p>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-network-wired"></i></div>
            <h3>Build Your Network</h3>
            <p>Connect with previous Chevening or DAAD scholars from Gambia on LinkedIn. They can give insider tips, review your essays, and refer you. Join the GamScholarship Community page to meet others applying for the same scholarships.</p>
        </div>
        <div class="tip-card">
            <div class="tip-icon"><i class="fas fa-file-alt"></i></div>
            <h3>Keep Your Documents Ready</h3>
            <p>Prepare digital copies of: passport, degree certificates, transcripts, IELTS/TOEFL result, CV, and a passport photo. Keep them in a Google Drive folder so you can submit instantly when a deadline is near.</p>
        </div>
    </div>

    <!-- Application checklist -->
    <div class="checklist" style="margin-top:2.5rem">
        <h2><i class="fas fa-clipboard-list" style="color:var(--primary)"></i> Scholarship Application Checklist</h2>
        <?php
        $checks = [
            ['Research the scholarship thoroughly', 'Read the FAQ, eligibility criteria, and previous winners\' profiles'],
            ['Contact your referees early', 'Give them at least 4 weeks and a brief of what to include'],
            ['Draft your personal statement', 'Start 6–8 weeks before the deadline, revise multiple times'],
            ['Prepare academic transcripts', 'Official certified copies — many programs require originals by post'],
            ['Get language test results', 'IELTS or TOEFL — check the minimum score required by the scholarship'],
            ['Write a research/study proposal', 'Required for most PhD scholarships — be specific about your topic'],
            ['Register on the scholarship portal', 'Some portals close registration days before the deadline'],
            ['Submit before the deadline', 'Aim to submit 48 hours early — never wait until the last minute'],
        ];
        foreach ($checks as [$title, $desc]): ?>
            <div class="check-item">
                <div class="check-icon"><i class="fas fa-check"></i></div>
                <div class="check-text">
                    <strong><?php echo $title; ?></strong>
                    <span><?php echo $desc; ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Mid ad -->
    <div style="text-align:center;margin:2rem 0">
        <?php echo adsense_unit(AD_SLOT_RECTANGLE); ?>
    </div>

    <!-- Useful resources -->
    <section style="margin-top:2rem">
        <h2 style="font-size:1.2rem;margin-bottom:.25rem">Useful Resources</h2>
        <p style="color:var(--muted);font-size:.92rem;margin:0 0 .5rem">Free tools to help with your application</p>
        <div class="resources-grid">
            <?php
            $resources = [
                ['fas fa-globe', 'Chevening', 'chevening.org', 'https://www.chevening.org/scholarship/how-to-apply/'],
                ['fas fa-book', 'British Council IELTS', 'ielts.org', 'https://www.ielts.org/'],
                ['fas fa-graduation-cap', 'DAAD Germany', 'daad.de', 'https://www.daad.de/en/'],
                ['fas fa-university', 'Mastercard Foundation', 'mastercardfdn.org', 'https://mastercardfdn.org/all/scholars/'],
                ['fas fa-search', 'Scholarship Search (DAAD)', 'daad.de', 'https://www2.daad.de/deutschland/stipendium/datenbank/en/21148-scholarship-database/'],
                ['fas fa-link', 'Study in Europe', 'study-in-europe.org', 'https://www.study-in-europe.org/'],
            ];
            foreach ($resources as [$icon, $name, $domain, $url]): ?>
                <a href="<?php echo $url; ?>" target="_blank" rel="noopener" class="resource-card">
                    <div class="resource-icon"><i class="<?php echo $icon; ?>"></i></div>
                    <div>
                        <div class="resource-title"><?php echo $name; ?></div>
                        <div class="resource-desc"><?php echo $domain; ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
