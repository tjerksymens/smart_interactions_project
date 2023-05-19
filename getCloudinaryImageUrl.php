<?php
require_once(__DIR__ . "/bootstrap.inc.php");

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'doxzjrtjh',
        'api_key'    => '436969446252812',
        'api_secret' => 'JMz0eaR82cExLX0ZgEWmgcn8lb4',
    ],
]);

$image = $_GET['image'];

if ($image) {
    $imageUrl = $cloudinary->image($image)->resize(Resize::fill(100, 150))->toUrl();
} else {
    $imageUrl = null;
}

// Return the Cloudinary image URL as the response
echo $imageUrl;
