<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/log-in.php");
}

$error = "";

require_once('../lib/utils/conn.php');

// On form submit
if (isset($_POST['submit'])) {
    // Get form data
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user']['id'];

    $escapedTitle = mysqli_real_escape_string($conn, $title);
    $escapedContent = mysqli_real_escape_string($conn, $content);

    // Insert into db
    $query = "INSERT INTO posts (title, content, author_id) VALUES ('$escapedTitle', '$escapedContent', '$author_id')";
    $res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

    // Redirect to home
    header("Location: ../../");
}

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

    <!-- CREATE POST START -->
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <p class="text-red-500"><?php echo $error ?></p>


            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="flex flex-col gap-2">
                <input type="text" name="title" placeholder="What're you calling this post?" class="text-input" required minlength="3">


                <textarea id="editor" name="content">Write your family's greatest recipe, your thoughts on poutine, or anything else on your mind.</textarea>
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
                        min-height: 250px;
                        padding: 0 30px !important;
                    }
                </style>

                <input class="button" type="submit" name="submit" value="Post article" />
            </form>
        </div>
    </div>
    <!-- CREATE POST END -->
</body>

</html>