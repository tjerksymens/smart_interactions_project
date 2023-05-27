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

if ($_SESSION['loggedin'] !== true) {
    header('location: login.php');
}

if (isset($_GET['search'])) {
    $users = \SupriseConnect\Framework\User::searchUsers($_GET['search']);
}

if (isset($_POST['addfriend'])) {
    $friend = new \SupriseConnect\Framework\Friend();
    $friend->addFriend($_SESSION['user_id'], $_POST['addfriend']);
    header('location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>SupriseConnect add friends</title>
</head>

<body>
    <h1>Add friends</h1>
    <a id="go_to_homepage" href="index.php">⬅️ Back</a>
    <!-- Toont zoek friends -->
    <form action="" method="get" class="searchfriends">
        <label for="search">Search for friends</label>
        <input type="text" name="search" id="search">
        <input type="submit" value="Search">
    </form>

    <!-- Als users is geset geeft hij alle bijpassende users weer -->
    <?php if (isset($users)) : ?>
        <ul>
            <?php foreach ($users as $user) : ?>
                <li>
                    <div class="users" href="profile.php?id=<?php echo $user['id']; ?>">
                        <img src="<?php echo $cloudinary->image($user['image'])->resize(Resize::thumbnail()->width(100)->height(100))->toURL(); ?>" alt="profile picture">
                        <p><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></p>
                        <?php // Checkt of de user al vrienden is met de user waar je op klikt
                        $friend = new \SupriseConnect\Framework\Friend();
                        $isFriend = $friend->isFriend($_SESSION['user_id'], $user['id']);
                        if ($isFriend !== true) {
                            if ($user['id'] !== $_SESSION['user_id']) { // checkt of user de user is
                        ?>
                                <form action="" method="post">
                                    <button type="submit" name="addfriend" value="<?php echo ($user['id']) ?>">Add friend</button>
                                </form><?php
                                    } else { ?>
                                <p><?php echo "This is you"; ?></p><?php
                                                                }
                                                            } else { ?>
                            <p><?php echo "You are already friends"; ?></p>
                        <?php } ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>

</html>