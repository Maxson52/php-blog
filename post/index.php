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

// Get post (as long as it's visible or the author is the current user)
$query = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.author_id, posts.visible, users.name FROM posts 
        JOIN users ON posts.author_id = users.id 
        WHERE posts.id = $id 
        AND (posts.visible = 1 OR posts.author_id = " . $_SESSION['user']['id'] . ")";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

// Redirect if no post
if (mysqli_num_rows($res) == 0) {
    header("Location: ../");
}

// Get comments
$commentsQuery = "SELECT comments.id, comments.content, comments.created_at, comments.author_id, users.name FROM comments 
                JOIN users ON comments.author_id = users.id 
                WHERE comments.post_id = $id 
                AND comments.visible = 1
                ORDER BY comments.created_at ASC";
$comments = mysqli_query($conn, $commentsQuery) or die("Query failed: " . mysqli_error($conn));

// Create comment
$error = "";

if (isset($_POST['submit'])) {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author = $_SESSION['user']['id'];

    if (empty($content)) {
        $error = "Please enter a comment";
    } else {
        $query = "INSERT INTO comments (content, author_id, post_id) VALUES ('$content', $author, $id)";
        $res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

        if ($res) {
            header("Location: index.php?id=$id");
        } else {
            $error = "Something went wrong";
        }
    }
}

?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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


    <div class="grid place-items-center">
        <!--  ARTICLE START -->
        <article class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl px-8">
            <?php foreach ($res as $row) : ?>
                <?php
                $title = $row['title'];
                $content = $row['content'];
                $author = $row['name'];
                $date = date('M d, Y', strtotime($row['created_at'])); ?>

                <h1 class="mb-2 text-5xl font-medium text-center"><?= $title ?></h1>
                <div class="flex justify-center gap-2">
                    <p class="text-gray-500"><?= $author ?></p>
                    -
                    <p class="text-gray-500"><?= $date ?></p>

                    <?php if ($_SESSION['user']['id'] == $row['author_id'] || $_SESSION['user']['role'] == 'admin') : ?>
                        <span class="text-gray-400">- </span><a href="../post/edit?id=<?= $row['id'] ?>" class="link">Edit Post</a>
                    <?php endif; ?>
                </div>

                <?php if ($row['visible'] == 0) : ?>
                    <p class="font-bold text-center text-red-500">This post is not visible to the public.</p>
                <?php endif; ?>

                <div class="my-12 prose" id="content"><?= $content ?></div>
                <style>
                    #content {
                        max-width: none;
                    }
                </style>
            <?php endforeach; ?>
            <?php
            if (mysqli_num_rows($res) === 0) {
                echo "<p class='text-gray-400'>No post found.</p>";
            }
            ?>
        </article>
        <!-- ARTICLE END -->

        <!-- COMMENTS START -->
        <div class="flex flex-col gap-2 min-w-[50%] max-w-6xl px-8">
            <h2 class="pb-4 border-b h2">Comments</h2>
            <div class="flex flex-col gap-2">
                <?php foreach ($comments as $comment) : ?>
                    <?php
                    $content = $comment['content'];
                    $author = $comment['name'];
                    $date = date('M d, Y - g:i', strtotime($comment['created_at']));
                    ?>

                    <div class="flex flex-col w-full gap-2 p-4 transition border-b ">
                        <div class="flex justify-between">
                            <h3 class="h3"><?php echo $author ?></h3>
                            <?php if ($_SESSION['user']['id'] == $comment['author_id'] || $_SESSION['user']['role'] == 'admin') : ?>
                                <a href="../comment/edit?id=<?= $comment['id'] ?>" class="link">Edit Comment</a>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-500"><?php echo $date ?></p>
                        <p class="prose"><?php echo $content ?></p>

                    </div>
                <?php endforeach; ?>
                <?php
                if (mysqli_num_rows($comments) === 0) {
                    echo "<p class='text-gray-400'>No comments yet.</p>";
                }
                ?>
            </div>
            <!-- COMMENTS END -->


            <!-- COMMENT FORM START -->

            <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

            <div class="grid place-items-center">
                <div class="flex flex-col gap-2 mb-12 min-w-[50%] max-w-6xl">
                    <p class="text-red-500"><?php echo $error ?></p>


                    <form action="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] ?>" method="POST" class="flex flex-col gap-2">
                        <textarea id="editor" name="content" placeholder="Write your comment..."></textarea>
                        <script>
                            ClassicEditor
                                .create(document.querySelector('#editor'))
                                .then(editor => {
                                    console.log(editor);
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        </script>

                        <style>
                            .ck-editor__editable_inline {
                                padding: 0 30px !important;
                            }
                        </style>

                        <input class="button" type="submit" name="submit" value="Comment" />
                    </form>
                </div>
            </div>

            <!-- COMMENT FORM END -->


        </div>
</body>

</html>