<?php
require_once dirname(__DIR__) . '/config/security.php';
require_once dirname(__DIR__) . '/config/layout.php';

$extra_css = '
.guide-hero{background:linear-gradient(135deg,#003087,#1d4ed8);color:#fff;padding:3rem 1.2rem;text-align:center}
.guide-hero .flag{font-size:3rem;display:block;margin-bottom:.75rem}
.guide-hero h1{font-size:clamp(1.5rem,4vw,2.4rem);font-weight:800;margin:0 0 .5rem;letter-spacing:-.02em}
.guide-hero p{opacity:.92;font-size:1rem;margin:0 auto;max-width:600px}
.guide-hero .meta-pills{display:flex;justify-content:center;gap:.6rem;flex-wrap:wrap;margin-top:1.1rem}
.guide-hero .pill{background:rgba(255,255,255,.18);padding:.3rem .85rem;border-radius:99px;font-size:.82rem;font-weight:600}
.guide-wrap{max-width:860px;margin:0 auto;padding:2rem 1.2rem 3rem}
.guide-section{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border);padding:1.75rem;margin-bottom:1.5rem}
.guide-section h2{margin:0 0 1rem;font-size:1.15rem;font-weight:800;color:var(--primary);display:flex;align-items:center;gap:.5rem;padding-bottom:.6rem;border-bottom:2px solid var(--border)}
.guide-section h3{font-size:1rem;font-weight:700;margin:1.1rem 0 .45rem;color:var(--text)}
.guide-section p{color:var(--muted);font-size:.92rem;line-height:1.75;margin:.4rem 0}
.guide-section ul,.guide-section ol{padding-left:1.3rem;color:var(--muted);font-size:.91rem;line-height:1.85;margin:.4rem 0}
.guide-section li{margin-bottom:.25rem}
.info-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(175px,1fr));gap:.75rem;margin:1rem 0}
.info-box{background:var(--bg);padding:.9rem 1rem;border-radius:10px;border:1px solid var(--border)}
.info-label{font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;font-weight:700;color:var(--muted);margin-bottom:.3rem}
.info-val{font-weight:700;font-size:.97rem;color:var(--text)}
.tip-box{background:#fffbeb;border-left:4px solid var(--secondary);border-radius:8px;padding:.85rem 1rem;font-size:.88rem;color:#92400e;margin:1rem 0}
.tip-box strong{color:#78350f;display:block;margin-bottom:.2rem}
.warn-box{background:#fef2f2;border-left:4px solid var(--error);border-radius:8px;padding:.85rem 1rem;font-size:.88rem;color:#991b1b;margin:1rem 0}
.timeline{display:flex;flex-direction:column;gap:0}
.tl-item{display:flex;gap:1rem;padding:.75rem 0;border-bottom:1px dashed var(--border)}
.tl-item:last-child{border-bottom:none}
.tl-month{background:var(--primary);color:#fff;border-radius:8px;padding:.3rem .65rem;font-size:.78rem;font-weight:700;white-space:nowrap;height:fit-content;flex-shrink:0;min-width:70px;text-align:center}
.tl-text{font-size:.9rem;color:var(--muted);line-height:1.6}
.tl-text strong{color:var(--text)}
.apply-cta{background:linear-gradient(135deg,#003087,#1d4ed8);color:#fff;border-radius:var(--radius);padding:2rem;text-align:center}
.apply-cta h3{margin:0 0 .5rem;font-size:1.25rem;font-weight:800}
.apply-cta p{margin:0 0 1.2rem;opacity:.9;font-size:.95rem}
.apply-cta .btns{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap}
';

echo page_head(
    'Chevening Scholarship Guide for Gambian Students 2025/2026',
    'Everything Gambian students need to know to apply for the Chevening Scholarship — eligibility, documents, personal statement tips, and the interview.',
    $extra_css
);
echo '<body>';
echo site_header('guides');
?>

<!-- Breadcrumb -->
<div style="background:var(--surface);border-bottom:1px solid var(--border);padding:.6rem 1.2rem;font-size:.85rem;color:var(--muted)">
    <div style="max-width:860px;margin:0 auto">
        <a href="../index.php" style="color:var(--muted);text-decoration:none">Home</a> /
        <a href="../guides.php" style="color:var(--muted);text-decoration:none">Guides</a> /
        <span style="color:var(--text)">Chevening</span>
    </div>
</div>

<div class="guide-hero">
    <span class="flag">🇬🇧</span>
    <h1>Chevening Scholarship</h1>
    <p>The UK Government's flagship international scholarship — fully funded master's at any UK university</p>
    <div class="meta-pills">
        <span class="pill">🎓 Master's Degree</span>
        <span class="pill">💰 Fully Funded</span>
        <span class="pill">Deadline: 2026/27 closed</span>
        <span class="pill">🌍 Open to Gambians</span>
    </div>
</div>

<div class="guide-wrap">

    <div style="text-align:center;margin-bottom:1.5rem">
        <?php echo adsense_unit(AD_SLOT_LEADERBOARD, 'horizontal'); ?>
    </div>

    <!-- Quick Facts -->
    <div class="guide-section">
        <h2><i class="fas fa-info-circle"></i> Quick Facts</h2>
        <div class="info-grid">
            <div class="info-box"><div class="info-label">Country</div><div class="info-val">United Kingdom</div></div>
            <div class="info-box"><div class="info-label">Level</div><div class="info-val">Master's (1 year)</div></div>
            <div class="info-box"><div class="info-label">Funding</div><div class="info-val">100% — Full cost</div></div>
            <div class="info-box"><div class="info-label">Deadline</div><div class="info-val">Closed: 7 Oct 2025</div></div>
            <div class="info-box"><div class="info-label">Results</div><div class="info-val">March/April</div></div>
            <div class="info-box"><div class="info-label">Study starts</div><div class="info-val">September</div></div>
        </div>
        <h3>What does Chevening cover?</h3>
        <ul>
            <li>Full university tuition fees</li>
            <li>Monthly living allowance (around £1,300/month in London, £1,100 elsewhere)</li>
            <li>Economy return flights from Gambia to the UK</li>
            <li>Arrival allowance and departure allowance</li>
            <li>Thesis / dissertation allowance</li>
            <li>Study materials allowance</li>
        </ul>
    </div>

    <?php echo guide_verification_notice('Applications for the 2026/27 intake are closed. Prepare for the next cycle.', '17 June 2026', 'https://www.chevening.org/scholarships/application-timeline/', 'Chevening application timeline', [
        'The 2026/27 application deadline was 7 October 2025 at 12:00 UTC.',
        'Interview results are expected from mid-June 2026 onward.',
        'Successful candidates must submit at least one unconditional UK university offer by 9 July 2026.',
        'For the next cycle, start preparing before applications reopen.'
    ]); ?>

    <!-- Eligibility -->
    <div class="guide-section">
        <h2><i class="fas fa-check-circle"></i> Eligibility for Gambian Students</h2>
        <ul>
            <li>You must be a Gambian citizen and currently living in The Gambia</li>
            <li>You must have an undergraduate degree equivalent to a UK second class upper (2:1) or above</li>
            <li>You must have <strong>at least 2,800 hours of post-graduation work experience</strong> by the application deadline</li>
            <li>You must be returning to Gambia after your studies (this is a condition of the scholarship)</li>
            <li>You cannot have studied in the UK on a Chevening scholarship before</li>
            <li>You must apply to 3 different UK universities before submitting your Chevening application</li>
        </ul>
        <div class="tip-box">
            <strong>💡 The 2-year work experience rule:</strong> This is the most common reason Gambian students are disqualified. "Work experience" means paid employment, internships, volunteer work, or running your own business. Start counting from when you finished your undergraduate degree.
        </div>
    </div>

    <!-- Application Steps -->
    <div class="guide-section">
        <h2><i class="fas fa-list-ol"></i> How to Apply — Step by Step</h2>
        <ol>
            <li><strong>Register on the Chevening portal</strong> at <a href="https://www.chevening.org/scholarships/" target="_blank" rel="noopener">chevening.org</a> when applications open (usually August)</li>
            <li><strong>Apply to 3 UK universities</strong> through UCAS or directly — you must have applied to all 3 before submitting to Chevening</li>
            <li><strong>Complete all 4 essays</strong> on the Chevening portal (see below)</li>
            <li><strong>Submit 2 references</strong> — your referees receive an email link to submit directly</li>
            <li><strong>Upload documents:</strong> degree certificate, transcripts, passport, English test result</li>
            <li><strong>Submit before 12 noon UK time on the deadline day</strong></li>
        </ol>
        <div class="warn-box">
            ⚠️ The portal closes at <strong>12:00 noon UK time</strong> — not midnight. Many applicants miss this. Set your phone alarm for the morning of deadline day.
        </div>
    </div>

    <!-- The 4 Essays -->
    <div class="guide-section">
        <h2><i class="fas fa-pen-fancy"></i> The 4 Chevening Essays — How to Write Them</h2>
        <p>Chevening now uses shorter essay responses, with updated questions focused on leadership, course choices, global priorities, and impact. Treat every answer as evidence: use specific examples, not general claims.</p>

        <h3>Essay 1: Leadership & Influence</h3>
        <p>Describe a time you showed leadership. Use the <strong>STAR method</strong> — Situation, Task, Action, Result. Give one strong specific example, not a list of things you have done.</p>
        <div class="tip-box">
            <strong>Gambia example:</strong> "In 2023, I led a community health awareness campaign in my village in North Bank Region, coordinating 12 volunteers and reaching 400 households. As a result, clinic attendance increased by 35%..."
        </div>

        <h3>Essay 2: Networking</h3>
        <p>Describe how you have built and used a professional network to achieve a goal. Show that you understand networking is about giving, not just taking.</p>

        <h3>Essay 3: Studying in the UK</h3>
        <p>Why specifically the UK? Why this course? Why these 3 universities? Be very specific — name professors, research groups, modules. Generic answers here will fail.</p>

        <h3>Essay 4: Career Plan</h3>
        <p>What are your professional goals for the next 5 years? How will your master's help you achieve them? How will you contribute to Gambia when you return? This essay must show you are coming back and will make an impact.</p>
        <div class="tip-box">
            <strong>💡 Key principle:</strong> Chevening wants future leaders who will return to their country and make a difference. Every essay should connect to your impact in Gambia.
        </div>
    </div>

    <!-- Timeline -->
    <div class="guide-section">
        <h2><i class="fas fa-calendar-alt"></i> Chevening Application Timeline</h2>
        <div class="timeline">
            <div class="tl-item"><span class="tl-month">June–July</span><div class="tl-text"><strong>Preparation:</strong> Research UK universities and programmes. Draft your essays. Ask referees.</div></div>
            <div class="tl-item"><span class="tl-month">August</span><div class="tl-text"><strong>Portal opens:</strong> Create your account on chevening.org. Apply to your 3 UK universities.</div></div>
            <div class="tl-item"><span class="tl-month">Sep–Oct</span><div class="tl-text"><strong>Write & revise:</strong> Finalise all 4 essays. Have them reviewed by 2 people. Confirm your referees have submitted.</div></div>
            <div class="tl-item"><span class="tl-month">~5 Nov</span><div class="tl-text"><strong>DEADLINE:</strong> Submit by 12:00 noon UK time. Submit 48 hours early to be safe.</div></div>
            <div class="tl-item"><span class="tl-month">Mar–Apr</span><div class="tl-text"><strong>Shortlist notification:</strong> If shortlisted, you will be contacted for an interview in Banjul.</div></div>
            <div class="tl-item"><span class="tl-month">May–Jun</span><div class="tl-text"><strong>Interviews:</strong> Held at the British High Commission in Banjul. Panel of 2–3 Chevening alumni.</div></div>
            <div class="tl-item"><span class="tl-month">June</span><div class="tl-text"><strong>Awards announced:</strong> Successful candidates notified. Accept your offer and begin visa process.</div></div>
            <div class="tl-item"><span class="tl-month">September</span><div class="tl-text"><strong>Depart for UK:</strong> Your studies begin. Chevening flies you there and pays for everything.</div></div>
        </div>
    </div>

    <!-- Interview Tips -->
    <div class="guide-section">
        <h2><i class="fas fa-comments"></i> The Chevening Interview — What to Expect</h2>
        <p>The Chevening interview in Banjul is conducted by a panel of 2–3 Chevening alumni. It lasts about 30–45 minutes. Common questions include:</p>
        <ul>
            <li>"Tell us about a time you showed leadership"</li>
            <li>"Why Chevening specifically? Why not another scholarship?"</li>
            <li>"What will you do differently in Gambia after your studies?"</li>
            <li>"What is your biggest professional achievement so far?"</li>
            <li>"How will you use your network from the UK to benefit Gambia?"</li>
        </ul>
        <div class="tip-box">
            <strong>💡 Interview tip:</strong> Dress formally. Arrive 15 minutes early. Bring a copy of your application. The interviewers have read your essays — they will dig deeper into your specific examples. Know your essays inside out.
        </div>
    </div>

    <!-- Official link + WhatsApp -->
    <div class="apply-cta">
        <h3>Ready to Apply for Chevening?</h3>
        <p>Applications open in August every year at the official Chevening website.</p>
        <div class="btns">
            <a href="https://www.chevening.org/scholarships/" target="_blank" rel="noopener" class="btn btn-amber">
                <i class="fas fa-external-link-alt"></i> Apply on chevening.org
            </a>
            <a href="../how-to-apply.php" class="btn btn-secondary">
                <i class="fas fa-book"></i> Full Application Guide
            </a>
        </div>
    </div>

    <div style="text-align:center;margin:1.75rem 0">
        <?php echo adsense_unit(AD_SLOT_RECTANGLE); ?>
    </div>

    <div style="text-align:center;margin:1rem 0 2rem">
        <p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Know a Gambian student who could win Chevening? Share this guide:</p>
        <?php echo whatsapp_btn('Chevening Scholarship Guide for Gambian Students', SITE_URL . '/guides/chevening.php', 'lg'); ?>
    </div>

    <div style="display:flex;gap:1rem;justify-content:space-between;flex-wrap:wrap;font-size:.9rem">
        <a href="../guides.php" style="color:var(--primary-light);text-decoration:none"><i class="fas fa-arrow-left"></i> All Guides</a>
        <a href="daad.php" style="color:var(--primary-light);text-decoration:none">Next: DAAD Germany <i class="fas fa-arrow-right"></i></a>
    </div>
</div>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
