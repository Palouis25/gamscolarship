<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/newsletter.php';
require_once __DIR__ . '/config/layout.php';

$SITE_EMAIL = 'info@gamscolaship.online';
$FROM_EMAIL = 'no-reply@gamscolaship.online';

function clean($v): string {
    return trim(htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'));
}
function valid_email($email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ── Load scholarships from JSON (auto-updated daily by scraper.php) ───────────
$json_file    = __DIR__ . '/scholarships.json';
$scholarships = [];
if (file_exists($json_file)) {
    $json_data    = json_decode(file_get_contents($json_file), true);
    $scholarships = $json_data['scholarships'] ?? [];
}

// ── Form handling ─────────────────────────────────────────────────────────────
$msg_contact   = null;
$msg_subscribe = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Contact form
    if (isset($_POST['contact_submit'])) {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $msg_contact = ['type' => 'error', 'text' => 'Invalid request. Please refresh and try again.'];
        } else {
            $name    = clean($_POST['name']    ?? '');
            $email   = clean($_POST['email']   ?? '');
            $message = clean($_POST['message'] ?? '');

            if (!$name || !$email || !$message) {
                $msg_contact = ['type' => 'error', 'text' => 'Please fill in all fields.'];
            } elseif (!valid_email($email)) {
                $msg_contact = ['type' => 'error', 'text' => 'Please enter a valid email address.'];
            } else {
                $subject = "GamScholarship Contact: {$name}";
                $body    = "Name: {$name}\nEmail: {$email}\nDate: " . date('Y-m-d H:i:s') . "\n\nMessage:\n{$message}";
                $headers = "From: {$FROM_EMAIL}\r\nReply-To: {$email}\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
                if (@mail($SITE_EMAIL, $subject, $body, $headers)) {
                    $msg_contact = ['type' => 'success', 'text' => 'Your message has been sent! We\'ll reply soon.'];
                    $_POST = [];
                } else {
                    $msg_contact = ['type' => 'error', 'text' => 'Failed to send. Please email info@gamscolaship.online directly.'];
                }
            }
        }
    }

    // Subscribe form
    if (isset($_POST['subscribe_submit'])) {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $msg_subscribe = ['type' => 'error', 'text' => 'Invalid request. Please refresh and try again.'];
        } else {
            $sub_email = clean($_POST['subscriber_email'] ?? '');
            if (!$sub_email || !valid_email($sub_email)) {
                $msg_subscribe = ['type' => 'error', 'text' => 'Please enter a valid email address.'];
            } else {
                $link = db_connect();
                $result = newsletter_subscribe($link, $sub_email, ['all'], 'homepage');
                mysqli_close($link);
                $msg_subscribe = ['type' => $result['ok'] ? 'success' : 'error', 'text' => $result['message']];
                if ($result['ok']) $_POST = [];
            }
        }
    }
}

// ── Filter / search ───────────────────────────────────────────────────────────
$search  = isset($_GET['search']) ? clean($_GET['search']) : '';
$filter  = isset($_GET['filter']) ? clean($_GET['filter']) : 'all';

$filtered = array_values(array_filter($scholarships, function($s) use ($search, $filter) {
    if ($filter !== 'all') {
        $haystack = strtolower(implode(' ', (array)($s['tags'] ?? [])) . ' ' . ($s['level'] ?? '') . ' ' . ($s['type'] ?? ''));
        if (strpos($haystack, $filter) === false) return false;
    }
    if ($search) {
        $hay = strtolower(($s['title'] ?? '') . ' ' . ($s['description'] ?? '') . ' ' . ($s['country'] ?? '') . ' ' . implode(' ', (array)($s['tags'] ?? [])));
        if (strpos($hay, strtolower($search)) === false) return false;
    }
    return true;
}));

// Homepage: show featured first, then fill to 6
if (!$search && $filter === 'all') {
    $feat    = array_values(array_filter($scholarships, fn($s) => !empty($s['featured'])));
    $others  = array_values(array_filter($scholarships, fn($s) => empty($s['featured'])));
    $display = array_slice(array_merge($feat, $others), 0, 6);
} else {
    $display = $filtered;
}

// ── Page output ───────────────────────────────────────────────────────────────
$extra_css = '
.hero{background:var(--grad);color:#fff;padding:clamp(2.5rem,6vw,4.5rem) 1.2rem;text-align:center}
.hero h1{font-size:clamp(1.6rem,4.5vw,3rem);font-weight:800;margin:0 0 .75rem;line-height:1.1;letter-spacing:-.02em}
.hero p{font-size:clamp(.95rem,2.5vw,1.12rem);margin:0 auto 1.5rem;max-width:640px;opacity:.93}
.hero-btns{display:flex;gap:.7rem;justify-content:center;flex-wrap:wrap}
.search-wrap{background:var(--surface);padding:1.5rem 1.2rem;border-bottom:1px solid var(--border)}
.search-inner{max-width:1200px;margin:0 auto;display:flex;flex-direction:column;gap:1rem}
.search-row{position:relative}
.search-row input{width:100%;padding:.85rem 1.1rem .85rem 3rem;border:2px solid var(--border);border-radius:99px;font-size:1rem;font-family:inherit;transition:border-color .18s}
.search-row input:focus{outline:none;border-color:var(--primary-light)}
.search-row i{position:absolute;left:1.1rem;top:50%;transform:translateY(-50%);color:var(--muted)}
.filter-pills{display:flex;gap:.5rem;flex-wrap:wrap}
.filter-pill{padding:.35rem .9rem;border-radius:99px;font-size:.88rem;font-weight:500;text-decoration:none;color:var(--text);background:var(--bg);border:1.5px solid var(--border);transition:all .15s}
.filter-pill:hover{border-color:var(--primary-light);color:var(--primary-light)}
.filter-pill.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.section-title{font-size:1.3rem;font-weight:700;text-align:center;margin:0 0 .4rem}
.section-sub{text-align:center;color:var(--muted);font-size:.95rem;margin:0 0 1.5rem}
.sch-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;margin-top:1rem}
.sch-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);display:flex;flex-direction:column;overflow:hidden;transition:transform .2s,box-shadow .2s}
.sch-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.13)}
.sch-card-head{padding:1.1rem;background:var(--grad);color:#fff;position:relative}
.sch-card-head.government{background:linear-gradient(135deg,#065f46,#10b981)}
.sch-card-head.foundation{background:linear-gradient(135deg,#7c2d12,#ea580c)}
.sch-card-head.international{background:linear-gradient(135deg,#1e40af,#06b6d4)}
.sch-card-type{position:absolute;top:.75rem;right:.75rem;background:rgba(255,255,255,.92);color:var(--text);padding:.2rem .55rem;border-radius:8px;font-size:.72rem;font-weight:600}
.sch-card-title{font-size:1rem;font-weight:700;margin:0 3.5rem .3rem 0;line-height:1.3}
.sch-card-meta{font-size:.78rem;opacity:.9;display:flex;gap:.75rem;flex-wrap:wrap}
.sch-card-body{padding:1rem;flex:1}
.sch-card-body p{color:var(--muted);font-size:.88rem;line-height:1.55;margin:0 0 .75rem}
.sch-card-foot{padding:.75rem 1rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#fafbfc}
.sch-apply{background:var(--success);color:#fff;padding:.4rem .9rem;border-radius:10px;text-decoration:none;font-weight:600;font-size:.83rem;display:inline-flex;align-items:center;gap:.35rem;transition:background .15s}
.sch-apply:hover{background:#059669}
.contact-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem}
.contact-card{background:var(--surface);padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--border)}
.contact-card h3{margin:0 0 1rem;font-size:1.1rem}
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.9rem;margin:1.5rem 0}
.stat-box{background:var(--surface);padding:1rem;border-radius:var(--radius);box-shadow:var(--shadow);text-align:center;border:1px solid var(--border)}
.stat-num{font-size:1.5rem;font-weight:800;color:var(--primary)}
.stat-lbl{font-size:.85rem;color:var(--muted)}
';

echo page_head('GamScholarship — Scholarships for Gambian Students', 'Find the latest scholarships for Gambian students. Chevening, DAAD, Mastercard Foundation and more.', $extra_css);
echo '<body>';
echo site_header('home');
?>

<!-- ── Top Ad banner ────────────────────────────────────────────────────── -->
<div style="max-width:1200px;margin:.5rem auto;padding:0 1.2rem;text-align:center">
    <?php echo adsense_unit(AD_SLOT_LEADERBOARD, 'horizontal'); ?>
</div>

<!-- ── Hero ──────────────────────────────────────────────────────────────── -->
<section class="hero">
    <div>
        <h1>Unlock Your Educational Future</h1>
        <p>Discover real, verified scholarship opportunities for Gambian students — curated weekly from trusted sources worldwide.</p>
        <div class="hero-btns">
            <a href="#scholarships" class="btn btn-amber"><i class="fas fa-search"></i> Explore Scholarships</a>
            <a href="tips.php"      class="btn btn-secondary"><i class="fas fa-lightbulb"></i> Application Tips</a>
        </div>
    </div>
</section>

<!-- ── Search & filter ───────────────────────────────────────────────────── -->
<div class="search-wrap">
    <div class="search-inner">
        <form method="get" action="#scholarships" class="search-row">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Search by country, level, keyword…"
                   value="<?php echo htmlspecialchars($search); ?>" aria-label="Search scholarships">
        </form>
        <div class="filter-pills">
            <?php
            $pills = ['all' => 'All', 'masters' => 'Master\'s', 'phd' => 'PhD', 'undergraduate' => 'Undergraduate', 'government' => 'Government', 'foundation' => 'Foundation'];
            foreach ($pills as $key => $label):
                $active = $filter === $key ? ' active' : '';
                $href   = '?filter=' . urlencode($key) . ($search ? '&search=' . urlencode($search) : '') . '#scholarships';
            ?>
                <a href="<?php echo $href; ?>" class="filter-pill<?php echo $active; ?>"><?php echo $label; ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ── Stats ─────────────────────────────────────────────────────────────── -->
<main class="container" style="margin-top:2rem">
    <div class="stats-row">
        <div class="stat-box"><div class="stat-num"><?php echo count($scholarships) > 0 ? count($scholarships) . '+' : '24+'; ?></div><div class="stat-lbl">Live Scholarships</div></div>
        <div class="stat-box"><div class="stat-num">Weekly</div><div class="stat-lbl">Curated Updates</div></div>
        <div class="stat-box"><div class="stat-num">30+</div><div class="stat-lbl">Countries</div></div>
        <div class="stat-box"><div class="stat-num">Free</div><div class="stat-lbl">Always Free</div></div>
    </div>

    <!-- ── Scholarship cards ─────────────────────────────────────────────── -->
    <section id="scholarships" style="padding-top:1rem">
        <h2 class="section-title">
            <?php echo ($search || $filter !== 'all') ? 'Search Results' : 'Featured Scholarships'; ?>
        </h2>
        <p class="section-sub">
            <?php
            if ($search)           echo 'Results for <strong>' . htmlspecialchars($search) . '</strong>';
            elseif ($filter !== 'all') echo 'Filtered: <strong>' . htmlspecialchars(ucfirst($filter)) . '</strong>';
            else                   echo 'Real, verified opportunities from official programme pages';
            ?>
        </p>

        <div class="sch-grid">
        <?php if (empty($display)): ?>
            <div style="grid-column:1/-1;text-align:center;padding:2rem;background:var(--surface);border-radius:var(--radius);color:var(--muted)">
                <i class="fas fa-search" style="font-size:2rem;margin-bottom:.75rem;display:block"></i>
                No scholarships match your search. Try different keywords.
            </div>
        <?php else: ?>
            <?php foreach ($display as $s): ?>
            <article class="sch-card">
                <div class="sch-card-head <?php echo htmlspecialchars($s['type']); ?>">
                    <span class="sch-card-type"><?php echo ucfirst($s['type']); ?></span>
                    <h3 class="sch-card-title"><?php echo htmlspecialchars($s['title']); ?></h3>
                    <div class="sch-card-meta">
                        <span><i class="fas fa-globe"></i> <?php echo htmlspecialchars($s['country']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo htmlspecialchars($s['deadline']); ?></span>
                    </div>
                </div>
                <div class="sch-card-body">
                    <p><?php echo htmlspecialchars($s['description']); ?></p>
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                        <span class="tag"><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($s['level']); ?></span>
                        <span class="tag"><i class="fas fa-coins"></i> <?php echo htmlspecialchars($s['funding']); ?></span>
                    </div>
                </div>
                <div class="sch-card-foot">
                    <span style="font-size:.78rem;color:var(--muted)"><?php echo htmlspecialchars($s['source'] ?? ''); ?></span>
                    <div style="display:flex;gap:.45rem;align-items:center">
                        <?php echo whatsapp_btn($s['title'] ?? '', $s['url'] ?? '#'); ?>
                        <a href="<?php echo htmlspecialchars($s['url'] ?? '#'); ?>" target="_blank" rel="noopener" class="sch-apply">
                            Apply <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>

        <?php if (!$search && $filter === 'all'): ?>
        <div style="text-align:center;margin-top:1.75rem">
            <a href="scholarship.php" class="btn btn-primary" style="border-radius:12px">View All <?php echo count($scholarships) > 0 ? count($scholarships) : ''; ?> Scholarships</a>
        </div>
        <?php endif; ?>
    </section>

    <!-- ── Mid-page ad ───────────────────────────────────────────────────── -->
    <div style="margin:2rem 0;text-align:center">
        <?php echo adsense_unit(AD_SLOT_RECTANGLE); ?>
    </div>

    <!-- ── Contact & Subscribe ───────────────────────────────────────────── -->
    <section id="contact" style="padding-top:.5rem">
        <h2 class="section-title">Stay Connected</h2>
        <p class="section-sub">Get in touch or subscribe for weekly scholarship alerts</p>

        <div class="contact-grid">

            <!-- Contact form -->
            <div class="contact-card">
                <h3><i class="fas fa-envelope" style="color:var(--primary)"></i> Get in Touch</h3>
                <?php if ($msg_contact): ?>
                    <div class="alert alert-<?php echo $msg_contact['type'] === 'success' ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($msg_contact['text']); ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="#contact">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label>Your Name</label>
                        <input class="form-control" type="text" name="name" required
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input class="form-control" type="email" name="email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea class="form-control" name="message" rows="4" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    <button type="submit" name="contact_submit" class="btn btn-primary" style="width:100%;justify-content:center">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Subscribe -->
            <div class="contact-card">
                <h3><i class="fas fa-bell" style="color:var(--secondary)"></i> Weekly Scholarship Alerts</h3>
                <p style="color:var(--muted);font-size:.92rem;margin-bottom:1.2rem">
                    Enter your email and we will send verified scholarship updates and deadline reminders every week.
                </p>
                <?php if ($msg_subscribe): ?>
                    <div class="alert alert-<?php echo $msg_subscribe['type'] === 'success' ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($msg_subscribe['text']); ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="#contact">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label>Your Email</label>
                        <input class="form-control" type="email" name="subscriber_email" placeholder="you@example.com" required>
                    </div>
                    <button type="submit" name="subscribe_submit" class="btn btn-amber" style="width:100%;justify-content:center">
                        <i class="fas fa-bolt"></i> Subscribe — It's Free
                    </button>
                </form>
                <p style="font-size:.85rem;color:var(--muted);margin:.75rem 0 0">
                    Want alerts by level? <a href="subscribe.php" style="color:var(--primary-light)">Choose your alert preferences</a>.
                </p>

                <hr style="border:none;border-top:1px solid var(--border);margin:1.25rem 0">
                <h4 style="margin:0 0 .6rem;font-size:.95rem">Why GamScholarship?</h4>
                <ul style="padding-left:1.2rem;color:var(--muted);font-size:.9rem;line-height:1.8;margin:0">
                    <li>Real scholarships with official links</li>
                    <li>Updated every week with new opportunities</li>
                    <li>Tips &amp; guides written for Gambian students</li>
                    <li>Always 100% free to use</li>
                </ul>
            </div>
        </div>
    </section>
</main>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
