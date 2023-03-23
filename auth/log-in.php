<?php
session_start();

$error = "";
$userInfo;

require_once('../utils/conn.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $matchingEmails = "SELECT * FROM `users` WHERE `email`='$email'";
    $res = mysqli_query($conn, $matchingEmails) or die("Query failed: " . mysqli_error($conn));

    if (mysqli_num_rows($res) == 0) {
        $error = "Username or password is incorrect";
    } else {
        $userInfo = mysqli_fetch_array($res);
        if (password_verify($password, $userInfo['password'])) {
            $_SESSION['user'] = $userInfo;
            header("Location: index.php");
        } else {
            $error = "Username or password is incorrect";
        }
    }

    $_POST = [];
}
?>
<html>

<head>
    <title>Log in</title>
    <link href="../output.css" rel="stylesheet" />
</head>

<body>
    <section class="grid w-screen min-h-screen bg-gradient-to-r from-indigo-300 to-purple-400 place-items-center">
        <div class="flex flex-col gap-4 p-12 bg-white rounded-xl drop-shadow-md min-w-[50%] max-w-6xl">
            <h1 class="h1">Log in to continue</h1>
            <p>Don't have an account? <a class="link" href="./create-account.php">Create one</a></p>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="flex flex-col gap-2">
                <input class="text-input" type="email" name="email" placeholder="Enter your email" required>

                <input class="text-input" type="password" name="password" placeholder="Enter your password" required>

                <input class="button" type="submit" name="submit" value="Log in" />
            </form>
        </div>
    </section>
</body>

</html>