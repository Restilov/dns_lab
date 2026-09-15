<?php
header('Content-Type: application/json');
http_response_code(200);
echo json_encode([
  'service' => 'feysbook-graph-api',
  'version' => '2.1.0',
  'status'  => 'ok',
  'flag'    => 'FLAG{d1r_brut3f0rc3_w1ns}',
  'message' => 'Config could not be loaded from config.php. '
             . 'Reminder: remove leftover *.bak backup files before launch.',
  'endpoints' => ['/api/me', '/api/feed', '/api/config.php']
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
