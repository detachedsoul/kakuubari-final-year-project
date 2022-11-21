<?php
$pageTitle = "User Dashboard &mdash; Oustanding Loans";
require_once("includes/header.php");
?>

<h2>
    View Oustanding Loans
</h2>

<div class="table-responsive">
    <?= viewOutstandingLoans(); ?>
</div>

<?php
require_once("includes/footer.php");
