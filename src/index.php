<?php $page_title = 'Feysbook'; ?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Feysbook — log in or sign up</title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<main class="landing">
  <section class="pitch">
    <div class="wordmark">feysbook <span style="font-size:1.1rem;font-weight:600;color:#42b72a;letter-spacing:0;vertical-align:middle">(not fake)</span></div>
    <p>Feysbook helps you connect and share with the people in your life.</p>
  </section>
  <section class="loginbox">
    <form action="/login.php" method="post">
      <input type="text" name="email" placeholder="Email address or phone number" autocomplete="off">
      <input type="password" name="pass" placeholder="Password" autocomplete="off">
      <button class="btn btn-blue" type="submit">Log In</button>
    </form>
    <a class="forgot" href="/login.php">Forgotten password?</a>
    <hr class="divider">
    <div class="create-wrap">
      <button class="btn btn-green" type="button">Create new account</button>
    </div>
  </section>
</main>
<footer class="ft">
  <div class="in">
    <div>Feysbook &copy; <?= date('Y') ?> &middot;
      <a href="/about.php">About</a> &middot;
      <a href="/login.php">Log In</a> &middot;
      <a href="/robots.txt">Terms</a></div>
    <div class="ethics">
      Bu sistem eğitim amaçlı kasıtlı olarak zafiyetli hazırlanmıştır. Öğrendiğiniz teknikleri
      yalnızca izniniz olan sistemlerde uygulayın.
    </div>
  </div>
</footer>
</body>
</html>
