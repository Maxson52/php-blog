<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/log-in.php");
}

// Get all users from DB
require_once('../../lib/utils/conn.php');

$query = "SELECT * FROM users";
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
            <h1 class="h1">Admin Panel - Manage Users</h1>

            <table class="table-auto">
                <thead class="text-left">
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_array($res)) {
                        $name = $row['name'];
                        $email = $row['email'];
                        $role = $row['role'];
                        $id = $row['id'];


                        echo "<tr" . ($id === $_SESSION['user']['id'] ? " class='bg-gray-200' "  : '') . ">" .
                            "<td class='px-4 py-2 border'>$name</td>
                        <td class='px-4 py-2 border'>$email</td>
                        <td class='px-4 py-2 border'>$role</td>
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