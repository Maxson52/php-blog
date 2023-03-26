<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/log-in.php");
}

// Get all posts from DB
require_once('../../lib/utils/conn.php');

$query = "SELECT posts.id, posts.title, posts.created_at, posts.visible, users.name FROM posts JOIN users ON posts.author_id = users.id ORDER BY posts.created_at DESC";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

?>

<html>

<head>
    <title>Fry Me to the Moon</title>
    <link rel="icon" href="../../lib/assets/strawberry.png" />
    <link href="../../lib/css/output.css" rel="stylesheet" />
</head>


<body>
    <!-- NAV START -->
    <nav class="container flex justify-between px-8 py-8 mx-auto bg-white">
        <div>
            <h3 class="text-purple-600 h3">Fry Me to the Moon</h3>
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
            <h1 class="h1">Admin Panel - Manage Posts</h1>

            <table class="table-auto">
                <thead class="text-left">
                    <tr>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Visibility</th>
                        <th class="px-4 py-2">Published Date</th>
                        <th class="px-4 py-2">Author</th>
                        <th class="px-4 py-2">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_array($res)) {
                        $title = $row['title'];
                        $visible = $row['visible'] ? "Visible" : "Hidden";
                        $date = date('M d, Y', strtotime($row['created_at']));
                        $author = $row['name'];
                        $id = $row['id'];


                        echo "<tr>
                        <td class='px-4 py-2 border'>$title</td>
                        <td class='px-4 py-2 border'>$visible</td>
                        <td class='px-4 py-2 border'>$date</td>
                        <td class='px-4 py-2 border'>$author</td>
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