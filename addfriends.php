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
</body>

</html>