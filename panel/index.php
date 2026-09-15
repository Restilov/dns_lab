<?php
session_start();

// Flag'ler burada DUZ METIN degil, SHA-256 hash olarak durur (panel kurcalansa bile sizmaz).
$FLAGS = [
  ['title' => 'HTTP Başlık Analizi',  'phase' => 'Pasif',  'hash' => '1e2e88ae76c8fafe096e916b13a8729c6fe4d84f84d6057556807e5a620800ab'],
  ['title' => 'robots.txt',           'phase' => 'Pasif',  'hash' => 'f39df07f24e19a44fa940694dd670d65a6bf44ece30e70283f8a6fd569fddd3a'],
  ['title' => 'Linklenmemiş Sayfa',   'phase' => 'Aktif',  'hash' => 'f15f773b6aa0530efbf169bc51568a7beb5c44f0b8f714c67b155dcd2190c2b8'],
  ['title' => 'Dizin Brute-force',    'phase' => 'Aktif',  'hash' => '7d48b44f2b23294b8b261efbff833bd59b375b249df9614a68b6c30440d86d28'],
  ['title' => 'Yedek/Config Dosyası', 'phase' => 'Final',  'hash' => 'aa0112598d7e18931711f57dde9eb31c556c15670bd71fac420731c69f625a1c'],
];
$TOTAL = count($FLAGS);

if (!isset($_SESSION['solved'])) { $_SESSION['solved'] = 0; }

$error = '';
$just_solved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['reset'])) {
    $_SESSION['solved'] = 0;
    header('Location: /'); exit;
  }
  $idx = (int)($_POST['idx'] ?? -1);
  $submitted = trim($_POST['flag'] ?? '');
  // Sirali kilit: sadece SIRADAKI flag denenebilir
  if ($idx !== $_SESSION['solved']) {
    $error = 'Önce sıradaki flag\'i gir.';
  } elseif ($submitted === '') {
    $error = 'Boş flag gönderilemez.';
  } elseif (hash('sha256', $submitted) === $FLAGS[$idx]['hash']) {
    $_SESSION['solved']++;
    $just_solved = true;
  } else {
    $error = 'Yanlış flag. Tekrar dene.';
  }
}

$solved = $_SESSION['solved'];
$done = ($solved >= $TOTAL);
$pct = (int)round($solved / $TOTAL * 100);
?><!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>feysbook :: capture the flags</title>
<link rel="stylesheet" href="/panel.css">
</head>
<body>
<div class="scan"></div>
<main class="wrap">
  <header class="hd">
    <div class="logo">feysbook<span>_ctf</span></div>
    <div class="sub">web enumeration lab &mdash; hedef: <code>http://localhost:8080</code></div>
    <div class="tools">
      <a class="dl" href="/common.txt" download="common.txt">⬇ gobuster wordlist'ini indir (common.txt)</a>
    </div>
  </header>

  <section class="prog">
    <div class="prog-top">
      <span>İLERLEME</span>
      <span class="count"><?= $solved ?> / <?= $TOTAL ?> flag</span>
    </div>
    <div class="bar"><div class="fill" style="width:<?= $pct ?>%"></div></div>
  </section>

  <?php if ($error): ?>
    <div class="msg err">[!] <?= htmlspecialchars($error) ?></div>
  <?php elseif ($just_solved && !$done): ?>
    <div class="msg ok">[+] Flag kabul edildi. Sıradaki adım açıldı.</div>
  <?php endif; ?>

  <?php if ($done): ?>
    <div class="pwned">
      <div class="big">PWNED</div>
      <p>5/5 flag yakalandı. Feysbook (not fake) tamamen ele geçirildi.</p>
      <p class="tip">Pasif kaynaklar sadece yayınlanmış olanı gösterir; yanlış yapılandırma ise
         her şeyi verir. Profesyonel keşif birden fazla yöntemi birleştirir.</p>
      <form method="post"><button class="reset" name="reset" value="1">↺ Baştan başla</button></form>
    </div>
  <?php endif; ?>

  <section class="flags">
    <?php foreach ($FLAGS as $i => $f):
      $state = $i < $solved ? 'done' : ($i === $solved ? 'active' : 'locked'); ?>
      <div class="flag <?= $state ?>">
        <div class="flag-hd">
          <span class="num">FLAG <?= $i + 1 ?></span>
          <span class="phase phase-<?= strtolower($f['phase']) ?>"><?= $f['phase'] ?></span>
          <span class="title"><?= htmlspecialchars($f['title']) ?></span>
          <span class="icon">
            <?php if ($state === 'done') echo '✓'; elseif ($state === 'locked') echo '🔒'; else echo '›'; ?>
          </span>
        </div>
        <?php if ($state === 'active'): ?>
          <form method="post" class="flag-form" autocomplete="off">
            <input type="hidden" name="idx" value="<?= $i ?>">
            <input type="text" name="flag" placeholder="FLAG{...}" autofocus>
            <button type="submit">Gönder</button>
          </form>
        <?php elseif ($state === 'done'): ?>
          <div class="captured">✓ yakalandı</div>
        <?php else: ?>
          <div class="hint-locked">Önceki flag'i bul, sonra açılır.</div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </section>

  <?php if (!$done): ?>
  <footer class="ft">
    <form method="post"><button class="reset small" name="reset" value="1">↺ ilerlemeyi sıfırla</button></form>
    <span>Flag formatı: <code>FLAG{...}</code> &middot; ipuçlarını hedef sitede ara</span>
  </footer>
  <?php endif; ?>
</main>
</body>
</html>
