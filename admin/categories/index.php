<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/log-in.php");
}

// If not admin redirect to home
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../");
}

// Get all cats from DB
require_once('../../lib/utils/conn.php');

$query =  "SELECT * FROM categories";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Categories - Fry Me to the Moon</title>
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
            <a href="../">Back</a>
            <a href="../../auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- ADMIN DASHBOARD START -->

    <div class="grid place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <h1 class="h1">Admin Panel - Manage Categories</h1>

            <a href="create" class="mb-2 w-fit button">Create new category</a>

            <table class="table-auto">
                <thead class="text-left">
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Visibility</th>
                        <th class="px-4 py-2">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_array($res)) {
                        $name = $row['name'];
                        $visible = $row['visible'] ? "Visible" : "Hidden";
                        $id = $row['id'];

                        echo "<tr>
                        <td class='px-4 py-2 border'>$name</td>
                        <td class='px-4 py-2 border'>$visible</td>
                        <td class='px-4 py-2 border'><a class='link' href='edit?id=$id'>Edit</a></td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ADMIN DASHBOARD END -->
</body>

</html>