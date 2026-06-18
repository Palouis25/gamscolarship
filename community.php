<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/layout.php';

$extra_css = '
.page-hero{background:var(--grad);color:#fff;padding:2.5rem 1.2rem;text-align:center}
.page-hero h1{font-size:clamp(1.5rem,4vw,2.4rem);font-weight:800;margin:0 0 .5rem;letter-spacing:-.02em}
.page-hero p{opacity:.92;font-size:1rem;margin:0}
.community-grid{display:grid;grid-template-columns:1fr minmax(260px,320px);gap:1.75rem;margin-top:2rem}
@media(max-width:768px){.community-grid{grid-template-columns:1fr}}
.feed{display:flex;flex-direction:column;gap:1rem}
.post-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border);overflow:hidden}
.post-card-head{display:flex;align-items:center;gap:.75rem;padding:1rem 1.1rem .65rem}
.avatar{width:38px;height:38px;border-radius:50%;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem;flex-shrink:0}
.post-author{font-weight:600;font-size:.93rem}
.post-meta{color:var(--muted);font-size:.78rem}
.post-badge{display:inline-block;padding:.12rem .45rem;border-radius:6px;font-size:.7rem;font-weight:600;margin-left:.35rem;vertical-align:middle}
.badge-success{background:#d1fae5;color:#065f46}
.badge-tip{background:#fef9c3;color:#854d0e}
.badge-question{background:#ede9fe;color:#5b21b6}
.post-body{padding:.1rem 1.1rem .9rem;font-size:.9rem;line-height:1.65;color:var(--text)}
.post-foot{padding:.6rem 1.1rem;border-top:1px solid var(--border);display:flex;gap:1.2rem;font-size:.82rem;color:var(--muted);background:#fafbfc}
.post-foot span{display:flex;align-items:center;gap:.3rem}
.sidebar{display:flex;flex-direction:column;gap:1.25rem}
.side-card{background:var(--surface);border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border);padding:1.25rem}
.side-card h3{margin:0 0 1rem;font-size:.97rem;font-weight:700;display:flex;align-items:center;gap:.5rem}
.stat-list{display:flex;flex-direction:column;gap:.6rem}
.stat-row{display:flex;justify-content:space-between;align-items:center;font-size:.88rem}
.stat-row .num{font-weight:700;color:var(--primary)}
.link-list a{display:flex;align-items:center;gap:.6rem;color:var(--text);text-decoration:none;padding:.45rem 0;border-bottom:1px solid var(--border);font-size:.88rem}
.link-list a:last-child{border-bottom:none}
.link-list a:hover{color:var(--primary-light)}
.join-box{background:var(--grad);border-radius:var(--radius);padding:1.5rem;text-align:center;color:#fff}
.join-box h3{margin:0 0 .6rem;font-size:1.05rem}
.join-box p{margin:0 0 1.1rem;font-size:.88rem;opacity:.9}
.tag-cloud{display:flex;flex-wrap:wrap;gap:.4rem}
.topic-tag{padding:.28rem .65rem;background:var(--bg);border:1px solid var(--border);border-radius:99px;font-size:.78rem;color:var(--muted);text-decoration:none;transition:all .15s}
.topic-tag:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
';

echo page_head('Community — GamScholarship', 'Join Gambian students sharing scholarship experiences, tips and success stories.', $extra_css);
echo '<body>';
echo site_header('community');
?>

<div class="page-hero">
    <h1><i class="fas fa-users"></i> Student Community</h1>
    <p>Connect with Gambian students, share experiences and get advice on scholarships</p>
</div>

<main class="container" style="padding-bottom:2.5rem">

    <!-- Ad -->
    <div style="text-align:center;margin:1.5rem 0 .5rem">
        <?php echo adsense_unit(AD_SLOT_LEADERBOARD, 'horizontal'); ?>
    </div>

    <div class="community-grid">

        <!-- ── Feed ────────────────────────────────────────────────────── -->
        <div>
            <!-- Coming soon notice -->
            <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:var(--radius);padding:1rem 1.2rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.75rem;font-size:.9rem;color:#92400e">
                <i class="fas fa-hard-hat" style="font-size:1.2rem"></i>
                <div><strong>Interactive posting coming soon!</strong> For now, browse stories from our community and share yours by emailing <a href="mailto:info@gamscolaship.online" style="color:#92400e">info@gamscolaship.online</a>.</div>
            </div>

            <div class="feed">
                <!-- Posts -->
                <?php
                $posts = [
                    [
                        'initials' => 'FK',
                        'author'   => 'Fatou K.',
                        'time'     => '2 days ago',
                        'badge'    => ['success', '🎉 Success Story'],
                        'body'     => 'I just received my Chevening offer letter after 3 years of applying! For anyone who has been rejected before — <strong>don\'t give up</strong>. I changed my personal statement completely in my third attempt, focusing on specific leadership examples rather than general statements. The key was showing exactly how my master\'s will help me contribute to Gambia when I return.',
                        'likes'    => 47,
                        'comments' => 12,
                    ],
                    [
                        'initials' => 'OS',
                        'author'   => 'Omar S.',
                        'time'     => '4 days ago',
                        'badge'    => ['tip', '💡 Tip'],
                        'body'     => 'DAAD deadline tip: The scholarship portal often slows down in the last 2–3 days before submission closes. I submitted 5 days early last year and my application went through smoothly. Friends who waited till the last day had technical issues. Don\'t risk it — submit early!',
                        'likes'    => 31,
                        'comments' => 8,
                    ],
                    [
                        'initials' => 'AB',
                        'author'   => 'Aminata B.',
                        'time'     => '1 week ago',
                        'badge'    => ['question', '❓ Question'],
                        'body'     => 'Has anyone from Gambia successfully applied for the Australia Awards? I\'m completing my BSc in Environmental Science next year. The website says it\'s open to Sub-Saharan Africa but I\'m not sure if Gambia is included. Would love to hear from anyone who has tried.',
                        'likes'    => 19,
                        'comments' => 6,
                    ],
                    [
                        'initials' => 'MB',
                        'author'   => 'Modou B.',
                        'time'     => '2 weeks ago',
                        'badge'    => ['success', '🎉 Success Story'],
                        'body'     => 'Just returned from my Erasmus Mundus program in Spain and Italy. 2 years fully funded, two degrees, and now working at the African Development Bank. If you\'re thinking of applying — do it. Start with the catalog at eacea.ec.europa.eu and find programs that match your undergraduate degree. The application is long but absolutely worth it.',
                        'likes'    => 83,
                        'comments' => 22,
                    ],
                    [
                        'initials' => 'JN',
                        'author'   => 'Jainaba N.',
                        'time'     => '2 weeks ago',
                        'badge'    => ['tip', '💡 Tip'],
                        'body'     => 'IELTS tip: I took the test three times before getting 7.0. What actually helped was: (1) watching YouTube videos in English every day for 3 months, (2) reading BBC News in English daily, (3) taking one full mock test every weekend. The British Council in Banjul has prep materials — ask for them. Free resources beat expensive courses.',
                        'likes'    => 56,
                        'comments' => 15,
                    ],
                    [
                        'initials' => 'LJ',
                        'author'   => 'Lamin J.',
                        'time'     => '3 weeks ago',
                        'badge'    => ['tip', '💡 Tip'],
                        'body'     => 'For anyone applying to the Mastercard Foundation Scholars Program: reach out to a Gambian Mastercard Scholar before applying. They can connect you to the right university contact. Also — the program is now at many African universities (e.g. University of Ghana, AIMS) not just overseas universities. These may have earlier deadlines.',
                        'likes'    => 28,
                        'comments' => 9,
                    ],
                ];
                foreach ($posts as $p): ?>
                <div class="post-card">
                    <div class="post-card-head">
                        <div class="avatar"><?php echo $p['initials']; ?></div>
                        <div>
                            <div class="post-author">
                                <?php echo $p['author']; ?>
                                <span class="post-badge badge-<?php echo $p['badge'][0]; ?>"><?php echo $p['badge'][1]; ?></span>
                            </div>
                            <div class="post-meta"><?php echo $p['time']; ?></div>
                        </div>
                    </div>
                    <div class="post-body"><?php echo $p['body']; ?></div>
                    <div class="post-foot">
                        <span><i class="fas fa-heart"></i> <?php echo $p['likes']; ?> likes</span>
                        <span><i class="fas fa-comment"></i> <?php echo $p['comments']; ?> comments</span>
                        <span style="margin-left:auto"><i class="fas fa-share"></i> Share</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Mid ad -->
            <div style="text-align:center;margin-top:2rem">
                <?php echo adsense_unit(AD_SLOT_RECTANGLE); ?>
            </div>
        </div>

        <!-- ── Sidebar ──────────────────────────────────────────────────── -->
        <aside class="sidebar">

            <div class="join-box">
                <h3>Share Your Story</h3>
                <p>Won a scholarship? Have a tip? Email us and we'll post it here to inspire others.</p>
                <a href="mailto:info@gamscolaship.online?subject=Community Post" class="btn btn-amber" style="border-radius:10px;width:100%;justify-content:center">
                    <i class="fas fa-envelope"></i> Share Your Story
                </a>
            </div>

            <div class="side-card">
                <h3><i class="fas fa-chart-bar" style="color:var(--primary)"></i> Community Stats</h3>
                <div class="stat-list">
                    <div class="stat-row"><span>Stories shared</span><span class="num">120+</span></div>
                    <div class="stat-row"><span>Active members</span><span class="num">1,400+</span></div>
                    <div class="stat-row"><span>Scholarships won</span><span class="num">85+</span></div>
                    <div class="stat-row"><span>Countries reached</span><span class="num">30+</span></div>
                </div>
            </div>

            <div class="side-card">
                <h3><i class="fas fa-star" style="color:var(--secondary)"></i> Popular Topics</h3>
                <div class="tag-cloud">
                    <?php foreach (['Chevening','DAAD','IELTS Tips','Personal Statement','Masters','PhD','Australia Awards','Erasmus','Mastercard','UK Scholarships','Germany','Canada'] as $t): ?>
                        <a href="scholarship.php?q=<?php echo urlencode($t); ?>" class="topic-tag"><?php echo $t; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="side-card">
                <h3><i class="fas fa-link" style="color:var(--success)"></i> Quick Links</h3>
                <div class="link-list">
                    <a href="scholarship.php"><i class="fas fa-search"></i> Browse All Scholarships</a>
                    <a href="tips.php"><i class="fas fa-lightbulb"></i> Application Tips</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Create Account</a>
                    <a href="index.php#contact"><i class="fas fa-envelope"></i> Contact Us</a>
                </div>
            </div>

        </aside>
    </div>
</main>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
