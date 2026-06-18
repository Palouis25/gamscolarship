<?php
require_once dirname(__DIR__) . '/config/security.php';
require_once dirname(__DIR__) . '/config/layout.php';
$extra_css = '
.guide-hero{background:linear-gradient(135deg,#002868,#BF0A30);color:#fff;padding:3rem 1.2rem;text-align:center}
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
.apply-cta{background:linear-gradient(135deg,#002868,#BF0A30);color:#fff;border-radius:var(--radius);padding:2rem;text-align:center}
.apply-cta h3{margin:0 0 .5rem;font-size:1.2rem;font-weight:800}
.apply-cta p{margin:0 0 1.1rem;opacity:.9}
.apply-cta .btns{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap}
';
echo page_head('Fulbright Foreign Student Program Guide for Gambian Students','Fulbright Foreign Student Program — complete guide for Gambian students including eligibility, documents, how to apply, and tips.',$extra_css);
echo '<body>';
echo site_header('guides');
?>
<div style="background:var(--surface);border-bottom:1px solid var(--border);padding:.6rem 1.2rem;font-size:.85rem;color:var(--muted)"><div style="max-width:860px;margin:0 auto"><a href="../index.php" style="color:var(--muted);text-decoration:none">Home</a> / <a href="../guides.php" style="color:var(--muted);text-decoration:none">Guides</a> / <span style="color:var(--text)">Fulbright Foreign Student Program</span></div></div>
<div class="guide-hero"><span class="flag">🇺🇸</span><h1>Fulbright Foreign Student Program</h1><p>The US government's most prestigious scholarship for international students. Fully funded master's and PhD programmes.</p><div class="meta-pills"><span class="pill">🎓 Masters / PhD</span><span class="pill">💰 Fully Funded</span><span class="pill">📅 Deadline: October</span><span class="pill">🌍 Open to Gambians</span></div></div>
<div class="guide-wrap">
<div style="text-align:center;margin-bottom:1.5rem"><?php echo adsense_unit(AD_SLOT_LEADERBOARD,'horizontal');?></div>
<?php echo guide_verification_notice('Fulbright deadlines and fields are country-specific; confirm through the U.S. Embassy or official Fulbright country page.', '17 June 2026', 'https://foreign.fulbrightonline.org/about/foreign-fulbright', 'Fulbright Foreign Student Program', [
    'The Foreign Student Program is administered by U.S. embassies, Fulbright commissions, or foundations in each country.',
    'Application instructions and deadlines vary by country.',
    'Use the official Fulbright portal and the U.S. Embassy information for The Gambia.',
    'Do not rely on copied deadline dates from scholarship blogs.'
]); ?>
<div class="guide-section"><h2><i class="fas fa-info-circle"></i> Quick Facts</h2><div class="info-grid"><div class="info-box"><div class="info-label">Country</div><div class="info-val">USA</div></div><div class="info-box"><div class="info-label">Level</div><div class="info-val">Masters / PhD</div></div><div class="info-box"><div class="info-label">Duration</div><div class="info-val">1–3 years</div></div><div class="info-box"><div class="info-label">Deadline</div><div class="info-val">~October</div></div><div class="info-box"><div class="info-label">Results</div><div class="info-val">March/April</div></div><div class="info-box"><div class="info-label">Start</div><div class="info-val">August next year</div></div></div><h3>What this scholarship covers</h3><ul><li>Full tuition and fees at a US university</li><li>Monthly living stipend</li><li>Round-trip airfare to and from the USA</li><li>Accident and sickness insurance</li><li>Pre-academic English training if needed</li><li>Cultural enrichment activities</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-check-circle"></i> Eligibility for Gambian Students</h2><ul><li>Must be a Gambian citizen currently living in The Gambia</li><li>Cannot hold US citizenship or permanent residency</li><li>Bachelor's degree with strong academic results</li><li>English proficiency: TOEFL 80+ (iBT) or IELTS 7.0+</li><li>Strong leadership potential and community involvement</li><li>Commitment to return to The Gambia after studies</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-list-ol"></i> How to Apply — Step by Step</h2><ol><li>Contact the US Embassy in Banjul or visit <a href='https://foreign.fulbrightonline.org/' target='_blank' rel='noopener'>fulbrightonline.org</a> for Gambia-specific application details</li><li>Apply through the Fulbright online application portal</li><li>Write 2 essays: a Personal Statement and a Statement of Purpose</li><li>Request 3 reference letters from academic or professional contacts</li><li>Applications typically open in May and close in October</li><li>Shortlisted candidates are interviewed at the US Embassy in Banjul</li></ol></div>
<div class="guide-section"><h2><i class="fas fa-file-alt"></i> Documents You Need</h2><ul><li>Completed Fulbright online application</li><li>Personal Statement (1 page)</li><li>Statement of Purpose (2 pages — describes your proposed study)</li><li>3 reference letters</li><li>Bachelor's degree and transcripts</li><li>TOEFL or IELTS score</li><li>CV/resume</li><li>Passport copy</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-lightbulb"></i> Tips for Gambian Applicants</h2><div class="tip-box"><strong>💡 Tip 1:</strong> The Fulbright is highly competitive globally but less well-known in West Africa — fewer Gambians apply, meaning your chances may be better than you think.</div><div class="tip-box"><strong>💡 Tip 2:</strong> Your Statement of Purpose must clearly state what you will study, at which US universities, and why this study is critical for The Gambia. Be very specific.</div><div class="tip-box"><strong>💡 Tip 3:</strong> Contact the US Embassy Public Affairs section in Banjul — they often run Fulbright information sessions and can advise Gambian applicants directly.</div><div class="tip-box"><strong>💡 Tip 4:</strong> Fulbright is not just about academics — they want well-rounded people with cultural curiosity. Highlight any community work, sports, arts or cultural activities.</div></div>
<div class="apply-cta"><h3>Ready to Apply for Fulbright Foreign Student Program?</h3><p>Visit the official website to start your application.</p><div class="btns"><a href="https://foreign.fulbrightonline.org/" target="_blank" rel="noopener" class="btn btn-amber"><i class="fas fa-external-link-alt"></i> Apply on fulbrightonline.org</a><a href="../how-to-apply.php" class="btn btn-secondary"><i class="fas fa-book"></i> Full Application Guide</a></div></div>
<div style="text-align:center;margin:1.75rem 0"><?php echo adsense_unit(AD_SLOT_RECTANGLE);?></div>
<div style="text-align:center;margin:1rem 0 2rem"><p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Share with Gambian students who need this:</p><?php echo whatsapp_btn('Fulbright Foreign Student Program Guide for Gambian Students', SITE_URL.'/guides/fulbright.php','lg');?></div>
<div style="display:flex;gap:1rem;justify-content:space-between;flex-wrap:wrap;font-size:.9rem"><a href="australia.php" style="color:var(--primary-light);text-decoration:none"><i class="fas fa-arrow-left"></i> Australia Awards</a><a href="gates.php" style="color:var(--primary-light);text-decoration:none">Next: Gates Cambridge <i class="fas fa-arrow-right"></i></a></div>
</div>
<?php echo site_footer(); echo cookie_banner(); ?>
</body></html>
