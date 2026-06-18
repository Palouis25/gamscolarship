<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/layout.php';

// ── Load scholarships from JSON (auto-updated by scraper.php) ─────────────────
$json_file = __DIR__ . '/scholarships.json';

if (file_exists($json_file)) {
    $data         = json_decode(file_get_contents($json_file), true);
    $scholarships = $data['scholarships'] ?? [];
    $meta         = $data['meta']         ?? [];
} else {
    // JSON hasn't been generated yet — show a friendly message
    $scholarships = [];
    $meta         = [];
}

// ── Filters ───────────────────────────────────────────────────────────────────
$search       = isset($_GET['q'])     ? trim(htmlspecialchars($_GET['q'],     ENT_QUOTES, 'UTF-8')) : '';
$filter_level = isset($_GET['level']) ? trim(htmlspecialchars($_GET['level'], ENT_QUOTES, 'UTF-8')) : 'all';
$filter_type  = isset($_GET['type'])  ? trim(htmlspecialchars($_GET['type'],  ENT_QUOTES, 'UTF-8')) : 'all';
$sort_by      = isset($_GET['sort'])  ? trim(htmlspecialchars($_GET['sort'],  ENT_QUOTES, 'UTF-8')) : 'published';

$filtered = array_values(array_filter($scholarships, function($s) use ($search, $filter_level, $filter_type) {
    // Level filter
    if ($filter_level !== 'all') {
        $level_low = strtolower($s['level'] ?? '');
        if (strpos($level_low, $filter_level) === false) return false;
    }
    // Type filter
    if ($filter_type !== 'all' && ($s['type'] ?? '') !== $filter_type) return false;
    // Search
    if ($search) {
        $hay = strtolower(
            ($s['title']       ?? '') . ' ' .
            ($s['description'] ?? '') . ' ' .
            ($s['country']     ?? '') . ' ' .
            implode(' ', $s['tags'] ?? [])
        );
        if (strpos($hay, strtolower($search)) === false) return false;
    }
    return true;
}));

// Sort
usort($filtered, function($a, $b) use ($sort_by) {
    if ($sort_by === 'name')    return strcmp($a['title']   ?? '', $b['title']   ?? '');
    if ($sort_by === 'country') return strcmp($a['country'] ?? '', $b['country'] ?? '');
    // Default: newest first
    return strcmp($b['published'] ?? '', $a['published'] ?? '');
});

// ── Pagination ────────────────────────────────────────────────────────────────
$per_page    = 18;
$page        = max(1, (int)($_GET['page'] ?? 1));
$total       = count($filtered);
$total_pages = max(1, (int)ceil($total / $per_page));
$page        = min($page, $total_pages);
$offset      = ($page - 1) * $per_page;
$page_items  = array_slice($filtered, $offset, $per_page);

// ── Query string helper ────────────────────────────────────────────────────────
function qs(array $override = []): string {
    $params = array_merge([
        'q'     => $_GET['q']     ?? '',
        'level' => $_GET['level'] ?? '',
        'type'  => $_GET['type']  ?? '',
        'sort'  => $_GET['sort']  ?? '',
    ], $override);
    $params = array_filter($params, fn($v) => $v !== '' && $v !== 'all');
    return $params ? '?' . http_build_query($params) : '?';
}

function is_official_source(array $s): bool {
    $url = strtolower($s['url'] ?? '');
    $source = strtolower($s['source'] ?? '');
    $official_domains = [
        '.gov', '.edu', '.ac.', 'chevening.org', 'daad.de', 'mastercardfdn.org',
        'cscuk.fcdo.gov.uk', 'dfat.gov.au', 'eacea.ec.europa.eu',
        'gatescambridge.org', 'studyinjapan.go.jp', 'studyinkorea.go.kr',
        'fulbrightonline.org', 'sbfi.admin.ch', 'campuschina.org',
        'turkiyeburslari.gov.tr', 'campusfrance.org', 'si.se', 'nuffic.nl',
        'vanier.gc.ca', 'mfat.govt.nz', 'afdb.org', 'akdn.org', 'ox.ac.uk',
        'ethz.ch', 'ugc.edu.hk', 'kaist.ac.kr'
    ];

    foreach ($official_domains as $domain) {
        if (str_contains($url, $domain) || str_contains($source, $domain)) return true;
    }
    return false;
}

function deadline_status(string $deadline): array {
    $deadline = trim($deadline);
    $timestamp = strtotime($deadline);

    if (!$deadline || strtolower($deadline) === 'varies' || stripos($deadline, 'see') !== false) {
        return ['Deadline to confirm', 'status-watch'];
    }

    if (!$timestamp) {
        return ['Deadline listed', 'status-watch'];
    }

    $today = strtotime(date('Y-m-d'));
    if ($timestamp < $today) return ['Deadline may be closed', 'status-closed'];
    if ($timestamp <= strtotime('+30 days', $today)) return ['Closing soon', 'status-soon'];
    return ['Open / upcoming', 'status-open'];
}

function guide_link_for(array $s): string {
    $title = strtolower($s['title'] ?? '');
    $map = [
        'chevening' => 'guides/chevening.php',
        'commonwealth' => 'guides/commonwealth.php',
        'daad' => 'guides/daad.php',
        'erasmus' => 'guides/erasmus.php',
        'australia awards' => 'guides/australia.php',
        'fulbright' => 'guides/fulbright.php',
        'gates cambridge' => 'guides/gates.php',
        'mastercard' => 'guides/mastercard.php',
    ];

    foreach ($map as $needle => $href) {
        if (str_contains($title, $needle)) return $href;
    }
    return 'how-to-apply.php';
}

function application_steps_for(array $s): array {
    $level = strtolower($s['level'] ?? '');
    $steps = [
        'Read the official eligibility rules and confirm Gambian students can apply.',
        'Prepare your transcript, passport or national ID, CV, references, and statement.',
        'Apply only through the official programme or university website.',
        'Save your confirmation email and track the deadline in your calendar.',
    ];

    if (str_contains($level, 'phd') || str_contains($level, 'research')) {
        array_splice($steps, 2, 0, 'Prepare a research proposal and contact potential supervisors if required.');
    }

    return $steps;
}

$extra_css = '
.page-hero{background:var(--grad);color:#fff;padding:2.5rem 1.2rem;text-align:center}
.page-hero h1{font-size:clamp(1.5rem,4vw,2.4rem);font-weight:800;margin:0 0 .4rem;letter-spacing:-.02em}
.page-hero p{opacity:.92;font-size:1rem;margin:0}
.page-hero .meta-bar{display:flex;justify-content:center;gap:1.5rem;flex-wrap:wrap;margin-top:.9rem;font-size:.82rem;opacity:.85}
.page-hero .meta-bar span{display:flex;align-items:center;gap:.35rem}
.filters-bar{background:var(--surface);border-bottom:1px solid var(--border);padding:1.2rem}
.filters-inner{max-width:1200px;margin:0 auto;display:flex;flex-direction:column;gap:.9rem}
.search-row{position:relative}
.search-row input{width:100%;padding:.8rem 1rem .8rem 2.8rem;border:2px solid var(--border);border-radius:99px;font-size:.95rem;font-family:inherit;transition:border-color .18s}
.search-row input:focus{outline:none;border-color:var(--primary-light)}
.search-row i{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none}
.filter-row{display:flex;gap:.5rem;flex-wrap:wrap;align-items:center}
.filter-row select,.filter-row button{padding:.4rem .85rem;border:1.5px solid var(--border);border-radius:8px;font-size:.88rem;font-family:inherit;background:var(--surface);cursor:pointer}
.filter-row button{background:var(--primary);color:#fff;border-color:var(--primary);font-weight:600}
.filter-row select:focus{outline:none;border-color:var(--primary-light)}
.results-bar{max-width:1200px;margin:.75rem auto 0;padding:0 1.2rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;font-size:.88rem;color:var(--muted)}
.sch-grid{max-width:1200px;margin:1.25rem auto 1.5rem;padding:0 1.2rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.25rem}
.sch-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;display:flex;flex-direction:column;transition:transform .2s,box-shadow .2s}
.sch-card:hover{transform:translateY(-4px);box-shadow:0 14px 36px rgba(0,0,0,.12)}
.sch-head{padding:1.1rem;color:#fff;position:relative}
.sch-head.government{background:linear-gradient(135deg,#1a3c6e,#2563eb)}
.sch-head.foundation{background:linear-gradient(135deg,#7c2d12,#ea580c)}
.sch-head.university{background:linear-gradient(135deg,#4c1d95,#7c3aed)}
.sch-head.international,.sch-head.external{background:linear-gradient(135deg,#064e3b,#10b981)}
.sch-type-badge{position:absolute;top:.75rem;right:.75rem;background:rgba(255,255,255,.92);color:#1e293b;padding:.18rem .55rem;border-radius:8px;font-size:.7rem;font-weight:700;text-transform:capitalize}
.sch-title{font-size:.97rem;font-weight:700;margin:0 3.5rem .35rem 0;line-height:1.3}
.sch-meta{font-size:.77rem;opacity:.92;display:flex;gap:.7rem;flex-wrap:wrap}
.sch-body{padding:1rem;flex:1;display:flex;flex-direction:column;gap:.65rem}
.sch-desc{color:var(--muted);font-size:.86rem;line-height:1.55;margin:0}
.sch-details{display:grid;grid-template-columns:1fr 1fr;gap:.5rem}
.sch-detail{background:var(--bg);padding:.5rem .65rem;border-radius:8px;font-size:.78rem}
.sch-detail-label{color:var(--muted);font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:.15rem}
.sch-detail-val{font-weight:600;color:var(--text)}
.sch-tags{display:flex;gap:.35rem;flex-wrap:wrap}
.trust-row{display:grid;grid-template-columns:1fr;gap:.35rem;margin-top:.15rem}
.trust-pill{display:flex;align-items:center;gap:.4rem;border-radius:8px;padding:.4rem .55rem;font-size:.76rem;font-weight:600;background:#f8fafc;color:var(--text);border:1px solid var(--border)}
.trust-pill i{color:var(--success)}
.status-open{color:#047857}.status-soon{color:#b45309}.status-watch{color:#475569}.status-closed{color:#b91c1c}
.apply-steps{border-top:1px solid var(--border);padding-top:.7rem;margin-top:.15rem}
.apply-steps summary{cursor:pointer;font-weight:700;font-size:.82rem;color:var(--primary);list-style:none}
.apply-steps summary::-webkit-details-marker{display:none}
.apply-steps ol{margin:.55rem 0 0;padding-left:1.2rem;color:var(--muted);font-size:.8rem;line-height:1.45}
.guide-link{font-size:.78rem;color:var(--primary-light);text-decoration:none;font-weight:700}
.guide-link:hover{text-decoration:underline}
.sch-foot{padding:.75rem 1rem;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#fafbfc}
.apply-btn{background:var(--success);color:#fff;padding:.42rem .9rem;border-radius:10px;text-decoration:none;font-weight:600;font-size:.82rem;display:inline-flex;align-items:center;gap:.35rem;transition:background .15s}
.apply-btn:hover{background:#059669}
.new-badge{background:#dbeafe;color:#1d4ed8;font-size:.68rem;font-weight:700;padding:.15rem .45rem;border-radius:6px;margin-left:.35rem}
.pagination{display:flex;justify-content:center;gap:.4rem;margin:1.5rem 0 2rem;flex-wrap:wrap}
.pagination a,.pagination span{padding:.45rem .85rem;border-radius:8px;font-size:.88rem;font-weight:500;text-decoration:none;border:1.5px solid var(--border);color:var(--text);background:var(--surface);transition:all .15s}
.pagination a:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.pagination .active{background:var(--primary);color:#fff;border-color:var(--primary)}
.empty-state{grid-column:1/-1;text-align:center;padding:3rem;background:var(--surface);border-radius:var(--radius);color:var(--muted)}
.empty-state i{font-size:2.5rem;margin-bottom:.75rem;display:block}
@media(max-width:600px){.sch-details{grid-template-columns:1fr}}
';

echo page_head(
    'All Scholarships — GamScholarship',
    'Browse ' . count($scholarships) . ' scholarships for Gambian students. Updated daily from top scholarship sources.',
    $extra_css
);
echo '<body>';
echo site_header('scholarships');
?>

<!-- ── Hero ─────────────────────────────────────────────────────────────── -->
<div class="page-hero">
    <h1><i class="fas fa-search"></i> Scholarship Database</h1>
    <p>Auto-updated daily from the world's top scholarship sources</p>
    <div class="meta-bar">
        <span><i class="fas fa-graduation-cap"></i> <?php echo count($scholarships); ?> scholarships</span>
        <?php if (!empty($meta['last_updated'])): ?>
        <span><i class="fas fa-sync-alt"></i> Updated: <?php echo htmlspecialchars($meta['last_updated']); ?></span>
        <?php endif; ?>
        <span><i class="fas fa-check-circle"></i> Verified sources</span>
    </div>
</div>

<!-- ── Filters ────────────────────────────────────────────────────────────── -->
<div class="filters-bar">
    <form method="get" action="scholarship.php" class="filters-inner">
        <div class="search-row">
            <i class="fas fa-search"></i>
            <input type="text" name="q" placeholder="Search by name, country, field, keyword…"
                   value="<?php echo $search; ?>" aria-label="Search scholarships">
        </div>
        <div class="filter-row">
            <select name="level" onchange="this.form.submit()">
                <option value="all"          <?php echo $filter_level==='all'          ?'selected':'';?>>All Levels</option>
                <option value="undergraduate"<?php echo $filter_level==='undergraduate'?'selected':'';?>>Undergraduate</option>
                <option value="masters"      <?php echo $filter_level==='masters'      ?'selected':'';?>>Master's</option>
                <option value="phd"          <?php echo $filter_level==='phd'          ?'selected':'';?>>PhD</option>
                <option value="research"     <?php echo $filter_level==='research'     ?'selected':'';?>>Research / Fellowship</option>
            </select>
            <select name="type" onchange="this.form.submit()">
                <option value="all"          <?php echo $filter_type==='all'          ?'selected':'';?>>All Types</option>
                <option value="government"   <?php echo $filter_type==='government'   ?'selected':'';?>>Government</option>
                <option value="foundation"   <?php echo $filter_type==='foundation'   ?'selected':'';?>>Foundation</option>
                <option value="university"   <?php echo $filter_type==='university'   ?'selected':'';?>>University</option>
                <option value="external"     <?php echo $filter_type==='external'     ?'selected':'';?>>External / Other</option>
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="published"<?php echo $sort_by==='published'?'selected':'';?>>Newest First</option>
                <option value="name"     <?php echo $sort_by==='name'     ?'selected':'';?>>Name A–Z</option>
                <option value="country"  <?php echo $sort_by==='country'  ?'selected':'';?>>Country A–Z</option>
            </select>
            <button type="submit"><i class="fas fa-search"></i> Search</button>
            <?php if ($search || $filter_level!=='all' || $filter_type!=='all'): ?>
                <a href="scholarship.php" style="font-size:.85rem;color:var(--muted);text-decoration:none;padding:.4rem">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ── Results bar ────────────────────────────────────────────────────────── -->
<div class="results-bar">
    <span>
        Showing <strong><?php echo $offset + 1; ?>–<?php echo min($offset + $per_page, $total); ?></strong>
        of <strong><?php echo $total; ?></strong>
        <?php echo ($search || $filter_level!=='all' || $filter_type!=='all') ? 'matching' : ''; ?> scholarships
    </span>
    <span>✅ All links go to official programme pages</span>
</div>

<!-- ── Top ad ─────────────────────────────────────────────────────────────── -->
<div style="max-width:1200px;margin:.5rem auto 0;padding:0 1.2rem;text-align:center">
    <?php echo adsense_unit(AD_SLOT_LEADERBOARD, 'horizontal'); ?>
</div>

<!-- ── Cards ─────────────────────────────────────────────────────────────── -->
<section class="sch-grid">
<?php if (empty($scholarships) && !file_exists($json_file)): ?>
    <div class="empty-state">
        <i class="fas fa-cog fa-spin"></i>
        <h3>Scholarships loading…</h3>
        <p>Run <code>php scraper.php</code> on your server to populate the database for the first time.</p>
    </div>

<?php elseif (empty($page_items)): ?>
    <div class="empty-state">
        <i class="fas fa-search"></i>
        <h3>No scholarships found</h3>
        <p>Try different keywords or clear the filters.</p>
        <a href="scholarship.php" class="btn btn-primary" style="margin-top:.75rem">Clear Filters</a>
    </div>

<?php else: ?>
    <?php
    $now = time();
    foreach ($page_items as $i => $s):
        $pub_ts   = strtotime($s['published'] ?? '');
        $is_new   = $pub_ts && ($now - $pub_ts) < 7 * 86400;   // published in last 7 days
        $type_cls = htmlspecialchars($s['type'] ?? 'external');
        $official = is_official_source($s);
        [$deadline_label, $deadline_class] = deadline_status($s['deadline'] ?? '');
        $guide_href = guide_link_for($s);
    ?>
    <article class="sch-card">
        <div class="sch-head <?php echo $type_cls; ?>">
            <span class="sch-type-badge"><?php echo ucfirst($s['type'] ?? 'external'); ?></span>
            <h3 class="sch-title">
                <?php echo htmlspecialchars($s['title'] ?? ''); ?>
                <?php if ($is_new): ?><span class="new-badge">NEW</span><?php endif; ?>
            </h3>
            <div class="sch-meta">
                <span><i class="fas fa-globe"></i> <?php echo htmlspecialchars($s['country'] ?? 'International'); ?></span>
                <span><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($s['deadline'] ?? 'See page'); ?></span>
            </div>
        </div>

        <div class="sch-body">
            <p class="sch-desc"><?php echo htmlspecialchars($s['description'] ?? ''); ?></p>
            <div class="sch-details">
                <div class="sch-detail">
                    <div class="sch-detail-label">Level</div>
                    <div class="sch-detail-val"><?php echo htmlspecialchars($s['level'] ?? '—'); ?></div>
                </div>
                <div class="sch-detail">
                    <div class="sch-detail-label">Funding</div>
                    <div class="sch-detail-val"><?php echo htmlspecialchars($s['funding'] ?? '—'); ?></div>
                </div>
            </div>
            <?php if (!empty($s['tags'])): ?>
            <div class="sch-tags">
                <?php foreach (array_slice($s['tags'], 0, 5) as $tag): ?>
                    <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="trust-row">
                <div class="trust-pill">
                    <i class="fas fa-shield-alt"></i>
                    <?php echo $official ? 'Official source checked' : 'Needs manual source review'; ?>
                </div>
                <div class="trust-pill">
                    <i class="fas fa-clock"></i>
                    <span class="<?php echo $deadline_class; ?>"><?php echo htmlspecialchars($deadline_label); ?></span>
                    <span style="color:var(--muted);font-weight:500">Last reviewed: <?php echo htmlspecialchars($meta['last_updated'] ?? date('Y-m-d')); ?></span>
                </div>
            </div>
            <details class="apply-steps">
                <summary><i class="fas fa-list-check"></i> How to apply safely</summary>
                <ol>
                    <?php foreach (application_steps_for($s) as $step): ?>
                        <li><?php echo htmlspecialchars($step); ?></li>
                    <?php endforeach; ?>
                </ol>
            </details>
        </div>

        <div class="sch-foot">
            <span style="font-size:.77rem;color:var(--muted)"><?php echo htmlspecialchars($s['source'] ?? ''); ?></span>
            <div style="display:flex;gap:.5rem;align-items:center">
                <a href="<?php echo htmlspecialchars($guide_href); ?>" class="guide-link">Guide</a>
                <?php echo whatsapp_btn($s['title'] ?? '', $s['url'] ?? '#'); ?>
                <a href="<?php echo htmlspecialchars($s['url'] ?? '#'); ?>"
                   target="_blank" rel="noopener noreferrer" class="apply-btn">
                    Apply <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>
    </article>

    <?php // Mid-page ad after 9th card
    if ($i === 8): ?>
        </section>
        <div style="max-width:1200px;margin:.5rem auto;padding:0 1.2rem;text-align:center">
            <?php echo adsense_unit(AD_SLOT_RECTANGLE); ?>
        </div>
        <section class="sch-grid" style="margin-top:0">
    <?php endif; ?>

    <?php endforeach; ?>
<?php endif; ?>
</section>

<!-- ── Pagination ─────────────────────────────────────────────────────────── -->
<?php if ($total_pages > 1): ?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($page > 1): ?>
        <a href="<?php echo qs(['page' => $page - 1]); ?>"><i class="fas fa-chevron-left"></i> Prev</a>
    <?php endif; ?>

    <?php
    $start = max(1, $page - 2);
    $end   = min($total_pages, $page + 2);
    if ($start > 1) echo '<span>…</span>';
    for ($p = $start; $p <= $end; $p++):
        $cls = $p === $page ? ' active' : '';
    ?>
        <a href="<?php echo qs(['page' => $p]); ?>" class="<?php echo trim($cls); ?>"><?php echo $p; ?></a>
    <?php endfor;
    if ($end < $total_pages) echo '<span>…</span>';
    ?>

    <?php if ($page < $total_pages): ?>
        <a href="<?php echo qs(['page' => $page + 1]); ?>">Next <i class="fas fa-chevron-right"></i></a>
    <?php endif; ?>
</nav>
<?php endif; ?>

<!-- ── Bottom ad ─────────────────────────────────────────────────────────── -->
<div style="max-width:1200px;margin:0 auto 2.5rem;padding:0 1.2rem;text-align:center">
    <?php echo adsense_unit(AD_SLOT_RECTANGLE); ?>
</div>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
