<?php
$pageTitle = "User Dashboard &mdash; Rejected Loans";
require_once("includes/header.php");
?>

<h2>
    Rejected Loan Requests
</h2>

<?=
viewRejectedLoans();
?>

<?php
require_once("includes/footer.php");
