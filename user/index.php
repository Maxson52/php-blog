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
$postQuery = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.visible, categories.name AS category, categories.visible AS cat_visible FROM posts 
            JOIN categories ON posts.category_id = categories.id 
            WHERE author_id = $uid";
$posts = mysqli_query($conn, $postQuery) or die("Query failed: " . mysqli_error($conn));

// Get all comments on visible posts
$commentQuery = "SELECT comments.id, comments.content, comments.created_at, posts.title AS post_title, posts.id AS post_id FROM comments 
                JOIN posts ON comments.post_id = posts.id 
                WHERE comments.author_id = $uid AND posts.visible = 1";
$comments = mysqli_query($conn, $commentQuery) or die("Query failed: " . mysqli_error($conn));

// Estimate reading time
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
    <title>Account - Fry Me to the Moon</title>
    <link rel="icon" href="../lib/assets/strawberry.png" />
    <link href="../lib/css/output.css" rel="stylesheet" />
</head>


<body>
    <!-- NAV START -->
    <nav class="fixed z-40 flex items-center justify-between w-full px-8 py-4 mx-auto text-black bg-transparent backdrop-blur-sm">
        <div>
            <img src="../lib/assets/strawberry.png" alt="egg" class="w-12 rounded-full aspect-auto">
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

                <a href="edit?id=<?= $_SESSION['user']['id'] ?>" class="link">Edit profile</a>
            </div>

            <!-- List all posts -->
            <div class="my-8">
                <h2 class="pb-4 border-b h2">Your Posts</h2>
                <div class="flex flex-col">
                    <?php foreach ($posts as $post) : ?>
                        <a href='../post?id=<?= $post['id'] ?>' class='flex flex-col gap-2 p-6 transition border-b'>
                            <h2 class='h2'><?= $post['title'] ?></h2>
                            <?php if (!$post['visible']) : ?>
                                <p class='font-bold text-red-500'>This post is invisible.</p>
                            <?php endif; ?>
                            <p class='pb-4 font-serif'><?= strip_tags(substr($post['content'], 0, 100)) ?>...</p>
                            <div class='flex items-center gap-2 text-sm'>
                                <?php if ($post['cat_visible']) echo "<p class='px-3 py-1 bg-gray-100 rounded-full w-min'>" . $post['category'] . "</p>" ?>
                                <p class='text-gray-400'><?= estimateReadingTime($post['content']) ?> min read</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <!-- No posts yet -->
                    <?php if (mysqli_num_rows($posts) == 0) : ?>
                        <p class='mt-4 text-gray-400'>You haven't posted anything yet.</p>
                    <?php endif; ?>
                </div>
            </div>


            <!-- List all comments -->
            <div>
                <h2 class="pb-4 border-b h2">Your Comments</h2>
                <div class="flex flex-col gap-2">
                    <?php foreach ($comments as $comment) : ?>
                        <a href=" ../post/?id=<?= $comment['post_id'] ?>" class="w-full gap-2 p-4 font-serif border-b link">
                            <?= strip_tags($comment['content']) ?>

                        </a>
                    <?php endforeach; ?>
                    <!-- No comments yet -->
                    <?php if (mysqli_num_rows($comments) == 0) : ?>
                        <p class='mt-4 text-gray-400'>You haven't commented anything yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <!-- ACCOUNT END -->
</body>

</html>