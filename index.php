<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
  header("Location: ./auth/log-in.php");
}

// Get all articles
require_once('./lib/utils/conn.php');

if (isset($_GET['category'])) {
  $category = $_GET['category'];

  if (!is_numeric($category)) header("Location: ./");

  $query = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.name AS author, categories.name AS category, categories.visible FROM posts 
            JOIN users ON posts.author_id = users.id 
            JOIN categories ON posts.category_id = categories.id
            WHERE posts.visible = 1 
            AND posts.category_id = $category
            AND categories.visible = 1
            ORDER BY posts.created_at DESC";
} else {
  $query = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.name AS author, categories.name AS category, categories.visible FROM posts 
            JOIN users ON posts.author_id = users.id 
            JOIN categories ON posts.category_id = categories.id
            WHERE posts.visible = 1 
            ORDER BY posts.created_at DESC";
}
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

// Get all categories
$catsQuery = "SELECT * FROM categories WHERE visible = 1";
$cats = mysqli_query($conn, $catsQuery) or die("Query failed: " . mysqli_error($conn));

function estimateReadingTime($text, $wpm = 200)
{
  $totalWords = str_word_count(strip_tags($text));
  $minutes = round($totalWords / $wpm);

  $minutes = $minutes < 1 ? 1 : $minutes;

  return $minutes;
}
?>

<html>

<head>
  <title>Fry Me to the Moon</title>
  <link rel="icon" href="./lib/assets/strawberry.png" />
  <link href="./lib/css/output.css" rel="stylesheet" />
</head>


<body>
  <!-- NAV START -->
  <nav class="container flex items-center justify-between px-8 py-4 mx-auto bg-white">
    <div>
      <img src="./lib/assets/strawberry.png" alt="egg" class="w-12 rounded-full aspect-auto">
    </div>
    <div class="flex space-x-8">
      <a href="./post/create">Write</a>
      <a href='./user'>Account</a>

      <?php if ($_SESSION['user']['role'] === 'admin') echo
      "<a href='./admin'>Admin Panel</a>" ?>
      <a href="./auth/log-out.php">Log out</a>
    </div>
  </nav>
  <!-- NAV END -->

  <section class="grid w-full place-items-center">
    <div class="flex flex-col w-full max-w-6xl gap-2 p-8">

      <!-- LIST CATEGORIES START -->
      <div class="flex flex-wrap gap-2 mb-4">
        <a href="./" class="px-3 py-1 bg-gray-100 rounded-full w-min">
          All
        </a>
        <?php foreach ($cats as $cat) : ?>
          <a href="./?category=<?= $cat['id'] ?>" class="px-3 py-1 bg-gray-100 rounded-full w-min">
            <?= $cat['name'] ?>
          </a>
        <?php endforeach; ?>
      </div>
      <!-- LIST CATEGORIES END -->

      <!-- LIST ARTICLES START -->
      <div class="grid w-full gap-4">
        <?php
        while ($row = mysqli_fetch_assoc($res)) {
          $date = date('M d, Y', strtotime($row['created_at']));

          echo "
          <a href='./post?id=" . $row['id'] . "' class='flex flex-col gap-2 p-4 transition border-b'>
            <div class='flex gap-2'>
              <img src='https://source.boringavatars.com/beam?name=$row[id]' class='w-6 h-6 rounded-full' />
              <p>" . $row['author'] . "<span class='text-gray-400'> · " . $date . "</span></p>
            </div>
            <h2 class='h2'>" . $row['title'] . "</h2>
            <p class='pb-4'>" . strip_tags(substr($row['content'], 0, 100)) . "...</p>
            <div class='flex items-center gap-2 text-sm'>
              <p class='px-3 py-1 bg-gray-100 rounded-full w-min'>" . ($row['visible'] ?  $row['category']  : "") . "</p>
              <p class='text-gray-400'>" . estimateReadingTime($row['content']) . " min read</p>
            </div>
          </a>
          ";
        }

        if (mysqli_num_rows($res) === 0) {
          echo "<p class='text-gray-400'>No articles found.</p>";
        }
        ?>
      </div>
      <!-- LIST ARTICLES END -->

    </div>
  </section>

</body>

</html>