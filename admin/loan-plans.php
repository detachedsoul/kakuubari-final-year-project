<?php
$pageTitle = "Admin Dashboard &mdash; Loan Plans";
require_once("includes/header.php");
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h2>
        Loan Plans
    </h2>

    <button class="btn btn-primary" href="#" data-toggle="modal" data-target="#addNewPlan" type="btn">
        Add New Loan Plan
    </button>
</div>

<!-- Add new loan plan modal -->
<div class="modal fade" id="addNewPlan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?= addNewLoanPlan() ?>
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="loanPlan" class="form-label">Plan</label>
                        <input type="text" class="form-control" id="loanPlan" aria-describedby="loanPlan" name="loanPlan">
                    </div>
                    <div class="mb-3">
                        <label for="interestRate" class="form-label">Interest Rate</label>
                        <input type="number" min="1" class="form-control" id="interestRate" name="interestRate">
                    </div>
                    <div class="mb-3">
                        <label for="overduePenalty" class="form-label">Overdue Penalty</label>
                        <input type="number" min="1" class="form-control" id="overduePenalty" name="overduePenalty">
                    </div>

                    <button type="submit" class="btn btn-primary" name="addNewPlan">Add New Plan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <?= viewLoanPlan(); ?>
</div>


<?php
require_once("includes/footer.php");
