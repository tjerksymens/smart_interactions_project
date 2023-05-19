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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $user_id = $_SESSION['user_id'];

    try {
        \SupriseConnect\Framework\User::updateLocation($latitude, $longitude, $user_id);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    exit(); // End the PHP script execution after handling the POST request
}

$user = \SupriseConnect\Framework\User::getUserById($_SESSION['user_id']);

// Get the friends locations from the database
$friends = \SupriseConnect\Framework\Friend::getFriends($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="./css/style.css">
    <title>SupriseConnect</title>
</head>

<body>
    <div id="map"></div>
    <h1>SupriseConnect</h1>
    <a id="addfriends" href="addfriends.php">Add Friends</a>

    <script>
        // Function to update friend markers on the map
        function updateFriendMarkers() {
            // Remove all the friend markers from the map
            friends.forEach(marker => marker.remove());

            // Fetch the updated friend locations from the server
            // and update the friends array
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "getFriendsLocations.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    friends = JSON.parse(xhr.responseText);
                    addFriendMarkers();
                }
            };
            xhr.send();
        }

        // Function to add friend markers to the map
        function addFriendMarkers() {
            friends.forEach(friend => {
                const {
                    latitude,
                    longitude,
                    image
                } = friend;

                // Create a new image object for the marker icon so we can see the user profile image
                const friendPhoto = new Image();
                <?php foreach ($friends as $friend) : ?>
                    if (friend['image']) {
                        friendPhoto.src = "<?php echo $cloudinary->image($friend['image'])->resize(Resize::fill(100, 150))->toUrl(); ?>";
                    } else {
                        friendPhoto.src = "default-profile-image.jpg";
                    }
                <?php endforeach; ?>

                // Add a marker for the friend
                const friendMarker = new mapboxgl.Marker({
                        element: friendPhoto,
                        anchor: 'bottom'
                    })
                    .setLngLat([longitude, latitude])
                    .addTo(map);

                friends.push(friendMarker); // Add the marker to the friends array
            });
        }

        // Function to get the users location and send it to the server
        function sendLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "index.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            console.log(xhr.responseText);
                        }
                    };
                    xhr.send("latitude=" + latitude + "&longitude=" + longitude);
                });
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }

        // Call sendLocation initially
        sendLocation();

        // Update and send the location every minute
        setInterval(sendLocation, 60000);

        // Create a new image object for the marker icon so we can see the user profile image
        const profilePhoto = new Image();
        <?php if ($user['image']) : ?>
            profilePhoto.src = "<?php echo $cloudinary->image($user['image'])->resize(Resize::fill(100, 150))->toUrl(); ?>";
        <?php else : ?>
            // Set a default image URL or handle the situation when $user['image'] is not available
            profilePhoto.src = "default-profile-image.jpg";
        <?php endif; ?>

        // Mapbox token
        mapboxgl.accessToken = 'pk.eyJ1IjoidGphYWFyayIsImEiOiJjbGhnYXVocmExeWV2M3JwY2Nuc2h5cDZ1In0.r0PN1HMzTTtYxdu4pWD3NA';

        // Mapbox map
        const map = new mapboxgl.Map({
            container: 'map', // container ID
            style: 'mapbox://styles/mapbox/streets-v12', // style URL
            center: [-74.5, 40], // starting position
            zoom: 15 // starting zoom
        });

        // Location user and center
        navigator.geolocation.getCurrentPosition(position => {
            const {
                longitude,
                latitude
            } = position.coords;
            map.setCenter([longitude, latitude]);
            // New marker as profile pic
            const marker = new mapboxgl.Marker({
                    element: profilePhoto, // use image
                    anchor: 'bottom'
                })
                .setLngLat([longitude, latitude]).addTo(map);
        });

        // Add zoom and rotation controls to the map.
        map.addControl(new mapboxgl.NavigationControl());

        var friends = <?php echo json_encode($friends); ?>;

        // Call the initial friend marker update
        addFriendMarkers();

        // Update the friend markers every minute
        setInterval(updateFriendMarkers, 60000);
    </script>
</body>

</html>