<?php
// require db file
require_once '/var/www/config/db.php';

// test db connection (rm if successful)
try {
    // Test 1: Connection
    echo "✅ Connected to database successfully<br>";

    // Test 2: Run a simple query
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbName = $stmt->fetchColumn();
    echo "✅ Using database: " . htmlspecialchars($dbName) . "<br>";

    // Test 3: List your tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "✅ Tables found: " . count($tables) . "<br>";
    foreach ($tables as $table) {
        echo " - " . htmlspecialchars(array_values($table)[0]) . "<br>";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}

?>

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '/var/www/config/db.php';

// h() defined in header.inc.php — only declare here as safety fallback
if (!function_exists('h')) {
    function h(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}

// ── Get profile ID from URL ──
$profileId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($profileId === 0) {
    header('Location: profiles.php');
    exit();
}

// ── Fetch profile from DB ──
$profile = null;
try {
    $stmt = $pdo->prepare("
        SELECT u.id,
               p.display_name  AS name,
               p.age,
               p.location,
               p.occupation,
               p.biography     AS bio,
               p.interests,
               p.religion,
               p.horoscope,
               p.main_image    AS image_1,
               p.image_2,
               p.image_3,
               p.image_4,
               p.image_5,
               p.image_6
        FROM users u
        JOIN profile p ON p.user_id = u.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$profileId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $profile = null;
}

// ── 404 if user/profile not found ──
if (!$profile) {
    http_response_code(404);
    // You can make a proper 404 page later — for now redirect back
    header('Location: profiles.php');
    exit();
}

// ── Build tags ──
$tags = [];
if (!empty($profile['interests'])) {
    $tags = array_map('trim', explode(',', $profile['interests']));
}
if (!empty($profile['religion']))  $tags[] = $profile['religion'];
if (!empty($profile['horoscope'])) $tags[] = '♏ ' . $profile['horoscope'];

// ── Build images list (hero + up to 5 extras) ──
// image_1 is the hero. Extra images are image_2 through image_6.
$heroImage  = $profile['image_1'] ?? null;
$extraImages = [];
for ($i = 2; $i <= 6; $i++) {
    $key = 'image_' . $i;
    if (!empty($profile[$key])) {
        $extraImages[] = $profile[$key];
    }
}

// Helper: resolve filename → web URL
function imageUrl(?string $img): ?string {
    if (empty($img)) return null;
    if (str_starts_with($img, 'http')) return $img; // full URL (test data)
    return '/images/' . $img;                        // local file
}

// ── Fetch answered prompts from DB ──
// SQL for when user_answers table is ready:
//
// SELECT q.question_text, a.answer_text
// FROM user_answers a
// JOIN questions q ON q.id = a.question_id
// WHERE a.user_id = ?
// ORDER BY a.id
// LIMIT 6
//
$prompts = [];
try {
    $stmtP = $pdo->prepare("
        SELECT q.question_text AS q, a.answer_text AS a
        FROM user_answers a
        JOIN questions q ON q.id = a.question_id
        WHERE a.user_id = ?
        ORDER BY a.id
        LIMIT 6
    ");
    $stmtP->execute([$profileId]);
    $prompts = $stmtP->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Table doesn't exist yet — fall back to hardcoded prompts
    $prompts = [
        ['q' => 'My perfect Sunday looks like...',   'a' => 'Waking up at 10am, grabbing a flat white, then wandering around a hawker centre before ending up at a bookshop I\'ve never been to.'],
        ['q' => 'The way to my heart is...',         'a' => 'Recommending me a song I haven\'t heard yet. Bonus points if it becomes my new favourite.'],
        ['q' => 'I\'m weirdly passionate about...', 'a' => 'Mechanical keyboards. I have too many. My colleagues hate me.'],
        ['q' => 'A life goal of mine is...',         'a' => 'To build something people actually use every day — could be an app, could be a really good sandwich shop.'],
        ['q' => 'My most controversial opinion is...','a' => 'Pineapple on pizza is fine actually and I will die on this hill.'],
    ];
}

$activePage = 'discover';
$pageTitle  = h($profile['name']) . '\'s Profile';
require_once 'includes/header.inc.php';
?>
