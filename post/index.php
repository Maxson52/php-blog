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

// Get post
$query = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.name FROM posts JOIN users ON posts.author_id = users.id WHERE posts.id = $id";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

// Get comments
$commentsQuery = "SELECT comments.id, comments.content, comments.created_at, users.name FROM comments JOIN users ON comments.author_id = users.id WHERE comments.post_id = $id ORDER BY comments.created_at ASC";
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
                    <style>
                        #content {
                            max-width: none;
                        }
                    </style>

            <?php
                }
            } else {
                echo "No post found";
            }
            ?>

            <hr>
        </article>
        <!-- ARTICLE END -->

        <!-- COMMENTS START -->
        <div class="flex flex-col gap-2 my-12 min-w-[50%] max-w-6xl px-8">
            <?php
            if (mysqli_num_rows($comments) > 0) {
                while ($row = mysqli_fetch_array($comments)) {
                    $comment = $row['content'];
                    $author = $row['name'];
                    $date = date('M d, Y - g:i', strtotime($row['created_at']));

            ?>

                    <div class="flex flex-col w-full gap-2 p-4 transition border rounded-lg shadow-md">
                        <h3 class="h3"><?php echo $author ?></h3>
                        <p class="text-gray-500"><?php echo $date ?></p>
                        <p class="prose"><?php echo $comment ?></p>
                    </div>

            <?php
                }
            } else {
                echo "No comments yet!";
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