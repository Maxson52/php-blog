<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
  header("Location: ./auth/log-in.php");
}

?>

<html>

<head>
  <link href="./output.css" rel="stylesheet" />
</head>

<body>
  <p class="text-blue-500">HI</p>
</body>

</html>