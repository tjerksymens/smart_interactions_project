<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $userId = 1; // Specify the user ID you want to update

    try {
        // Create a PDO instance and establish the database connection
        $pdo = new PDO('mysql:host=ID393251_SupriseConnect.db.webhosting.be;dbname=ID393251_SupriseConnect', 'ID393251_SupriseConnect', 'ConnectionProject69!');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the SQL statement to update the location data
        $query = "UPDATE user SET latitude = :latitude, longitude = :longitude WHERE id = :id";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':latitude', $latitude);
        $statement->bindParam(':longitude', $longitude);
        $statement->bindParam(':id', $userId);
        $statement->execute();

        echo "Location data updated successfully";
    } catch (PDOException $e) {
        echo "Error updating data in the database: " . $e->getMessage();
    }

    // Close the statement and database connection
    $statement = null;
    $pdo = null;

    exit(); // End the PHP script execution after handling the POST request
}




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
    <title>Suprise Connect</title>
</head>

<body>

    <div id="map"></div>
    <script>
        //Send the location to the database
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

        // Create a new image object for the marker icon so we can see the user profile pik
        const profilePhoto = new Image();
        profilePhoto.src = "./images/profile.jpg";

        // friend image
        const secondUserPhoto = new Image();
        secondUserPhoto.src = "./images/DSCF7530.jpg";

        //mapbox token
        mapboxgl.accessToken = 'pk.eyJ1IjoidGphYWFyayIsImEiOiJjbGhnYXVocmExeWV2M3JwY2Nuc2h5cDZ1In0.r0PN1HMzTTtYxdu4pWD3NA';

        //mapbox map
        const map = new mapboxgl.Map({
            container: 'map', // container ID
            // Choose from Mapbox's core styles, or make your own style with Mapbox Studio
            style: 'mapbox://styles/mapbox/streets-v12', // style URL
            center: [-74.5, 40], // starting position
            zoom: 15 // starting zoom
        });

        //location user and center
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

        //second user's location
        const secondUserLngLat = [4.485876, 51.023313];
        //second user's profile photo
        const secondUserMarker = new mapboxgl.Marker({
                element: secondUserPhoto,
                anchor: 'bottom'
            })
            .setLngLat(secondUserLngLat)
            .addTo(map);

        // Add zoom and rotation controls to the map.
        map.addControl(new mapboxgl.NavigationControl());
    </script>

</body>

</html>