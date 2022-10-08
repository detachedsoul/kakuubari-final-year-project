<?php
$pageTitle = "Admin Dashboard &mdash; Borrowers";
require_once("includes/header.php");
?>

<h2>
    Borrower's List
</h2>

<table class="table mt-4 table-hover table-striped">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Loan ID</th>
            <th scope="col">Full Name</th>
            <th scope="col">Loan Plan</th>
            <th scope="col">Amount</th>
            <th scope="col">Repayment Date</th>
            <th scope="col">Action</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <th scope="row">1</th>
            <th scope="row">52</th>
            <td>Adoobe Kakuubari Believe</td>
            <td>3 years Plan</td>
            <td>₦200, 000</td>
            <td>12 September, 2022</td>
            <td>
                <a href="/admin/set-paid.php?id=1">
                    Set as paid
                </a>
            </td>
        </tr>
    </tbody>
</table>

<?php
require_once("includes/footer.php");
