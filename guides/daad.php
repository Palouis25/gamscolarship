<?php
require_once dirname(__DIR__) . '/config/security.php';
require_once dirname(__DIR__) . '/config/layout.php';
$extra_css = '
.guide-hero{background:linear-gradient(135deg,#000000,#dd0000);color:#fff;padding:3rem 1.2rem;text-align:center}
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
.info-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.75rem;margin:1rem 0}
.info-box{background:var(--bg);padding:.9rem;border-radius:10px;border:1px solid var(--border)}
.info-label{font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--muted);margin-bottom:.25rem}
.info-val{font-weight:700;font-size:.95rem}
.tip-box{background:#fffbeb;border-left:4px solid var(--secondary);border-radius:8px;padding:.85rem 1rem;font-size:.88rem;color:#92400e;margin:1rem 0}
.tip-box strong{color:#78350f;display:block;margin-bottom:.2rem}
.apply-cta{background:linear-gradient(135deg,#000,#dd0000);color:#fff;border-radius:var(--radius);padding:2rem;text-align:center}
.apply-cta h3{margin:0 0 .5rem;font-size:1.2rem;font-weight:800}
.apply-cta p{margin:0 0 1.1rem;opacity:.9}
.apply-cta .btns{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap}
';
echo page_head('DAAD Scholarship Germany Guide for Gambian Students','Complete guide for Gambian students applying for DAAD Germany scholarships — eligibility, application process, and tips.',$extra_css);
echo '<body>';
echo site_header('guides');
?>
<div style="background:var(--surface);border-bottom:1px solid var(--border);padding:.6rem 1.2rem;font-size:.85rem;color:var(--muted)"><div style="max-width:860px;margin:0 auto"><a href="../index.php" style="color:var(--muted);text-decoration:none">Home</a> / <a href="../guides.php" style="color:var(--muted);text-decoration:none">Guides</a> / <span style="color:var(--text)">DAAD Germany</span></div></div>
<div class="guide-hero"><span class="flag">🇩🇪</span><h1>DAAD Scholarship — Germany</h1><p>Germany's top scholarship for students from developing countries. Fully funded master's and PhD programmes.</p><div class="meta-pills"><span class="pill">🎓 Masters / PhD</span><span class="pill">💰 Fully Funded</span><span class="pill">📅 Deadline: September/October</span><span class="pill">🌍 Open to Gambians</span></div></div>
<div class="guide-wrap">
<div style="text-align:center;margin-bottom:1.5rem"><?php echo adsense_unit(AD_SLOT_LEADERBOARD,'horizontal');?></div>
<?php echo guide_verification_notice('Use the DAAD scholarship database for the exact deadline for your programme.', '17 June 2026', 'https://www.daad.de/en/studying-in-germany/scholarships/daad-scholarships/', 'DAAD scholarships overview', [
    'DAAD says deadlines are listed in each scholarship call inside the scholarship database.',
    'DAAD funding is mainly for graduates, doctoral students, and postdocs.',
    'Funding amounts and benefits depend on the scholarship programme.',
    'For development-related programmes, check the individual course call before applying.'
]); ?>
<div class="guide-section"><h2><i class="fas fa-info-circle"></i> Quick Facts</h2><div class="info-grid"><div class="info-box"><div class="info-label">Country</div><div class="info-val">Germany</div></div><div class="info-box"><div class="info-label">Level</div><div class="info-val">Masters / PhD</div></div><div class="info-box"><div class="info-label">Duration</div><div class="info-val">1–4 years</div></div><div class="info-box"><div class="info-label">Deadline</div><div class="info-val">September–October</div></div><div class="info-box"><div class="info-label">Language</div><div class="info-val">English or German</div></div><div class="info-box"><div class="info-label">Website</div><div class="info-val">daad.de</div></div></div><h3>What DAAD covers</h3><ul><li>Full tuition fees at a German university</li><li>Monthly stipend: €934 for master's students, €1,200 for PhD students</li><li>Health, accident and personal liability insurance</li><li>Travel allowance (flights to/from Germany)</li><li>Rent subsidy in some cases</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-check-circle"></i> Eligibility for Gambian Students</h2><ul><li>Gambian citizens currently residing in The Gambia</li><li>Bachelor's degree with strong academic results (minimum upper second class)</li><li>Usually required: 2 years of relevant work or research experience</li><li>Language: most English-taught programmes require IELTS 6.0+ or TOEFL 80+. German-taught programmes require TestDaF or DSH certificate.</li><li>Must apply to a German university programme separately and be accepted (or apply simultaneously)</li></ul><div class="tip-box"><strong>💡 Gambia Tip:</strong> Germany has many English-taught master's programmes — you do NOT need to speak German to study there. Search daad.de for "English-taught programmes in Germany".</div></div>
<div class="guide-section"><h2><i class="fas fa-list-ol"></i> How to Apply</h2><ol><li>Visit <strong>daad.de</strong> and search for the scholarship that matches your field and level</li><li>Find a German university and programme you want to apply to — you need an acceptance letter or apply simultaneously</li><li>Create a DAAD portal account at <strong>portal.daad.de</strong></li><li>Complete the online application form</li><li>Upload: CV, motivation letter, degree certificate, transcripts, language certificate, 2 reference letters</li><li>Submit before the deadline — usually September or October for the following year</li></ol><div class="tip-box"><strong>💡 Motivation Letter tip:</strong> Your DAAD motivation letter should explain your research/study plan, why Germany specifically, and how the scholarship connects to development in The Gambia. Be specific — mention professors or research groups you want to work with.</div></div>
<div class="guide-section"><h2><i class="fas fa-file-alt"></i> Documents You Need</h2><ul><li>Completed DAAD online application form</li><li>CV in DAAD format (template available on their website)</li><li>Motivation letter (2 pages maximum)</li><li>University degree certificate (certified copy)</li><li>Academic transcripts (certified copy)</li><li>2 academic or professional reference letters</li><li>Language certificate (IELTS/TOEFL for English programmes)</li><li>Copy of passport</li><li>If applying for PhD: research proposal (3–5 pages)</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-lightbulb"></i> Tips Specifically for Gambian Applicants</h2><ul><li>Germany is particularly interested in students from West Africa working in <strong>agriculture, health, governance and environment</strong> — highlight connections to these fields</li><li>Contact German professors directly before applying — if a professor agrees to supervise you, it significantly strengthens your PhD application</li><li>Many Gambian DAAD scholars have studied at TU Berlin, University of Cologne, and University of Bonn — search for Gambians who studied there and ask for advice</li><li>Germany offers a <strong>post-study work visa</strong> — but DAAD requires you return to Gambia. Plan your timeline carefully.</li></ul></div>
<div class="apply-cta"><h3>Apply for DAAD Germany</h3><p>Applications open each year — search for your specific programme on the DAAD website.</p><div class="btns"><a href="https://www.daad.de/en/study-and-research-in-germany/scholarships/daad-scholarships/" target="_blank" rel="noopener" class="btn btn-amber"><i class="fas fa-external-link-alt"></i> Apply on daad.de</a><a href="../how-to-apply.php" class="btn btn-secondary"><i class="fas fa-book"></i> Full Application Guide</a></div></div>
<div style="text-align:center;margin:1.75rem 0"><?php echo adsense_unit(AD_SLOT_RECTANGLE);?></div>
<div style="text-align:center;margin:1rem 0 2rem"><p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Share this guide with Gambian students:</p><?php echo whatsapp_btn('DAAD Germany Scholarship Guide for Gambian Students', SITE_URL.'/guides/daad.php','lg');?></div>
<div style="display:flex;gap:1rem;justify-content:space-between;flex-wrap:wrap;font-size:.9rem"><a href="chevening.php" style="color:var(--primary-light);text-decoration:none"><i class="fas fa-arrow-left"></i> Chevening</a><a href="commonwealth.php" style="color:var(--primary-light);text-decoration:none">Next: Commonwealth <i class="fas fa-arrow-right"></i></a></div>
</div>
<?php echo site_footer(); echo cookie_banner(); ?>
</body></html>
