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
                header("Refresh: 5; /");
            }
        } else {
            echo "<p class='text-danger'>All fields are required</p>";
        }
    } else {
        echo "Please sign in";
    }
}

function login($table, $redirectTo)
{
    $con = dbConnect();

    if (isset($_POST['login'])) {
        $usernamePassword = strtolower($_POST['usernamePassword']);
        $password = $_POST['password'];

        $sql = "SELECT `id`, `password`, `name` FROM {$table} WHERE username = ? OR email = ?";
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
                $id = $row->id;
                $userPassword = $row->password;
                $userFullName = $row->name;
            }

            if (password_verify($password, $userPassword)) {
                echo "<p class='text-success'>Login successful.</p>";
                $_SESSION['userFullName'] = $userFullName;
                $_SESSION['id'] = $id;

                header("Refresh: 3; {$redirectTo}");
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

    approveLoan();
    declineLoan();

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
                <th scope="col">Loan Type</th>
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
                    <td>₦ <?= number_format($row->amount) ?></td>
                    <td class="d-flex w-100 justify-content-between">
                        <a href="/admin/pending-loan.php?approve=true&id=<?= $row->id ?>&user_id=<?= $row->user_id ?>">
                            Approve Loan
                        </a>

                        <a href="/admin/pending-loan.php?decline=true&id=<?= $row->id ?>&user_id=<?= $row->user_id ?>">
                            Decline Loan
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

    $sql = "SELECT * FROM loan WHERE `status` = 'paid'";
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
                    <td>₦ <?= number_format($row->amount) ?></td>
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

    $sql = "SELECT * FROM loan WHERE status = 'debtor'";
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
                    <td>₦ <?= number_format($row->amount) ?></td>
                    <td><?= $row->date ?></td>
                    <td>
                        <a href="/admin/borrowers.php?set-paid=true&loanID=<?= $row->id ?>&id=<?= $row->user_id ?>">
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
                        <a href="/admin/edit-loan-type.php?editByID=<?= $row->id ?>">
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
            <div id="edit" aria-labelledby="exampleModalLabel">
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

function updateLoanType()
{
    $con = dbConnect();
    $id = returnEditID();
    $message = "Update Loan Type";

    $sql = "SELECT loan_type, interest_rate, `description` FROM loan_type WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows < 1) {
        echo "<p class='text-danger h1 text-center'>Loan type not found</p>";

        header("Refresh: 3, /admin/loan-types.php");
    } else {
        while ($row = $res->fetch_object()) : ?>
            <div id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <label for="loanType" class="form-label">Loan Type</label>
                                    <input type="text" class="form-control" id="loanType" aria-describedby="loanType" name="loanType" value="<?= $row->loan_type ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="interestRate" class="form-label">Interest Rate</label>
                                    <input type="number" min="1" class="form-control" id="interestRate" name="interestRate" value="<?= $row->interest_rate ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description"><?= $row->description ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary" name="updateType">Update Loan Type</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        endwhile;
        if (isset($_POST['updateType'])) {
            $loanType = ucwords($_POST['loanType']);
            $interestRate = $_POST['interestRate'];
            $description = $_POST['description'];

            if (empty($_POST['loanType']) || empty($_POST['interestRate']) || empty($_POST['description'])) {
                $message = "<span class='text-danger'>Please fill all the fields</span>";

                return;
            } else {
                $sql = "UPDATE loan_type SET loan_type = ?, interest_rate = ?, `description` = ? WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('ssss', $loanType, $interestRate, $description, $id);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $message = "<p class='text-success '>Loan type updated successfully</p>";

                    header("Location: /admin/loan-types.php");
                } else {
                    $message = "<p class='text-danger'>Loan plan not updated. Please try again</p>";
                }
            }
        }
    }
}

function getTotalBorrowers()
{
    $con = dbConnect();
    $sql = "SELECT id FROM loan WHERE status = 'debtor'";
    $stmt = $con->query($sql);

    return $stmt->num_rows;
}

function getTotalPendingRequest()
{
    $con = dbConnect();
    $sql = "SELECT id FROM loan WHERE status = 'pending'";
    $stmt = $con->query($sql);

    return $stmt->num_rows;
}

function getTotalExpectedFunds()
{
    $con = dbConnect();
    $sql = "SELECT amount FROM loan WHERE status = 'debtor'";
    $stmt = $con->query($sql);
    $total = 0;

    while ($row = $stmt->fetch_object()) {
        $total = $row->amount + $total;
    }

    return $total;
}

function approveLoan()
{
    $con = dbConnect();
    if (isset($_GET['approve']) && isset($_GET['id']) && isset($_GET['user_id'])) {
        if ($_GET['approve'] === 'true') {
            $id = $_GET['id'];
            $userID = $_GET['user_id'];

            $sql = "SELECT loan_plan FROM loan WHERE id = ? AND user_id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $id, $userID);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows < 1) {
                header("Location: /admin/pending-loan.php");
            } else {
                $repaymentDate = date('D jS M, Y', strtotime("+ {$res->fetch_object()->loan_plan}"));

                $sql = "UPDATE loan SET status = 'debtor', `date` = '{$repaymentDate}' WHERE id = ? AND user_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ss", $id, $userID);
                $stmt->execute();
                $stmt->get_result();

                if ($stmt->affected_rows > 0) {
                    echo "<p class='text-success h3'>Loan approved successfully.</p>";

                    header("Refresh: 3, /admin/pending-loan.php");
                } else {
                    echo "<p class='text-danger h3'>There was problem approving this loan. Please again later.</p>";
                }
            }
        } else {
            header("Location: /admin/pending-loan.php");
        }
    }
}

function declineLoan()
{
    $con = dbConnect();
    if (isset($_GET['decline']) && isset($_GET['id']) && isset($_GET['user_id'])) {
        if ($_GET['decline'] === 'true') {
            $id = $_GET['id'];
            $userID = $_GET['user_id'];

            $sql = "SELECT loan_plan FROM loan WHERE id = ? AND user_id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $id, $userID);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows < 1) {
                header("Location: /admin/pending-loan.php");
            } else {
                $sql = "UPDATE loan SET status = 'rejected' WHERE id = ? AND user_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ss", $id, $userID);
                $stmt->execute();
                $stmt->get_result();

                if ($stmt->affected_rows > 0) {
                    echo "<p class='text-success h3'>Loan rejected successfully.</p>";

                    header("Refresh: 3, /admin/pending-loan.php");
                } else {
                    echo "<p class='text-danger h3'>There was problem rejecting this loan. Please again later.</p>";
                }
            }
        } else {
            header("Location: /admin/pending-loan.php");
        }
    }
}

function updatePaidStatus()
{
    $con = dbConnect();
    if (isset($_GET['set-paid']) && isset($_GET['loanID']) && isset($_GET['id'])) {
        if ($_GET['set-paid'] === 'true') {
            $loanID = $_GET['loanID'];
            $id = $_GET['id'];

            $sql = "SELECT user_id FROM loan WHERE id = ? AND user_id = ? AND `status` = 'debtor'";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $loanID, $id);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows < 1) {
                header("Location: /admin/borrowers.php");
            } else {
                $sql = "UPDATE loan SET status = 'paid' WHERE id = ? AND user_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ss", $loanID, $id);
                $stmt->execute();
                $stmt->get_result();

                if ($stmt->affected_rows > 0) {
                    echo "<p class='text-success h3'>Loan paid successfully.</p>";

                    header("Refresh: 3, /admin/borrowers.php");
                } else {
                    echo "<p class='text-danger h3'>There was problem paying this loan. Please again later.</p>";
                }
            }
        } else {
            header("Location: /admin/borrowers.php");
        }
    }
}

function changePassword($table)
{
    $con = dbConnect();
    if (isset($_POST['changePassword'])) {
        $oldPassword = $_POST['oldPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $message = "<p class='text-danger'>Please fill all the fields</p>";
        } else {
            $sql = "SELECT password FROM `{$table}` WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $_SESSION['id']);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows < 1) {
                $message = "<p class='text-danger'>There was problem changing your password. Please try again later.</p>";
            } else {
                $row = $res->fetch_object();

                if (password_verify($oldPassword, $row->password)) {
                    if ($newPassword === $confirmPassword) {
                        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                        $sql = "UPDATE `{$table}` SET password = ? WHERE id = ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("ss", $newPassword, $_SESSION['id']);
                        $stmt->execute();

                        if ($stmt->affected_rows > 0) {
                            $message = "<p class='text-success'>Password changed successfully.</p>";
                        } else {
                            $message = "<p class='text-danger'>There was problem changing your password. Please try again later.</p>";
                        }
                    } else {
                        $message = "<p class='text-danger'>New password and confirm password does not match.</p>";
                    }
                } else {
                    $message = "<p class='text-danger'>Old password is incorrect.</p>";
                }
            }
        }
    } else {
        $message = "<p>Change Password</p>";
    }

    echo $message;
}

function logOut($redirectTo)
{
    unset($_SESSION['id']);
    unset($_SESSION['userFullName']);
    $_SESSION['id'] = null;
    $_SESSION['userFullName'] = null;
    session_destroy();
    header("Location: {$redirectTo}");
}

function viewRejectedLoans()
{
    $con = dbConnect();
    $sql = "SELECT * FROM loan WHERE status = 'rejected' AND user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $_SESSION['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows < 1) {
        echo "<p class='text-danger h2 text-center mt-5'>No rejected loan found.</p>";

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
                <th scope="col">Status</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $res->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->user_id ?></td>
                    <td><?= $row->name ?></td>
                    <td><?= $row->loan_type ?></td>
                    <td><?= $row->loan_plan ?></td>
                    <td>₦ <?= number_format($row->amount) ?></td>
                    <td class="text-danger"><?= ucfirst($row->status) ?></td>
                </tr>
            <?php endwhile;
            ?>
        </tbody>
    </table>
<?php
}

function viewOutstandingLoans()
{
    $con = dbConnect();
    $sql = "SELECT * FROM loan WHERE status = 'debtor' AND user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $_SESSION['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows < 1) {
        echo "<p class='text-danger h2 text-center mt-5'>No outstanding loan found.</p>";

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
                <th scope="col">Status</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $res->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->user_id ?></td>
                    <td><?= $row->name ?></td>
                    <td><?= $row->loan_type ?></td>
                    <td><?= $row->loan_plan ?></td>
                    <td>₦ <?= number_format($row->amount) ?></td>
                    <td><?= $row->date ?></td>
                    <td class="text-danger"><?= ucfirst($row->status) ?></td>
                </tr>
            <?php endwhile;
            ?>
        </tbody>
    </table>
<?php
}

function userViewPaymentHistory()
{
    $con = dbConnect();
    $sql = "SELECT * FROM loan WHERE status = 'paid' AND user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $_SESSION['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows < 1) {
        echo "<p class='text-danger h2 text-center mt-5'>No payment history  found.</p>";

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
                <th scope="col">Status</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $res->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->user_id ?></td>
                    <td><?= $row->name ?></td>
                    <td><?= $row->loan_type ?></td>
                    <td><?= $row->loan_plan ?></td>
                    <td>₦ <?= number_format($row->amount) ?></td>
                    <td class="text-success"><?= ucfirst($row->status) ?></td>
                </tr>
            <?php endwhile;
            ?>
        </tbody>
    </table>
<?php
}


function userViewPendingLoan()
{
    $con = dbConnect();
    $sql = "SELECT * FROM loan WHERE status = 'pending' AND user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $_SESSION['id']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows < 1) {
        echo "<p class='text-danger h2 text-center mt-5'>No pending loan found.</p>";

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
                <th scope="col">Status</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = $res->fetch_object()) : ?>
                <tr>
                    <td><?= $row->id ?></td>
                    <td><?= $row->user_id ?></td>
                    <td><?= $row->name ?></td>
                    <td><?= $row->loan_type ?></td>
                    <td><?= $row->loan_plan ?></td>
                    <td>₦ <?= number_format($row->amount) ?></td>
                    <td class="text-danger"><?= ucfirst($row->status) ?></td>
                </tr>
            <?php endwhile;
            ?>
        </tbody>
    </table>
    <?php
}

function getLoanTypes()
{
    $con = dbConnect();
    $sql = "SELECT loan_type FROM loan_type";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows < 1) {
        echo "<p class='text-danger h2 text-center mt-5'>No loan type found.</p>";

        return;
    } else {
        while ($row = $res->fetch_object()) : ?>
            <option value="<?= $row->loan_type ?>" name="loanType"><?= $row->loan_type ?></option>

        <?php
        endwhile;
    }
}

function getLoanPlans()
{
    $con = dbConnect();
    $sql = "SELECT plan FROM loan_plan";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows < 1) {
        echo "<p class='text-danger h2 text-center mt-5'>No loan plan found.</p>";

        return;
    } else {
        while ($row = $res->fetch_object()) : ?>
            <option value="<?= $row->plan ?>" name="loanPlan"><?= $row->plan ?></option>

<?php
        endwhile;
    }
}

function applyForLoan()
{
    $con = dbConnect();

    if (isset($_POST['applyForLoan'])) {
        $userID = $_SESSION['id'];
        $name = $_SESSION['userFullName'];
        $loanType = $_POST['loanType'];
        $loanPlan = $_POST['loanPlan'];
        $amount = $_POST['amount'];

        // Check if all fields were filled
        $fields = [
            $loanType,
            $loanPlan,
            $amount,
        ];
        foreach ($fields as $field) {
            if (empty($field)) {
                echo "<span class='text-danger h2'>All fields are required.</span>";

                return;
            }
        }

        // Get the user's BVN
        $sql = "SELECT bvn FROM users WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $res = $stmt->get_result();
        $bvn = $res->fetch_object()->bvn;

        // Get percentage for the selected loan type and add it to the amount
        $sql = "SELECT interest_rate FROM loan_type WHERE loan_type = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $loanType);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows < 1) {
            echo "<span class='text-danger h2'>Invalid loan type.</span>";

            return;
        }

        $interestRate = $res->fetch_object()->interest_rate;
        settype($interestRate, "integer");

        $loanTypePercentage = $amount * ($interestRate / 100);

        // Get percentage for the selected loan plan and add it to the amount
        $sql = "SELECT interest_rate FROM loan_plan WHERE plan = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $loanPlan);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows < 1) {
            echo "<span class='text-danger h2'>Invalid loan plan.</span>";

            return;
        }

        $interestRate = $res->fetch_object()->interest_rate;
        settype($interestRate, "integer");


        $loanPlanPercentage =
            $amount * ($interestRate / 100);

        $totalAmount = $amount + $loanTypePercentage + $loanPlanPercentage;

        $sql = "INSERT INTO loan (user_id, name, loan_type, loan_plan, amount, bvn) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssss", $userID, $name, $loanType, $loanPlan, $totalAmount, $bvn);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p class='text-success h2'>Loan application successful.</p>";

            header("Refresh: 3, /users/pending-loan.php");
        } else {
            echo "<p class='text-danger h2'>Loan application failed.</p>";
        }
    } else {
        echo "Apply for loan.";
    }
}
