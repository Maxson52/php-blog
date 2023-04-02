<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/log-in.php");
}

$error = "";

require_once('../../lib/utils/conn.php');

// Get categories
$query = "SELECT * FROM categories";
$cats = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

// On form submit
if (isset($_POST['submit'])) {
    // Get form data
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user']['id'];
    $category_id = $_POST['category'];

    $escapedTitle = mysqli_real_escape_string($conn, $title);
    $escapedContent = mysqli_real_escape_string($conn, $content);

    // Insert into db
    $query = "INSERT INTO posts (title, content, author_id, category_id) VALUES ('$escapedTitle', '$escapedContent', '$author_id', '$category_id')";
    $res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

    // Redirect to home
    header("Location: ../../");
}

?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Post - Fry Me to the Moon</title>
    <link rel="icon" href="../../lib/assets/strawberry.png" />
    <link href="../../lib/css/output.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/@tailwindcss/typography@0.1.2/dist/typography.min.css">
</head>


<body>
    <!-- NAV START -->
    <nav class="fixed z-40 flex items-center justify-between w-full px-8 py-4 mx-auto text-black bg-transparent backdrop-blur-sm">
        <div>
            <img src="../../lib/assets/strawberry.png" alt="egg" class="w-12 rounded-full aspect-auto">
        </div>
        <div class="flex space-x-8">
            <a href="../">Back</a>
            <a href="../../auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- CREATE POST START -->
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <p class="text-red-500"><?php echo $error ?></p>


            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="flex flex-col gap-2">
                <input type="text" name="title" placeholder="Title" class="p-4 mb-4 font-serif h1 focus:outline-none" required minlength="3">

                <textarea id="editor" name="content" placeholder="Tell your story..."></textarea>
                <script>
                    ClassicEditor
                        .create(document.querySelector('#editor'), {
                            toolbar: {
                                items: [
                                    'undo', 'redo',
                                    '|', 'heading',
                                    '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                                    '|', 'bold', 'italic',
                                    '|', 'link', 'uploadImage', 'blockQuote',
                                    '|', 'bulletedList', 'numberedList', 'outdent', 'indent'
                                ],
                                shouldNotGroupWhenFull: true
                            }
                        })
                        .then(editor => {
                            console.log(editor);
                            document.getElementsByClassName("ck-editor__main")[0].classList.add("prose");
                            document.getElementsByClassName("ck-editor__main")[0].style.maxWidth = "none";
                        })
                        .catch(error => {
                            console.error(error);
                        });
                </script>
                <style>
                    .ck-editor__editable_inline {
                        min-height: 400px;
                    }
                </style>

                <div class="flex flex-col w-full gap-2 md:flex-row md:gap-0">
                    <!-- Category select -->
                    <select name="category" class="flex-1 select-input md:rounded-r-none" required>
                        <option value="" disabled selected>Select a category</option>
                        <?php
                        while ($row = mysqli_fetch_array($cats)) {
                            echo "<option value='$row[id]'>$row[name]</option>";
                        }
                        ?>
                    </select>

                    <input class="md:rounded-l-none button" type="submit" name="submit" value="Publish" />
                </div>
            </form>
        </div>
    </div>
    <!-- CREATE POST END -->
</body>

</html>