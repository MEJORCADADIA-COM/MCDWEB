<?php require_once "inc/header.php"; ?>
<?php
if(isset($_GET['block'])) {
  $block_id = $_GET['block'];
  $block_update = $common->update("`users`", "`status` = '0'", "`id` = '$block_id'");
  if($block_update) {
    header("Location: https://mejorcadadia.com/admin");
  }
} elseif(isset($_GET['unblock'])) {
  $unblock_id = $_GET['unblock'];
  $unblock_update = $common->update("`users`", "`status` = '1'", "`id` = '$unblock_id'");
  if($unblock_update) {
    header("Location: https://mejorcadadia.com/admin");
  }
} elseif(isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];
  $user_delete = $common->delete("`users`", "`id` = '$delete_id'");
  if($user_delete) {
    header("Location: https://mejorcadadia.com/admin");
  }
}
?>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3">
      <div class="projects mb-4">
        <div class="projects-inner">
          <header class="projects-header">
            <div class="title">Current Users</div>
            <i class="zmdi zmdi-download"></i>
          </header>
          <table class="projects-table">
            <thead>
              <tr>
                <th>Join at</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <?php
            $all_users = $common->select("`users`");
            if($all_users) {
              while($all_user = mysqli_fetch_assoc($all_users)) {
            ?>
            <tr>
              <td>
                <p><?= $fm->dateFormat($all_user['created_at']); ?></p>
                <p><?= ucfirst($all_user['type']); ?></p>
              </td>
              <td class="member">
                <figure><img src="<?= $all_user['image'] != NULL ? $all_user['image'] : 'https://s3-us-west-2.amazonaws.com/s.cdpn.io/584938/people_8.png'; ?>" />
                </figure>
                <div class="member-info">
                  <p><?= ucwords($all_user['full_name']); ?></p>
                </div>
              </td>
              <td>
                <p><?= $all_user['gmail']; ?></p>
              </td>
              <td class="status">
                <?php
                if($all_user['status'] == 1) {
                ?>
                <span class="status-text status-orange">Active</span>
                <?php
                } else {
                ?>
                <span class="status-text status-red text-danger">Blocked</span>
                <?php
                }
                ?>
              </td>
              <td class="text-center">
                <?php
                if($all_user['status'] == 1) {
                ?>
                <a href="https://mejorcadadia.com/admin/users?block=<?= $all_user['id']; ?>" class="btn btn-info" onclick="return confirm('Are you sure to block?');">Block</a>
                <?php
                } else {
                ?>
                <a href="https://mejorcadadia.com/admin/users?unblock=<?= $all_user['id']; ?>" class="btn btn-success" onclick="return confirm('Are you sure to unblock?');">Unblock</a>
                <?php
                }
                ?>
                <a href="https://mejorcadadia.com/admin/users?delete=<?= $all_user['id']; ?>" onclick="return confirm('Are you sure to delete?');" class="btn btn-danger ms-2">Delete</a>
              </td>
            </tr>
            <?php
              }
            } else {

            }
            ?>
          </table>
        </div>
      </div>
    </main>
<?php require_once "inc/footer.php"; ?>