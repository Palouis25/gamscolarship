<?php
require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/config/layout.php';

$guides = [
    ['slug'=>'chevening',    'title'=>'Chevening Scholarships',          'country'=>'United Kingdom',  'flag'=>'🇬🇧','desc'=>'The UK government\'s flagship scholarship. Fully funded master\'s at any UK university. Deadline: November each year.','level'=>'Masters','funding'=>'Full funding','difficulty'=>'Competitive','tags'=>['government','fully-funded']],
    ['slug'=>'daad',         'title'=>'DAAD Scholarships',               'country'=>'Germany',          'flag'=>'🇩🇪','desc'=>'Germany\'s top scholarship for students from developing countries. Master\'s and PhD at German universities.','level'=>'Masters / PhD','funding'=>'Tuition + stipend','difficulty'=>'Moderate','tags'=>['government','phd']],
    ['slug'=>'commonwealth', 'title'=>'Commonwealth Scholarships',       'country'=>'United Kingdom',  'flag'=>'🇬🇧','desc'=>'For Commonwealth citizens to study a master\'s in the UK. Fully funded by the UK government.','level'=>'Masters','funding'=>'Full funding','difficulty'=>'Competitive','tags'=>['government','fully-funded']],
    ['slug'=>'mastercard',   'title'=>'Mastercard Foundation Scholars',  'country'=>'Africa',           'flag'=>'🌍','desc'=>'Full scholarships for talented Africans at partner universities across Africa and worldwide.','level'=>'Undergraduate / Masters','funding'=>'Full funding','difficulty'=>'Moderate','tags'=>['foundation','africa']],
    ['slug'=>'erasmus',      'title'=>'Erasmus Mundus',                  'country'=>'European Union',   'flag'=>'🇪🇺','desc'=>'Study in two or more European countries. Fully funded joint master\'s programmes open to all nationalities.','level'=>'Masters','funding'=>'Full funding','difficulty'=>'Moderate','tags'=>['government','europe']],
    ['slug'=>'australia',    'title'=>'Australia Awards',                'country'=>'Australia',        'flag'=>'🇦🇺','desc'=>'Australian government scholarships for students from developing countries including The Gambia.','level'=>'Masters / PhD','funding'=>'Full funding','difficulty'=>'Moderate','tags'=>['government','fully-funded']],
    ['slug'=>'fulbright',    'title'=>'Fulbright Program',               'country'=>'United States',    'flag'=>'🇺🇸','desc'=>'The US government\'s flagship scholarship for international students. Prestigious and fully funded.','level'=>'Masters / PhD','funding'=>'Full funding','difficulty'=>'Very Competitive','tags'=>['government','usa']],
    ['slug'=>'gates',        'title'=>'Gates Cambridge Scholarship',     'country'=>'United Kingdom',  'flag'=>'🇬🇧','desc'=>'One of the world\'s most prestigious scholarships — full funding to study any subject at Cambridge University.','level'=>'Masters / PhD','funding'=>'Full funding','difficulty'=>'Very Competitive','tags'=>['foundation','cambridge']],
];

$extra_css = '
.page-hero{background:var(--grad);color:#fff;padding:2.8rem 1.2rem;text-align:center}
.page-hero h1{font-size:clamp(1.6rem,4vw,2.5rem);font-weight:800;margin:0 0 .5rem;letter-spacing:-.02em}
.page-hero p{opacity:.92;font-size:1rem;margin:0 auto;max-width:580px}
.guides-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.25rem;margin-top:2rem}
.guide-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;display:flex;flex-direction:column;transition:transform .2s,box-shadow .2s;text-decoration:none;color:var(--text)}
.guide-card:hover{transform:translateY(-4px);box-shadow:0 14px 36px rgba(0,0,0,.13)}
.guide-card-head{background:var(--grad);padding:1.25rem;color:#fff;position:relative}
.guide-flag{font-size:2rem;display:block;margin-bottom:.4rem}
.guide-title{font-size:1.02rem;font-weight:700;margin:0 0 .25rem;line-height:1.3}
.guide-country{font-size:.82rem;opacity:.9}
.guide-body{padding:1rem;flex:1}
.guide-desc{color:var(--muted);font-size:.88rem;line-height:1.6;margin:0 0 .85rem}
.guide-meta{display:grid;grid-template-columns:1fr 1fr;gap:.5rem}
.guide-meta-item{background:var(--bg);padding:.45rem .6rem;border-radius:8px;font-size:.76rem}
.guide-meta-label{color:var(--muted);font-size:.68rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:.1rem}
.guide-meta-val{font-weight:600}
.guide-foot{padding:.75rem 1rem;border-top:1px solid var(--border);background:#fafbfc;display:flex;align-items:center;justify-content:space-between}
.guide-read{color:var(--primary-light);font-weight:600;font-size:.88rem;display:flex;align-items:center;gap:.35rem}
.difficulty-badge{font-size:.72rem;font-weight:700;padding:.18rem .5rem;border-radius:6px}
.diff-moderate{background:#d1fae5;color:#065f46}
.diff-competitive{background:#fef9c3;color:#854d0e}
.diff-very{background:#fee2e2;color:#991b1b}
';

echo page_head(
    'Scholarship Guides for Gambian Students — GamScholarship',
    'Detailed guides on how Gambian students can apply for the world\'s top scholarships — Chevening, DAAD, Commonwealth, Erasmus and more.',
    $extra_css
);
echo '<body>';
echo site_header('guides');
?>

<div class="page-hero">
    <h1>📚 Scholarship Guides</h1>
    <p>Step-by-step guides for each major scholarship — written specifically for Gambian applicants</p>
</div>

<main class="container" style="padding-bottom:3rem">

    <div style="text-align:center;margin:1.5rem 0">
        <?php echo adsense_unit(AD_SLOT_LEADERBOARD, 'horizontal'); ?>
    </div>

    <div style="background:var(--surface);border-radius:var(--radius);padding:1.25rem 1.5rem;border:1px solid var(--border);box-shadow:var(--shadow);margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
        <i class="fas fa-info-circle" style="color:var(--primary-light);font-size:1.2rem;flex-shrink:0"></i>
        <p style="margin:0;font-size:.92rem;color:var(--muted);flex:1">Each guide below covers eligibility, required documents, how to write your application, and specific tips for Gambian students. Click any guide to read the full details.</p>
    </div>

    <div class="guides-grid">
    <?php foreach ($guides as $g):
        $diff_cls = match(strtolower($g['difficulty'])) {
            'moderate'        => 'diff-moderate',
            'competitive'     => 'diff-competitive',
            'very competitive'=> 'diff-very',
            default           => 'diff-moderate',
        };
    ?>
        <a href="guides/<?php echo $g['slug']; ?>.php" class="guide-card">
            <div class="guide-card-head">
                <span class="guide-flag"><?php echo $g['flag']; ?></span>
                <div class="guide-title"><?php echo htmlspecialchars($g['title']); ?></div>
                <div class="guide-country"><?php echo htmlspecialchars($g['country']); ?></div>
            </div>
            <div class="guide-body">
                <p class="guide-desc"><?php echo htmlspecialchars($g['desc']); ?></p>
                <div class="guide-meta">
                    <div class="guide-meta-item">
                        <div class="guide-meta-label">Level</div>
                        <div class="guide-meta-val"><?php echo htmlspecialchars($g['level']); ?></div>
                    </div>
                    <div class="guide-meta-item">
                        <div class="guide-meta-label">Funding</div>
                        <div class="guide-meta-val"><?php echo htmlspecialchars($g['funding']); ?></div>
                    </div>
                </div>
            </div>
            <div class="guide-foot">
                <span class="difficulty-badge <?php echo $diff_cls; ?>"><?php echo htmlspecialchars($g['difficulty']); ?></span>
                <span class="guide-read">Read Guide <i class="fas fa-arrow-right"></i></span>
            </div>
        </a>
    <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin:2.5rem 0">
        <?php echo adsense_unit(AD_SLOT_RECTANGLE); ?>
    </div>

    <!-- WhatsApp share -->
    <div style="text-align:center;margin:1.5rem 0">
        <p style="color:var(--muted);font-size:.9rem;margin-bottom:.6rem">Share these guides with Gambian students who need them:</p>
        <?php echo whatsapp_btn('Scholarship Guides for Gambian Students', SITE_URL . '/guides.php', 'lg'); ?>
    </div>

</main>

<?php echo site_footer(); ?>
<?php echo cookie_banner(); ?>
</body>
</html>
