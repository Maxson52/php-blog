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

// Get all posts from DB
require_once('../../lib/utils/conn.php');

if (isset($_GET['search']) && $_GET['search'] === '') header("Location: ./");
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');

$cols = ['posts.title', 'categories.name', 'posts.visible', 'posts.created_at', 'users.name'];

$orderBy = isset($cols[$_GET['orderBy'] ?? -1]) ? $cols[$_GET['orderBy']] : 'posts.created_at';
$sortOrder = isset($_GET['asc']) ? 'ASC' : 'DESC';

$query = "SELECT posts.id, posts.title, posts.created_at, posts.visible, users.name, categories.name AS category 
        FROM posts 
        JOIN users ON posts.author_id = users.id 
        JOIN categories ON posts.category_id = categories.id 
        WHERE (posts.title LIKE '%$search%' OR categories.name LIKE '%$search%' OR users.name LIKE '%$search%')
        AND posts.title NOT LIKE '[deleted]'
        ORDER BY $orderBy $sortOrder";
$dbRes = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));
$res = [];

// Highlight search terms
while ($row = mysqli_fetch_array($dbRes)) {
    if (isset($_GET['search'])) {
        $row['title'] = preg_replace('/(' . $search . ')/i', '<mark>$1</mark>', $row['title']);
        $row['category'] = preg_replace('/(' . $search . ')/i', '<mark>$1</mark>', $row['category']);
        $row['name'] = preg_replace('/(' . $search . ')/i', '<mark>$1</mark>', $row['name']);
    }
    $res[] = $row;
}

?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Posts - Fry Me to the Moon</title>
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
            <h1 class="h1">Admin Panel - Manage Posts</h1>

            <!-- SEARCH START -->
            <form method="GET" action="./" class="flex flex-wrap gap-2">
                <input type="text" class="w-full border-gray-300 rounded-full text-input" name="search" placeholder="Search" value="<?= $_GET['search'] ?? '' ?>">
                <div class="flex justify-end w-full gap-2">
                    <input type="submit" value="Submit" class="px-3 py-1 rounded-full cursor-pointer bg-neutral-200 w-min">
                    <a href="./" class="px-3 py-1 bg-gray-100 rounded-full w-min">
                        Clear
                    </a>
                </div>
            </form>
            <!-- SEARCH END -->

            <script>
                function update(orderByIndex) {
                    let searchParams = new URLSearchParams(window.location.search);

                    if (searchParams.get('orderBy') == orderByIndex) {
                        if (searchParams.get('asc')) {
                            searchParams.delete('asc');
                        } else {
                            searchParams.set('asc', '1');
                        }
                    } else {
                        searchParams.set('orderBy', orderByIndex);
                    }

                    window.location.search = searchParams.toString();
                }
            </script>
            <?php
            function getSortIcon($index)
            {
                if (isset($_GET['orderBy']) && $_GET['orderBy'] == $index) {
                    if (isset($_GET['asc'])) {
                        return '▲';
                    } else {
                        return '▼';
                    }
                } else {
                    return '';
                }
            }
            ?>

            <table class="table-auto">
                <thead class="text-left">
                    <tr>
                        <th class="px-4 py-2 cursor-pointer" onclick="update('0')">Title <?= getSortIcon(0) ?></th>
                        <th class="px-4 py-2 cursor-pointer" onclick="update('1')">Category <?= getSortIcon(1) ?></th>
                        <th class="px-4 py-2 cursor-pointer" onclick="update('2')">Visibility <?= getSortIcon(2) ?></th>
                        <th class="px-4 py-2 cursor-pointer" onclick="update('3')">Published Date <?= getSortIcon(3) ?></th>
                        <th class="px-4 py-2 cursor-pointer" onclick="update('4')">Author <?= getSortIcon(4) ?></th>
                        <th class="px-4 py-2 cursor-pointer">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($res as $row) {
                        $title = $row['title'];
                        $category = $row['category'];
                        $visible = $row['visible'] ? "Visible" : "Hidden";
                        $date = date('M d, Y', strtotime($row['created_at']));
                        $author = $row['name'];
                        $id = $row['id'];


                        echo "<tr>
                        <td class='px-4 py-2 border'><a class='link' href='../../post?id=$id'>$title</a></td>
                        <td class='px-4 py-2 border'>$category</td>
                        <td class='px-4 py-2 border'>$visible</td>
                        <td class='px-4 py-2 border'>$date</td>
                        <td class='px-4 py-2 border'>$author</td>
                        <td class='px-4 py-2 border'><a class='link' href='../../post/edit?id=$id&redirect=../../admin/posts'>Edit</a></td>
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