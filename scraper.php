<?php
/**
 * GamScholarship — Automatic Scholarship Scraper
 * ─────────────────────────────────────────────────────────────
 * Sources:
 *   1. Opportunity Desk RSS feed     (free, no API key needed)
 *   2. Scholars4Dev RSS feed         (free, no API key needed)
 *   3. ScholarshipTab RSS            (free, no API key needed)
 *   4. AfricanScholarships RSS       (free, no API key needed)
 *   5. Curated fallback list         (always included)
 *
 * Run manually:   php scraper.php
 * Cron (daily):   0 3 * * * php /home/YOUR_USER/public_html/scraper.php
 *
 * Output: scholarships.json
 */

define('JSON_OUTPUT',     __DIR__ . '/scholarships.json');
define('LOG_FILE',        __DIR__ . '/scraper.log');
define('REQUEST_TIMEOUT', 15);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Forbidden. Run this script from cron or the command line.';
    exit;
}

// ── Logger ────────────────────────────────────────────────────────────────────
function log_msg(string $msg): void {
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents(LOG_FILE, $line, FILE_APPEND);
    echo $line;
}

// ── HTTP fetch ────────────────────────────────────────────────────────────────
function http_get(string $url): string|false {
    $ctx = stream_context_create([
        'http' => [
            'method'          => 'GET',
            'header'          => "User-Agent: GamScholarship-Bot/1.0 (gamscolaship.online)\r\n",
            'timeout'         => REQUEST_TIMEOUT,
            'follow_location' => true,
        ],
        'ssl'  => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);
    return @file_get_contents($url, false, $ctx);
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function make_slug(string $title): string {
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($title)), '-');
}

function clean_text(string $text, int $max = 300): string {
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', trim($text));
    return strlen($text) > $max ? substr($text, 0, $max - 3) . '…' : $text;
}

function is_scholarship(string $text): bool {
    $kws = ['scholarship','fellowship','grant','bursary','award','funding','study abroad','fully funded'];
    $low = strtolower($text);
    foreach ($kws as $k) { if (strpos($low, $k) !== false) return true; }
    return false;
}

function detect_country(string $text): string {
    $map = [
        'United Kingdom'  => ['united kingdom','uk ','britain','british','chevening','commonwealth'],
        'United States'   => ['united states','usa','america','fulbright'],
        'Germany'         => ['germany','german','daad'],
        'Canada'          => ['canada','canadian','vanier'],
        'Australia'       => ['australia','australian'],
        'France'          => ['france','french','eiffel'],
        'Japan'           => ['japan','japanese','mext'],
        'South Korea'     => ['korea','korean','gks','kaist'],
        'Netherlands'     => ['netherlands','dutch','nuffic'],
        'Sweden'          => ['sweden','swedish'],
        'Switzerland'     => ['switzerland','swiss'],
        'China'           => ['china','chinese','csc'],
        'Turkey'          => ['turkey','turkish'],
        'Norway'          => ['norway','norwegian'],
        'New Zealand'     => ['new zealand'],
        'Africa'          => ['africa','african union','mastercard foundation'],
        'European Union'  => ['erasmus','european union'],
    ];
    $low = strtolower($text);
    foreach ($map as $name => $kws) {
        foreach ($kws as $kw) { if (strpos($low, $kw) !== false) return $name; }
    }
    return 'International';
}

function detect_level(string $text): string {
    $t = strtolower($text);
    if (preg_match('/\bphd\b|docto|d\.phil/i', $t))                  return 'PhD';
    if (preg_match('/master|msc\b|m\.sc|m\.a\b|postgrad/i', $t))     return 'Masters';
    if (preg_match('/bachelor|undergrad|bsc\b|b\.sc|b\.a\b/i', $t))  return 'Undergraduate';
    if (preg_match('/research|fellow|postdoc/i', $t))                 return 'Research / Fellowship';
    return 'Masters / PhD';
}

function auto_tag(string $text): array {
    $low  = strtolower($text);
    $tags = [];
    $map  = [
        'masters'        => ['master','msc','postgraduate'],
        'phd'            => ['phd','doctorate','doctoral'],
        'undergraduate'  => ['undergraduate','bachelor','bsc'],
        'fully-funded'   => ['fully funded','full scholarship','full funding'],
        'research'       => ['research','fellowship','postdoc'],
        'africa'         => ['africa','african'],
        'uk'             => ['united kingdom','chevening','commonwealth'],
        'germany'        => ['germany','daad'],
        'usa'            => ['united states','fulbright'],
        'europe'         => ['europe','erasmus'],
        'stem'           => ['engineering','science','technology','mathematics'],
        'health'         => ['health','medicine','medical','nursing'],
        'business'       => ['business','mba','economics','finance'],
    ];
    foreach ($map as $tag => $kws) {
        foreach ($kws as $kw) {
            if (strpos($low, $kw) !== false) { $tags[] = $tag; break; }
        }
    }
    return array_values(array_unique($tags));
}

// ── Parse an RSS feed URL and return scholarship entries ──────────────────────
function parse_rss(string $url, string $source_name): array {
    $xml = http_get($url);
    if (!$xml) { log_msg("⚠ Failed: $source_name"); return []; }

    $results = [];
    try {
        $feed = new SimpleXMLElement($xml);
        foreach ($feed->channel->item as $item) {
            $title = clean_text((string)$item->title, 120);
            $desc  = clean_text((string)$item->description);
            $link  = trim((string)$item->link);
            $date  = (string)$item->pubDate;

            if (!$title || !is_scholarship($title . ' ' . $desc)) continue;

            $prefix    = strtolower(preg_replace('/[^a-zA-Z]/', '', $source_name));
            $results[] = [
                'id'          => $prefix . '_' . make_slug($title),
                'title'       => $title,
                'description' => $desc ?: 'Visit the official page for full details.',
                'country'     => detect_country($title . ' ' . $desc),
                'level'       => detect_level($title . ' ' . $desc),
                'deadline'    => 'See official page',
                'url'         => $link,
                'source'      => $source_name,
                'funding'     => 'See official page',
                'tags'        => auto_tag($title . ' ' . $desc),
                'type'        => 'external',
                'featured'    => false,
                'verified'    => false,
                'review_note' => 'Imported from RSS feed. Check the official programme page before applying.',
                'published'   => $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d'),
            ];
        }
        log_msg("✓ $source_name: " . count($results) . ' entries');
    } catch (Exception $e) {
        log_msg("⚠ Parse error ($source_name): " . $e->getMessage());
    }
    return $results;
}

// ── Curated fallback — always present ────────────────────────────────────────
function get_curated(): array {
    return [
        ['id'=>'c1','title'=>'Chevening Scholarships','country'=>'United Kingdom','level'=>'Masters','deadline'=>'2025-11-05','url'=>'https://www.chevening.org/scholarships/','source'=>'chevening.org','funding'=>'Full funding + stipend','description'=>'UK government flagship scholarship for future leaders. Covers tuition, flights and a monthly living allowance.','tags'=>['masters','fully-funded','uk'],'type'=>'government','featured'=>true,'published'=>'2025-08-01'],
        ['id'=>'c2','title'=>'DAAD Development-Related Postgraduate Courses','country'=>'Germany','level'=>'Masters / PhD','deadline'=>'2025-09-30','url'=>'https://www.daad.de/en/study-and-research-in-germany/scholarships/daad-scholarships/','source'=>'daad.de','funding'=>'Tuition + monthly stipend','description'=>'Scholarships for students from developing countries for master\'s and PhD programs in Germany.','tags'=>['masters','phd','germany','fully-funded'],'type'=>'government','featured'=>true,'published'=>'2025-08-01'],
        ['id'=>'c3','title'=>'Mastercard Foundation Scholars Program','country'=>'Various (Africa)','level'=>'Undergraduate / Masters','deadline'=>'Varies','url'=>'https://mastercardfdn.org/all/scholars/','source'=>'mastercardfdn.org','funding'=>'Full funding + living allowance','description'=>'Full scholarships for talented young Africans covering tuition, accommodation, travel and living expenses.','tags'=>['africa','undergraduate','masters','fully-funded'],'type'=>'foundation','featured'=>true,'published'=>'2025-08-01'],
        ['id'=>'c4','title'=>'Commonwealth Shared Scholarships','country'=>'United Kingdom','level'=>'Masters','deadline'=>'2025-12-01','url'=>'https://cscuk.fcdo.gov.uk/apply/','source'=>'cscuk.fcdo.gov.uk','funding'=>'Tuition + flights + stipend','description'=>'For citizens of Commonwealth developing countries to study a master\'s at a UK university.','tags'=>['commonwealth','masters','uk'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c5','title'=>'Australia Awards Scholarships','country'=>'Australia','level'=>'Masters / PhD','deadline'=>'2026-04-30','url'=>'https://www.dfat.gov.au/people-to-people/australia-awards/australia-awards-scholarships','source'=>'dfat.gov.au','funding'=>'Full tuition + living costs','description'=>'Australian government scholarships covering tuition, airfares, health insurance and living allowances.','tags'=>['australia','masters','phd','fully-funded'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c6','title'=>'Erasmus Mundus Joint Masters','country'=>'European Union','level'=>'Masters','deadline'=>'2026-01-15','url'=>'https://www.eacea.ec.europa.eu/scholarships/erasmus-mundus-catalogue_en','source'=>'eacea.ec.europa.eu','funding'=>'Tuition + travel + monthly allowance','description'=>'Study in two or more European countries within a joint master\'s programme. Open worldwide.','tags'=>['erasmus','masters','europe'],'type'=>'international','featured'=>true,'published'=>'2025-08-01'],
        ['id'=>'c7','title'=>'Gates Cambridge Scholarship','country'=>'United Kingdom','level'=>'Masters / PhD','deadline'=>'2025-10-08','url'=>'https://www.gatescambridge.org/apply/','source'=>'gatescambridge.org','funding'=>'Full cost of study + stipend','description'=>'Full-cost scholarship for outstanding students from outside the UK to study at the University of Cambridge.','tags'=>['cambridge','masters','phd','fully-funded'],'type'=>'foundation','featured'=>true,'published'=>'2025-08-01'],
        ['id'=>'c8','title'=>'MEXT Scholarship (Japan)','country'=>'Japan','level'=>'Undergraduate / Masters / PhD','deadline'=>'2026-06-01','url'=>'https://www.studyinjapan.go.jp/en/planning/scholarship/application/','source'=>'studyinjapan.go.jp','funding'=>'Tuition + monthly stipend','description'=>'Japanese Ministry of Education scholarship for international students at all degree levels.','tags'=>['japan','undergraduate','masters','phd'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c9','title'=>'Global Korea Scholarship (GKS)','country'=>'South Korea','level'=>'Undergraduate / Masters / PhD','deadline'=>'2026-03-01','url'=>'https://www.studyinkorea.go.kr/en/sub/gks/allnew_invite.do','source'=>'studyinkorea.go.kr','funding'=>'Full tuition + stipend + airfare','description'=>'Korean government flagship scholarship for international undergraduate and graduate students.','tags'=>['korea','masters','phd','fully-funded'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c10','title'=>'Fulbright Foreign Student Program','country'=>'United States','level'=>'Masters / PhD','deadline'=>'2025-10-15','url'=>'https://foreign.fulbrightonline.org/','source'=>'fulbrightonline.org','funding'=>'Full funding including travel','description'=>'US government flagship international exchange program — one of the most prestigious scholarships worldwide.','tags'=>['usa','masters','phd','fully-funded'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c11','title'=>'Swiss Government Excellence Scholarships','country'=>'Switzerland','level'=>'Masters / PhD','deadline'=>'2025-10-31','url'=>'https://www.sbfi.admin.ch/sbfi/en/home/education/scholarships-and-grants/swiss-government-excellence-scholarships.html','source'=>'sbfi.admin.ch','funding'=>'Monthly stipend + health insurance','description'=>'For postgraduate researchers to conduct research or study at a Swiss higher education institution.','tags'=>['switzerland','phd','research'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c12','title'=>'Chinese Government Scholarship (CSC)','country'=>'China','level'=>'Undergraduate / Masters / PhD','deadline'=>'2026-03-31','url'=>'https://www.campuschina.org/scholarships/index.html','source'=>'campuschina.org','funding'=>'Tuition + accommodation + stipend','description'=>'Full scholarships from the Chinese government for international students at all degree levels.','tags'=>['china','undergraduate','masters','phd'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c13','title'=>'Turkish Government Scholarships (Türkiye Bursları)','country'=>'Turkey','level'=>'Undergraduate / Masters / PhD','deadline'=>'2026-02-20','url'=>'https://www.turkiyeburslari.gov.tr/en','source'=>'turkiyeburslari.gov.tr','funding'=>'Tuition + accommodation + stipend + airfare','description'=>'Full scholarships from the Turkish government for international students at Turkish universities.','tags'=>['turkey','undergraduate','masters','phd','fully-funded'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c14','title'=>'Eiffel Excellence Scholarship (France)','country'=>'France','level'=>'Masters / PhD','deadline'=>'2026-01-09','url'=>'https://www.campusfrance.org/en/eiffel-scholarship-program-of-excellence','source'=>'campusfrance.org','funding'=>'Monthly grant + benefits','description'=>'Awarded by the French Ministry for study at French higher education institutions.','tags'=>['france','masters','phd'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c15','title'=>'Swedish Institute Scholarships (SISGP)','country'=>'Sweden','level'=>'Masters','deadline'=>'2026-02-10','url'=>'https://si.se/en/apply/scholarships/swedish-institute-scholarships-for-global-professionals/','source'=>'si.se','funding'=>'Tuition + living grant + insurance','description'=>'For professionals from select countries to study a full-time master\'s programme in Sweden.','tags'=>['sweden','masters','government'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c16','title'=>'Orange Knowledge Programme (OKP)','country'=>'Netherlands','level'=>'Masters','deadline'=>'2026-02-01','url'=>'https://www.nuffic.nl/en/subjects/orange-knowledge-programme','source'=>'nuffic.nl','funding'=>'Tuition + allowances','description'=>'Dutch government scholarships for short courses and master\'s programmes at Dutch institutions.','tags'=>['netherlands','masters','government'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c17','title'=>'Vanier Canada Graduate Scholarships','country'=>'Canada','level'=>'PhD','deadline'=>'2025-11-01','url'=>'https://vanier.gc.ca/en/home-accueil.html','source'=>'vanier.gc.ca','funding'=>'$50,000/year for 3 years','description'=>'Canada\'s most prestigious doctoral scholarship awarded to world-class PhD students.','tags'=>['canada','phd','fully-funded'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c18','title'=>'Manaaki New Zealand Scholarships','country'=>'New Zealand','level'=>'Masters / PhD','deadline'=>'2026-03-28','url'=>'https://www.mfat.govt.nz/en/aid-and-development/scholarships/manaaki-new-zealand-scholarships/','source'=>'mfat.govt.nz','funding'=>'Full tuition + living costs','description'=>'New Zealand government scholarships for people from developing nations.','tags'=>['new-zealand','masters','phd'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c19','title'=>'African Development Bank Scholarships','country'=>'Africa','level'=>'Masters / PhD','deadline'=>'Varies','url'=>'https://www.afdb.org/en/the-african-development-bank-group/scholarships','source'=>'afdb.org','funding'=>'Tuition + stipend + allowances','description'=>'The AfDB provides scholarships for African students at postgraduate level at partner institutions.','tags'=>['africa','masters','phd'],'type'=>'foundation','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c20','title'=>'Aga Khan Foundation International Scholarship','country'=>'Various','level'=>'Masters','deadline'=>'2026-03-31','url'=>'https://www.akdn.org/our-agencies/aga-khan-foundation/international-scholarship-programme','source'=>'akdn.org','funding'=>'50% grant + 50% loan','description'=>'For postgraduate students from developing countries who have no other means of funding their education.','tags'=>['foundation','masters'],'type'=>'foundation','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c21','title'=>'University of Oxford Clarendon Fund','country'=>'United Kingdom','level'=>'Masters / PhD','deadline'=>'2026-01-23','url'=>'https://www.ox.ac.uk/clarendon','source'=>'ox.ac.uk','funding'=>'Full fees + living stipend','description'=>'One of Oxford\'s most prestigious scholarships covering full course fees and a generous living grant.','tags'=>['uk','masters','phd'],'type'=>'university','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c22','title'=>'ETH Zurich Excellence Scholarship','country'=>'Switzerland','level'=>'Masters','deadline'=>'2025-12-15','url'=>'https://ethz.ch/en/studies/financial/scholarships/excellence-scholarship.html','source'=>'ethz.ch','funding'=>'Tuition waiver + monthly stipend','description'=>'For outstanding master\'s applicants at one of the world\'s leading science and technology universities.','tags'=>['switzerland','masters','stem'],'type'=>'university','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c23','title'=>'Hong Kong PhD Fellowship Scheme','country'=>'Hong Kong','level'=>'PhD','deadline'=>'2025-12-01','url'=>'https://www.ugc.edu.hk/eng/rgc/funding_opport/hkphd/','source'=>'ugc.edu.hk','funding'=>'HKD ~330,000/year + travel','description'=>'Prestigious PhD fellowship for outstanding students worldwide to pursue research at Hong Kong universities.','tags'=>['hong-kong','phd','research'],'type'=>'government','featured'=>false,'published'=>'2025-08-01'],
        ['id'=>'c24','title'=>'KAIST International Student Scholarship','country'=>'South Korea','level'=>'Masters / PhD','deadline'=>'2026-01-07','url'=>'https://apply.kaist.ac.kr/','source'=>'apply.kaist.ac.kr','funding'=>'Full tuition + monthly stipend','description'=>'KAIST offers generous scholarships for master\'s and PhD students in science, technology, engineering and mathematics.','tags'=>['korea','masters','phd','stem'],'type'=>'university','featured'=>false,'published'=>'2025-08-01'],
    ];
}

// ── Merge, deduplicate, save ──────────────────────────────────────────────────
function merge_and_save(array ...$sources): void {
    $all    = array_merge(...$sources);
    $seen   = [];
    $unique = [];

    foreach ($all as $item) {
        $key = make_slug($item['title'] ?? '');
        if (!$key || isset($seen[$key])) continue;
        $seen[$key] = true;

        // Ensure all fields present
        $item += [
            'id'          => 'sch_' . $key,
            'title'       => 'Untitled',
            'description' => '',
            'country'     => 'International',
            'level'       => 'Masters / PhD',
            'deadline'    => 'See official page',
            'url'         => '#',
            'source'      => '',
            'funding'     => 'See official page',
            'tags'        => [],
            'type'        => 'external',
            'featured'    => false,
            'verified'    => false,
            'review_note' => 'Imported automatically. Confirm eligibility and deadline on the official page.',
            'published'   => date('Y-m-d'),
        ];
        $unique[] = $item;
    }

    // Featured first, then newest published first
    usort($unique, function($a, $b) {
        if ($a['featured'] !== $b['featured']) return $b['featured'] <=> $a['featured'];
        return strcmp($b['published'], $a['published']);
    });

    $payload = [
        'meta' => [
            'total'        => count($unique),
            'last_updated' => date('Y-m-d H:i:s'),
            'next_update'  => date('Y-m-d H:i:s', strtotime('+1 day')),
        ],
        'scholarships' => $unique,
    ];

    file_put_contents(
        JSON_OUTPUT,
        json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
    log_msg('✅ Saved ' . count($unique) . ' unique scholarships → ' . basename(JSON_OUTPUT));
}

// ── Run all sources ───────────────────────────────────────────────────────────
log_msg('=== GamScholarship Scraper started ===');

$curated = get_curated();
$opdesk  = parse_rss('https://opportunitydesk.org/category/scholarships/feed/',                    'opportunitydesk.org');
$s4dev   = parse_rss('https://www.scholars4dev.com/category/scholarships-by-subject/feed/',       'scholars4dev.com');
$schtab  = parse_rss('https://www.scholarshiptab.com/feed',                                        'scholarshiptab.com');
$african = parse_rss('https://www.africanscholarships.org/feed/',                                  'africanscholarships.org');

merge_and_save($curated, $opdesk, $s4dev, $schtab, $african);

log_msg('=== Scraper finished ===');
echo PHP_EOL . '✅ Done. Total in scholarships.json: check scraper.log for details.' . PHP_EOL;
