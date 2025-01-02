<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Map with Layer Options</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Leaflet Fullscreen CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen/dist/leaflet.fullscreen.css" />

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        #map {
            height: 400px;
            margin-top: 30px;
        }
        .section {
            padding: 60px 0;
        }
        .navbar {
            background-color: #333;
        }
        .navbar a {
            color: white !important;
        }
        .hero-section {
            background-color: #007bff;
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero-section p {
            font-size: 1.2rem;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .map-controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        .fullscreen-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .fullscreen-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">My Interactive Map</a>
    </div>
</nav>

<!-- Hero Section -->
<section id="hero" class="hero-section">
    <div class="container">
        <h1>Explore Interactive Maps</h1>
        <p>Choose between thematic maps, base maps, and map albums for a better view.</p>
    </div>
</section>

<!-- Map Section -->
<section id="map-section" class="section">
    <div class="container">
        <h2 class="text-center mb-4">Choose a Map Type</h2>

        <!-- Map Controls (under the title) -->
        <div class="map-controls">
            <!-- Dropdown for selecting map type -->
            <select class="form-select" id="mapTypeSelect">
                <option value="baseMap">Base Map</option>
                <option value="thematicMap">Thematic Map</option>
                <option value="albumMap">Map Album</option>
            </select>

            <!-- Fullscreen Button -->
            <button class="fullscreen-btn" id="fullscreenBtn">Fullscreen</button>
        </div>

        <div id="map"></div>
    </div>
</section>

<!-- Footer -->
<section id="footer" class="footer">
    <p>&copy; 2025 My Interactive Map Website. All rights reserved.</p>
</section>

<!-- Bootstrap and Leaflet JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Leaflet Fullscreen JS -->
<script src="https://unpkg.com/leaflet.fullscreen/dist/leaflet.fullscreen.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.1/proj4.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4leaflet/1.0.2/proj4leaflet.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>

    // Inisialisasi peta
    var map = L.map('map', {
        crs: crs,
        center: [-3.4897, 122.1279],
        zoom: 14,
        fullscreenControl: true, // Menambahkan kontrol fullscreen
        fullscreenControlOptions: {
            position: 'topleft' // Menentukan posisi tombol fullscreen dalam peta
        }
    });

    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     maxZoom: 19
    // }).addTo(map);

    $.getJSON('/assets/maps/konawe-utara.json', function (geojson) {
        const processedData = preprocessGeoJSON(geojson);
        console.log(processedData)
        L.geoJSON(processedData).addTo(map);
    }).fail(function() {
        console.error('Failed to load GeoJSON data.');
    });



    function preprocessGeoJSON(geojson) {
        const processedGeoJSON = JSON.parse(JSON.stringify(geojson));
        processedGeoJSON.features.forEach(feature => {
            if (feature.geometry.type === "Polygon") {
                feature.geometry.coordinates = feature.geometry.coordinates.map(ring =>
                    ring
                        .filter(coord => coord.length >= 2 && !isNaN(coord[0]) && !isNaN(coord[1])) // Keep valid coordinates
                        .map(coord => [coord[0], coord[1]]) // Remove extra dimensions
                );
            } else if (feature.geometry.type === "MultiPolygon") {
                // Process MultiPolygon coordinates
                feature.geometry.coordinates = feature.geometry.coordinates.map(polygon =>
                    polygon.map(ring =>
                        ring
                            .filter(coord => coord.length >= 2 && !isNaN(coord[0]) && !isNaN(coord[1])) // Keep valid coordinates
                            .map(coord => [coord[0], coord[1]]) // Remove Z and M values
                    )
                );
            }
            // Handle other geometry types (e.g., MultiPolygon, LineString) if needed
        });
        // console.log('Original GeoJSON:', geojson);
        // geojson.features.forEach(feature => {
        //     feature.geometry.coordinates.forEach(ring => {
        //         ring.forEach(coord => {
        //             if (!Array.isArray(coord) || coord.length < 2 || isNaN(coord[0]) || isNaN(coord[1])) {
        //                 console.error('Invalid coordinate:', coord);
        //             }
        //         });
        //     });
        // });
        return processedGeoJSON;
    }


    // Menambahkan layer peta dasar (OpenStreetMap)
    var baseLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    // Menambahkan peta tematik (contoh: peta dengan data geojson atau layer custom)
    var thematicLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Thematic Map Example'
    });

    // Menambahkan peta album (contoh: peta dengan gambar sebagai layer)
    var albumLayer = L.layerGroup([
        L.marker([51.505, -0.09]).bindPopup("Album Marker 1"),
        L.marker([51.515, -0.1]).bindPopup("Album Marker 2")
    ]);

    // Menambahkan peta dasar ke peta awal
    baseLayer.addTo(map);

    // Fungsi untuk mengubah peta berdasarkan pilihan dropdown
    document.getElementById('mapTypeSelect').addEventListener('change', function(event) {
        var selectedMap = event.target.value;

        map.eachLayer(function(layer) {
            map.removeLayer(layer);
        });

        // Menambahkan layer sesuai pilihan
        if (selectedMap === 'baseMap') {
            baseLayer.addTo(map);
        } else if (selectedMap === 'thematicMap') {
            thematicLayer.addTo(map);
        } else if (selectedMap === 'albumMap') {
            albumLayer.addTo(map);
        }
    });

    // Menambahkan tombol fullscreen di luar peta
    var fullscreenButton = document.getElementById('fullscreenBtn');

    fullscreenButton.addEventListener('click', function() {
        if (!document.fullscreenElement) {
            map.getContainer().requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    });
</script>

</body>
</html>
