<?php
// ── footer.inc.php ──
// Include at the bottom of every page, just before closing </body>
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= isset($extraScripts) ? $extraScripts : '' ?>
</body>
</html>