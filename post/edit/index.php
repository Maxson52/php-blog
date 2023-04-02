<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/log-in.php");
}

// If no id set redirect to last page
if (!isset($_GET['id'])) {
    header("Location: ../");
}

$error = "";

// Get user from DB
require_once('../../lib/utils/conn.php');

$query = "SELECT * FROM posts WHERE id = " . $_GET['id'];
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));
if (mysqli_num_rows($res) === 0) header("Location: ../");
extract(mysqli_fetch_array($res));

// Get cats
$query = "SELECT * FROM categories";
$cats = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

// If not admin or not author redirect to home
if ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['id'] !== $author_id) {
    header("Location: ../");
}

// If form submitted
if (isset($_POST['submit'])) {
    $newTitle = $_POST['title'];
    $newContent = $_POST['content'];
    $newVisibility = $_POST['visibility'] ?? $visible;
    $newCategory = $_POST['category'];

    $escapedTitle = mysqli_real_escape_string($conn, $newTitle);
    $escapedContent = mysqli_real_escape_string($conn, $newContent);

    $query = "UPDATE posts SET title = '$escapedTitle', content = '$escapedContent', visible = '$newVisibility', category_id = '$newCategory' WHERE id = " . $_GET['id'];
    $res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

    if ($res) {
        $redirect = $_GET['redirect'] ?? ('../?id=' . $_GET['id']);
        header("Location: $redirect");
    } else {
        $error = "Something went wrong";
    }
}

?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Post - Fry Me to the Moon</title>
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
            <a href="<?= $_GET['redirect'] ?? ('../?id=' . $_GET['id']) ?>">Back</a>
            <a href="../../auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- EDIT POST START -->
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <h1 class="h1">Edit Post</h1>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . (isset($_GET['redirect']) ? "&redirect=" . $_GET['redirect'] : "") ?>" method="POST" class="flex flex-col gap-2">
                <input class="text-input" type="text" name="title" placeholder="Enter title" value="<?php echo $title ?>" required>

                <!-- If admin allow changing visibility -->
                <?php if ($_SESSION['user']['role'] === "admin") : ?>
                    <select class="select-input" name="visibility" id="visibility">
                        <option value="1" <?php if ($visible) echo "selected" ?>>Visible</option>
                        <option value="0" <?php if (!$visible) echo "selected" ?>>Hidden</option>
                    </select>
                <?php endif; ?>

                <!-- Category -->
                <select class="select-input" name="category" id="category">
                    <?php while ($cat = mysqli_fetch_array($cats)) { ?>
                        <option value="<?php echo $cat['id'] ?>" <?php if ($cat['id'] == $category_id) echo "selected" ?>><?php echo $cat['name'] ?></option>
                    <?php } ?>
                </select>

                <textarea id="editor" name="content"><?php echo $content ?></textarea>
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
                        min-height: 250px;
                    }
                </style>

                <input class="button" type="submit" name="submit" value="Update" />
            </form>
        </div>
    </div>
    <!-- EDIT POST END -->
</body>

</html>