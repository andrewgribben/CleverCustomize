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


<?php

    include("templates/default/shell/toolbar/main.tpl.php");

?>
