<?php
require __DIR__ . '../vendor/autoload.php'; // Ensure Composer autoload is included

use Pusher\Pusher;

$pusher = new Pusher(
    "c8b094c08cd4f1f1fc30", // Replace with your Pusher App Key
    "2ed133579295337bffe9", // Replace with your Pusher App Secret
    "1956417", // Replace with your Pusher App ID
    [
        'cluster' => 'ap1', // Replace with your Pusher App Cluster
        'useTLS' => true
    ]
);
?>
