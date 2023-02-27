<?php require_once "inc/header.php"; ?>
<?php require_once "inc/questions.php"; ?>

<?php
Session::checkSession();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = $fm->validation($_POST['name']);
    $description = $fm->validation($_POST['description']);

    $permited = array('jpg', 'jpeg', 'png', 'gif');
    $file_name = $_FILES['image']['name'];
    $file_size = $_FILES['image']['size'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_exp = explode(".", $file_name);
    $file_ext = strtolower(end($file_exp));
    $image = substr(md5(time()), 0, 10) . '.' . $file_ext;

    $interestsToAdd = $_POST['interests'];
    if ($interestsToAdd === null || count($interestsToAdd) === 0) {
        $common->delete('interest_user', "user_id = :user_id", ['user_id' => $user_infos['id']]);
    } else {
        $placeholder = array_fill(0, count($interestsToAdd), '?');
        $common->delete("interest_user", "user_id = ? AND interest_id not in (" . implode(',', $placeholder) . ")", [$user_infos['id'], ...$interestsToAdd]);
        $existingInterests = $common->get('interest_user', "user_id = :user_id", ['user_id' => $user_infos['id']], ['interest_id']);
        $hashMap = [];
        foreach ($existingInterests as $existingInterest) {
            $hashMap[$existingInterest['interest_id']] = true;
        }
        $queryData = "";
        $data = [];
        foreach ($interestsToAdd as $key => $value) {
            if (!isset($hashMap[$value])) {
                $queryData .= ($queryData !== '' ? ',' : '');
                $queryData .= "(?,?)";
                $data[] = $value;
                $data[] = $user_infos['id'];
            }
        }
        if ($queryData !== '') {
            $db->query("insert into interest_user (interest_id, user_id) values {$queryData}", $data);
        }
    }

    $answers = json_encode(prepareAnswers($_POST));

    if ($file_name) {
        $image_name = SITE_URL . '/uploads/users/' . $image;
        $result = $common->update(
                table: "users",
                data: ["full_name" => $full_name, 'description' => $description, 'image' => $image_name, 'answers' => $answers],
                cond: "id = :id",
                params: ['id' => $user_infos['id']],
                modifiedColumnName: 'updated_at'
        );
        if ($result) {
            move_uploaded_file($file_tmp, '../uploads/users/' . $image);
            header("Location: " . SITE_URL . "/users/profile.php");
            $update_msg = '<div class="alert alert-success mb-0">Profile updated successfully.</div>';
        } else {
            $update_msg = '<div class="alert alert-success mb-0">Something is wrong!</div>';
        }
    } else {
        $result = $common->update(
                table: "users",
                data: ['full_name' => $full_name, 'description' => $description, 'answers' => $answers],
                cond: "id = :id",
                params: ['id' => $user_infos['id']],
                modifiedColumnName: 'updated_at'
        );
        if ($result) {
            header("Location: " . SITE_URL . "/users/profile.php");
            $update_msg = '<div class="alert alert-success mb-0">Profile updated successfully.</div>';
        } else {
            $update_msg = '<div class="alert alert-success mb-0">Something is wrong!</div>';
        }
    }
}

$interests = $common->get('interests');

$userInterests = $common->get('interest_user', "user_id = :user_id", ['user_id' => $user_infos['id']]);

$userInterestsHashMap = [];
if ($userInterests) {
    foreach ($userInterests as $userInterest) {
        $userInterestsHashMap[$userInterest['interest_id']] = true;
    }
}
?>

<style>
    @media screen and (max-width: 480px) {
        .inputform {
            width: 100%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto;
        }
    }

    @media screen and (min-width: 600px) {
        .inputform {
            width: 100%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto
        }
    }

    @media screen and (min-width: 786px) {
        .inputform {
            width: 85%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto;
        }
    }

    @media screen and (min-width: 992px) {
        .inputform {
            width: 85%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto;
        }
    }

    @media screen and (min-width: 1200px) {
        .inputform {
            width: 85%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto;
        }
    }
</style>

<link rel="stylesheet" href="./assets/styleOne.css">
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 text-white">
    <form id="change-profile-form" class="inputform" action="" method="POST" enctype="multipart/form-data">
        <div class="change-profile-group">
            <div class="preview_image">
                <img src="<?= $user_infos['image'] != NULL ? $user_infos['image'] : 'https://s3-us-west-2.amazonaws.com/harriscarney/images/150x150.png'; ?>" name="image" alt="profile image" id="profile-preview-image">
                <div class="change_photo">
                    <input type="file" id="upload-new-image" accept="image/png,image/jpg" name="image" onchange="loadFile(event)" hidden />
                    <label for="upload-new-image">Change Profile Photo</label>
                </div>
            </div>

        </div>
        <div class="change-profile-group">
            <label for="newUserName">Name</label>
            <input class="username" id="newUserName" type="text" name="name" value="<?= $user_infos['full_name']; ?>" required="">
        </div>
        <div class="change-profile-group">
            <label for="newUserName">Description</label>
            <textarea name="description" id="newDescription" rows="5" required=""><?= $user_infos['description']; ?></textarea>
        </div>

        <!-- Interests start -->
        <div class="py-2">
            <h3>Interests</h3>
            <?php foreach ($interests as $interest) : ?>
                <div class="d-flex justify-content-start">
                    <input type="checkbox" name="interests[]" value="<?= $interest['id'] ?>" id="interest-<?= $interest['id'] ?>" <?= isset($userInterestsHashMap[$interest['id']]) ? 'checked' : '' ?>>
                    <label for="interest-<?= $interest['id'] ?>"><?= $interest['interest'] ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Interests End -->

        <?php $answersArr = json_decode($user_infos['answers']); ?>
        <?php foreach ($questions as $key => $question) : ?>
            <div class="change-profile-group">
                <label for="question-<?= $key ?>"><?= $question ?></label>
                <textarea name="<?= $key ?>" id="question-<?= $key ?>" rows="5"><?= $answersArr->{$key} ?? '' ?></textarea>
            </div>
        <?php endforeach; ?>

        <div class="btn-group">
            <a href="https://mejorcadadia.com/users/profile.php" class="profile_edit_btn bg-danger text-light me-2">cancel</a>
            <input type="submit" id="submit-new-details" name="update_profile" value="Update">
        </div>

    </form>
</main>
<script>
    var loadFile = function(event) {
        var output = document.getElementById('profile-preview-image');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }
    };
</script>
<?php require_once "inc/footer.php"; ?>