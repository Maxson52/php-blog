<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
  header("Location: ./auth/log-in.php");
}

?>

<html>

<head>
  <title>Fry Me to the Moon</title>
  <link rel="icon" href="./lib/assets/strawberry.png" />
  <link href="./output.css" rel="stylesheet" />
</head>


<body>
  <!-- NAV START -->
  <nav class="container flex justify-between px-8 py-8 mx-auto bg-white">
    <div>
      <h3 class="text-purple-600 h3">Fry Me to the Moon</h3>
    </div>
    <div class="flex space-x-8">
      <?php if ($_SESSION['user']['role'] === 'admin') echo
      "<a href='./admin-dashboard'>Admin Dashboard</a>" ?>
      <a href="./auth/log-out.php">Log out</a>
    </div>
  </nav>
  <!-- NAV END -->

  <!-- ARTICLES START -->



  <!-- ARTICLES END -->
</body>

</html>