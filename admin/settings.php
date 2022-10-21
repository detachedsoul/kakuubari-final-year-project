<?php
$pageTitle = "Admin Dashboard &mdash; Settings";
require_once("includes/header.php");
?>
<h2>
    Change Password
</h2>

<div>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>
                    <?= changePassword("admin") ?>
                </h5>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="oldPassword" class="form-label">Old Password</label>
                        <input type="password" class="form-control" id="oldPassword" aria-describedby="oldPassword" name="oldPassword">
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                    </div>

                    <button type=" submit" class="btn btn-primary" name="changePassword">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once("includes/footer.php");
