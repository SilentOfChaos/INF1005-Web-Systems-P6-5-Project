<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit();
}
$activePage = 'discover';
$pageTitle = 'Discover';

require_once '/var/www/config/db.php';
require_once 'includes/header.php';


// Sanitise helper
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ── Fetch real profiles from DB ──
$currentUser = $_SESSION['user_id'];
$dbProfiles  = [];

try {
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.age, u.location, u.occupation, u.bio, u.profile_pic, u.interests
        FROM users u
        WHERE u.id != ?
        AND u.id NOT IN (
            SELECT swiped_id FROM swipes WHERE swiper_id = ?
        )
        ORDER BY RAND()
        LIMIT 20
    ");
    $stmt->execute([$currentUser, $currentUser]);
    $dbProfiles = $stmt->fetchAll();
} catch (Exception $e) {
    $dbProfiles = [];
}

// ── Mock profiles (fallback when DB is empty) ──
$mockProfiles = [
    ['id'=>'mock_1','name'=>'Jonathan','age'=>24,'location'=>'Singapore','occupation'=>'Developer',
     'bio'=>'Coffee-fuelled code monkey. I debug by day and sketch by night. Looking for someone who appreciates terrible puns and good playlists.',
     'interests'=>'Coffee,Art,Hiking,Tech','profile_pic'=>null,'mock'=>true],
    ['id'=>'mock_2','name'=>'Aisha','age'=>22,'location'=>'Singapore','occupation'=>'UX Designer',
     'bio'=>'I design things by day and overthink everything else by night. Big fan of matcha, museums and spontaneous road trips.',
     'interests'=>'Design,Matcha,Travel,Music','profile_pic'=>null,'mock'=>true],
    ['id'=>'mock_3','name'=>'Marcus','age'=>26,'location'=>'Singapore','occupation'=>'Photographer',
     'bio'=>'Chasing golden hour every weekend. Will absolutely make you pose for a photo within 10 minutes of meeting me.',
     'interests'=>'Photography,Film,Cycling,Food','profile_pic'=>null,'mock'=>true],
    ['id'=>'mock_4','name'=>'Priya','age'=>23,'location'=>'Singapore','occupation'=>'Marketing',
     'bio'=>'Serial brunch enthusiast. I know every hidden café in town. Fluent in sarcasm, spreadsheets, and Taylor Swift lyrics.',
     'interests'=>'Brunch,Reading,Yoga,Cooking','profile_pic'=>null,'mock'=>true],
    ['id'=>'mock_5','name'=>'Ethan','age'=>25,'location'=>'Singapore','occupation'=>'Finance',
     'bio'=>'Numbers by week, basketball and bad movies by weekend. Looking for someone to debate whether pineapple belongs on pizza.',
     'interests'=>'Basketball,Finance,Movies,Travel','profile_pic'=>null,'mock'=>true],
];

$profiles  = !empty($dbProfiles) ? $dbProfiles : $mockProfiles;
$usingMock = empty($dbProfiles);
?>


    </div>
    <!-- Dev banner: only when using mock data -->
    <?php if ($usingMock): ?>
    <div style="background:#fff3cd; border-bottom:1px solid #ffc107; color:#856404; text-align:center; padding:0.5rem; font-size:0.8rem; font-weight:500;">
        ⚠️ Using mock profiles — no real users in the database yet. Swipes will not be saved.
    </div>
    <?php endif; ?>

    <!-- ══ Main ══ -->
    <main class = "discover-main" role="main" aria-label="Discover profiles">
        <p class="discover-label" aria-hidden="true">✦ Discover</p>
        <h1 class="discover-title">Find Your Spark</h1>

        <?php if (empty($profiles)): ?>
        <div class="empty-state" role="status">
            <div class="empty-icon" aria-hidden="true">💫</div>
            <h3>You've seen everyone!</h3>
            <p>Check back later for new profiles, or revisit your matches.</p>
            <a href="matches.php" class="btn-solid-custom mt-3 d-inline-block" style="text-decoration:none; padding:0.6rem 1.8rem;">
                View Matches →
            </a>
        </div>

        <?php else: ?>
        <div class="swipe-arena" role="region" aria-label="Profile cards">

            <!-- ← Pass (red left arrow) -->
            <button class="swipe-btn swipe-btn-no" id="btn-no"
                    aria-label="Pass on this profile" title="Pass">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>

            <!-- Card Stack -->
            <div class="card-stack" role="region" aria-live="polite" aria-atomic="true" aria-label="Current profile">
                <div class="swipe-feedback feedback-yes" id="feedback-yes" aria-hidden="true">LIKE</div>
                <div class="swipe-feedback feedback-no"  id="feedback-no"  aria-hidden="true">NOPE</div>

                <div class="profile-card" id="profile-card" tabindex="0" aria-label="Profile card">
                    <div class="card-image" id="card-image" role="img" aria-label="Profile photo"></div>
                    <div class="card-gradient" aria-hidden="true"></div>
                    <div class="card-body-content">
                        <h2 class="card-name" id="card-name"></h2>
                        <p class="card-meta" id="card-meta"></p>
                        <div class="card-tags" id="card-tags" aria-label="Interests"></div>
                        <p class="card-bio" id="card-bio"></p>
                    </div>
                </div>
            </div>

            <!-- → Like (green right arrow) -->
            <button class="swipe-btn swipe-btn-yes" id="btn-yes"
                    aria-label="Like this profile" title="Like">
                <svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>

        <!-- Counter -->
        <div class="profile-counter" aria-live="polite">
            <span id="counter-text"></span>
            <div class="counter-dots" id="counter-dots" aria-hidden="true"></div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Match Toast -->
    <div class="match-toast" id="match-toast" role="status" aria-live="assertive" aria-atomic="true">
        🎉 It's a Match!
    </div>

    <script>
        const profiles  = <?php echo json_encode($profiles); ?>;
        const usingMock = <?php echo $usingMock ? 'true' : 'false'; ?>;

        let currentIndex = 0;
        let isAnimating  = false;

        const card       = document.getElementById('profile-card');
        const cardImage  = document.getElementById('card-image');
        const cardName   = document.getElementById('card-name');
        const cardMeta   = document.getElementById('card-meta');
        const cardTags   = document.getElementById('card-tags');
        const cardBio    = document.getElementById('card-bio');
        const feedYes    = document.getElementById('feedback-yes');
        const feedNo     = document.getElementById('feedback-no');
        const counterTxt = document.getElementById('counter-text');
        const dotsWrap   = document.getElementById('counter-dots');
        const toast      = document.getElementById('match-toast');

        const mockGradients = [
            'linear-gradient(135deg, #4A1060, #9B2368)',
            'linear-gradient(135deg, #0D3875, #1A6FA8)',
            'linear-gradient(135deg, #1A5C3A, #2E9E65)',
            'linear-gradient(135deg, #6B2D0A, #C4622D)',
            'linear-gradient(135deg, #3D0A4A, #7B2FBE)',
        ];

        function renderProfile(index) {
            if (index >= profiles.length) {
                document.querySelector('.swipe-arena').innerHTML = `
                    <div class="empty-state" role="status">
                        <div class="empty-icon">💫</div>
                        <h3>You've seen everyone!</h3>
                        <p>Check back later for new profiles.</p>
                    </div>`;
                if (dotsWrap)   dotsWrap.innerHTML     = '';
                if (counterTxt) counterTxt.textContent = '';
                return;
            }

            const p = profiles[index];

            if (p.profile_pic) {
                cardImage.style.backgroundImage = `url('/uploads/${p.profile_pic}')`;
                cardImage.style.opacity = '0.4';
            } else if (p.mock) {
                cardImage.style.backgroundImage = mockGradients[index % mockGradients.length];
                cardImage.style.opacity = '0.6';
            } else {
                const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(p.name)}&background=4A1060&color=F5E6FF&size=400`;
                cardImage.style.backgroundImage = `url('${avatar}')`;
                cardImage.style.opacity = '0.4';
            }

            cardImage.setAttribute('aria-label', `Photo of ${p.name}`);
            cardName.textContent = `${p.name}, ${p.age}`;

            const parts = [];
            if (p.location)   parts.push(p.location);
            if (p.occupation) parts.push(p.occupation);
            cardMeta.innerHTML = parts.map((pt, i) =>
                i < parts.length - 1
                    ? `${pt} <span class="card-meta-dot" aria-hidden="true"></span>`
                    : pt
            ).join(' ');

            cardTags.innerHTML = '';
            if (p.interests) {
                p.interests.split(',').slice(0, 4).forEach(tag => {
                    const span = document.createElement('span');
                    span.className   = 'card-tag';
                    span.textContent = tag.trim();
                    cardTags.appendChild(span);
                });
            }

            cardBio.textContent = p.bio || '';

            const remaining = profiles.length - index;
            counterTxt.textContent = `${remaining} profile${remaining !== 1 ? 's' : ''} nearby`;

            dotsWrap.innerHTML = '';
            const dotCount = Math.min(profiles.length, 8);
            for (let i = 0; i < dotCount; i++) {
                const dot = document.createElement('div');
                dot.className = 'counter-dot' + (i === index % dotCount ? ' active' : '');
                dotsWrap.appendChild(dot);
            }
        }

        async function recordSwipe(direction) {
            const profile = profiles[currentIndex];
            if (!profile || usingMock) return false;
            try {
                const res  = await fetch('/api/swipe.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ swiped_id: profile.id, direction })
                });
                const data = await res.json();
                return data.match === true;
            } catch (e) {
                console.error('Swipe error:', e);
                return false;
            }
        }

        function showMatchToast() {
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function showFeedback(el) {
            el.style.opacity = '1';
            setTimeout(() => { el.style.opacity = '0'; }, 350);
        }

        async function swipe(direction) {
            if (isAnimating || currentIndex >= profiles.length) return;
            isAnimating = true;

            if (direction === 'like') {
                showFeedback(feedYes);
                card.classList.add('slide-out-right');
            } else {
                showFeedback(feedNo);
                card.classList.add('slide-out-left');
            }

            const matchPromise = recordSwipe(direction);

            setTimeout(async () => {
                card.classList.remove('slide-out-right', 'slide-out-left');
                currentIndex++;
                renderProfile(currentIndex);
                card.classList.add('slide-in-up');
                card.addEventListener('animationend', async () => {
                    card.classList.remove('slide-in-up');
                    isAnimating = false;
                    const isMatch = await matchPromise;
                    if (isMatch) showMatchToast();
                }, { once: true });
            }, 400);
        }

        document.getElementById('btn-yes')?.addEventListener('click', () => swipe('like'));
        document.getElementById('btn-no')?.addEventListener('click',  () => swipe('pass'));

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') swipe('like');
            if (e.key === 'ArrowLeft')  swipe('pass');
        });

        let touchStartX = 0;
        let touchMoved = false;
        card?.addEventListener('touchmove', () => { touchMoved = true; });
        card?.addEventListener('click', () => {
            const p = profiles[currentIndex];
            if (p && !isAnimating) window.location.href = `profile.php?id=${p.id}`;
        });

        card?.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
        card?.addEventListener('touchend',   (e) => {
            const delta = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(delta) > 60) swipe(delta > 0 ? 'like' : 'pass');
        });

        if (profiles.length > 0) renderProfile(0);


    </script>

    <?php require_once 'includes/footer.php'; ?>
</body>
