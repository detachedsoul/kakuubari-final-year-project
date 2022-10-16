<?php
$pageTitle = "Admin Dashboard &mdash; Loan Types";
require_once("includes/header.php");
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2>
        Loan Types
    </h2>

    <button class="btn btn-primary" href="#" data-toggle="modal" data-target="#addNewType" type="btn">
        Add New Loan Type
    </button>
</div>

<!-- Add new loan plan modal -->
<div class="modal fade" id="addNewType" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?= addNewLoanType() ?>
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="loanType" class="form-label">Loan Type</label>
                        <input type="text" class="form-control" id="loanType" aria-describedby="loanType" name="loanType">
                    </div>
                    <div class="mb-3">
                        <label for="interestRate" class="form-label">Interest Rate</label>
                        <input type="number" min="1" class="form-control" id="interestRate" name="interestRate">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" name="addNewType">Add New Type</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit loan plan modal -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Edit Loan Type
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="editloanType" class="form-label">Loan Type</label>
                        <input type="text" class="form-control" id="editloanType" aria-describedby="editloanType" name="editloanType">
                    </div>
                    <div class="mb-3">
                        <label for="editInterestRate" class="form-label">Interest Rate</label>
                        <input type="number" min="1" class="form-control" id="editInterestRate" name="editInterestRate">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <input type="number" min="1" class="form-control" id="editDescription" name="editDescription">
                    </div>

                    <button type="submit" class="btn btn-primary">Edit New Plan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= viewLoanTypes(); ?>

<?php
require_once("includes/footer.php");
