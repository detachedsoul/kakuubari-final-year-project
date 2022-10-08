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
                    Add New Loan Plan
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="planDuration" class="form-label">Plan Duration</label>
                        <input type="text" class="form-control" id="planDuration" aria-describedby="planDuration" name="planDuration">
                    </div>
                    <div class="mb-3">
                        <label for="interestRate" class="form-label">Interest Rate</label>
                        <input type="number" min="1" class="form-control" id="interestRate" name="interestRate">
                    </div>
                    <div class="mb-3">
                        <label for="overduePenalty" class="form-label">Overdue Penalty</label>
                        <input type="number" min="1" class="form-control" id="overduePenalty" name="overduePenalty">
                    </div>

                    <button type="submit" class="btn btn-primary">Add New Plan</button>
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
                    Edit Loan Plan
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="editPlanDuration" class="form-label">Plan Duration</label>
                        <input type="text" class="form-control" id="editPlanDuration" aria-describedby="editPlanDuration" name="editPlanDuration">
                    </div>
                    <div class="mb-3">
                        <label for="editInterestRate" class="form-label">Interest Rate</label>
                        <input type="number" min="1" class="form-control" id="editInterestRate" name="editInterestRate">
                    </div>
                    <div class="mb-3">
                        <label for="editOverduePenalty" class="form-label">Overdue Penalty</label>
                        <input type="number" min="1" class="form-control" id="editOverduePenalty" name="editOverduePenalty">
                    </div>

                    <button type="submit" class="btn btn-primary">Edit New Plan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete loan plan modal-->
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Are you sure you want to delete?
                </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                This action cannot be undone
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger" href="/admin/loan-plan.php?deleteByID=1">
                    Delete
                </a>
            </div>
        </div>
    </div>
</div>

<table class="table mt-4 table-hover table-striped">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Plan</th>
            <th scope="col">Interest</th>
            <th scope="col">Overdue Penalty</th>
            <th scope="col">Action</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <th scope="row">1</th>
            <th scope="row">3 years</th>
            <td>5%</td>
            <td>7%</td>
            <td class="d-flex w-100 justify-content-between">
                <a data-toggle="modal" data-target="#edit" href="#" role="button">
                    <i class="fas fa-fw fa-edit"></i>
                </a>

                <a data-toggle="modal" data-target="#delete" href="#" role="button">
                    <i class="fas fa-fw fa-trash"></i>
                </a>
            </td>
        </tr>
    </tbody>
</table>

<?php
require_once("includes/footer.php");
