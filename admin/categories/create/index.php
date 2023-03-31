<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/log-in.php");
}

// If not admin redirect to home
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../../");
}

$error = "";

require_once('../../../lib/utils/conn.php');

// If form submitted
if (isset($_POST['submit'])) {
    $cat = $_POST['category'];

    $escapedCat = mysqli_real_escape_string($conn, $cat);

    $query = "INSERT INTO categories (name) VALUES ('$escapedCat')";
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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Category - Fry Me to the Moon</title>
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

    <!-- Create CAT START -->
    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <h1 class="h1">Create Category</h1>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="flex flex-col gap-2">
                <input class="text-input" type="text" name="category" placeholder="Enter category name">

                <input class="button" type="submit" name="submit" value="Create category" />
            </form>
        </div>
    </div>
    <!-- Create CAT END -->
</body>

</html>