<?php

session_start();

// If not authed redirect to login page
// FIXME: Change path
if (!isset($_SESSION['user'])) {
    header("Location: ./auth/log-in.php");
}

?>

<html>

<head>
    <title>Fry Me to the Moon</title>
    <!-- FIXME: Change path -->
    <link rel="icon" href="./lib/assets/strawberry.png" />
    <!-- FIXME: Change path -->
    <link href="./lib/css/output.css" rel="stylesheet" />
</head>


<body>
    <!-- NAV START -->
    <nav class="container flex justify-between px-8 py-8 mx-auto bg-white">
        <div>
            <h3 class="text-purple-600 h3">Fry Me to the Moon</h3>
        </div>
        <div class="flex space-x-8">
            <!-- FIXME: Change path -->
            <a href="../">Back</a>
            <!-- FIXME: Change path -->
            <a href="./auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- EDIT USER START -->
    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 p-8 min-w-[50%] max-w-6xl">
            <h1 class="h1">Stuff</h1>
        </div>
    </div>

    <!-- EDIT USER END -->
</body>

</html>