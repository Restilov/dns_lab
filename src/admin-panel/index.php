<?php $page_title = 'Admin'; include '../_header.php'; ?>
<div class="container">
  <div class="card">
    <h1>Feysbook Moderation Dashboard</h1>
    <p><span class="tag">INTERNAL</span> Staff only. Do not share this URL.</p>
    <table class="tbl">
      <tr><th>Module</th><th>Status</th></tr>
      <tr><td>Reported posts queue</td><td>online</td></tr>
      <tr><td>Account suspensions</td><td>online</td></tr>
      <tr><td>User data export</td><td>maintenance</td></tr>
    </table>
    <p style="margin-top:20px">Build token: <code>FLAG{unl1nk3d_1s_n0t_h1dd3n}</code></p>
    <!--
      DevOps memo: the new backend services are not linked in the app yet.
      They are deployed under standard directory names (e.g. the REST service).
      Use a directory scan if you are looking for them.
    -->
  </div>
</div>
<?php include '../_footer.php'; ?>
