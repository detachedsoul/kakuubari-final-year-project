<?php
session_start();
ob_start();
require_once("includes/db.php");

// Registers a new user
function registerUser()
{
    $con = dbConnect();

    if (isset($_POST['register'])) {
        $name = ucwords($_POST['fullname']);
        $email = strtolower($_POST['email']);
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($email) && !empty($username) && !empty($password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);

            // First check if a user already exists for the choosen username or email
            $sql = "SELECT id FROM users WHERE name = ? OR username = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "ss",
                $name,
                $username
            );
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<p class='text-danger'>Username or password already taken. Please choose another one</p>";

                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<p class='text-danger'>Incorrect email format</p>";
            } else {
                $sql = "INSERT INTO users (`name`, `email`, `username`, `password`) VALUES(?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param(
                    "ssss",
                    $name,
                    $email,
                    $username,
                    $password
                );
                $stmt->execute();

                echo "<p class='text-success'>Registration successful.</p>";
                header("Refresh: 5; users/");
            }
        } else {
            echo "<p class='text-danger'>All fields are required</p>";
        }
    } else {
        echo "Please sign in";
    }
}

// Logs a user in if the provided details are valid
function login()
{
    $con = dbConnect();

    if (isset($_POST['login'])) {
        $usernamePassword = strtolower($_POST['usernamePassword']);
        $password = $_POST['password'];

        $sql = "SELECT `password`, `name` FROM users WHERE username = ? OR email = ?";
        $login = $con->prepare($sql);
        $login->bind_param(
            "ss",
            $usernamePassword,
            $usernamePassword
        );
        $login->execute();
        $result = $login->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $userPassword = $row->password;
                $userFullName = $row->name;
            }

            if (password_verify($password, $userPassword)) {
                echo "<p class='text-success'>Login successful.</p>";
                $_SESSION['userFullName'] = $userFullName;

                header("Refresh: 5; users/");
            } else {
                echo "<p class='text-danger'>Incorrect username, email or password</p>";
            }
        } else {
            echo "<p class='text-danger'>Record not found</p>";
        }
    } else {
        echo "Please sign in";
    }
}

// Reset's a user's password
function resetPassword()
{
    $con = dbConnect();

    if (isset($_POST['reset-password'])) {
        $usernamePassword = $_POST['usernamePassword'];

        // Check if the username or email exists
        $sql = "SELECT `email` FROM users WHERE email = ? OR username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "ss",
            $usernamePassword,
            $usernamePassword
        );
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows < 1) {
            echo "<p class='text-danger'>No record found</p>";
        } else {
            $newPassword = mt_rand(1, 99999);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $sql = "UPDATE `users` SET `password` = ? WHERE email = ? OR username = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param(
                "sss",
                $hashedPassword,
                $usernamePassword,
                $usernamePassword
            );
            $stmt->execute();

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: Adoobe Kakuubari Believe kakuubari1@gmail.com";

            if (mail($result->fetch_object()->email, "Password Reset", "Your new password is {$newPassword}", $headers)) {
                echo "<p class='text-success'>Your new password has been sent to {$result->fetch_object()->email}</p>";
            } else {
                echo "<p class='text-danger'>There was a problem resetting your password, please try again later.</p>";
            }
        }
    } else {
        echo "Reset your password";
    }
}
