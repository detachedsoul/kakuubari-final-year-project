<?php
$pageTitle = "Admin Dashboard &mdash; Payment History";
require_once("includes/header.php");
?>

<h2>
    Payment History
</h2>

<?= adminViewPaymentHistory(); ?>

<?php
require_once("includes/footer.php");
