<?php
if ($_SERVER['REQUEST_URI'] != "/now") {
?>
<style type="text/css">
    #current-status {
        cursor: pointer;
        position: fixed;
        bottom: 1em;
        right: 1em;
        padding: 1em;
        background: white;
        opacity: 0.85;
        text-align: center;
        z-index: 10000;
    }

    .charging, .full {
        color: #2ADD00;
    }

    .wifi-disconnected {
        color: #bbb;
    }

    #location a {
        color: #000;
    }
</style>

<div id="current-status" onclick="window.location.href='/now'">
<?php
    $status_file = fopen("current.json", "r");
    $raw_json = fgets($status_file);
    $status = json_decode($raw_json, true);
    
    $battery_icon = "fa-battery-full";
    if ($status['battery_level'] <= 0.75) {
        $battery_icon = "fa-battery-three-quarters";
    }
    if ($status['battery_level'] <= 0.5) {
        $battery_icon = "fa-battery-half";
    }
    if ($status['battery_level'] <= 0.25) {
        $battery_icon = "fa-battery-quarter";
    }
    if ($status['battery_level'] <= 0.05) {
        $battery_icon = "fa-battery-empty";
    }

    $wifi_state = "wifi-disconnected";
    if (strlen($status['wifi']) > 0) {
        $wifi_state = "wifi-connected";
    }

    $motion_icon = "fa-couch"; // stationary
    foreach ($status['motion'] as $value) {
        if ($value == "driving") {
            $motion_icon = "fa-car";
        } else if ($value == "walking") {
            $motion_icon = "fa-shoe-prints";
        } else if ($value == "running") {
            $motion_icon = "fa-walking";
        } else if ($value == "cycling") {
            $motion_icon = "fa-bicycle";
        }
    }
?>
    <div id="battery"><i class="fa <?= $battery_icon ?> <?= $status['battery_state'] ?>"></i></div>
    <div id="wifi"><i class="fa fa-wifi <?= $wifi_state ?>"></i></div>
    <div id="motion"><i class="fa <?= $motion_icon ?>"></i></div>
    <div id="location"><i class="fa fa-map-marker"></i></div>
</div>

<?php
}
?>

<?php

    include("templates/default/shell/toolbar/main.tpl.php");

?>
