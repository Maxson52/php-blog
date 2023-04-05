<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
  header("Location: ./auth/log-in.php");
}

// Get all articles
require_once('./lib/utils/conn.php');

$category = $_GET['category'] ?? -1;
$sortOrder = isset($_GET['asc']) ? 'ASC' : 'DESC';

if (!is_numeric($category)) header("Location: ./");
if (isset($_GET['search']) && $_GET['search'] === '') header("Location: ./");

$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$query = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.author_id, users.name AS author, categories.name AS category, categories.visible AS cat_visible 
            FROM posts 
            JOIN users ON posts.author_id = users.id 
            JOIN categories ON posts.category_id = categories.id
            WHERE posts.visible = 1 
            AND posts.title NOT LIKE '[deleted]'
            AND (posts.category_id = $category OR $category = -1)
            AND (posts.title LIKE '%$search%' OR posts.content LIKE '%$search%' OR users.name LIKE '%$search%')
            ORDER BY posts.created_at $sortOrder";


$dbRes = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));
$res = [];

// Highlight search terms
while ($row = mysqli_fetch_array($dbRes)) {
  if (isset($_GET['search'])) {
    $row['content'] = preg_replace('/(' . $search . ')/i', '<mark>$1</mark>', $row['content']);
    $row['title'] = preg_replace('/(' . $search . ')/i', '<mark>$1</mark>', $row['title']);
    $row['author'] = preg_replace('/(' . $search . ')/i', '<mark>$1</mark>', $row['author']);
  }
  $res[] = $row;
}

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
      <h1 class="font-serif font-bold text-white text-7xl">Fry Me to the Moon</h1>
      <p class="mt-4 text-xl text-white md:w-1/2">Don't settle for ordinary meals, discover extraordinary dishes, then add your own cosmic flair.</p>
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
    <div class="flex flex-col-reverse w-full max-w-6xl grid-cols-none gap-8 p-8 lg:grid lg:grid-cols-3">

      <!-- LIST ARTICLES START -->
      <div class="grid w-full gap-4 lg:col-span-2">
        <?php foreach ($res as $article) : ?>
          <a href='./post?id=<?= $article['id'] ?>' class='flex flex-col gap-2 p-4 transition border-b'>
            <div class='flex gap-2'>
              <img src='https://source.boringavatars.com/beam?name=<?= $article['author_id'] ?>' class='w-6 h-6 rounded-full' />
              <p> <?= $article['author'] ?> <span class='text-gray-400'> · <?= date('M d, Y', strtotime($article['created_at'])) ?></span></p>
            </div>
            <h2 class='h2'><?= $article['title'] ?></h2>
            <p class='pb-4 font-serif'><?= (substr(strip_tags($article['content'], ['mark']), 0, 225)) ?>...</p>
            <div class='flex items-center gap-2 text-sm'>
              <?php if ($article['cat_visible']) echo "<p class='px-3 py-1 bg-gray-100 rounded-full w-min'>" . $article['category'] . "</p>" ?>
              <p class='text-gray-400'><?= estimateReadingTime($article['content']) ?> min read</p>
            </div>
          </a>
        <?php endforeach; ?>
        <?php
        if (count($res) === 0) {
          echo "<p class='text-gray-400'>No articles found.</p>";
        }
        ?>
      </div>
      <!-- LIST ARTICLES END -->

      <!-- FILTERS START -->
      <div class="lg:col-start-3">
        <script>
          function update(a, b) {
            let searchParams = new URLSearchParams(window.location.search);
            if (b != '')
              searchParams.set(a, b);
            else
              searchParams.delete(a);
            window.location.search = searchParams.toString();
          }
        </script>
        <!-- FILTERS END -->
        <!-- LIST CATEGORIES START -->
        <div class="flex flex-wrap gap-2">
          <a onclick="update('category', '');" href="javascript:void(0)" class="px-3 py-1 rounded-full w-fit <?= (isset($_GET['category']) ? "bg-gray-100" : "bg-neutral-200") ?>">
            All
          </a>
          <?php foreach ($cats as $cat) : ?>
            <a onclick="update('category', '<?= $cat['id'] ?>');" href="javascript:void(0)" class="px-3 py-1 rounded-full w-fit <?= (isset($_GET['category']) && $_GET['category'] == $cat['id'] ? "bg-neutral-200" : "bg-gray-100") ?>">
              <?= $cat['name'] ?>
            </a>
          <?php endforeach; ?>
        </div>
        <!-- LIST CATEGORIES END -->
        <hr class="my-4" />
        <!-- LIST SORT ORDER START -->
        <div class="flex flex-wrap gap-2">
          <a onclick="update('asc', '');" href="javascript:void(0)" class="px-3 py-1 rounded-full w-fit <?= (isset($_GET['asc']) ? "bg-gray-100" : "bg-neutral-200") ?>">
            Newest First
          </a>
          <a onclick="update('asc', 'true');" href="javascript:void(0)" class="px-3 py-1 rounded-full w-fit <?= (!isset($_GET['asc']) ? "bg-gray-100" : "bg-neutral-200") ?>">
            Oldest First
          </a>
        </div>
        <!-- LIST SORT ORDER END -->
        <hr class="my-4" />
        <!-- SEARCH START -->
        <form method="GET" action="./" class="flex flex-wrap gap-2">
          <input type="text" class="w-full border-gray-300 rounded-full text-input" name="search" placeholder="Search" value="<?= $_GET['search'] ?? '' ?>">
          <div class="flex justify-end w-full gap-2">
            <input type="submit" value="Submit" class="px-3 py-1 rounded-full cursor-pointer bg-neutral-200 w-min">
            <a onclick="update('search', '');" href="javascript:void(0)" class="px-3 py-1 bg-gray-100 rounded-full w-min">
              Clear
            </a>
          </div>
        </form>
        <!-- SEARCH END -->
      </div>
      <!-- FILTERS END -->
    </div>
  </section>

</body>

</html>