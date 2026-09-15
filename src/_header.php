<?php if (!isset($page_title)) { $page_title = 'Feysbook'; } ?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($page_title) ?> — Feysbook</title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="bar">
  <div class="in">
    <a class="wordmark" href="/">feysbook <span style="font-size:.75rem;font-weight:600;color:#dbe7ff;letter-spacing:0">(not fake)</span></a>
    <nav>
      <a href="/">Home</a>
      <a href="/about.php">About</a>
      <a href="/login.php">Log In</a>
    </nav>
  </div>
</header>
