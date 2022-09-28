<?php require_once "inc/header.php"; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_email_update'])) {
	$email = $fm->validation($_POST['email']);
	if(!empty($email)) {
		$email_change = $common->update("`admin`", "`email` = '$email'", "`id` = '$admin_id'");
		if ($email_change) {
			$email_error = '<div class="alert alert-success">Email update successfully.</div>';
		} else {
			$email_error = '<div class="alert alert-danger">Something is wrong!</div>';
		}
	} else {
		$email_error = '<div class="alert alert-danger">Email is required!</div>';
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_password_update'])) {
	$old_password = $_POST['old_password'];
	$new_password = $_POST['new_password'];
	$confirm_password = $_POST['confirm_password'];
	
	$password_check = $common->select("`admin`", "`password` = '$old_password' && `id` = '$admin_id'");
	if ($password_check) {
		if ($new_password == $confirm_password) {
			$password_change = $common->update("`admin`", "`password` = '$new_password'", "`id` = '$admin_id'");
			if ($password_change) {
              	Session::adminDestroy();
				$confirm_password_error = '<div class="alert alert-success">Password update successfully.</div>';
			} else {
				$confirm_password_error = '<div class="alert alert-danger">Something is wrong!</div>';
			}
		} else {
			$confirm_password_error = '<div class="alert alert-danger">Confirm password does not match!</div>';
		}
	} else {
		$old_password_error = '<div class="alert alert-danger">Old password does not match!</div>';
	}
}
?>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3">
      <div class="projects mb-4">
        <div class="projects-inner">
          <header class="projects-header">
            <div class="title">Update email and password</div>
            <i class="zmdi zmdi-download"></i>
          </header>
          <div class="col-sm-6 offset-sm-3">
          	<div class="card m-3">
              <div class="card-body">
                <form action="" method="POST">
                  <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $admin_infos['email']; ?>" placeholder="Enter email" required="">
                    <?= isset($email_error) ? $email_error : ''; ?>
                  </div>
                  <input class="btn btn-primary float-end" type="submit" name="admin_email_update" value="Update">
                </form>
              </div>
            </div>
          </div>
          <div class="col-sm-6 offset-sm-3">
          	<div class="card m-3">
              <div class="card-body">
                <form action="" method="POST">
                  <div class="mb-3">
                    <label for="old_password" class="form-label">Old password</label>
                    <input type="old_password" class="form-control" id="old_password" name="old_password" placeholder="Enter old password" required="">
                    <?= isset($old_password_error) ? $old_password_error : ''; ?>
                  </div>
                  <div class="mb-3">
                    <label for="new_password" class="form-label">New password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required="">
                  </div>
                  <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Enter confrim password" required="">
                    <?= isset($confirm_password_error) ? $confirm_password_error : ''; ?>
                  </div>
                  <input class="btn btn-primary float-end" type="submit" name="admin_password_update" value="Update">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
<?php require_once "inc/footer.php"; ?>