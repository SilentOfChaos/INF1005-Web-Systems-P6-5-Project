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
// ── Mock profile data (replace with DB later) ──
$profile = [
    'id'         => 'mock_1',
    'name'       => 'Jonathan',
    'age'        => 24,
    'location'   => 'Singapore',
    'occupation' => 'Developer',
    'bio'        => 'Coffee-fuelled code monkey. I debug by day and sketch by night. Looking for someone who appreciates terrible puns and good playlists.',
    'interests'  => 'Coffee,Art,Hiking,Tech',
    'profile_pic'=> null,
];


$activePage = 'discover';
$pageTitle  = h($profile['name']) . '\'s Profile';
require_once 'includes/header.php';
?>

    <main class="container py-5" style="max-width:1100px;">
        <h1 class="mb-4">Edit Your Profile</h1>

        <div class="row g-5">

            <!-- ═══════ FORM ═══════ -->
            <div class="col-lg-6">
                <form method="POST">

                    <!-- Core Information Fields -->
                    <div class="card border-0" style="border-radius: 20px;">

                        <div class="card-body">
                            <h2 class="text-start mb-4 fw-bold">Core Information</h2>

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="name"
                                    value="<?= h($profile['name']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Age</label>
                                <input type="number" class="form-control" id="age"
                                    value="<?= h((string)$profile['age']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" id="location"
                                    value="<?= h($profile['location']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" class="form-control" id="occupation"
                                    value="<?= h($profile['occupation']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Core Information Fields -->
                    <div class="card border-0" style="border-radius: 20px;">

                        <div class="card-body">
                            <h2 class="text-start mb-4 fw-bold">About Me</h2>

                             <div class="mb-3">
                                <label class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" rows="4"><?= h($profile['bio']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Interests</label>
                                <div id="interests-container" class="interests-input-container">
                                    <!-- Existing interests as spans -->
                                     <?php
                                        $tags = explode(',', $profile['interests']);
                                        foreach ($tags as $tag):
                                            $trimmed = trim($tag);
                                            if (!$trimmed) continue;
                                        ?>
                                        <span class="tag-span"><?= h($trimmed) ?> <span class="remove-tag" aria-label="Remove tag">×</span></span>
                                    <?php endforeach; ?>
                                </div>
                                <input type="text" id="interests-input" class="form-control mt-2" placeholder="Type an interest and press Enter">
                            </div>
                        
                        </div>
                    </div>
                    
                    <button type="submit" class="btn px-4">
                        Save Changes
                    </button>

                </form>
            </div>

            <!-- ═══════ LIVE PREVIEW ═══════ -->
            <div class="card-profile-preview col-lg-6">
                <div class="profile-card card-front">
                    <div class="card-info">
                        <h3><?= h($profile['name']) ?>, <?= h((string)$profile['age']) ?></h3>
                        <p><?= h($profile['location']) ?> &bull; <?= h($profile['occupation']) ?> </p>
                        <div class="tags">
                            <span class="tag">Coffee</span>
                            <span class="tag">Art</span>
                        </div>
                        <p><?= h($profile['bio']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const interestsContainer = document.getElementById('interests-container');
        const interestsInput = document.getElementById('interests-input');

        function updatePreviewTags() {
            const tagTexts = Array.from(interestsContainer.querySelectorAll('.tag-span'))
                                .map(span => span.firstChild.textContent.trim());
            const previewTags = document.getElementById('preview-tags');
            previewTags.innerHTML = '';
            tagTexts.slice(0, 4).forEach(tag => {
                const span = document.createElement('span');
                span.className = 'badge bg-light text-dark me-1 mb-1';
                span.textContent = tag;
                previewTags.appendChild(span);
            });
        }

        // Add tag on Enter
        interestsInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = interestsInput.value.trim();
                if (!val) return;

                const span = document.createElement('span');
                span.className = 'tag-span';
                span.innerHTML = `${val} <span class="remove-tag" aria-label="Remove tag">×</span>`;
                interestsContainer.appendChild(span);

                interestsInput.value = '';
                updatePreviewTags();
            }
        });

        // Remove tag
        interestsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-tag')) {
                e.target.parentElement.remove();
                updatePreviewTags();
            }
        });

        // Initialize preview with existing tags
        updatePreviewTags();
    </script>

<?php require_once 'includes/footer.php'; ?>