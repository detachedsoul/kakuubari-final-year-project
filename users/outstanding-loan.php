<?php
$pageTitle = "User Dashboard &mdash; Oustanding Loans";
require_once("includes/header.php");
?>

<h2>
    View Oustanding Loans
</h2>

<?=
    viewOutstandingLoans();
?>

<?php
require_once("includes/footer.php");
