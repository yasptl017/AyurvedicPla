<?php

/**
 * Laravel front controller for root document roots.
 *
 * When the web server points to the repository root instead of `public`,
 * this file forwards the request to the actual Laravel public entry point.
 */

require __DIR__ . '/public/index.php';
