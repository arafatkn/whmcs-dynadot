<?php

namespace Arafatkn\WhmcsDynadot;

class Update
{
    const VERSION = '0.0.1';

    public static function checkUpdate(): bool
    {
        if (isset($_COOKIE['kn_dynadot_module_has_update'])) {
            return boolval($_COOKIE['kn_dynadot_module_has_update']);
        }

        $url = 'https://github.com/arafatkn/whmcs-dynadot/raw/master/release';
        $release = Http::get($url);
        logModuleCall('dynadot', 'update check', self::VERSION, intval($release));
        $has_update = intval($release) > intval(self::VERSION);

        setcookie('kn_dynadot_module_has_update', $has_update, time() + 24 * 3600);

        return $has_update;
    }
}