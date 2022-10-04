<!DOCTYPE html>
<html lang="en" class="index-html">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register a free account</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>

<body class="index-body text-center">

    <main class="form-signin w-100 m-auto">
        <form method="POST">
            <h1 class="h3 mb-3 fw-normal">Create an account today</h1>

            <div class="form-floating">
                <input type="text" class="form-control" id="username" placeholder="name@example.com">
                <label for="username">Username</label>
            </div>

            <div class="form-floating">
                <input type="email" class="form-control" id="email" placeholder="name@example.com">
                <label for="email">Email address</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" placeholder="Password">
                <label for="password">Password</label>
            </div>

            <p>
                Already have an account? <a class="link-primary text-decoration-none" href="index.php">Login instead</a>
            </p>

            <p>
                Forgot password? <a class="link-primary text-decoration-none" href="reset-password.php">Reset your password</a>
            </p>

            <button class="w-100 btn btn-lg btn-primary" type="submit" name="sign-up">Sign up</button>
            
            <p class="mt-5 mb-3 text-muted">&copy; <?= date('Y') ?>. All rights reserved.</p>
        </form>
    </main>

    <script src="assets/js/bootstrap.min.js"></script>
</body>

</html>