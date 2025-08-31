<?php
// Get the relative path from URL
$get = $_GET['get'];

// Construct the full URL to the original DASH server
$mpdUrl = 'https://linearjitp-playback.astro.com.my/dash-wv/linear/' . $get;

// Detect file type for correct content-type
$fileExt = pathinfo($get, PATHINFO_EXTENSION);

// Set proper content-type header
switch ($fileExt) {
    case "mpd":
        header("Content-Type: application/dash+xml");
        break;
    case "m4s":
        header("Content-Type: video/iso.segment");
        break;
    case "mp4":
        header("Content-Type: video/mp4");
        break;
    default:
        header("Content-Type: application/octet-stream");
}

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: *");

// Disable caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Optional: Handle OPTIONS preflight request (for some browsers/players)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Set up request headers (e.g. to mimic Android device)
$mpdheads = [
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Linux; Android 10; Mi 9T Pro Build/QKQ1.190825.002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/83.0.4103.101 Mobile Safari/537.36\r\n",
        'follow_location' => 1,
        'timeout' => 5
    ]
];

$context = stream_context_create($mpdheads);
$res = @file_get_contents($mpdUrl, false, $context);

if ($res === false) {
    http_response_code(404);
    echo "Error: Failed to fetch media from origin.";
} else {
    echo $res;
}
?>
