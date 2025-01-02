<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Map with Shapefile</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>
</head>
<body>
<div id="map" style="height: 500px;"></div>
<input type="file" id="shapefile" accept=".zip" style="margin-top: 10px;">

<script>
    // Initialize the Leaflet map
    const map = L.map('map').setView([0, 0], 2);

    // Add a tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

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


    // Handle shapefile upload
    document.getElementById('shapefile').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const buffer = e.target.result;

                // Convert shapefile (.zip) to GeoJSON
                shp(buffer).then(function(geojson) {
                    // Add GeoJSON to the map
                    console.log(geojson);
                    const layer = L.geoJSON(geojson).addTo(map);

                    // Fit the map to the GeoJSON layer
                    map.fitBounds(layer.getBounds());
                }).catch(function(error) {
                    console.error('Error loading shapefile:', error);
                });
            };

            reader.readAsArrayBuffer(file);
        }
    });
</script>
</body>
</html>
