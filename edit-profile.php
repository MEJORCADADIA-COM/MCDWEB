<?php require_once "inc/header.php"; ?>

<?php
Session::checkSession();
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
	$full_name = $fm->validation($_POST['name']);
  	$description = $fm->validation($_POST['description']);
  
  	$permited = array('jpg', 'jpeg', 'png', 'gif');
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_exp = explode(".", $file_name);
    $file_ext = strtolower(end($file_exp));
    $image = substr(md5(time()), 0, 10) . '.' . $file_ext;
  
  	if ($file_name) {
      $image_name = 'https://mejorcadadia.com/uploads/users/' . $image;
      $result = $common->update("`users`", "`full_name` = '$full_name', `description` = '$description', `image` = '$image_name'", "`id` = '$user_id'");
      if ($result) {
        move_uploaded_file($file_tmp, 'uploads/users/' . $image);
        header("Location: https://mejorcadadia.com/profile.php");
        $update_msg = '<div class="alert alert-success mb-0">Profile updated successfully.</div>';
      } else {
        $update_msg = '<div class="alert alert-success mb-0">Something is wrong!</div>';
      }
    } else {
      $result = $common->update("`users`", "`full_name` = '$full_name', `description` = '$description'", "`id` = '$user_id'");
      if ($result) {
        header("Location: https://mejorcadadia.com/profile.php");
        $update_msg = '<div class="alert alert-success mb-0">Profile updated successfully.</div>';
      } else {
        $update_msg = '<div class="alert alert-success mb-0">Something is wrong!</div>';
      }
    }
}
?>	

<form id="change-profile-form" action="" method="POST" enctype="multipart/form-data">
    <div class="change-profile-group">
        <div class="preview_image">
            <img src="<?= $user_infos['image'] != NULL ? $user_infos['image'] : 'https://s3-us-west-2.amazonaws.com/harriscarney/images/150x150.png'; ?>" name="image" alt="profile image"
                id="profile-preview-image">
            <div class="change_photo">
                <input type="file" id="upload-new-image" accept="image/png,image/jpg" name="image" onchange="loadFile(event)"
                    hidden />
                <label for="upload-new-image">Change Profile</label>
            </div>
        </div>

    </div>
    <div class="change-profile-group">
        <label for="newUserName">Name</label>
        <input class="username" id="newUserName" type="text" name="name" value="<?= $user_infos['full_name']; ?>" required="">
    </div>
    <div class="change-profile-group">
        <label for="newUserName">Description</label>
        <textarea name="description" id="newDescription" rows="10" required=""><?= $user_infos['description']; ?></textarea>
    </div>
  <div class="btn-group">
            <a href="/profile" class="profile_edit_btn bg-danger text-light me-2">cancel</a>
            <input type="submit" id="submit-new-details" name="update_profile" value="Update">
  </div>
    
</form>
<script>
        var loadFile = function (event) {
            var output = document.getElementById('profile-preview-image');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function () {
                URL.revokeObjectURL(output.src) // free memory
            }
        };
    </script>
<?php require_once "inc/footer.php"; ?>