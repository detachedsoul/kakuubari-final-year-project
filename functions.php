<?php
session_start();
ob_start();
require_once("includes/db.php");

// Registers a new user
function registerUser () {
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
function login ()
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

                // header("Refresh: 5; users/");
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
