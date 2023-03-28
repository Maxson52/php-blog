<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/log-in.php");
}

// Get all my stuff from DB
require_once('../lib/utils/conn.php');

$uid = $_SESSION['user']['id'];

$query = "SELECT users.id, users.name, users.email, comments.content AS comments 
            FROM users
            JOIN comments on users.id = comments.author_id
            WHERE users.id = $uid";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

$row = mysqli_fetch_assoc($res);
$comments = mysqli_fetch_all($res);
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

    <!-- ACCOUNT START -->
    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <h1 class="h1">Account</h1>

            <!-- Display user name and email -->
            <div class="flex justify-center gap-2 h3">
                <p><?php echo $row['name'] ?></p>
                -
                <p><?php echo $row['email'] ?></p>
            </div>

            <!-- List all comments -->
            <div class="flex flex-col gap-2">
                <?php foreach ($comments as $comment) : ?>
                    <div class="flex flex-col w-full gap-2 p-4 transition border rounded-lg shadow-md">
                        <?php echo $comment[3] ?>
                    </div>
                <?php endforeach; ?>


            </div>
        </div>
        <!-- ACCOUNT END -->
</body>

</html>