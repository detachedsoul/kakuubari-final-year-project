<?php require_once("functions.php"); ?>
<!DOCTYPE html>
<html lang="en" class="index-html">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>

<body class="index-body text-center">

    <main class="form-signin w-100 m-auto">
        <form method="POST">
            <h1 class="h3 mb-3 fw-normal">
                <?= resetPassword('users') ?>
            </h1>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="usernamePassword" placeholder="name@example.com" name="usernamePassword">
                <label for="usernamePassword">Email address or username</label>
            </div>

            <p>
                Don't have an account yet? <a class="link-primary text-decoration-none" href="register.php">Register a free account now</a>
            </p>

            <p>
                Already have an account? <a class="link-primary text-decoration-none" href="/">Login instead</a>
            </p>

            <button class="w-100 btn btn-lg btn-primary" type="submit" name="reset-password">Reset Password</button>

            <p class="mt-5 mb-3 text-muted">&copy; <?= date('Y') ?>. All rights reserved.</p>
        </form>
    </main>
</body>
</html>