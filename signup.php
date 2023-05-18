<?php
include_once(__DIR__ . "/bootstrap.inc.php");

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

$cloudinary = new Cloudinary(
    [
        'cloud' => [
            'cloud_name' => 'doxzjrtjh',
            'api_key'    => '436969446252812',
            'api_secret' => 'JMz0eaR82cExLX0ZgEWmgcn8lb4',
        ],
    ]
);


if (!empty($_POST)) {
    try {
        $user = new \SupriseConnect\Framework\User();
        if ($user->checkExistingEmail($_POST['email']) || $user->checkExistingUsername($_POST['username'])) {
            if ($user->checkExistingEmail($_POST['email'])) {
                $error = "This email already exists. Please try again with a different email address.";
            } else {
                $error = "This username already exists. Please try again with a different username.";
            }
        } else {
            if (isset($_FILES['image'])) {
                try {
                    $image = new \SupriseConnect\Framework\Image($cloudinary);
                    $newImgName = $image->upload($_FILES['image']);

                    $user = new \SupriseConnect\Framework\User();
                    $user->setEmail($_POST['email']);
                    $user->setUsername($_POST['username']);
                    $user->setFirstname($_POST['firstname']);
                    $user->setLastname($_POST['lastname']);
                    $user->setPassword($_POST['password']);
                    $user->setConfirmPassword($_POST['confirmpassword']);
                    $user->setImage($newImgName);
                    $user->save();
                    header("Location: login.php");

                    //sessie starten voor nieuwe user die wordt gebruikt voor controle bij validation
                    $_SESSION['user_id'] = $user->getId($_POST['email']);
                } catch (Throwable $e) {
                    $error = $e->getMessage();
                }
            } else {
                $error = "No image selected.";
            }
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Sign up</title>
</head>

<body>
    <div class="signup">
        <div class="form form__signup">
            <form action="" method="post" enctype="multipart/form-data">
                <h2 class="form__title" form__title>Sign Up</h2>
                <!--Toont errors-->
                <?php if (isset($error)) : ?>
                    <div class="form__error">
                        <p>
                            <?php echo $error; ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="form__field">
                    <label for="Email">Email</label>
                    <input type="text" name="email">
                </div>
                <div class="form__field">
                    <label for="Username">Username</label>
                    <input type="text" name="username">
                </div>
                <div class="form__field">
                    <label for="Firstname">Firstname</label>
                    <input type="text" name="firstname">
                </div>
                <div class="form__field">
                    <label for="Lastname">Lastname</label>
                    <input type="text" name="lastname">
                </div>
                <div class="form__field">
                    <label for="Password">Password</label>
                    <input type="password" name="password">
                </div>
                <div class="form__field">
                    <label for="ConfirmPassword">Confirm Password</label>
                    <input type="password" name="confirmpassword">
                </div>
                <div class="form__field">
                    <label for="image">Upload image</label>
                    <input type="file" name="image">
                </div>
                <div class="form__field">
                    <input type="submit" value="Sign Up" class="btn btn--primary">
                </div>
            </form>
            <div class="form__signup__links">
                <p>Already have an account?</p>
                <a href="login.php">Log in here</a>
            </div>
        </div>
    </div>
</body>

</html>