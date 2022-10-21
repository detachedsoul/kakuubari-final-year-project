<?php
$pageTitle = "User Dashboard &mdash; Settings";
require_once("includes/header.php");
?>

<div>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>
                    <?= changePassword("users") ?>
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

<?php if (!checkBVNStatus()) : ?>

    <div id="bvn">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>
                        <?= updateBVN() ?>
                    </h5>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="bvn" class="form-label">BVN</label>
                            <input type="number" class="form-control" id="bvn" aria-describedby="bvn" name="bvn">
                        </div>

                        <button type=" submit" class="btn btn-primary" name="updateBVN">Update BVN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
endif;
require_once("includes/footer.php");
