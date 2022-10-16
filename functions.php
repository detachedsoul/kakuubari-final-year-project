<?php
ob_start();
session_start();
require_once("includes/db.php");

function returnDeleteID()
{
    return $_GET['deleteByID'];
}

function returnEditID()
{
    return $_GET['editByID'];
}

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
function login($table, $redirectTo)
{
    $con = dbConnect();

    if (isset($_POST['login'])) {
        $usernamePassword = strtolower($_POST['usernamePassword']);
        $password = $_POST['password'];

        $sql = "SELECT `password`, `name` FROM {$table} WHERE username = ? OR email = ?";
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

                header("Refresh: 5; {$redirectTo}");
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
function resetPassword($table)
{
    $con = dbConnect();

    if (isset($_POST['reset-password'])) {
        $usernamePassword = $_POST['usernamePassword'];

        // Check if the username or email exists
        $sql = "SELECT `email` FROM {$table} WHERE email = ? OR username = ?";
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

            $sql = "UPDATE `{$table}` SET `password` = ? WHERE email = ? OR username = ?";
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

function viewPendingLoans()
{
    $con = dbConnect();

    $sql = "SELECT * FROM loan WHERE status = 'pending'";
    $stmt = $con->query($sql);

    if ($stmt->num_rows < 1) {
        echo "<p class='text-danger text-center h1 mt-5'>No pending loans available</p>";

        return;
    } else ?>
    <table class="table mt-4 table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Loan ID</th>
                <th scope="col">BVN</th>
                <th scope="col">Full Name</th>
                <th scope="col">Loan Plan</th>
                <th scope="col">Amount</th>
                <th scope="col">Action</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $stmt->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->user_id ?></th>
                    <td><?= $row->bvn ?></td>
                    <td><?= $row->name ?></td>
                    <td><?= $row->loan_type ?></td>
                    <td><?= $row->loan_plan ?></td>
                    <td>₦ <?= $row->amount ?></td>
                    <td>
                        <a href="/admin/pending-loan.php?approve=true&id=1">
                            Approve Loan
                        </a>
                    </td>
                </tr>
            <?php endwhile;
            ?>
        </tbody>
    </table>
<?php
}

function adminViewPaymentHistory()
{
    $con = dbConnect();

    $sql = "SELECT * FROM payments";
    $stmt = $con->query($sql);

    if ($stmt->num_rows < 1) {
        echo "<p class='text-danger text-center h1 mt-5'>No payment history available</p>";

        return;
    } else ?>
    <table class="table mt-4 table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Loan ID</th>
                <th scope="col">Full Name</th>
                <th scope="col">Loan Type</th>
                <th scope="col">Loan Plan</th>
                <th scope="col">Amount Paid</th>
                <th scope="col">Payment Date</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $stmt->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->user_id ?></td>
                    <td><?= $row->name ?></td>
                    <td><?= $row->loan_type ?></td>
                    <td><?= $row->loan_plan ?></td>
                    <td>₦ <?= $row->amount ?></td>
                    <td><?= $row->date ?></td>
                </tr>
            <?php endwhile;
            ?>
        </tbody>
    </table>
<?php
}

function viewBorrowers()
{
    $con = dbConnect();

    $sql = "SELECT * FROM loan WHERE status = 'oweing'";
    $stmt = $con->query($sql);

    if ($stmt->num_rows < 1) {
        echo "<p class='text-danger text-center h1 mt-5'>No debtor available</p>";

        return;
    } else ?>
    <table class="table mt-4 table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Loan ID</th>
                <th scope="col">Full Name</th>
                <th scope="col">Loan Type</th>
                <th scope="col">Loan Plan</th>
                <th scope="col">Amount</th>
                <th scope="col">Repayment Date</th>
                <th scope="col">Action</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $stmt->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->user_id ?></td>
                    <td><?= $row->name ?></td>
                    <td><?= $row->loan_type ?></td>
                    <td><?= $row->loan_plan ?></td>
                    <td>₦ <?= $row->amount ?></td>
                    <td><?= $row->date ?></td>
                    <td>
                        <a href="/admin/borrowes.php?set-paid=true&id=1">
                            Set as paid
                        </a>
                    </td>
                </tr>
            <?php endwhile;
            ?>
        </tbody>
    </table>
<?php
}

function viewLoanTypes()
{
    $con = dbConnect();

    $sql = "SELECT * FROM loan_type";
    $stmt = $con->query($sql);

    if ($stmt->num_rows < 1) {
        echo "<p class='text-danger text-center h1 mt-5'>No loan type available. Please use the add new loan type button above to add a new one.</p>";

        return;
    } else ?>
    <table class="table mt-4 table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Loan Type</th>
                <th scope="col">Interest Rate</th>
                <th scope="col">Loan Description</th>
                <th scope="col">Action</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $stmt->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->loan_type ?></td>
                    <td><?= $row->interest_rate ?>%</td>
                    <td><?= $row->description ?></td>
                    <td class="d-flex w-100 justify-content-between">
                        <a data-toggle="modal" data-target="#edit" href="/admin/loan-types.php?editID=<?= $row->id ?>" role="button">
                            <i class="fas fa-fw fa-edit"></i>
                        </a>

                        <a href="/admin/delete-loan-types.php?deleteByID=<?= $row->id ?>">
                            <i class="fas fa-fw fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php

            endwhile;
            ?>
        </tbody>
    </table>
<?php
}

function viewLoanPlan()
{
    $con = dbConnect();

    $sql = "SELECT * FROM loan_plan";
    $stmt = $con->query($sql);

    if ($stmt->num_rows < 1) {
        echo "<p class='text-danger text-center h1 mt-5'>No loan plan available. Please use the add new loan plan button above to add a new one.</p>";

        return;
    } else ?>
    <table class="table mt-4 table-hover table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Loan Plan</th>
                <th scope="col">Interest Rate</th>
                <th scope="col">Overdue Penalty</th>
                <th scope="col">Action</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $stmt->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->plan ?></td>
                    <td><?= $row->interest_rate ?>%</td>
                    <td><?= $row->overdue_penalty ?>%</td>
                    <td class="d-flex w-100 justify-content-between">
                        <a href="/admin/edit-loan-plan.php?editByID=<?= $row->id ?>">
                            <i class="fas fa-fw fa-edit"></i>
                        </a>

                        <a href="/admin/delete-loan-plan.php?deleteByID=<?= $row->id ?>">
                            <i class="fas fa-fw fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile;
            ?>
        </tbody>
    </table>
    <?php
}

function addNewLoanType()
{
    $con = dbConnect();
    if (isset($_POST['addNewType'])) {
        $loanType = ucwords($_POST['loanType']);
        $interestRate = $_POST['interestRate'];
        $description = $_POST['description'];

        if (empty($_POST['loanType']) || empty($_POST['interestRate']) || empty($_POST['description'])) {
            echo "<span class='text-danger'>Please fill all the fields</span>";
        } else {
            $sql = "INSERT INTO loan_type (loan_type, interest_rate, description) VALUES (?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('sss', $loanType, $interestRate, $description);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<p class='text-success '>Loan type added successfully</p>";

                header("Location: /admin/loan-types.php");
            } else {
                echo "<p class='text-danger'>Loan type not added. Please try again</p>";
            }
        }
    } else {
        echo "Add New Loan Type";
    }
}

function addNewLoanPlan()
{
    $con = dbConnect();
    if (isset($_POST['addNewPlan'])) {
        $loanPlan = ucwords($_POST['loanPlan']);
        $interestRate = $_POST['interestRate'];
        $overduePenalty = $_POST['overduePenalty'];

        if (empty($_POST['loanPlan']) || empty($_POST['interestRate']) || empty($_POST['overduePenalty'])) {
            echo "<span class='text-danger'>Please fill all the fields</span>";
        } else {
            $sql = "INSERT INTO loan_plan (plan, interest_rate, overdue_penalty) VALUES (?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('sss', $loanPlan, $interestRate, $overduePenalty);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<p class='text-success '>Loan plan added successfully</p>";

                header("Location: /admin/loan-plans.php");
            } else {
                echo "<p class='text-danger'>Loan plan not added. Please try again</p>";
            }
        }
    } else {
        echo "Add New Loan Plan";
    }
}

function deleteLoanType()
{
    if (isset($_GET['deleteByID'])) {
        $con = dbConnect();
        $id = returnDeleteID();

        $sql = "SELECT id FROM loan_type WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows < 1) {
            echo "<p class='text-danger h1 text-center'>Loan type not found</p>";

            header("Refresh: 3, /admin/loan-types.php");
        } else {
            $sql = "DELETE FROM loan_type WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('s', $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<p class='text-success h1 text-center'>Loan type deleted successfully</p>";

                header("Refresh: 3, /admin/loan-types.php");
            } else {
                echo "<p class='text-danger h1 text-center'>Loan type not deleted. Please try again</p>";

                header("Refresh: 3, /admin/loan-types.php");
            }
        }
    }
}

function deleteLoanPlan()
{
    if (isset($_GET['deleteByID'])) {
        $con = dbConnect();
        $id = returnDeleteID();

        $sql = "SELECT id FROM loan_plan WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows < 1) {
            echo "<p class='text-danger h1 text-center'>Loan plan not found</p>";

            header("Refresh: 3, /admin/loan-plans.php");
        } else {
            $sql = "DELETE FROM loan_plan WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('s', $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<p class='text-success h1 text-center'>Loan plan deleted successfully</p>";

                header("Refresh: 3, /admin/loan-plans.php");
            } else {
                echo "<p class='text-danger h1 text-center'>Loan plan not deleted. Please try again</p>";

                header("Refresh: 3, /admin/loan-plans.php");
            }
        }
    }
}

function updateLoanPlan()
{
    $con = dbConnect();
    $id = returnEditID();
    $message = "Update Loan Plan";

    $sql = "SELECT plan, interest_rate, overdue_penalty FROM loan_plan WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows < 1) {
        echo "<p class='text-danger h1 text-center'>Loan plan not found</p>";

        header("Refresh: 3, /admin/loan-plans.php");
     } else {
        while ($row = $res->fetch_object()) : ?>
                <div class="moda" id="edit" aria-labelledby="exampleModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    <?= $message ?>
                                </h5>
                            </div>
                            <div class="modal-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="plan" class="form-label">Plan Duration</label>
                                        <input type="text" class="form-control" id="plan" aria-describedby="plan" name="plan" value="<?= $row->plan ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="interestRate" class="form-label">Interest Rate</label>
                                        <input type="number" min="1" class="form-control" id="interestRate" name="interestRate" value="<?= $row->interest_rate ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="overduePenalty" class="form-label">Overdue Penalty</label>
                                        <input type="number" min="1" class="form-control" id="overduePenalty" name="overduePenalty" value="<?= $row->overdue_penalty ?>">
                                    </div>

                                    <button type="submit" class="btn btn-primary" name="updatePlan">Update Loan Plan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
    <?php
            endwhile;
        if (isset($_POST['updatePlan'])) {
            $plan = ucwords($_POST['plan']);
            $interestRate = $_POST['interestRate'];
            $overduePenalty = $_POST['overduePenalty'];

            if (empty($_POST['plan']) || empty($_POST['interestRate']) || empty($_POST['overduePenalty'])) {
                $message = "<span class='text-danger'>Please fill all the fields</span>";

                return;
            } else {
                $sql = "UPDATE loan_plan SET plan = ?, interest_rate = ?, overdue_penalty = ? WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('ssss', $plan, $interestRate, $overduePenalty, $id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $message = "<p class='text-success '>Loan plan updated successfully</p>";

                    header("Location: /admin/loan-plans.php");
                } else {
                    $message = "<p class='text-danger'>Loan plan not updated. Please try again</p>";
                }
            }
        }
    }
}
