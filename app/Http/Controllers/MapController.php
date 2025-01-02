<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use File;

class MapController extends Controller
{
    public function index()
    {
        return view('map');
    }

    // Get files in the selected folder from the ZIP
    public function getFiles(Request $request)
    {
        $folderName = $request->input('folder');
        $zipPath = public_path('temp/shp.zip');
        $tempDir = public_path('temp/shp/');


        // Ensure temp directory exists
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0777, true);
        }

        // Open the ZIP file
        $zip = new ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            // Extract files to temporary directory
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to open ZIP file']);
        }

        // Get all files in the selected folder
        $folderPath = $tempDir . $folderName;
        if (File::isDirectory($folderPath)) {
            // Use `allFiles()` to get all files in the folder, sorted by filename
            $files = File::allFiles($folderPath);

            // Filter for only .shp, .shx, .dbf, and related files
            $shpFiles = array_filter($files, function ($file) {
                return in_array($file->getExtension(), ['shp', 'shx', 'dbf', 'prj', 'cpg', 'sbn', 'shp.xml']);
            });

            // Map files to URLs based on their extensions
            $fileUrls = [];
            foreach ($shpFiles as $file) {
                $fileNameWithoutExtension = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                // Construct the URLs for all related files
                if (!isset($fileUrls[$fileNameWithoutExtension])) {
                    $fileUrls[$fileNameWithoutExtension] = [];
                }

                // Add the file URL based on its extension
                switch ($file->getExtension()) {
                    case 'shp':
                        $fileUrls[$fileNameWithoutExtension]['shp'] = url("temp/shp/{$folderName}/{$fileNameWithoutExtension}.shp");
                        break;
                    case 'shx':
                        $fileUrls[$fileNameWithoutExtension]['shx'] = url("temp/shp/{$folderName}/{$fileNameWithoutExtension}.shx");
                        break;
                    case 'dbf':
                        $fileUrls[$fileNameWithoutExtension]['dbf'] = url("temp/shp/{$folderName}/{$fileNameWithoutExtension}.dbf");
                        break;
                    case 'prj':
                        $fileUrls[$fileNameWithoutExtension]['prj'] = url("temp/shp/{$folderName}/{$fileNameWithoutExtension}.prj");
                        break;
                    case 'cpg':
                        $fileUrls[$fileNameWithoutExtension]['cpg'] = url("temp/shp/{$folderName}/{$fileNameWithoutExtension}.cpg");
                        break;
                    case 'sbn':
                        $fileUrls[$fileNameWithoutExtension]['sbn'] = url("temp/shp/{$folderName}/{$fileNameWithoutExtension}.sbn");
                        break;
                    case 'shp.xml':
                        $fileUrls[$fileNameWithoutExtension]['shp_xml'] = url("temp/shp/{$folderName}/{$fileNameWithoutExtension}.shp.xml");
                        break;
                }
            }

            // Re-index the array numerically to make sure it's sequential
            $fileUrls = array_values($fileUrls);

            return response()->json(['files' => $fileUrls]);
        }

        return response()->json(['files' => []]);
    }

    // Get the content of a specific file (GeoJSON)
    public function getFile(Request $request)
    {
        $folderName = $request->input('folder');
        $fileName = $request->input('file');
        $filePath = public_path("temp/shp/{$folderName}/{$fileName}");

        if (File::exists($filePath)) {
            $geojsonData = File::get($filePath);
            return response()->json(json_decode($geojsonData));
        }

        return response()->json(['error' => 'File not found']);
    }
}
