<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/log-in.php");
}

// Get all my stuff from DB
require_once('../lib/utils/conn.php');

$uid = $_SESSION['user']['id'];

$query = "SELECT * FROM users WHERE id = $uid";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));
$row = mysqli_fetch_assoc($res);

// Get all posts
$postQuery = "SELECT posts.id, posts.title, posts.content, posts.created_at, categories.name AS category, categories.visible AS cat_visible FROM posts 
            JOIN categories ON posts.category_id = categories.id 
            WHERE author_id = $uid";
$posts = mysqli_query($conn, $postQuery) or die("Query failed: " . mysqli_error($conn));

// Get all comments
$commentQuery = "SELECT * FROM comments WHERE author_id = $uid";
$comments = mysqli_query($conn, $commentQuery) or die("Query failed: " . mysqli_error($conn));
?>

<html>

<head>
    <title>Fry Me to the Moon</title>
    <link rel="icon" href="../lib/assets/strawberry.png" />
    <link href="../lib/css/output.css" rel="stylesheet" />
</head>


<body>
    <!-- NAV START -->
    <nav class="container flex justify-between px-8 py-8 mx-auto bg-white">
        <div>
            <h3 class="text-purple-600 h3">Fry Me to the Moon</h3>
        </div>
        <div class="flex space-x-8">
            <a href="../">Back</a>
            <a href="../auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- ACCOUNT START -->
    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl p-8">

            <!-- Display user name and email -->
            <div class="flex flex-col gap-3 mb-4">
                <img src='https://source.boringavatars.com/beam?name=<?= $row['id'] ?>' class='h-20 rounded-full w-min' />

                <h1 class="h1"><?php echo $row['name'] ?></h1>

                <h3 class="h3"><?php echo $row['email'] ?></h3>
            </div>

            <!-- List all posts -->
            <div class="my-8">
                <h2 class="pb-4 border-b h2">Your Posts</h2>
                <div class="flex flex-col gap-2">
                    <?php foreach ($posts as $post) : ?>
                        <a href='../post?id=<?= $post['id'] ?>' class='flex flex-col gap-2 p-4 transition border-b'>
                            <h2 class='h2'><?= $post['title'] ?></h2>
                            <p class='pb-4'><?= strip_tags(substr($post['content'], 0, 100)) ?>...</p>
                            <div class='flex items-center gap-2 text-sm'>
                                <?php if ($post['cat_visible']) echo "<p class='px-3 py-1 bg-gray-100 rounded-full w-min'>" . $post['category'] . "</p>" ?>
                            </div>
                        </a>

                    <?php endforeach; ?>
                </div>
            </div>


            <!-- List all comments -->
            <div>
                <h2 class="pb-4 border-b h2">Your Comments</h2>
                <div class="flex flex-col gap-2">
                    <?php foreach ($comments as $comment) : ?>
                        <a href=" ../post/?id=<?= $comment['post_id'] ?>" class="w-full gap-2 p-4 border-b link">
                            <?= $comment['content'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
        <!-- ACCOUNT END -->
</body>

</html>