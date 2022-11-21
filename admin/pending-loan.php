<?php
$pageTitle = "Admin Dashboard &mdash; Pending Loans";
require_once("includes/header.php");
?>

<h2>
    Pending Loans
</h2>

<div class="table-responsive">
    <?= viewPendingLoans(); ?>
</div>

<?php
require_once("includes/footer.php");
