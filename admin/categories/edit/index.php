<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/log-in.php");
}

if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../../");
}

// If no id set redirect to admin page
if (!isset($_GET['id'])) {
    header("Location: ../");
}

$error = "";

// Get cat from DB
require_once('../../../lib/utils/conn.php');

$query = "SELECT * FROM categories WHERE id = " . $_GET['id'];
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));
if (mysqli_num_rows($res) === 0) header("Location: ../");
extract(mysqli_fetch_array($res));

// If form submitted
if (isset($_POST['submit'])) {
    $newName = $_POST['category'];
    $newVisibility = $_POST['visibility'];

    $escapedName = mysqli_real_escape_string($conn, $newName);

    $query = "UPDATE categories SET name = '$escapedName', visible = '$newVisibility' WHERE id = " . $_GET['id'];
    $res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

    if ($res) {
        header("Location: ../");
    } else {
        $error = "Something went wrong";
    }
}

?>

<html>

<head>
    <title>Edit Category - Fry Me to the Moon</title>
    <link rel="icon" href="../../../lib/assets/strawberry.png" />
    <link href="../../../lib/css/output.css" rel="stylesheet" />
</head>


<body>
    <!-- NAV START -->
    <nav class="fixed z-40 flex items-center justify-between w-full px-8 py-4 mx-auto text-black bg-transparent backdrop-blur-sm">
        <div>
            <img src="../../../lib/assets/strawberry.png" alt="egg" class="w-12 rounded-full aspect-auto">
        </div>
        <div class="flex space-x-8">
            <a href="../">Back</a>
            <a href="../../../auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- EDIT CAT START -->
    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <h1 class="h1">Edit category</h1>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] ?>" method="POST" class="flex flex-col gap-2">
                <!-- Visibility -->
                <select class="select-input" name="visibility" id="visibility">
                    <option value="1" <?php if ($visible) echo "selected" ?>>Visible</option>
                    <option value="0" <?php if (!$visible) echo "selected" ?>>Hidden</option>
                </select>

                <input class="text-input" type="text" name="category" placeholder="Enter category" value="<?= $name ?>">

                <input class="button" type="submit" name="submit" value="Update category" />
            </form>
        </div>
    </div>
    <!-- EDIT CAT END -->
</body>

</html>