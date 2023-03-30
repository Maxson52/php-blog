<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
  header("Location: ./auth/log-in.php");
}

// Get all articles
require_once('./lib/utils/conn.php');

$category = $_GET['category'] ?? -1;
$sort_order = $_GET['sort_order'] ?? 'DESC';

if (!is_numeric($category)) header("Location: ./");

$query = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.author_id, users.name AS author, categories.name AS category, categories.visible AS cat_visible FROM posts 
            JOIN users ON posts.author_id = users.id 
            JOIN categories ON posts.category_id = categories.id
            WHERE posts.visible = 1 
            AND (posts.category_id = $category OR $category = -1)
            ORDER BY posts.created_at $sort_order";

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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Fry Me to the Moon</title>
  <link rel="icon" href="./lib/assets/strawberry.png" />
  <link href="./lib/css/output.css" rel="stylesheet" />
</head>


<body>
  <!-- NAV START -->
  <script>
    // Change navbar bg on scroll
    window.onscroll = function() {
      scrollFunction()
    };

    function scrollFunction() {
      if (document.body.scrollTop > 400 || document.documentElement.scrollTop > 400) {
        document.querySelector("nav").classList.add("bg-white");
        document.querySelector("nav").classList.remove("bg-transparent");

        document.querySelector("nav").classList.add("text-black");
        document.querySelector("nav").classList.remove("text-white");
      } else {
        document.querySelector("nav").classList.remove("bg-white");
        document.querySelector("nav").classList.add("bg-transparent");

        document.querySelector("nav").classList.add("text-white");
        document.querySelector("nav").classList.remove("text-black");
      }
    }

    // Move background on hero based on mouse position
    document.addEventListener('mousemove', function(e) {
      const hero = document.querySelector('#hero-bg');
      const x = e.clientX / window.innerWidth;
      const y = e.clientY / window.innerHeight;
      hero.style.backgroundPosition = `${x * 6}% ${y * 6}%`;
    });
  </script>

  <nav class="fixed z-40 flex items-center justify-between w-full px-8 py-4 mx-auto text-white transition-all bg-transparent">
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

  <!-- HERO START -->
  <section id="hero" class="grid place-items-center w-full bg-gradient-to-r from-indigo-300 to-purple-400 h-[50rem]">
    <div class="z-20 flex flex-col justify-center w-3/4 gap-4">
      <h1 class="font-bold text-white text-7xl">Fry Me to the Moon</h1>
      <p class="mt-4 text-lg text-white">The place for all your cooking needs</p>
      <a href="./post/create" class="px-6 py-1 mt-6 font-bold bg-black border-black rounded-full button w-fit">Start writing →</a>
    </div>
    <div id="hero-bg"></div>
  </section>

  <style>
    #hero-bg {
      background-image: radial-gradient(rgba(255, 255, 255, 0.1) 8%,
          transparent 8%);
      background-position: 0% 0%;
      background-size: 9.5vmin 9.5vmin;
      height: 50rem;
      width: 100%;
      left: 0px;
      position: absolute;
      top: 0px;
      transition: opacity 800ms ease;
      z-index: 1;
    }
  </style>
  <!-- HERO END -->

  <section class="grid w-full place-items-center">
    <div class="flex flex-col-reverse w-full max-w-6xl grid-cols-none gap-2 p-8 lg:grid lg:grid-cols-3">

      <!-- LIST ARTICLES START -->
      <div class="grid w-full gap-4 lg:col-span-2">
        <?php foreach ($res as $article) : ?>
          <a href='./post?id=<?= $article['id'] ?>' class='flex flex-col gap-2 p-4 transition border-b'>
            <div class='flex gap-2'>
              <img src='https://source.boringavatars.com/beam?name=<?= $article['author_id'] ?>' class='w-6 h-6 rounded-full' />
              <p> <?= $article['author'] ?> <span class='text-gray-400'> · <?= date('M d, Y', strtotime($article['created_at'])) ?></span></p>
            </div>
            <h2 class='h2'><?= $article['title'] ?></h2>
            <p class='pb-4'><?= strip_tags(substr($article['content'], 0, 100)) ?>...</p>
            <div class='flex items-center gap-2 text-sm'>
              <?php if ($article['cat_visible']) echo "<p class='px-3 py-1 bg-gray-100 rounded-full w-min'>" . $article['category'] . "</p>" ?>
              <p class='text-gray-400'><?= estimateReadingTime($article['content']) ?> min read</p>
            </div>
          </a>
        <?php endforeach; ?>
        <?php
        if (mysqli_num_rows($res) === 0) {
          echo "<p class='text-gray-400'>No articles found.</p>";
        }
        ?>
      </div>
      <!-- LIST ARTICLES END -->

      <!-- FILTERS START -->
      <div class="lg:col-start-3">
        <script>
          function update(a, b) {
            var searchParams = new URLSearchParams(window.location.search);
            if (b != '')
              searchParams.set(a, b);
            else
              searchParams.delete(a);
            window.location.search = searchParams.toString();
          }
        </script>
        <!-- FILTERS END -->
        <!-- LIST CATEGORIES START -->
        <div class="flex flex-wrap gap-2 mb-4">
          <a onclick="update('category', '');" href="javascript:void(0)" class="px-3 py-1 bg-gray-100 rounded-full w-fit">
            All
          </a>
          <?php foreach ($cats as $cat) : ?>
            <a onclick="update('category', '<?= $cat['id'] ?>');" href="javascript:void(0)" class="px-3 py-1 bg-gray-100 rounded-full w-fit">
              <?= $cat['name'] ?>
            </a>
          <?php endforeach; ?>
        </div>
        <!-- LIST CATEGORIES END -->
        <!-- LIST SORT ORDER START -->
        <div class="flex flex-wrap gap-2 mb-4">
          <a onclick="update('sort_order', '');" href="javascript:void(0)" class="px-3 py-1 bg-gray-100 rounded-full w-fit">
            Newest First
          </a>
          <a onclick="update('sort_order', 'ASC');" href="javascript:void(0)" class="px-3 py-1 bg-gray-100 rounded-full w-fit">
            Oldest First
          </a>
        </div>
        <!-- LIST SORT ORDER END -->
      </div>
      <!-- FILTERS END -->
    </div>
  </section>

</body>

</html>