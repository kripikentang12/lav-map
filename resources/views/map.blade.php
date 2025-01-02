<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Map with GeoJSON Layers</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
<!-- Peta akan muncul di sini -->
<div id="map" style="height: 500px;"></div>

<script>
    // Inisialisasi peta Leaflet
    var map = L.map('map').setView([0.7893, 113.9213], 5); // Sesuaikan koordinat dan zoom level

    // Menambahkan tile layer dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Daftar file GeoJSON yang ada di folder public/geojson
    var geojsonFiles = [
        '/temp/petadasar/Batas_Kabupaten.geojson',
        '/temp/petadasar/Batas_Kecamatan.geojson',
        '/temp/petadasar/Batas_Provinsi.geojson',
        '/temp/petadasar/Batas_Sulteng.geojson',
        '/temp/petadasar/Ibukota_Kabupaten.geojson',
        '/temp/petadasar/Jaringan_Jalan.geojson',
        '/temp/petadasar/Kecamatan_Konut.geojson',
        '/temp/petadasar/Laut.geojson',
        '/temp/petadasar/Sulawesi_Tenggara.geojson',
        // Tambahkan lebih banyak layer jika diperlukan
    ];

    // Menyimpan layer GeoJSON
    var geojsonLayers = {};

    // Memuat file GeoJSON dan menambahkannya ke peta
    geojsonFiles.forEach(function(url, index) {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Cek data GeoJSON di console
                // console.log('Loaded GeoJSON:', data);

                // Membuat layer untuk GeoJSON
                var geojsonLayer = L.geoJSON(data);

                // Menambahkan layer ke objek geojsonLayers (untuk kontrol layer)
                geojsonLayers[data.name] = geojsonLayer;

                // Menambahkan layer GeoJSON ke peta
                geojsonLayer.addTo(map);

                // Menambahkan popup untuk setiap fitur (klik pada geojson)
                geojsonLayer.eachLayer(function (layer) {
                    console.log(layer.feature)
                    layer.bindPopup("ID: " + layer.feature.properties.OBJECTID); // Ganti dengan properti yang sesuai
                });
            })
            .catch(error => console.error('Error loading GeoJSON:', error));
    });

    // Menambahkan kontrol layer agar pengguna bisa memilih layer
    L.control.layers(null, geojsonLayers).addTo(map);
</script>
</body>
</html>
