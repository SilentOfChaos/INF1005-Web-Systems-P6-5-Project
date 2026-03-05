<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!function_exists('h')) {
    function h(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}

// ══════════════════════════════════════════════════════
// HARDCODED MOCK DATA — replace with DB queries later
// ══════════════════════════════════════════════════════
$profile = [
    'id'         => 1,
    'name'       => 'Jonathan',
    'age'        => 24,
    'location'   => 'Singapore',
    'occupation' => 'Software Developer',
    'bio'        => 'Coffee-fuelled code monkey. I debug by day and sketch by night. Looking for someone who appreciates terrible puns, good playlists, and spontaneous midnight snack runs.',
    'interests'  => ['Coffee', 'Art', 'Hiking', 'Tech', 'Photography'],
    'religion'   => 'Agnostic',
    'horoscope'  => '♏ Scorpio',
    // Up to 6 images — set to null if not available
    'images'     => [
        null, // image 1 — hero (null = show gradient placeholder)
        null, // image 2
        null, // image 3
        null, // image 4
        null, // image 5
        null, // image 6
    ],
    // Answered prompts — remove any entry to hide it, max 6
    // These will eventually come from a `user_answers` table
    'prompts'    => [
        [
            'q' => 'My perfect Sunday looks like...',
            'a' => 'Waking up at 10am, grabbing a flat white, then wandering around a hawker centre before ending up at a bookshop I\'ve never been to.',
        ],
        [
            'q' => 'The way to my heart is...',
            'a' => 'Recommending me a song I haven\'t heard yet. Bonus points if it becomes my new favourite.',
        ],
        [
            'q' => 'I\'m weirdly passionate about...',
            'a' => 'Mechanical keyboards. I have too many. My colleagues hate me.',
        ],
        [
            'q' => 'A life goal of mine is...',
            'a' => 'To build something people actually use every day — could be an app, could be a really good sandwich shop.',
        ],
        [
            'q' => 'My most controversial opinion is...',
            'a' => 'Pineapple on pizza is fine actually and I will die on this hill.',
        ],
    ],
];

// Build tags from interests + religion + horoscope
$tags = $profile['interests'];
if (!empty($profile['religion']))  $tags[] = $profile['religion'];
if (!empty($profile['horoscope'])) $tags[] = $profile['horoscope'];

// Filter out null images, keep index for alt text numbering
$images = array_values(array_filter($profile['images'], fn($img) => $img !== null));
$hasHeroImage = isset($profile['images'][0]) && $profile['images'][0] !== null;

// Remaining images after hero (indices 1–5)
$extraImages = array_slice($profile['images'], 1);
$extraImages = array_values(array_filter($extraImages, fn($img) => $img !== null));

$prompts = array_slice($profile['prompts'], 0, 6);

$activePage = 'discover';
$pageTitle  = h($profile['name']) . '\'s Profile';
require_once 'includes/header.php';
?>

<div class="vp-wrap">

    <!-- ── Hero Image ── -->
    <div class="vp-hero" aria-label="Profile photo of <?= h($profile['name']) ?>">

        <?php if ($hasHeroImage): ?>
            <img src="<?= h('/uploads/' . $profile['images'][0]) ?>"
                 alt="Profile photo of <?= h($profile['name']) ?>"
                 class="vp-hero-img">
        <?php else: ?>
            <!-- Gradient placeholder when no photo uploaded -->
            <div class="vp-hero-placeholder" aria-hidden="true"></div>
            <div class="vp-hero-initials" aria-hidden="true">
                <?= h(mb_substr($profile['name'], 0, 1)) ?>
            </div>
        <?php endif; ?>

        <!-- Hero bottom fade -->
        <div class="vp-hero-fade" aria-hidden="true"></div>

        <!-- Back button -->
        <a href="javascript:history.back()" class="vp-back-btn" aria-label="Go back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </a>

        <!-- Pass / Like buttons -->
        <div class="vp-hero-actions" aria-label="Profile actions">
            <button class="vp-action-btn vp-btn-pass" aria-label="Pass on <?= h($profile['name']) ?>" title="Pass">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            <button class="vp-action-btn vp-btn-like" aria-label="Like <?= h($profile['name']) ?>" title="Like">
                <svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- ── Scrollable content ── -->
    <div class="vp-content">

        <!-- Name, age, location, occupation -->
        <div class="vp-identity">
            <h1 class="vp-name">
                <?= h($profile['name']) ?><span class="vp-comma">,</span>
                <span class="vp-age"><?= h((string)$profile['age']) ?></span>
            </h1>

            <?php if (!empty($profile['location']) || !empty($profile['occupation'])): ?>
            <p class="vp-meta">
                <?php if (!empty($profile['location'])): ?>
                <span class="vp-meta-chip">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <?= h($profile['location']) ?>
                </span>
                <?php endif; ?>
                <?php if (!empty($profile['occupation'])): ?>
                <span class="vp-meta-chip">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                    <?= h($profile['occupation']) ?>
                </span>
                <?php endif; ?>
            </p>
            <?php endif; ?>

            <!-- Tags -->
            <?php if (!empty($tags)): ?>
            <div class="vp-tags" aria-label="Interests and attributes">
                <?php foreach ($tags as $tag): ?>
                    <span class="vp-tag"><?= h($tag) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Divider -->
        <div class="vp-divider" aria-hidden="true"></div>

        <!-- Bio -->
        <?php if (!empty($profile['bio'])): ?>
        <section class="vp-bio-section" aria-label="About <?= h($profile['name']) ?>">
            <p class="vp-bio"><?= nl2br(h($profile['bio'])) ?></p>
        </section>
        <div class="vp-divider" aria-hidden="true"></div>
        <?php endif; ?>

        <!-- ══════════════════════════════════════════
             INTERLEAVED IMAGES + PROMPTS
             Pattern: prompt, image, prompt, image...
             Only renders a block if the content exists.
        ══════════════════════════════════════════ -->
        <?php
        $totalSections = max(count($extraImages), count($prompts));
        for ($i = 0; $i < $totalSections; $i++):
        ?>

            <?php if (isset($prompts[$i])): ?>
            <!-- Prompt card -->
            <div class="vp-prompt-card"
                 role="button"
                 tabindex="0"
                 aria-expanded="false"
                 aria-label="Reply to: <?= h($prompts[$i]['q']) ?>"
                 data-idx="<?= $i ?>">

                <span class="vp-prompt-label" aria-hidden="true">💬</span>
                <p class="vp-prompt-q"><?= h($prompts[$i]['q']) ?></p>
                <p class="vp-prompt-a"><?= h($prompts[$i]['a']) ?></p>

                <div class="vp-tap-hint" aria-hidden="true">Tap to reply →</div>

                <!-- Reply box — hidden by default -->
                <div class="vp-reply-box" id="reply-<?= $i ?>" hidden aria-hidden="true">
                    <textarea
                        class="vp-reply-textarea"
                        rows="3"
                        maxlength="500"
                        placeholder="Say something to <?= h($profile['name']) ?>..."
                        aria-label="Your reply to: <?= h($prompts[$i]['q']) ?>"></textarea>
                    <div class="vp-reply-footer">
                        <span class="vp-chars" id="chars-<?= $i ?>">0 / 500</span>
                        <button class="vp-send-btn"
                                data-profile-id="<?= h((string)$profile['id']) ?>"
                                data-prompt-q="<?= h($prompts[$i]['q']) ?>"
                                aria-label="Send reply">
                            Send
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($extraImages[$i])): ?>
            <!-- Extra photo -->
            <div class="vp-photo-block">
                <img src="<?= h('/uploads/' . $extraImages[$i]) ?>"
                     alt="Photo of <?= h($profile['name']) ?>"
                     class="vp-photo" loading="lazy">
            </div>
            <?php endif; ?>

        <?php endfor; ?>

        <div style="height:1.5rem" aria-hidden="true"></div>
    </div><!-- /vp-content -->
</div><!-- /vp-wrap -->

<style>
/* ── Page wrapper ── */
.vp-wrap {
    max-width: 480px;
    margin: 0 auto;
    background: #fff;
    min-height: 100vh;
}

/* ── Hero ── */
.vp-hero {
    position: relative;
    height: 520px;
    overflow: hidden;
    background: #160330;
}

.vp-hero-img {
    width: 100%; height: 100%;
    object-fit: cover;
    object-position: center top;
    display: block;
}

/* Gradient placeholder */
.vp-hero-placeholder {
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, #7A1090 0%, #3B0060 50%, #160330 100%);
}

.vp-hero-initials {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6rem;
    font-weight: 800;
    color: rgba(255,255,255,0.15);
    letter-spacing: -2px;
    pointer-events: none;
}

.vp-hero-fade {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 140px;
    background: linear-gradient(to top, #fff 0%, transparent 100%);
    pointer-events: none;
}

/* Back button */
.vp-back-btn {
    position: absolute;
    top: 1rem; left: 1rem;
    width: 40px; height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
    color: var(--text-dark);
    box-shadow: 0 2px 12px rgba(0,0,0,0.18);
    z-index: 10;
    transition: transform 0.2s;
}
.vp-back-btn svg { width: 20px; height: 20px; }
.vp-back-btn:hover { transform: scale(1.08); }
.vp-back-btn:focus-visible { outline: 3px solid var(--primary-pink); outline-offset: 2px; }

/* Action buttons */
.vp-hero-actions {
    position: absolute;
    bottom: 1.5rem; right: 1.2rem;
    display: flex; gap: 0.6rem;
    z-index: 10;
}

.vp-action-btn {
    width: 52px; height: 52px;
    border-radius: 50%; border: none;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    transition: transform 0.2s, box-shadow 0.2s;
}
.vp-action-btn svg { width: 22px; height: 22px; }
.vp-action-btn:hover { transform: scale(1.1); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
.vp-action-btn:focus-visible { outline: 3px solid var(--primary-pink); outline-offset: 3px; }

/* ── Content area ── */
.vp-content {
    padding: 0.5rem 1.4rem 1.4rem;
}

/* ── Identity ── */
.vp-identity { padding-top: 0.8rem; margin-bottom: 1.2rem; }

.vp-name {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-dark);
    line-height: 1.15;
    margin-bottom: 0.6rem;
}
.vp-comma { color: var(--text-dark); }
.vp-age   { color: var(--primary-pink); }

.vp-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.9rem;
}

.vp-meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: #f7f7f7;
    border-radius: 50px;
    padding: 0.3rem 0.75rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #555;
}
.vp-meta-chip svg { flex-shrink: 0; color: #999; }

.vp-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
}

.vp-tag {
    background: #FFF0F5;
    color: var(--primary-pink);
    border: 1.5px solid rgba(255,74,122,0.18);
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.28rem 0.75rem;
    border-radius: 50px;
}

/* ── Divider ── */
.vp-divider {
    height: 1px;
    background: #f0f0f0;
    margin: 1.2rem 0;
}

/* ── Bio ── */
.vp-bio {
    font-size: 0.95rem;
    color: #444;
    line-height: 1.75;
}

/* ── Prompt card ── */
.vp-prompt-card {
    background: #FAFAFA;
    border: 1.5px solid #EFEFEF;
    border-radius: 18px;
    padding: 1.2rem 1.3rem 1rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
    position: relative;
}

.vp-prompt-card:hover,
.vp-prompt-card:focus-visible {
    border-color: rgba(255,74,122,0.3);
    box-shadow: 0 4px 20px rgba(255,74,122,0.07);
    outline: none;
}

.vp-prompt-card.is-open {
    background: #fff;
    border-color: rgba(255,74,122,0.35);
}

.vp-prompt-label {
    display: block;
    font-size: 1.1rem;
    margin-bottom: 0.4rem;
}

.vp-prompt-q {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--primary-pink);
    margin-bottom: 0.45rem;
}

.vp-prompt-a {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-dark);
    line-height: 1.55;
    margin-bottom: 0.6rem;
}

.vp-tap-hint {
    font-size: 0.75rem;
    color: #ccc;
    font-weight: 600;
    transition: color 0.2s;
}

.vp-prompt-card:hover .vp-tap-hint,
.vp-prompt-card.is-open .vp-tap-hint {
    color: var(--primary-pink);
}

/* ── Reply box ── */
.vp-reply-box {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
    animation: replyIn 0.22s ease;
}

@keyframes replyIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.vp-reply-textarea {
    width: 100%;
    border: 1.5px solid #e8e8e8;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.88rem;
    color: var(--text-dark);
    resize: none;
    background: #f9f9f9;
    line-height: 1.5;
    transition: border-color 0.2s, background 0.2s;
    display: block;
}
.vp-reply-textarea:focus {
    border-color: var(--primary-pink);
    background: #fff;
    outline: none;
}
.vp-reply-textarea::placeholder { color: #c0c0c0; }

.vp-reply-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.55rem;
}

.vp-chars {
    font-size: 0.72rem;
    color: #ccc;
    font-weight: 600;
}

.vp-send-btn {
    background: var(--primary-pink);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 0.42rem 1.2rem;
    font-size: 0.85rem;
    font-weight: 700;
    font-family: 'Plus Jakarta Sans', sans-serif;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
}
.vp-send-btn:hover { background: #e63e6d; transform: translateY(-1px); }
.vp-send-btn:focus-visible { outline: 3px solid var(--primary-pink); outline-offset: 2px; }

/* Sent confirmation */
.vp-sent-msg {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: #22c55e;
    font-size: 0.85rem;
    font-weight: 700;
    padding: 0.5rem 0 0.2rem;
}

/* ── Extra photos ── */
.vp-photo-block {
    margin-bottom: 1rem;
    border-radius: 16px;
    overflow: hidden;
}
.vp-photo {
    width: 100%;
    height: 380px;
    object-fit: cover;
    display: block;
    border-radius: 16px;
}

/* ── Responsive ── */
@media (min-width: 480px) {
    .vp-wrap { box-shadow: 0 0 60px rgba(0,0,0,0.07); }
}
@media (max-width: 360px) {
    .vp-hero { height: 420px; }
    .vp-name { font-size: 1.7rem; }
}
</style>

<script>
document.querySelectorAll('.vp-prompt-card').forEach(card => {
    const idx      = card.dataset.idx;
    const box      = document.getElementById('reply-' + idx);
    const textarea = box?.querySelector('.vp-reply-textarea');
    const charEl   = document.getElementById('chars-' + idx);
    const sendBtn  = box?.querySelector('.vp-send-btn');

    // Toggle reply box
    function toggle(e) {
        // Don't toggle when interacting inside the reply box
        if (box && box.contains(e.target)) return;

        const opening = !card.classList.contains('is-open');
        card.classList.toggle('is-open', opening);
        box.hidden = !opening;
        box.setAttribute('aria-hidden', String(!opening));
        card.setAttribute('aria-expanded', String(opening));

        if (opening) setTimeout(() => textarea?.focus(), 40);
    }

    card.addEventListener('click', toggle);
    card.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(e); }
    });

    // Char counter
    textarea?.addEventListener('input', () => {
        const n = textarea.value.length;
        if (charEl) {
            charEl.textContent = n + ' / 500';
            charEl.style.color = n > 450 ? '#ef4444' : '#ccc';
        }
    });

    // Send
    sendBtn?.addEventListener('click', e => {
        e.stopPropagation();
        const msg = textarea?.value.trim();

        if (!msg) {
            textarea?.focus();
            textarea.style.borderColor = '#ef4444';
            setTimeout(() => textarea.style.borderColor = '', 1200);
            return;
        }

        // TODO: wire up to /api/send_reply.php when backend is ready
        console.log('Reply to prompt:', sendBtn.dataset.promptQ);
        console.log('Message:', msg);

        // Show confirmation
        box.innerHTML = `
            <div class="vp-sent-msg" role="status" aria-live="polite">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Sent! We'll let <?= h($profile['name']) ?> know.
            </div>`;

        card.style.cursor = 'default';
        card.classList.add('is-open');
        card.removeEventListener('click', toggle);
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>