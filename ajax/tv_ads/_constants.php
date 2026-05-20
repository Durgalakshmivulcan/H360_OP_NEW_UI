<?php
// FIX_B_2340: shared validation constants for the TV-ad uploader.
// Tuned for a 60-inch waiting-room TV (typically 4K, sometimes 1080p).
// Centralised here so the page-side JS and the server-side handlers stay in lockstep.

// Hard rejects (server-side enforced; client mirrors these exactly)
const TVAD_MIN_WIDTH   = 1920;        // 1080p baseline — anything narrower looks soft on a 60" panel
const TVAD_MIN_HEIGHT  = 600;         // matches the bottom-strip height at 1080p
const TVAD_MAX_WIDTH   = 4096;        // 4K-ready ceiling (DCI-4K width)
const TVAD_MAX_HEIGHT  = 2160;        // 4K vertical ceiling
const TVAD_MIN_BYTES   = 50 * 1024;        // 50 KB — below this, JPEG artifacts visible at TV scale
const TVAD_MAX_BYTES   = 5  * 1024 * 1024; // 5 MB cap to keep crossfades smooth + storage modest

// Aspect-ratio band: target is 16:5 (3.20). Allow ±15% so 3:1 and 16:4.5 also pass.
const TVAD_ASPECT_MIN  = 2.72;        // ~3:1.10 — loose lower bound
const TVAD_ASPECT_MAX  = 3.68;        // ~16:4.35 — loose upper bound
const TVAD_ASPECT_HINT = 3.20;        // ideal (16:5)

const TVAD_ALLOWED_MIME = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
const TVAD_ALLOWED_EXT  = ['jpg', 'jpeg', 'png', 'webp'];

// Folder lives at webroot/tv_ads/
function tvad_dir() { return realpath(__DIR__ . '/../../tv_ads'); }
function tvad_url($file) { return 'tv_ads/' . rawurlencode($file); }

// Skip files that start with '_' (admin staging convention).
function tvad_is_visible($name) {
    return $name !== '' && $name[0] !== '.' && $name[0] !== '_'
        && preg_match('/\.(jpe?g|png|webp)$/i', $name);
}
