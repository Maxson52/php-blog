<?php
session_start();

// If authed redirect to login page
if (isset($_SESSION['user'])) {
    header("Location: ../");
}

$error = "";
$userInfo;

require_once('../lib/utils/conn.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $matchingEmails = "SELECT * FROM `users` WHERE `email`='$email'";
    $res = mysqli_query($conn, $matchingEmails) or die("Query failed: " . mysqli_error($conn));

    if (mysqli_num_rows($res) == 0) {
        $error = "Email or password is incorrect";
    } else {
        $userInfo = mysqli_fetch_array($res);

        if ($userInfo['role'] === "deleted") {
            $error = "Your account has been deleted. Contact us if you think it was by mistake";
        } else {
            if (password_verify($password, $userInfo['password'])) {
                $_SESSION['user'] = $userInfo;
                header("Location: ../");
            } else {
                $error = "Email or password is incorrect";
            }
        }
    }

    $_POST = [];
}
?>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Log in - Fry Me To The Moon</title>
    <link rel="icon" href="../lib/assets/strawberry.png" />
    <link href="../lib/css/output.css" rel="stylesheet" />
</head>

<body>
    <section class="grid w-screen min-h-screen bg-gradient-to-r from-indigo-300 to-purple-400 place-items-center">
        <div class="flex flex-col gap-4 p-12 bg-white rounded-xl drop-shadow-md min-w-[50%] max-w-6xl">
            <h1 class="h1">Log in to continue</h1>
            <p>Don't have an account? <a class="link" href="./create-account.php">Create one</a></p>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="flex flex-col gap-2">
                <input class="text-input" type="email" name="email" placeholder="Enter your email" value="<?= $email ?? "" ?>" required autofocus>

                <input class="text-input" type="password" name="password" placeholder="Enter your password" required>

                <input class="button" type="submit" name="submit" value="Log in" />
            </form>
        </div>
    </section>
</body>

</html>