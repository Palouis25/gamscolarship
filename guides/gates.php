<?php
require_once dirname(__DIR__) . '/config/security.php';
require_once dirname(__DIR__) . '/config/layout.php';
$extra_css = '
.guide-hero{background:linear-gradient(135deg,#003087,#012169);color:#fff;padding:3rem 1.2rem;text-align:center}
.guide-hero .flag{font-size:3rem;display:block;margin-bottom:.75rem}
.guide-hero h1{font-size:clamp(1.5rem,4vw,2.4rem);font-weight:800;margin:0 0 .5rem}
.guide-hero p{opacity:.92;font-size:1rem;margin:0 auto;max-width:600px}
.guide-hero .meta-pills{display:flex;justify-content:center;gap:.6rem;flex-wrap:wrap;margin-top:1.1rem}
.guide-hero .pill{background:rgba(255,255,255,.18);padding:.3rem .85rem;border-radius:99px;font-size:.82rem;font-weight:600}
.guide-wrap{max-width:860px;margin:0 auto;padding:2rem 1.2rem 3rem}
.guide-section{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border);padding:1.75rem;margin-bottom:1.5rem}
.guide-section h2{margin:0 0 1rem;font-size:1.15rem;font-weight:800;color:var(--primary);padding-bottom:.6rem;border-bottom:2px solid var(--border);display:flex;align-items:center;gap:.5rem}
.guide-section h3{font-size:1rem;font-weight:700;margin:1.1rem 0 .4rem}
.guide-section p,.guide-section li{color:var(--muted);font-size:.92rem;line-height:1.75}
.guide-section ul,.guide-section ol{padding-left:1.3rem;margin:.4rem 0}
.info-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:.75rem;margin:1rem 0}
.info-box{background:var(--bg);padding:.9rem;border-radius:10px;border:1px solid var(--border)}
.info-label{font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--muted);margin-bottom:.25rem}
.info-val{font-weight:700;font-size:.93rem}
.tip-box{background:#fffbeb;border-left:4px solid var(--secondary);border-radius:8px;padding:.85rem 1rem;font-size:.88rem;color:#92400e;margin:1rem 0}
.tip-box strong{color:#78350f;display:block;margin-bottom:.2rem}
.apply-cta{background:linear-gradient(135deg,#003087,#012169);color:#fff;border-radius:var(--radius);padding:2rem;text-align:center}
.apply-cta h3{margin:0 0 .5rem;font-size:1.2rem;font-weight:800}
.apply-cta p{margin:0 0 1.1rem;opacity:.9}
.apply-cta .btns{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap}
';
echo page_head('Gates Cambridge Scholarship Guide for Gambian Students','Gates Cambridge Scholarship — complete guide for Gambian students including eligibility, documents, how to apply, and tips.',$extra_css);
echo '<body>';
echo site_header('guides');
?>
<div style="background:var(--surface);border-bottom:1px solid var(--border);padding:.6rem 1.2rem;font-size:.85rem;color:var(--muted)"><div style="max-width:860px;margin:0 auto"><a href="../index.php" style="color:var(--muted);text-decoration:none">Home</a> / <a href="../guides.php" style="color:var(--muted);text-decoration:none">Guides</a> / <span style="color:var(--text)">Gates Cambridge Scholarship</span></div></div>
<div class="guide-hero"><span class="flag">🇬🇧</span><h1>Gates Cambridge Scholarship</h1><p>One of the world's most prestigious scholarships — full funding to study any subject at Cambridge University.</p><div class="meta-pills"><span class="pill">🎓 Masters / PhD</span><span class="pill">💰 Full Cost</span><span class="pill">📅 Deadline: October</span><span class="pill">🌍 Open Worldwide</span></div></div>
<div class="guide-wrap">
<div style="text-align:center;margin-bottom:1.5rem"><?php echo adsense_unit(AD_SLOT_LEADERBOARD,'horizontal');?></div>
<?php echo guide_verification_notice('Applications for the 2026/27 academic year are closed; the next round is expected to open in September 2026.', '17 June 2026', 'https://www.gatescambridge.org/apply/timeline/', 'Gates Cambridge timeline', [
    'The 2026/27 application rounds closed in December 2025 and January 2026, depending on course and round.',
    'Applicants apply through the University of Cambridge postgraduate application system.',
    'There is no separate standalone Gates Cambridge form outside the Cambridge application.',
    'Prepare your Cambridge course choice, research proposal, and references before the next opening.'
]); ?>
<div class="guide-section"><h2><i class="fas fa-info-circle"></i> Quick Facts</h2><div class="info-grid"><div class="info-box"><div class="info-label">University</div><div class="info-val">Cambridge, UK</div></div><div class="info-box"><div class="info-label">Level</div><div class="info-val">Masters / PhD</div></div><div class="info-box"><div class="info-label">Funding</div><div class="info-val">Full cost of study</div></div><div class="info-box"><div class="info-label">Deadline</div><div class="info-val">~October (PhD) / Jan (Masters)</div></div><div class="info-box"><div class="info-label">Website</div><div class="info-val">gatescambridge.org</div></div></div><h3>What this scholarship covers</h3><ul><li>Full University of Cambridge tuition fees</li><li>Maintenance allowance (£21,000/year)</li><li>Return airfare from Gambia</li><li>Visa costs</li><li>Family allowance if applicable</li><li>Academic development funding</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-check-circle"></i> Eligibility for Gambian Students</h2><ul><li>Any nationality outside the UK — Gambian students are eligible</li><li>Must apply for admission to Cambridge University at the same time</li><li>Outstanding academic achievement — typically top 1% of graduates</li><li>Strong leadership, commitment to improving lives of others</li><li>Research potential (especially for PhD)</li><li>Must have a place or be applying for a place at Cambridge</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-list-ol"></i> How to Apply — Step by Step</h2><ol><li>First apply to a programme at the University of Cambridge at <a href='https://www.graduate.study.cam.ac.uk' target='_blank' rel='noopener'>graduate.study.cam.ac.uk</a></li><li>Within your Cambridge application, tick the box indicating you wish to be considered for Gates Cambridge</li><li>There is no separate Gates Cambridge application — it is done within the Cambridge admission form</li><li>Shortlisted candidates are invited to an interview (usually by video call)</li><li>The Gates Cambridge selection focuses on your research proposal and leadership vision</li></ol></div>
<div class="guide-section"><h2><i class="fas fa-file-alt"></i> Documents You Need</h2><ul><li>Cambridge University application (all supporting documents)</li><li>Academic transcripts and certificates</li><li>Research proposal (2–3 pages for PhD)</li><li>Personal statement</li><li>3 reference letters (submitted through Cambridge portal)</li><li>IELTS or TOEFL score</li><li>CV</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-lightbulb"></i> Tips for Gambian Applicants</h2><div class="tip-box"><strong>💡 Tip 1:</strong> Gates Cambridge is one of the hardest scholarships in the world — but there is no harm applying if you have outstanding grades and a strong research vision.</div><div class="tip-box"><strong>💡 Tip 2:</strong> The selection criteria: intellectual ability, leadership potential, commitment to improving lives, and fit with Cambridge. Address all four explicitly in your personal statement.</div><div class="tip-box"><strong>💡 Tip 3:</strong> Contact Cambridge professors in your field before applying — having a supervisor who wants to work with you significantly increases your chances for PhD applications.</div><div class="tip-box"><strong>💡 Tip 4:</strong> For Gambian students: your unique perspective as someone from a developing nation with direct experience of development challenges is genuinely valued at Cambridge — use it.</div></div>
<div class="apply-cta"><h3>Ready to Apply for Gates Cambridge Scholarship?</h3><p>Visit the official website to start your application.</p><div class="btns"><a href="https://www.gatescambridge.org/apply/" target="_blank" rel="noopener" class="btn btn-amber"><i class="fas fa-external-link-alt"></i> Apply on gatescambridge.org</a><a href="../how-to-apply.php" class="btn btn-secondary"><i class="fas fa-book"></i> Full Application Guide</a></div></div>
<div style="text-align:center;margin:1.75rem 0"><?php echo adsense_unit(AD_SLOT_RECTANGLE);?></div>
<div style="text-align:center;margin:1rem 0 2rem"><p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Share with Gambian students who need this:</p><?php echo whatsapp_btn('Gates Cambridge Scholarship Guide for Gambian Students', SITE_URL.'/guides/gates.php','lg');?></div>
<div style="display:flex;gap:1rem;justify-content:space-between;flex-wrap:wrap;font-size:.9rem"><a href="fulbright.php" style="color:var(--primary-light);text-decoration:none"><i class="fas fa-arrow-left"></i> Fulbright USA</a><a href="../guides.php" style="color:var(--primary-light);text-decoration:none">Next: All Guides <i class="fas fa-arrow-right"></i></a></div>
</div>
<?php echo site_footer(); echo cookie_banner(); ?>
</body></html>
