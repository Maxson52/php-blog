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
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $matchingEmails = "SELECT * FROM `users` WHERE `email`='$email'";
    $res = mysqli_query($conn, $matchingEmails) or die("Query failed: " . mysqli_error($conn));

    $validate = validateAll($name, $email, $password);
    if (isset($validate) && is_string($validate)) {
        $error = $validate;
    } else if (mysqli_num_rows($res) > 0) {
        $error = "Account with that email already exists";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertQuery = "INSERT INTO `users` (`name`, `email`, `password`) VALUES ('$name', '$email', '$hashedPassword')";

        mysqli_query($conn, $insertQuery) or die("Query Error: " . mysqli_error($conn));

        header("Location: log-in.php");
    }

    $_POST = [];
}

function validateAll($name, $email, $password)
{
    if (strlen($name) < 2 || !preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        return "Invalid name";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email";
    }
    if (strlen($password) < 5) {
        return "Your password must contain at least 5 characters";
    }

    return null;
}
?>
<html>

<head>
    <title>Create account</title>
    <link rel="icon" href="../lib/assets/strawberry.png" />
    <link href="../lib/css/output.css" rel="stylesheet" />
</head>

<body>
    <section class="grid w-screen min-h-screen bg-gradient-to-r from-indigo-300 to-purple-400 place-items-center">
        <div class="flex flex-col gap-4 p-12 bg-white rounded-xl drop-shadow-md min-w-[50%] max-w-6xl">
            <h1 class="h1">Create account</h1>
            <p>Already have an account? <a class="link" href="./log-in.php">Log in</a></p>

            <p class="text-red-500"><?php echo $error ?></p>

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="flex flex-col gap-2">
                <input class="text-input" type="text" name="name" placeholder="Enter your name" required autofocus>

                <input class="text-input" type="email" name="email" placeholder="Enter your email" required>

                <input class="text-input" type="password" name="password" placeholder="Enter your password" required minlength="5">

                <input class="button" type="submit" name="submit" value="Create account" />
            </form>
        </div>
    </section>
</body>

</html>