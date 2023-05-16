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
    const { longitude, latitude } = position.coords;
    map.setCenter([longitude, latitude]);
    // New marker as profile pic
    const marker = new mapboxgl.Marker({
    element: profilePhoto, // use image
    anchor: 'bottom'})
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