<?php
$pageTitle = "Admin Dashboard &mdash; Payment History";
require_once("includes/header.php");
?>

<h2>
    Payment History
</h2>

<table class="table mt-4 table-hover table-striped">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Loan ID</th>
            <th scope="col">Full Name</th>
            <th scope="col">Loan Type</th>
            <th scope="col">Amount Paid</th>
            <th scope="col">Payment Date</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <th scope="row">1</th>
            <th scope="row">52</th>
            <td>Adoobe Kakuubari Believe</td>
            <td>Mortage Plan</td>
            <td>₦200, 000</td>
            <td>12 September, 2022</td>
        </tr>
    </tbody>
</table>

<?php
require_once("includes/footer.php");
