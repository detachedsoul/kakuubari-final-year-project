<?php
$pageTitle = "Admin Dashboard &mdash; Borrowers";
require_once("includes/header.php");
?>

<h2>
    Borrower's List
</h2>

<?= viewBorrowers(); ?>

<?php
require_once("includes/footer.php");
