<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . "/includes/Update.php";
require_once __DIR__ . "/includes/Http/Request.php";

add_hook("AdminHomepage", 1, function () {
    $notice = '';
    $need_update = Arafatkn\WhmcsDynadot\Update::checkUpdate();
    if ($need_update) {
        $notice = '<div class="infobox"><strong><span class="title">Dynadot Domain Registrar Module Update Available!</span></strong><br>You can download the update for the Dynadot Registrar Module from <a href="https://github.com/arafatkn/whmcs-dynadot">GitHub</a></div>';
    }

    return $notice;
});