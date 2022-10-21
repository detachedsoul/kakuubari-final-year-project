<?php
$pageTitle = "User Dashboard &mdash; Pending Loan";
require_once("includes/header.php");
?>

<h2>
    View Pending Loan
</h2>

<?=
    userViewPendingLoan();
?>

<?php
require_once("includes/footer.php");
