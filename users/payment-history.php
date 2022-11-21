<?php
$pageTitle = "User Dashboard &mdash; Payment History";
require_once("includes/header.php");
?>

<h2>
    View Payments History
</h2>

<div class="table-responsive">
    <?= userViewPaymentHistory(); ?>
</div>

<?php
require_once("includes/footer.php");
