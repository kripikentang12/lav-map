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
            <select id="mapTypeSelect" onchange="loadLayers()" class="form-select">
                <option value="" selected disabled>Select a map</option>
                <option value="peta_dasar">Peta Dasar</option>
                <option value="peta_tematik">Peta Tematik</option>
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
        center: [0.7893, 113.9213],
        zoom: 5,
        fullscreenControl: true, // Menambahkan kontrol fullscreen
        fullscreenControlOptions: {
            position: 'topleft' // Menentukan posisi tombol fullscreen dalam peta
        }
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);


    const mapLayers = {
        'peta_dasar': [
            { name: 'Batas Kabupaten', file: '/temp/petadasar/Batas_Kabupaten.geojson' },
            { name: 'Batas Kecamatan', file: '/temp/petadasar/Batas_Kecamatan.geojson' },
            { name: 'Batas Provinsi', file: '/temp/petadasar/Batas_Provinsi.geojson' },
            { name: 'Batas Sulteng', file: '/temp/petadasar/Batas_Sulteng.geojson' },
            { name: 'Ibukota Kabupaten', file: '/temp/petadasar/Ibukota_Kabupaten.geojson' },
            { name: 'Jaringan Jalan', file: '/temp/petadasar/Jaringan_Jalan.geojson' },
            { name: 'Kecamatan Konut', file: '/temp/petadasar/Kecamatan_Konut.geojson' },
            { name: 'Laut', file: '/temp/petadasar/Laut.geojson' },
            { name: 'Sulawesi Tenggara', file: '/temp/petadasar/Sulawesi_Tenggara.geojson'}
        ],
        'peta_tematik': [
            { name: 'Curah Hujan', file: '/temp/petatematik/Curah_Hujan.geojson' },
            { name: 'DAS', file: '/temp/petatematik/DAS.geojson' },
            { name: 'Geologi', file: '/temp/petatematik/Geologi.geojson' },
            { name: 'Jenis Tanah', file: '/temp/petatematik/Jenis_Tanah.geojson' },
            { name: 'Kehutanan', file:  '/temp/petatematik/Kehutanan.geojson' },
            { name: 'Kelerengan', file: '/temp/petatematik/Kelerengan.geojson' },
            { name: 'Rawan Bencana Longsor', file: '/temp/petatematik/Rawan_Bencana_longsor.geojson' },
            { name: 'Topografi', file: '/temp/petatematik/Topografi.geojson' }
        ]
    };

    // Objek untuk menyimpan layer GeoJSON
    let geoJsonLayers = {};

    // Fungsi untuk memuat dan menambahkan layer GeoJSON ke peta
    function loadGeoJsonLayer(layerName, fileUrl) {
        fetch(fileUrl)
            .then(response => response.json())
            .then(data => {
                const layer = L.geoJSON(data).addTo(map);
                geoJsonLayers[layerName] = layer; // Menyimpan layer untuk kontrol
            })
            .catch(error => console.error(`Error loading ${fileUrl}:`, error));
    }

    // Fungsi untuk memuat layer berdasarkan pemilihan kategori peta
    function loadLayers() {
        // Menghapus semua layer GeoJSON sebelumnya
        map.eachLayer(layer => {
            // Hapus semua layer GeoJSON, kecuali tile layer dasar
            if (!(layer instanceof L.TileLayer)) {
                map.removeLayer(layer);
            }
        });

        // Mendapatkan kategori peta yang dipilih
        const mapType = document.getElementById("mapTypeSelect").value;

        // Pastikan kategori peta yang dipilih valid
        if (mapType && mapLayers[mapType]) {
            // Memuat semua layer GeoJSON untuk kategori yang dipilih
            mapLayers[mapType].forEach(function(layerInfo) {
                loadGeoJsonLayer(layerInfo.name, layerInfo.file);
            });

            // Menambahkan kontrol layer untuk memilih layer yang akan ditampilkan
            var baseMaps = {
                "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                })
            };

            var overlayMaps = {};
            mapLayers[mapType].forEach(function(layerInfo) {
                overlayMaps[layerInfo.name] = geoJsonLayers[layerInfo.name];
            });

            L.control.layers(baseMaps, overlayMaps).addTo(map);
        } else {
            console.error("Invalid map type selected: ", mapType);
        }
    }

    // Memuat layer pertama kali ketika peta dimuat
    loadLayers();

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
