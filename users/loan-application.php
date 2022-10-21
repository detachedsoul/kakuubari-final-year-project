<?php
$pageTitle = "User Dashboard &mdash; Loan Application";
require_once("includes/header.php");
?>

<h2>
    Apply For A New Loan
</h2>

<div>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>
                    <?= applyForLoan(); ?>
                </h5>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="loanType" class="form-label">Loan Type</label>
                        <select class="form-control" name="loanType">
                            <?= getLoanTypes() ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="loanPlan" class="form-label">Loan Plan</label>
                        <select class="form-control" name="loanPlan">
                            <?= getLoanPlans() ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1">
                    </div>

                    <button type="submit" class="btn btn-primary" name="applyForLoan">Apply For Loan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once("includes/footer.php");
