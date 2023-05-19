<?php
include_once(__DIR__ . "/bootstrap.inc.php");

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'doxzjrtjh',
        'api_key'    => '436969446252812',
        'api_secret' => 'JMz0eaR82cExLX0ZgEWmgcn8lb4',
    ],
]);

if ($_SESSION['loggedin'] !== true) {
    header('location: login.php');
}

// Get the friends locations from the database
$friends = \SupriseConnect\Framework\Friend::getFriends($_SESSION['user_id']);

// Return the friends locations as a JSON response
header('Content-Type: application/json');
echo json_encode($friends);
