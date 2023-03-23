<?php
session_start();

$error = "";
$userInfo;

require_once('../lib/utils/conn.php');

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $matchingEmails = "SELECT * FROM `users` WHERE `email`='$email'";
    $res = mysqli_query($conn, $matchingEmails) or die("Query failed: " . mysqli_error($conn));

    if (mysqli_num_rows($res) > 0) {
        $error = "Account with that email already exists";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertQuery = "INSERT INTO `users` (`name`, `email`, `password`) VALUES ('$name', '$email', '$hashedPassword')";

        mysqli_query($conn, $insertQuery) or die("Query Error: " . mysqli_error($conn));

        header("Location: log-in.php");
    }

    $_POST = [];
}
?>
<html>

<head>
    <title>Create account</title>
    <link href="../output.css" rel="stylesheet" />
</head>

<body>
    <section class="grid w-screen min-h-screen bg-gradient-to-r from-indigo-300 to-purple-400 place-items-center">
        <div class="flex flex-col gap-4 p-12 bg-white rounded-xl drop-shadow-md min-w-[50%] max-w-6xl">
            <h1 class="h1">Create account</h1>
            <p>Already have an account? <a class="link" href="./log-in.php">Log in</a></p>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="flex flex-col gap-2">
                <input class="text-input" type="text" name="name" placeholder="Enter your name" required>

                <input class="text-input" type="email" name="email" placeholder="Enter your email" required>

                <input class="text-input bg-orange-50" type="password" name="password" placeholder="Enter your password" required>

                <input class="button" type="submit" name="submit" value="Create account" />
            </form>
        </div>
    </section>
</body>

</html>