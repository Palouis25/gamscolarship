<?php
require_once dirname(__DIR__) . '/config/security.php';
require_once dirname(__DIR__) . '/config/layout.php';
$extra_css = '
.guide-hero{background:linear-gradient(135deg,#003087,#cf142b);color:#fff;padding:3rem 1.2rem;text-align:center}
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
.apply-cta{background:linear-gradient(135deg,#003087,#cf142b);color:#fff;border-radius:var(--radius);padding:2rem;text-align:center}
.apply-cta h3{margin:0 0 .5rem;font-size:1.2rem;font-weight:800}
.apply-cta p{margin:0 0 1.1rem;opacity:.9}
.apply-cta .btns{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap}
';
echo page_head('Commonwealth Shared Scholarship Guide for Gambian Students','Commonwealth Shared Scholarship — complete guide for Gambian students including eligibility, documents, how to apply, and tips.',$extra_css);
echo '<body>';
echo site_header('guides');
?>
<div style="background:var(--surface);border-bottom:1px solid var(--border);padding:.6rem 1.2rem;font-size:.85rem;color:var(--muted)"><div style="max-width:860px;margin:0 auto"><a href="../index.php" style="color:var(--muted);text-decoration:none">Home</a> / <a href="../guides.php" style="color:var(--muted);text-decoration:none">Guides</a> / <span style="color:var(--text)">Commonwealth Shared Scholarship</span></div></div>
<div class="guide-hero"><span class="flag">🇬🇧</span><h1>Commonwealth Shared Scholarship</h1><p>For Commonwealth citizens to study a master's in the UK — fully funded by the UK government.</p><div class="meta-pills"><span class="pill">🎓 Master's Degree</span><span class="pill">💰 Fully Funded</span><span class="pill">📅 Deadline: December</span><span class="pill">🌍 Open to Gambians</span></div></div>
<div class="guide-wrap">
<div style="text-align:center;margin-bottom:1.5rem"><?php echo adsense_unit(AD_SLOT_LEADERBOARD,'horizontal');?></div>
<?php echo guide_verification_notice('Deadline and eligible courses must be checked on the Commonwealth Scholarship Commission site before applying.', '17 June 2026', 'https://cscuk.fcdo.gov.uk/apply/', 'Commonwealth Scholarship Commission apply page', [
    'Use only the official CSC application system and official university/course links.',
    'Exact opening and closing dates can change by award type.',
    'Connect your course choice clearly to development impact in The Gambia.',
    'Do not pay an agent who promises a Commonwealth Scholarship.'
]); ?>
<div class="guide-section"><h2><i class="fas fa-info-circle"></i> Quick Facts</h2><div class="info-grid"><div class="info-box"><div class="info-label">Country</div><div class="info-val">UK</div></div><div class="info-box"><div class="info-label">Level</div><div class="info-val">Masters</div></div><div class="info-box"><div class="info-label">Duration</div><div class="info-val">1 year</div></div><div class="info-box"><div class="info-label">Deadline</div><div class="info-val">~December</div></div><div class="info-box"><div class="info-label">Results</div><div class="info-val">April</div></div><div class="info-box"><div class="info-label">Start</div><div class="info-val">September</div></div></div><h3>What this scholarship covers</h3><ul><li>Full university tuition fees</li><li>Monthly living allowance</li><li>Return flights from Gambia to UK</li><li>Arrival and departure allowance</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-check-circle"></i> Eligibility for Gambian Students</h2><ul><li>Must be a Gambian citizen</li><li>Undergraduate degree (upper second class or above)</li><li>Must be committed to returning to Gambia after studies</li><li>Cannot have studied in the UK before on a Commonwealth scholarship</li><li>Must be applying for your first master's degree</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-list-ol"></i> How to Apply — Step by Step</h2><ol><li>Register at <a href='https://cscuk.fcdo.gov.uk/apply/' target='_blank' rel='noopener'>cscuk.fcdo.gov.uk</a> when applications open</li><li>Apply to a UK university for the programme you want</li><li>Complete the online application form including 3 short essays</li><li>Submit 2 reference letters</li><li>Upload degree certificate, transcripts, passport, English test</li><li>Submit before the December deadline</li></ol></div>
<div class="guide-section"><h2><i class="fas fa-file-alt"></i> Documents You Need</h2><ul><li>Degree certificate (certified copy)</li><li>Academic transcripts</li><li>2 reference letters</li><li>Personal statement (500 words)</li><li>IELTS/TOEFL result</li><li>Passport copy</li><li>Passport photo</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-lightbulb"></i> Tips for Gambian Applicants</h2><div class="tip-box"><strong>💡 Tip 1:</strong> Commonwealth specifically funds students in fields related to development — health, education, agriculture, governance. Connect your application to development in Gambia.</div><div class="tip-box"><strong>💡 Tip 2:</strong> Unlike Chevening, Commonwealth does NOT require work experience — recent graduates can apply. This makes it ideal if you just finished your bachelor's degree.</div><div class="tip-box"><strong>💡 Tip 3:</strong> The personal statement (500 words) must explain: why this course, why the UK, and most importantly — how will you contribute to Gambia's development on return?</div></div>
<div class="apply-cta"><h3>Ready to Apply for Commonwealth Shared Scholarship?</h3><p>Visit the official website to start your application.</p><div class="btns"><a href="https://cscuk.fcdo.gov.uk/apply/" target="_blank" rel="noopener" class="btn btn-amber"><i class="fas fa-external-link-alt"></i> Apply on cscuk.fcdo.gov.uk</a><a href="../how-to-apply.php" class="btn btn-secondary"><i class="fas fa-book"></i> Full Application Guide</a></div></div>
<div style="text-align:center;margin:1.75rem 0"><?php echo adsense_unit(AD_SLOT_RECTANGLE);?></div>
<div style="text-align:center;margin:1rem 0 2rem"><p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Share with Gambian students who need this:</p><?php echo whatsapp_btn('Commonwealth Shared Scholarship Guide for Gambian Students', SITE_URL.'/guides/commonwealth.php','lg');?></div>
<div style="display:flex;gap:1rem;justify-content:space-between;flex-wrap:wrap;font-size:.9rem"><a href="daad.php" style="color:var(--primary-light);text-decoration:none"><i class="fas fa-arrow-left"></i> DAAD Germany</a><a href="mastercard.php" style="color:var(--primary-light);text-decoration:none">Next: Mastercard Foundation <i class="fas fa-arrow-right"></i></a></div>
</div>
<?php echo site_footer(); echo cookie_banner(); ?>
</body></html>
