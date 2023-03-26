<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/log-in.php");
}

// If no id set redirect to admin page
if (!isset($_GET['id'])) {
    header("Location: ../");
}

$error = "";

// Get user from DB
require_once('../../../lib/utils/conn.php');

$query = "SELECT * FROM users WHERE id = " . $_GET['id'];
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));
extract(mysqli_fetch_array($res));

// If form submitted
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "UPDATE users SET name = '$name', email = '$email', role = '$role' WHERE id = " . $_GET['id'];
    $res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

    header("Location: ../");
}

?>

<html>

<head>
    <title>Fry Me to the Moon</title>
    <link rel="icon" href="../../../lib/assets/strawberry.png" />
    <link href="../../../lib/css/output.css" rel="stylesheet" />
</head>


<body>
    <!-- NAV START -->
    <nav class="container flex justify-between px-8 py-8 mx-auto bg-white">
        <div>
            <h3 class="text-purple-600 h3">Fry Me to the Moon</h3>
        </div>
        <div class="flex space-x-8">
            <a href="../">Back</a>
            <a href="../../../auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- EDIT USER START -->
    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <h1 class="h1">Edit user</h1>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] ?>" method="POST" class="flex flex-col gap-2">
                <input class="text-input" type="text" name="name" placeholder="Enter name" value="<?php echo $name ?>" required>

                <input class="text-input" type="email" name="email" placeholder="Enter email" value="<?php echo $email ?>" required>

                <select class="select-input" name="role">
                    <option <?php echo $role === "user" ? "selected" : "" ?> value="user">User</option>
                    <option <?php echo $role === "mod" ? "selected" : "" ?> value="mod">Mod</option>
                    <option <?php echo $role === "admin" ? "selected" : "" ?> value="admin">Admin</option>
                </select>

                <input class="button" type="submit" name="submit" value="Update user" />
            </form>
        </div>
    </div>

    <!-- EDIT USER END -->
</body>

</html>