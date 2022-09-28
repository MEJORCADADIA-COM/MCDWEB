<?php require_once "inc/header.php"; ?>

<?php
Session::checkSession();
if(isset($_GET['logout'])) {
	Session::destroy();
}
?>
<div class="sign_out">
  <a href="https://mejorcadadia.com/users/index.php" style="background-color: #1e01ff;" class=" me-4">Home</a>
  <a href="https://blog.mejorcadadia.com/" style="background-color: #FF007A;" class=" me-4">Blog</a>
  <a href="https://mejorcadadia.com/users/logout.php" onclick="return confirm('Are you sure to logout?');">Sign out</a>
</div>
<div id="Profile">
  <img class="profile_image" src="<?= $user_infos['image'] != NULL ? $user_infos['image'] : 'https://s3-us-west-2.amazonaws.com/harriscarney/images/150x150.png'; ?>" />
  <h3 class="user_name"><?= $user_infos['full_name']; ?></h3>
  <span class="id"><?= $user_infos['gmail']; ?></span>
  
  <p class="description"><?= $user_infos['description']; ?></p>

  <a href="https://mejorcadadia.com/users/edit-profile.php" class="profile_edit_btn">Edit profile</a>
</div>
<?php require_once "inc/footer.php"; ?>