<?php
require_once dirname(__DIR__) . '/config/security.php';
require_once dirname(__DIR__) . '/config/layout.php';
$extra_css = '
.guide-hero{background:linear-gradient(135deg,#eb001b,#f79e1b);color:#fff;padding:3rem 1.2rem;text-align:center}
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
.apply-cta{background:linear-gradient(135deg,#eb001b,#f79e1b);color:#fff;border-radius:var(--radius);padding:2rem;text-align:center}
.apply-cta h3{margin:0 0 .5rem;font-size:1.2rem;font-weight:800}
.apply-cta p{margin:0 0 1.1rem;opacity:.9}
.apply-cta .btns{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap}
';
echo page_head('Mastercard Foundation Scholars Program Guide for Gambian Students','Mastercard Foundation Scholars Program — complete guide for Gambian students including eligibility, documents, how to apply, and tips.',$extra_css);
echo '<body>';
echo site_header('guides');
?>
<div style="background:var(--surface);border-bottom:1px solid var(--border);padding:.6rem 1.2rem;font-size:.85rem;color:var(--muted)"><div style="max-width:860px;margin:0 auto"><a href="../index.php" style="color:var(--muted);text-decoration:none">Home</a> / <a href="../guides.php" style="color:var(--muted);text-decoration:none">Guides</a> / <span style="color:var(--text)">Mastercard Foundation Scholars Program</span></div></div>
<div class="guide-hero"><span class="flag">🌍</span><h1>Mastercard Foundation Scholars Program</h1><p>Full scholarships for talented young Africans — covering tuition, accommodation, travel and living expenses.</p><div class="meta-pills"><span class="pill">🎓 Undergraduate / Masters</span><span class="pill">💰 Fully Funded</span><span class="pill">📅 Deadline: Varies</span><span class="pill">🌍 For Africans</span></div></div>
<div class="guide-wrap">
<div style="text-align:center;margin-bottom:1.5rem"><?php echo adsense_unit(AD_SLOT_LEADERBOARD,'horizontal');?></div>
<?php echo guide_verification_notice('Deadlines vary by Mastercard Foundation partner institution.', '17 June 2026', 'https://mastercardfdn.org/all/scholars/', 'Mastercard Foundation Scholars Program', [
    'Apply through the partner university or institution, not through a single central Mastercard application.',
    'Each partner sets its own deadline, degree levels, and required documents.',
    'The programme focuses on talented young Africans, financial need, leadership, and giving back.',
    'Check the official partner page before preparing documents.'
]); ?>
<div class="guide-section"><h2><i class="fas fa-info-circle"></i> Quick Facts</h2><div class="info-grid"><div class="info-box"><div class="info-label">Region</div><div class="info-val">Africa & Beyond</div></div><div class="info-box"><div class="info-label">Level</div><div class="info-val">Undergrad / Masters</div></div><div class="info-box"><div class="info-label">Funding</div><div class="info-val">100% Full cost</div></div><div class="info-box"><div class="info-label">Deadline</div><div class="info-val">Varies by university</div></div><div class="info-box"><div class="info-label">Website</div><div class="info-val">mastercardfdn.org</div></div></div><h3>What this scholarship covers</h3><ul><li>Full tuition fees</li><li>Accommodation on campus</li><li>Meals allowance</li><li>Books and supplies</li><li>Return travel to/from home country</li><li>Health insurance</li><li>Personal development activities</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-check-circle"></i> Eligibility for Gambian Students</h2><ul><li>Must be African — Gambian students are eligible</li><li>Academic excellence (strong grades required)</li><li>Demonstrated financial need</li><li>Commitment to give back to Africa after graduation</li><li>Leadership potential</li><li>Many partner universities are in Africa itself (no need to travel overseas)</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-list-ol"></i> How to Apply — Step by Step</h2><ol><li>Visit <a href='https://mastercardfdn.org/all/scholars/' target='_blank' rel='noopener'>mastercardfdn.org</a> to see all partner universities</li><li>Choose a partner university that offers your programme</li><li>Apply directly to that university through their Mastercard Foundation Scholars portal</li><li>Each university has its own deadline and process — check the specific university page</li><li>Submit: academic records, essays, 2 references, proof of financial need</li></ol></div>
<div class="guide-section"><h2><i class="fas fa-file-alt"></i> Documents You Need</h2><ul><li>Academic transcripts and certificates</li><li>2 letters of recommendation</li><li>Personal statement / motivation letter</li><li>Evidence of financial need (family income statement)</li><li>Copy of national ID or passport</li><li>Essay responses (questions vary by university)</li></ul></div>
<div class="guide-section"><h2><i class="fas fa-lightbulb"></i> Tips for Gambian Applicants</h2><div class="tip-box"><strong>💡 Tip 1:</strong> Partner universities include University of Ghana, AIMS Rwanda, African Leadership University, and many more — you don't have to leave Africa to benefit.</div><div class="tip-box"><strong>💡 Tip 2:</strong> The selection focuses heavily on leadership potential and commitment to Africa. Give concrete examples of community involvement.</div><div class="tip-box"><strong>💡 Tip 3:</strong> Some partner universities are in the US and Canada too — check the full list on mastercardfdn.org as it grows every year.</div><div class="tip-box"><strong>💡 Tip 4:</strong> Financial need is assessed — be honest and provide accurate documentation of your family's financial situation.</div></div>
<div class="apply-cta"><h3>Ready to Apply for Mastercard Foundation Scholars Program?</h3><p>Visit the official website to start your application.</p><div class="btns"><a href="https://mastercardfdn.org/all/scholars/" target="_blank" rel="noopener" class="btn btn-amber"><i class="fas fa-external-link-alt"></i> Apply on mastercardfdn.org</a><a href="../how-to-apply.php" class="btn btn-secondary"><i class="fas fa-book"></i> Full Application Guide</a></div></div>
<div style="text-align:center;margin:1.75rem 0"><?php echo adsense_unit(AD_SLOT_RECTANGLE);?></div>
<div style="text-align:center;margin:1rem 0 2rem"><p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Share with Gambian students who need this:</p><?php echo whatsapp_btn('Mastercard Foundation Scholars Program Guide for Gambian Students', SITE_URL.'/guides/mastercard.php','lg');?></div>
<div style="display:flex;gap:1rem;justify-content:space-between;flex-wrap:wrap;font-size:.9rem"><a href="commonwealth.php" style="color:var(--primary-light);text-decoration:none"><i class="fas fa-arrow-left"></i> Commonwealth</a><a href="erasmus.php" style="color:var(--primary-light);text-decoration:none">Next: Erasmus Mundus <i class="fas fa-arrow-right"></i></a></div>
</div>
<?php echo site_footer(); echo cookie_banner(); ?>
</body></html>
