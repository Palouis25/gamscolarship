<?php
require_once dirname(__DIR__) . '/config/security.php';
require_once dirname(__DIR__) . '/config/layout.php';
$extra_css = '
.guide-hero{background:linear-gradient(135deg,#00008B,#FF0000);color:#fff;padding:3rem 1.2rem;text-align:center}
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
.apply-cta{background:linear-gradient(135deg,#00008B,#FF0000);color:#fff;border-radius:var(--radius);padding:2rem;text-align:center}
.apply-cta h3{margin:0 0 .5rem;font-size:1.2rem;font-weight:800}
.apply-cta p{margin:0 0 1.1rem;opacity:.9}
.apply-cta .btns{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap}
';
echo page_head('Australia Awards Scholarships Guide for Gambian Students','Australia Awards Scholarships — complete guide for Gambian students including eligibility, documents, how to apply, and tips.',$extra_css);
echo '<body>';
echo site_header('guides');
?>
<div style="background:var(--surface);border-bottom:1px solid var(--border);padding:.6rem 1.2rem;font-size:.85rem;color:var(--muted)"><div style="max-width:860px;margin:0 auto"><a href="../index.php" style="color:var(--muted);text-decoration:none">Home</a> / <a href="../guides.php" style="color:var(--muted);text-decoration:none">Guides</a> / <span style="color:var(--text)">Australia Awards Scholarships</span></div></div>
<div class="guide-hero"><span class="flag">🇦🇺</span><h1>Australia Awards Scholarships</h1><p>Australian government scholarships for students from developing countries — fully funded including flights and living costs.</p><div class="meta-pills"><span class="pill">🎓 Masters / PhD</span><span class="pill">💰 Fully Funded</span><span class="pill">📅 Deadline: April</span><span class="pill">🌍 Open to Gambians</span></div></div>
<div class="guide-wrap">
<div style="text-align:center;margin-bottom:1.5rem"><?php echo adsense_unit(AD_SLOT_LEADERBOARD,'horizontal');?></div>
<?php echo guide_verification_notice('Check the official participating-country profile before assuming The Gambia is eligible in the current round.', '17 June 2026', 'https://www.dfat.gov.au/people-to-people/australia-awards/australia-awards-scholarships', 'Australia Awards Scholarships', [
    'Australia Awards eligibility is managed by participating country and region.',
    'Opening and closing dates can differ by country.',
    'Apply only through the official Australia Awards process listed for your country.',
    'If The Gambia is not listed in a round, do not submit through unofficial agents.'
]); ?>
<div class="guide-section"><h2><i class="fas fa-info-circle"></i> Quick Facts</h2><div class="info-grid"><div class="info-box"><div class="info-label">Country</div><div class="info-val">Australia</div></div><div class="info-box"><div class="info-label">Level</div><div class="info-val">Masters / PhD</div></div><div class="info-box"><div class="info-label">Duration</div><div class="info-val">1–4 years</div></div><div class="info-box"><div class="info-label">Deadline</div><div class="info-val">~April each year</div></div><div class="info-box"><div class="info-label">Results</div><div class="info-val">October</div></div><div class="info-box"><div class="info-label">Start</div><div class="info-val">February next year</div></div></div><h3>What this scholarship covers</h3><ul><li>Full tuition fees</li><li>Return economy airfare from Gambia to Australia</li><li>Establishment allowance on arrival</li><li>Contribution to living expenses</li><li>Overseas student health cover (OSHC)</li><li>Pre-course English language training if needed</li><li>Academic support programs</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-check-circle"></i> Eligibility for Gambian Students</h2><ul><li>Must be a Gambian citizen residing in The Gambia</li><li>Must not hold Australian or New Zealand citizenship or permanent residency</li><li>Bachelor's degree with strong academic results</li><li>At least 2 years work experience (preferred but not always mandatory)</li><li>English proficiency — IELTS 6.5 overall (no band below 6.0)</li><li>Commitment to return to Gambia after the award</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-list-ol"></i> How to Apply — Step by Step</h2><ol><li>Visit <a href='https://www.dfat.gov.au/people-to-people/australia-awards/australia-awards-scholarships' target='_blank' rel='noopener'>dfat.gov.au</a> to check if Gambia is listed as an eligible country in the current round</li><li>Applications open around February each year and close in April</li><li>Complete the online application through the Australia Awards portal</li><li>Identify 2–3 Australian universities and programmes you want to study</li><li>Submit all required documents before the April deadline</li></ol></div>
<div class="guide-section"><h2><i class="fas fa-file-alt"></i> Documents You Need</h2><ul><li>Passport copy</li><li>Degree certificate and transcripts</li><li>2 professional or academic references</li><li>IELTS test result</li><li>CV / resume</li><li>Personal statement (500–800 words)</li><li>Employment records (if applicable)</li><li>Any research publications (for PhD)</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-lightbulb"></i> Tips for Gambian Applicants</h2><div class="tip-box"><strong>💡 Tip 1:</strong> Australia particularly values applicants whose study will contribute to development in their home country — specifically in health, education, agriculture, water, and governance.</div><div class="tip-box"><strong>💡 Tip 2:</strong> The programme includes study at an Australian university followed by mandatory return to your home country. Demonstrate clearly how you will apply what you learn in Gambia.</div><div class="tip-box"><strong>💡 Tip 3:</strong> If your IELTS score is slightly below requirement, you can still apply — Australia Awards includes pre-course English training.</div><div class="tip-box"><strong>💡 Tip 4:</strong> Contact the Australian High Commission in Accra, Ghana (covers The Gambia) for specific guidance on Gambian applications.</div></div>
<div class="apply-cta"><h3>Ready to Apply for Australia Awards Scholarships?</h3><p>Visit the official website to start your application.</p><div class="btns"><a href="https://www.dfat.gov.au/people-to-people/australia-awards/australia-awards-scholarships" target="_blank" rel="noopener" class="btn btn-amber"><i class="fas fa-external-link-alt"></i> Apply on dfat.gov.au</a><a href="../how-to-apply.php" class="btn btn-secondary"><i class="fas fa-book"></i> Full Application Guide</a></div></div>
<div style="text-align:center;margin:1.75rem 0"><?php echo adsense_unit(AD_SLOT_RECTANGLE);?></div>
<div style="text-align:center;margin:1rem 0 2rem"><p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Share with Gambian students who need this:</p><?php echo whatsapp_btn('Australia Awards Scholarships Guide for Gambian Students', SITE_URL.'/guides/australia.php','lg');?></div>
<div style="display:flex;gap:1rem;justify-content:space-between;flex-wrap:wrap;font-size:.9rem"><a href="erasmus.php" style="color:var(--primary-light);text-decoration:none"><i class="fas fa-arrow-left"></i> Erasmus Mundus</a><a href="fulbright.php" style="color:var(--primary-light);text-decoration:none">Next: Fulbright USA <i class="fas fa-arrow-right"></i></a></div>
</div>
<?php echo site_footer(); echo cookie_banner(); ?>
</body></html>
