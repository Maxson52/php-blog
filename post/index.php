<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/log-in.php");
}

// If no id redirect to index
if (!isset($_GET['id'])) {
    header("Location: ../");
}

// Get article
require_once('../lib/utils/conn.php');

$id = $_GET['id'];

$query = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.name FROM posts JOIN users ON posts.author_id = users.id WHERE posts.id = $id";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

?>

<html>

<head>
    <title>Fry Me to the Moon</title>
    <link rel="icon" href="../lib/assets/strawberry.png" />
    <link href="../lib/css/output.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/@tailwindcss/typography@0.1.2/dist/typography.min.css">
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

    <!--  ARTICLE START -->

    <div class="grid place-items-center">
        <article class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl px-8">

            <?php
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $title = $row['title'];
                    $content = $row['content'];
                    $author = $row['name'];
                    $date = date('M d, Y', strtotime($row['created_at']));
            ?>

                    <h1 class="text-5xl font-medium text-center"><?php echo $title ?></h1>
                    <div class="flex justify-center gap-2">
                        <p class="text-gray-500"><?php echo $author ?></p>
                        -
                        <p class="text-gray-500"><?php echo $date ?></p>
                    </div>
                    <div class="my-12 prose" id="content"><?php echo $content ?></div>

            <?php
                }
            } else {
                echo "No posts found";
            }
            ?>

        </article>

        <style>
            #content {
                max-width: none;
            }
        </style>
    </div>
    <!-- ARTICLE END -->
</body>

</html>