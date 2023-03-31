<?php

session_start();

// If not authed redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/log-in.php");
}

// If not admin redirect to home
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../");
}

// Get all users from DB
require_once('../lib/utils/conn.php');

$query = "SELECT * FROM users";
$res = mysqli_query($conn, $query) or die("Query failed: " . mysqli_error($conn));

// --------------------- STATS ---------------------
// Get all posts from DB
$postsQuery = "SELECT * FROM posts";
$posts = mysqli_query($conn, $postsQuery) or die("Query failed: " . mysqli_error($conn));

// Get all comments from DB
$commentsQuery = "SELECT * FROM comments";
$comments = mysqli_query($conn, $commentsQuery) or die("Query failed: " . mysqli_error($conn));

?>

<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - Fry Me to the Moon</title>
    <link rel="icon" href="../lib/assets/strawberry.png" />
    <link href="../lib/css/output.css" rel="stylesheet" />
</head>


<body>
    <!-- NAV START -->
    <nav class="fixed z-40 flex items-center justify-between w-full px-8 py-4 mx-auto text-black bg-transparent backdrop-blur-sm">
        <div>
            <img src="../lib/assets/strawberry.png" alt="egg" class="w-12 rounded-full aspect-auto">
        </div>
        <div class="flex space-x-8">
            <a href="../">Back</a>
            <a href="../auth/log-out.php">Log out</a>
        </div>
    </nav>
    <!-- NAV END -->

    <!-- ADMIN DASHBOARD START -->
    <div class="grid gap-8 place-items-center">
        <div class="flex flex-col gap-2 mt-24 min-w-[50%] max-w-6xl">
            <h1 class="h1">Admin Panel</h1>

            <a href="users" class="link">Manage Users</a>
            <a href="posts" class="link">Manage Posts</a>
            <a href="comments" class="link">Manage Comments</a>
            <a href="categories" class="link">Manage Categories</a>
        </div>

        <!-- STATS START -->
        <div class="flex flex-col gap-2 min-w-[50%] max-w-6xl">

            <h2 class="h2">Stats For Nerds</h2>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"></script>

            <!-- Create chart of posts in last 14 days -->
            <h3 class="h3">Posts Over Last 14 Days</h3>
            <canvas id="myChart" width="500" height="200"></canvas>
            <script>
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            <?php
                            // Get the last 14 days
                            $days = array();
                            for ($i = 0; $i < 14; $i++) {
                                $days[] = date("M d, Y", strtotime("-$i days"));
                            }
                            // Get the day for each post
                            $postsPerDay = array();
                            foreach ($days as $day) {
                                $postsPerDay[$day] = 0;
                            }
                            while ($row = mysqli_fetch_assoc($posts)) {
                                $date = date("M d, Y", strtotime($row['created_at']));

                                if (array_key_exists($date, $postsPerDay)) {
                                    $postsPerDay[$date]++;
                                }
                            }
                            // Create labels for chart
                            $postsPerDay = array_reverse($postsPerDay);
                            foreach ($postsPerDay as $day => $count) {
                                echo "'$day', ";
                            }
                            ?>
                        ],
                        datasets: [{
                            label: '# of Posts',
                            data: [
                                <?php
                                // Create data for chart
                                foreach ($postsPerDay as $day => $count) {
                                    echo "$count, ";
                                }
                                ?>
                            ],
                            borderColor: [
                                'rgba(147, 51, 234, 1)',
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        elements: {
                            line: {
                                tension: 0
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                },
                            }
                        }
                    }
                });
            </script>

            <!-- Create chart of comments in last 14 days -->
            <h3 class="mt-4 h3">Comments Over Last 14 Days</h3>
            <canvas id="myChart2" width="500" height="200"></canvas>
            <script>
                var ctx = document.getElementById('myChart2').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            <?php
                            // Get the day for each comment
                            $commentsPerDay = array();
                            foreach ($days as $day) {
                                $commentsPerDay[$day] = 0;
                            }
                            while ($row = mysqli_fetch_assoc($comments)) {
                                $date = date("M d, Y", strtotime($row['created_at']));

                                if (array_key_exists($date, $commentsPerDay)) {
                                    $commentsPerDay[$date]++;
                                }
                            }
                            // Create labels for chart
                            $commentsPerDay = array_reverse($commentsPerDay);
                            foreach ($commentsPerDay as $day => $count) {
                                echo "'$day', ";
                            }
                            ?>
                        ],
                        datasets: [{
                            label: '# of Comments',
                            data: [
                                <?php
                                // Create data for chart
                                foreach ($commentsPerDay as $day => $count) {
                                    echo "$count, ";
                                }
                                ?>
                            ],
                            borderColor: [
                                'rgba(147, 51, 234, 1)',
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        elements: {
                            line: {
                                tension: 0
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                },
                            }
                        }
                    }
                });
            </script>
        </div>
        <!-- STATS END -->

        <br />
    </div>
    <!-- ADMIN DASHBOARD END -->
</body>

</html>