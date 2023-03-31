<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/log-in.php");
}

// If not admin or the user redirect to home
if ($_SESSION['user']['role'] !== "admin" && $_SESSION['user']['id'] !== $_GET['id']) {
    header("Location: ../");
}

// If no id set redirect to admin page
if (!isset($_GET['id'])) {
    header("Location: ../");
}

$error = "";

// Get user from DB
require_once('../../lib/utils/conn.php');

$query = "SELECT * FROM users WHERE id = " . $_GET['id'];
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));
if (mysqli_num_rows($res) === 0) header("Location: ../../");
extract(mysqli_fetch_array($res));

// If form submitted
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'] ?? $_SESSION['user']['role'];

    $query = "UPDATE users SET name = '$name', email = '$email', role = '$role' WHERE id = " . $_GET['id'];
    $res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

    $redirect = $_GET['redirect'] ?? "../";
    header("Location: $redirect");
}

?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit User - Fry Me to the Moon</title>
    <link rel="icon" href="../../lib/assets/strawberry.png" />
    <link href="../../lib/css/output.css" rel="stylesheet" />
</head>


<body>
    <!-- NAV START -->
    <nav class="fixed z-40 flex items-center justify-between w-full px-8 py-4 mx-auto text-black bg-transparent backdrop-blur-sm">
        <div>
            <img src="../../lib/assets/strawberry.png" alt="egg" class="w-12 rounded-full aspect-auto">
        </div>
        <div class="flex space-x-8">
            <a href="<?= $_GET['redirect'] ?? '../' ?>">Back</a>
            <a href="../../auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- EDIT USER START -->
    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <h1 class="h1">Edit user</h1>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . (isset($_GET['redirect']) ? "&redirect=" . $_GET['redirect'] : "") ?>" method="POST" class="flex flex-col gap-2">
                <input class="text-input" type="text" name="name" placeholder="Enter name" value="<?php echo $name ?>" required>

                <input class="text-input" type="email" name="email" placeholder="Enter email" value="<?php echo $email ?>" required>

                <!-- If admin allow changing role -->
                <?php if ($_SESSION['user']['role'] === "admin") : ?>
                    <select class="select-input" name="role">
                        <option <?php echo $role === "deleted" ? "selected" : "" ?> value="deleted">Shadow Delete</option>
                        <option <?php echo $role === "user" ? "selected" : "" ?> value="user">User</option>
                        <option <?php echo $role === "admin" ? "selected" : "" ?> value="admin">Admin</option>
                    </select>
                <?php endif; ?>

                <input class="button" type="submit" name="submit" value="Update user" />
            </form>
        </div>
    </div>

    <!-- EDIT USER END -->
</body>

</html>