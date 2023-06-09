<?php
include_once(__DIR__ . "/bootstrap.inc.php");
$config = parse_ini_file("config/config.ini");
$api_key = $config['sendgrid_api_key'];

//cloudinary connection
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

//if your not logged in you can't acces this page
if ($_SESSION['loggedin'] !== true) {
    header('location: login.php');
}

//logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('location: login.php');
    exit;
}

//update location of user every minute
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

// Get the user from the database
$user = \SupriseConnect\Framework\User::getUserById($_SESSION['user_id']);

// Get the friends locations from the database
$friends = \SupriseConnect\Framework\Friend::getFriends($_SESSION['user_id']);

// Get the friends information from the database
$vriendjes = \SupriseConnect\Framework\User::getFriendsInformation($_SESSION['user_id']);
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
    <div class="logout">
        <a href="logout.php">Log out</a>
    </div>



    <script>
        // Function to update friend markers on the map
        function updateFriendMarkers() {
            // Remove all the friend markers from the map
            friends.forEach(marker => {
                if (marker instanceof mapboxgl.Marker) {
                    marker.remove();
                }
            });
            // Fetch the updated friend locations from the server
            // and update the friends array
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "getFriendsLocations.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        friends = JSON.parse(xhr.responseText);
                        addFriendMarkers();
                    } else {
                        console.error('Error:', xhr.status, xhr.statusText);
                    }
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

                // Calculate the distance between the user and the friend
                const userLatitude = <?php echo $user['latitude']; ?>;
                const userLongitude = <?php echo $user['longitude']; ?>;
                const distance = calculateDistance(latitude, longitude, userLatitude, userLongitude);

                // Distance that is close for friends to be notified
                const distanceThreshold = 1;

                $emailSent = false;

                <?php foreach ($vriendjes as $vriend) : ?>
                    if (distance <= distanceThreshold && !$emailSent) {
                        $emailSent = true;
                        console.log("You are close to <?php echo htmlspecialchars($vriend['firstname'])  . ' ' . htmlspecialchars($vriend['lastname']) ?>");
                        <?php
                        $email = new \SendGrid\Mail\Mail(); //create new email
                        $email->setFrom("r0883194@student.thomasmore.be", "SuprisConnect");
                        $email->setSubject("You are close to a friend");
                        $email->addTo($user['email'], $user['firstname'] . ' ' . $user['lastname']);
                        $email->addContent(
                            "text/html",
                            "Hi there,<br><br>You are close to " . $vriend['firstname'] . ' ' . $vriend['lastname'] . "<br><br>Best,<br>SupriseConnect"
                        );
                        $sendgrid = new \SendGrid($api_key);
                        try {
                            $response = $sendgrid->send($email);
                            $responseData = $response;
                        } catch (Exception $e) {
                            echo 'Caught exception: ' . $e->getMessage() . "\n";
                        } ?>
                    }
                <?php endforeach; ?>


                // Create a new image object for the marker icon so we can see the user profile image
                const friendPhoto = new Image();

                // Fetch the Cloudinary image URL dynamically using AJAX
                const xhr = new XMLHttpRequest();
                xhr.open("GET", "getCloudinaryImageUrl.php?image=" + encodeURIComponent(image), true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const imageUrl = xhr.responseText;

                        if (imageUrl) {
                            friendPhoto.src = imageUrl;
                        } else {
                            friendPhoto.src = "default-profile-image.jpg";
                        }

                        // Add a marker for the friend
                        const friendMarker = new mapboxgl.Marker({
                                element: friendPhoto,
                                anchor: 'bottom'
                            })
                            .setLngLat([longitude, latitude])
                            .addTo(map);

                        friends.push(friendMarker); // Add the marker to the friends array
                    }
                };
                xhr.send();
            });
        }

        // Function to calculate the distance between two coordinates
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const degToRad = Math.PI / 180;
            const earthRadius = 6371; // Radius of the Earth in kilometers

            const dLat = (lat2 - lat1) * degToRad;
            const dLon = (lon2 - lon1) * degToRad;

            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * degToRad) * Math.cos(lat2 * degToRad) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            const distance = earthRadius * c;
            return distance;
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