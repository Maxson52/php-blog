<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
  header("Location: ./auth/log-in.php");
}

// Get all articles
require_once('./lib/utils/conn.php');

$query = "SELECT posts.id, posts.title, posts.created_at, users.name AS author FROM posts JOIN users ON posts.author_id = users.id WHERE posts.visible = 1 ORDER BY posts.created_at DESC";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

?>

<html>

<head>
  <title>Fry Me to the Moon</title>
  <link rel="icon" href="./lib/assets/strawberry.png" />
  <link href="./lib/css/output.css" rel="stylesheet" />
</head>


<body>
  <!-- NAV START -->
  <nav class="container flex justify-between px-8 py-8 mx-auto bg-white">
    <div>
      <h3 class="text-purple-600 h3">Fry Me to the Moon</h3>
    </div>
    <div class="flex space-x-8">
      <?php if ($_SESSION['user']['role'] === 'admin') echo
      "<a href='./admin'>Admin Panel</a>" ?>
      <a href="./auth/log-out.php">Log out</a>
    </div>
  </nav>
  <!-- NAV END -->

  <section class="grid w-full place-items-center">
    <div class="flex flex-col w-full gap-2 p-8">

      <!-- TOP BAR START -->
      <div class="flex justify-between">
        <h1 class="h1">Articles</h1>
        <a href="./post/create" class="mb-2 w-fit button">Write your own article</a>
      </div>
      <!-- TOP BAR END -->

      <!-- LIST ARTICLES START -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <?php
        while ($row = mysqli_fetch_array($res)) {
          $date = date('M d, Y', strtotime($row['created_at']));
          echo "
          <a href='./post?id=" . $row['id'] . "' class='flex flex-col gap-2 p-4 transition border rounded-lg shadow-md hover:shadow-lg'>
            <h2 class='link'>" . $row['title'] . "</h2>
            <p class='text-gray-400'>By " . $row['author'] . " on " . $date . "</p>
          </a>
          ";
        }
        ?>
      </div>
      <!-- LIST ARTICLES END -->

    </div>
  </section>

</body>

</html>