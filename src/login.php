<?php $page_title = 'Log In'; include '_header.php'; ?>
<div class="container">
  <div class="card" style="max-width:460px;margin:40px auto">
    <h1>Log In</h1>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
      <p style="color:#c0392b">The email or password you entered is incorrect. Please try again.</p>
    <?php endif; ?>
    <p>Account creation is disabled on this demo instance.</p>
    <p><a href="/">Back to home</a></p>
  </div>
</div>
<?php include '_footer.php'; ?>
