<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/layout.php';

$extra_css = '
.page-hero{background:var(--grad);color:#fff;padding:3rem 1.2rem;text-align:center}
.page-hero h1{font-size:clamp(1.6rem,4vw,2.6rem);font-weight:800;margin:0 0 .6rem;letter-spacing:-.02em}
.page-hero p{opacity:.92;font-size:1.05rem;margin:0 auto;max-width:620px}
.steps-wrap{max-width:860px;margin:2.5rem auto;padding:0 1.2rem}
.step-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border);padding:1.75rem;margin-bottom:1.5rem;display:flex;gap:1.25rem;align-items:flex-start}
.step-num{width:44px;height:44px;border-radius:50%;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;flex-shrink:0}
.step-body h3{margin:0 0 .5rem;font-size:1.08rem;font-weight:700}
.step-body p{margin:0 0 .65rem;color:var(--muted);font-size:.92rem;line-height:1.7}
.step-body ul{margin:.4rem 0;padding-left:1.2rem;color:var(--muted);font-size:.9rem;line-height:1.8}
.step-body .tip-box{background:#fffbeb;border-left:4px solid var(--secondary);border-radius:8px;padding:.75rem 1rem;font-size:.88rem;color:#92400e;margin-top:.65rem}
.step-body .tip-box strong{color:#78350f}
.docs-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;margin:1.5rem 0}
.doc-card{background:var(--surface);border-radius:12px;padding:1.1rem;border:1px solid var(--border);box-shadow:var(--shadow);display:flex;gap:.75rem;align-items:flex-start}
.doc-icon{width:38px;height:38px;border-radius:10px;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:1rem;color:var(--primary);flex-shrink:0}
.doc-title{font-weight:600;font-size:.92rem;margin-bottom:.2rem}
.doc-desc{font-size:.8rem;color:var(--muted);line-height:1.5}
.mistakes-list{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border);padding:1.75rem;margin-bottom:1.5rem}
.mistakes-list h2{margin:0 0 1.1rem;font-size:1.15rem;display:flex;align-items:center;gap:.5rem}
.mistake-item{display:flex;gap:.75rem;padding:.7rem 0;border-bottom:1px solid var(--border);align-items:flex-start}
.mistake-item:last-child{border-bottom:none}
.mistake-x{color:var(--error);font-size:1rem;flex-shrink:0;margin-top:.1rem}
.mistake-text strong{display:block;font-size:.92rem;margin-bottom:.15rem}
.mistake-text span{font-size:.85rem;color:var(--muted)}
.section-title{font-size:1.25rem;font-weight:800;margin:2.5rem 0 .4rem;color:var(--primary)}
.section-sub{color:var(--muted);font-size:.93rem;margin:0 0 1.25rem}
.cta-box{background:var(--grad);color:#fff;border-radius:var(--radius);padding:2rem;text-align:center;margin:2.5rem 0}
.cta-box h3{margin:0 0 .5rem;font-size:1.3rem;font-weight:800}
.cta-box p{margin:0 0 1.25rem;opacity:.92;font-size:.95rem}
.cta-box .btns{display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap}
@media(max-width:600px){.step-card{flex-direction:column}.step-num{width:36px;height:36px;font-size:.95rem}}
';

echo page_head(
    'How to Apply for Scholarships in Gambia — Step by Step Guide',
    'A complete step-by-step guide for Gambian students on how to apply for international scholarships — from finding the right opportunity to submitting a winning application.',
    $extra_css
);
echo '<body>';
echo site_header('how-to-apply');
?>

<div class="page-hero">
    <h1>How to Apply for Scholarships</h1>
    <p>A complete step-by-step guide written specifically for Gambian students — from finding the right scholarship to sending your application.</p>
</div>

<main class="steps-wrap">

    <!-- Ad -->
    <div style="text-align:center;margin:1.5rem 0">
        <?php echo adsense_unit(AD_SLOT_LEADERBOARD, 'horizontal'); ?>
    </div>

    <!-- ── Intro ─────────────────────────────────────────────────────────── -->
    <div style="background:var(--surface);border-radius:var(--radius);padding:1.5rem;border:1px solid var(--border);box-shadow:var(--shadow);margin-bottom:2rem">
        <p style="margin:0;font-size:.95rem;line-height:1.8;color:var(--muted)">
            Every year, thousands of scholarships go <strong style="color:var(--text)">unclaimed</strong> simply because students don't know how to apply or miss the deadlines. 
            This guide will walk you through every step of the process — from choosing the right scholarship to writing your personal statement and submitting your documents. 
            Follow these steps and you will give yourself the best possible chance of winning.
        </p>
    </div>

    <!-- ── Step by Step ──────────────────────────────────────────────────── -->
    <h2 class="section-title">The 8-Step Application Process</h2>
    <p class="section-sub">Follow these steps in order for every scholarship you apply to</p>

    <div class="step-card">
        <div class="step-num">1</div>
        <div class="step-body">
            <h3>Find the Right Scholarship for You</h3>
            <p>Not every scholarship is right for every student. Before applying, make sure you actually qualify. Check these things first:</p>
            <ul>
                <li><strong>Nationality:</strong> Most scholarships on this site are open to Gambian nationals</li>
                <li><strong>Academic level:</strong> Are you applying for undergraduate, master's or PhD?</li>
                <li><strong>Field of study:</strong> Some scholarships only fund specific subjects (e.g. STEM, law, agriculture)</li>
                <li><strong>GPA / grades:</strong> Many require a minimum grade — check the requirements</li>
                <li><strong>Work experience:</strong> Some (like Chevening) require at least 2 years of work experience</li>
            </ul>
            <div class="tip-box">
                <strong>💡 Gambia Tip:</strong> Start with Chevening, Commonwealth and DAAD — these are the most commonly won by Gambian students and have dedicated support for West African applicants.
            </div>
        </div>
    </div>

    <div class="step-card">
        <div class="step-num">2</div>
        <div class="step-body">
            <h3>Mark Your Deadlines Immediately</h3>
            <p>The biggest reason Gambian students miss scholarships is simply missing the deadline. As soon as you find a scholarship you want to apply for:</p>
            <ul>
                <li>Write the deadline in your phone calendar with a reminder 30 days before</li>
                <li>Set another reminder 7 days before</li>
                <li>Aim to submit your application at least <strong>48 hours early</strong> — portals crash on deadline day</li>
                <li>Note: Chevening opens in August and closes in November. DAAD opens in September. Start preparing in July.</li>
            </ul>
            <div class="tip-box">
                <strong>💡 Gambia Tip:</strong> Subscribe to GamScholarship email alerts so you never miss a new opportunity. We send updates every week.
            </div>
        </div>
    </div>

    <div class="step-card">
        <div class="step-num">3</div>
        <div class="step-body">
            <h3>Prepare Your Documents Early</h3>
            <p>Most Gambian students underestimate how long it takes to collect all required documents. Start at least 3 months before the deadline. Here is what you will typically need:</p>
            <div class="docs-grid" style="margin-top:.75rem">
                <?php
                $docs = [
                    ['fas fa-passport','Valid Passport','Must be valid for at least 6 months beyond the scholarship period. Renew at Immigration in Banjul — allow 4–6 weeks.'],
                    ['fas fa-graduation-cap','Degree Certificate','Original or certified copy. Get it from your university\'s examination office. Allow 2–4 weeks.'],
                    ['fas fa-file-alt','Academic Transcripts','Official transcripts with your GPA/grades. Must have the university stamp and signature.'],
                    ['fas fa-language','IELTS/TOEFL Result','Most scholarships require IELTS 6.5+ or TOEFL 90+. Book your test at British Council Banjul or the American Embassy.'],
                    ['fas fa-user-tie','Reference Letters','2–3 letters from professors or employers. Give them 6 weeks notice and your CV.'],
                    ['fas fa-file-signature','Personal Statement','Your most important document. See Step 5 below.'],
                    ['fas fa-search','Research Proposal','Required for PhD scholarships. 1,500–3,000 words describing your research plan.'],
                    ['fas fa-image','Passport Photo','Recent, professional background. White or light blue background preferred.'],
                ];
                foreach ($docs as [$icon, $title, $desc]): ?>
                <div class="doc-card">
                    <div class="doc-icon"><i class="<?php echo $icon; ?>"></i></div>
                    <div>
                        <div class="doc-title"><?php echo $title; ?></div>
                        <div class="doc-desc"><?php echo $desc; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="tip-box">
                <strong>💡 Gambia Tip:</strong> Scan all your documents and save them in a Google Drive folder named "Scholarship Documents". This way you can access and submit them from any computer or phone instantly.
            </div>
        </div>
    </div>

    <div class="step-card">
        <div class="step-num">4</div>
        <div class="step-body">
            <h3>Take the English Language Test</h3>
            <p>If English is not the language of your previous education, you will need IELTS or TOEFL. Even if it is, some scholarships still require it.</p>
            <ul>
                <li><strong>IELTS Academic:</strong> Required for UK scholarships (Chevening, Commonwealth, Oxford). Register at <a href="https://www.britishcouncil.org.gm" target="_blank" rel="noopener">britishcouncil.org.gm</a></li>
                <li><strong>TOEFL iBT:</strong> Required for US scholarships (Fulbright). Register at <a href="https://www.ets.org/toefl" target="_blank" rel="noopener">ets.org/toefl</a></li>
                <li><strong>Target score:</strong> Aim for IELTS 7.0+ to be competitive (minimum is usually 6.5)</li>
                <li><strong>Preparation:</strong> Use free resources — British Council LearnEnglish app, IELTS.org sample tests, and YouTube channels like IELTS Liz</li>
                <li><strong>Cost in Gambia:</strong> IELTS costs approximately D6,000–D8,000. Many scholarships reimburse this if you win.</li>
            </ul>
            <div class="tip-box">
                <strong>💡 Gambia Tip:</strong> The British Council in Banjul runs IELTS preparation workshops. Contact them early — test dates fill up fast, especially before the November scholarship deadlines.
            </div>
        </div>
    </div>

    <div class="step-card">
        <div class="step-num">5</div>
        <div class="step-body">
            <h3>Write a Powerful Personal Statement</h3>
            <p>Your personal statement is the most important part of your application. This is where you win or lose the scholarship. A strong personal statement:</p>
            <ul>
                <li>Tells a <strong>specific, real story</strong> — not "I have always been passionate about..." but "In 2022, I worked at a health clinic in Brikama and saw firsthand how..."</li>
                <li>Shows <strong>leadership</strong> — what have you led, organised or improved?</li>
                <li>Explains <strong>why this specific scholarship</strong> — not just any master's, but why this programme in this country</li>
                <li>Connects your past → your study plan → your return to Gambia. What will you contribute when you come back?</li>
                <li>Is <strong>proofread</strong> by at least 2 people before submitting</li>
            </ul>
            <div class="tip-box">
                <strong>💡 Chevening-specific tip:</strong> Chevening asks 4 separate essays — Leadership, Networking, Studying in the UK, and Career Plan. Each is 500 words. Answer the specific question asked, don't write a generic essay.
            </div>
        </div>
    </div>

    <div class="step-card">
        <div class="step-num">6</div>
        <div class="step-body">
            <h3>Get Strong Reference Letters</h3>
            <p>Bad reference letters can kill a great application. Here is how to get good ones:</p>
            <ul>
                <li>Choose people who <strong>know your work</strong> well — a professor who supervised your thesis, a manager who saw you lead a project</li>
                <li>Ask them <strong>at least 6 weeks</strong> before the deadline</li>
                <li>Give them: your CV, a summary of the scholarship, and bullet points on what you'd like them to highlight</li>
                <li>Follow up politely 2 weeks before the deadline to confirm they have submitted</li>
                <li>For Chevening: you need 2 referees and they submit directly via the online portal</li>
            </ul>
            <div class="tip-box">
                <strong>💡 Gambia Tip:</strong> Lecturers at UTG and Gambia College are frequently asked for references. Give them as much lead time as possible — they are busy and need time to write a quality letter for you.
            </div>
        </div>
    </div>

    <div class="step-card">
        <div class="step-num">7</div>
        <div class="step-body">
            <h3>Submit Your Application</h3>
            <p>When everything is ready, submit carefully:</p>
            <ul>
                <li>Read every question again before submitting — make sure you answered what was asked</li>
                <li>Check all uploaded documents are the right files (not accidentally uploading the wrong PDF)</li>
                <li>Submit <strong>at least 48 hours before the deadline</strong> — portals are slow on deadline day and sometimes crash</li>
                <li>Take a screenshot of your submission confirmation page</li>
                <li>You will usually receive a confirmation email — check your spam folder</li>
            </ul>
            <div class="tip-box">
                <strong>💡 Internet tip:</strong> If your internet connection in Gambia is unreliable, go to a café or university library to submit. Don't risk losing your application to a dropped connection.
            </div>
        </div>
    </div>

    <div class="step-card">
        <div class="step-num">8</div>
        <div class="step-body">
            <h3>Prepare for the Interview</h3>
            <p>If shortlisted, most scholarships invite you to an interview. This is your chance to show the real you.</p>
            <ul>
                <li><strong>Chevening interview:</strong> Usually held at the British High Commission in Banjul. 30–45 minutes, panel of 2–3 people. They ask about your leadership, why Chevening, and your plans after the scholarship.</li>
                <li><strong>Fulbright interview:</strong> Held at the American Embassy in Banjul. More conversational — they want to see your personality and ambition.</li>
                <li>Research the country, university and programme you applied for</li>
                <li>Practice answering: "Why do you deserve this scholarship?" and "What will you do when you return to Gambia?"</li>
                <li>Dress professionally. Arrive 15 minutes early.</li>
            </ul>
            <div class="tip-box">
                <strong>💡 Gambia Tip:</strong> Ask in the GamScholarship community if anyone has been interviewed recently — they can share exactly what questions were asked.
            </div>
        </div>
    </div>

    <!-- Ad -->
    <div style="text-align:center;margin:2rem 0">
        <?php echo adsense_unit(AD_SLOT_RECTANGLE); ?>
    </div>

    <!-- ── Common Mistakes ────────────────────────────────────────────────── -->
    <div class="mistakes-list">
        <h2><i class="fas fa-exclamation-triangle" style="color:var(--error)"></i> 8 Mistakes Gambian Students Make</h2>
        <?php
        $mistakes = [
            ['Applying for too few scholarships', 'Apply for at least 8–10 scholarships every cycle. Even the best candidates get rejected. Spread your applications across different countries and funding types.'],
            ['Starting the personal statement the week before deadline', 'A great personal statement takes 4–6 weeks of drafting and revision. Starting late means a weak essay and a rejected application.'],
            ['Not reading the eligibility requirements', 'Many students spend weeks on an application only to discover they don\'t qualify. Read the requirements first — before anything else.'],
            ['Generic personal statements', '"I have always been passionate about development" tells a panel nothing. Be specific. Use real examples from your life in Gambia.'],
            ['Forgetting to follow up with referees', 'Many applications are rejected because a referee didn\'t submit on time. Set reminders and check in with your referees 2 weeks before the deadline.'],
            ['Submitting on deadline day', 'Scholarship portals get thousands of submissions on the last day and often slow down or crash. Submit at least 48 hours early.'],
            ['Applying only for overseas scholarships', 'The Mastercard Foundation Scholars Program funds study at African universities — often easier to win and just as valuable. Don\'t overlook African-based opportunities.'],
            ['Giving up after one rejection', 'Fatou from Banjul won Chevening on her third attempt. Modou won DAAD on his second. Rejection is part of the process — apply again with a stronger application.'],
        ];
        foreach ($mistakes as [$title, $desc]): ?>
        <div class="mistake-item">
            <div class="mistake-x"><i class="fas fa-times-circle"></i></div>
            <div class="mistake-text">
                <strong><?php echo $title; ?></strong>
                <span><?php echo $desc; ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── CTA ────────────────────────────────────────────────────────────── -->
    <div class="cta-box">
        <h3>Ready to Find Your Scholarship?</h3>
        <p>Browse our database of real, verified scholarships — updated daily from top sources worldwide.</p>
        <div class="btns">
            <a href="scholarship.php" class="btn btn-amber"><i class="fas fa-search"></i> Browse Scholarships</a>
            <a href="guides.php"      class="btn btn-secondary"><i class="fas fa-book"></i> Read Country Guides</a>
        </div>
    </div>

    <!-- WhatsApp share -->
    <div style="text-align:center;margin:1.5rem 0 2rem">
        <p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Know a Gambian student who needs this guide? Share it:</p>
        <?php echo whatsapp_btn('How to Apply for Scholarships — Complete Guide for Gambian Students', SITE_URL . '/how-to-apply.php', 'lg'); ?>
    </div>

</main>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
