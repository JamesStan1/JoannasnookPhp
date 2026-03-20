x<?php
/**
 * One-time gallery migration script.
 * Moves photos from public_html/Gallery/ → public_html/api/uploads/gallery/
 *
 * USAGE:
 *   1. Upload this file to public_html/api/migrate-gallery.php
 *   2. Open https://www.joannasnook.online/api/migrate-gallery.php in your browser
 *   3. DELETE this file from the server immediately after running it.
 */

// ── Resolve paths ──────────────────────────────────────────────────────────
// APP_ROOT = public_html/api  (where this script lives on Hostinger)
$appRoot = __DIR__;

// Source: public_html/Gallery/
$src = dirname($appRoot) . '/Gallery/';

// Destination: public_html/api/uploads/gallery/
$dst = $appRoot . '/uploads/gallery/';

// ── Safety check ───────────────────────────────────────────────────────────
if (!is_dir($src)) {
    echo "<p style='color:orange'>Source folder <code>$src</code> does not exist — nothing to migrate.</p>";
    exit;
}

if (!is_dir($dst)) {
    if (!mkdir($dst, 0755, true)) {
        echo "<p style='color:red'>Failed to create destination folder <code>$dst</code>.</p>";
        exit;
    }
    echo "<p>Created destination folder: <code>$dst</code></p>";
}

// ── Move files ─────────────────────────────────────────────────────────────
$moved  = 0;
$skipped = 0;
$failed = 0;

foreach (glob($src . '*') as $file) {
    if (!is_file($file)) continue;
    $name    = basename($file);
    $target  = $dst . $name;

    if (file_exists($target)) {
        echo "<p style='color:gray'>SKIP (already exists): <code>$name</code></p>";
        $skipped++;
        continue;
    }

    if (rename($file, $target)) {
        echo "<p style='color:green'>MOVED: <code>$name</code></p>";
        $moved++;
    } else {
        // rename() may fail across filesystems — fall back to copy + unlink
        if (copy($file, $target)) {
            @unlink($file);
            echo "<p style='color:green'>COPIED: <code>$name</code></p>";
            $moved++;
        } else {
            echo "<p style='color:red'>FAILED: <code>$name</code></p>";
            $failed++;
        }
    }
}

echo "<hr>";
echo "<strong>Done.</strong> Moved: $moved &nbsp;|&nbsp; Skipped: $skipped &nbsp;|&nbsp; Failed: $failed";
echo "<p style='color:red'><strong>⚠ Delete this file from the server now!</strong></p>";
