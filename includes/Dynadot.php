<?php

namespace Arafatkn\WhmcsDynadot;

use Arafatkn\WhmcsDynadot\Http\Request;

/**
 * @title Main Class for Controlling communication.
 *
 * @author Arafat Islam
 */
class Dynadot
{
    /**
     * Project Version.
     */
    const VERSION = '0.0.1';

    /**
     * Dynadot API Base Url.
     */
    const API_URL = 'https://api.dynadot.com/api3.xml';

    /**
     * Dynadot API Key.
     */
    protected $api_key;

    /**
     * Action for Domain.
     */
    protected $domain;

    /**
     * Parameters of Action.
     */
    protected $params;

    /**
     * Debug enabled or disabled.
     */
    protected $debug_enabled = false;

    public function __construct($params)
    {
        $this->api_key = $params['api_key'];
        $this->domain = $params['sld'] . '.' . $params['tld'];
        $this->params = $params;
    }

    /**
     * Register a new domain.
     * @throws \Exception
     */
    public function register(): array
    {
        try {
            $this->call('register', [
                'duration' => $this->params['regperiod'],
            ]);

            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * Fetch current nameservers.
     */
    public function getNameServers(): array
    {
        try {
            $response = $this->call('domain_info');
            $nameservers = $response->xpath('//NameServerSettings/NameServers')[0];
            $nameservers = array_filter(self::xmlToArray($nameservers->ServerName), 'is_string');

            $data = ['success' => true];

            foreach ($nameservers as $key => $value) {
                $data['ns' . ($key + 1)] = $value;
            }

            return $data;
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * Save nameserver changes.
     */
    public function saveNameServers(): array
    {
        try {
            $data = [];
            for ($i = 1; $i < 14; $i++) {
                if (!empty($this->params['ns' . $i])) {
                    $data['ns' . ($i - 1)] = $this->params['ns' . $i];
                }
            }

            $this->call('set_ns', $data);

            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * Get registrar lock status.
     */
    public function getRegistrarLock()
    {
        try {
            $response = $this->call('domain_info');
            $status = $response->xpath('//Domain/Locked');

            if (!$status || count($status) < 1 || !in_array($status[0], ['yes', 'no'])) {
                throw new \Exception("Unable to get domain lock status");
            }

            return $status[0] == "yes" ? "locked" : "unblocked";
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * Set registrar lock status.
     */
    public function saveRegistrarLock(): array
    {
        try {
            $lockStatus = $this->params['lockenabled'];

            $this->call('get_transfer_auth_code', [
                'unlock_domain_for_transfer' => ($lockStatus == 'locked') ? 1 : 0,
            ]);

            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * Enable/Disable ID Protection.
     */
    public function togglePrivacyProtection(): array
    {
        $protectEnable = (bool)$this->params['protectenable'];

        try {
            $this->call('set_privacy', [
                'option' => $protectEnable ? 'full' : 'off',
            ]);

            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * Request EEP or Domain Transfer Auth Code.
     */
    public function getEppCode(): array
    {
        try {
            $response = $this->call('get_transfer_auth_code', [
                'unlock_domain_for_transfer' => 1,
            ]);

            $auth_code = $response->xpath('//AuthCode');

            if (!$auth_code || count($auth_code) < 1) {
                throw new \Exception("Unable to get domain EPP code");
            }

            return [
                'eppcode' => $auth_code[0],
            ];
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    /**
     * Call Dynadot API.
     * @throws \Exception
     */
    protected function call($command, $data = [])
    {
        $url = self::API_URL . "?key={$this->api_key}&command={$command}&domain={$this->domain}&" . http_build_query($data);

        $response = Request::get($url);

        $this->log($command, $url, $response);

        $result = simplexml_load_string($response);

        if (!$result) {
            throw new \Exception("Unable to parse Dynadot Response");
        }

        $status = $result->xpath('//Status');

        if (is_array($status) && count($status) && $status[0] == 'error') {
            $error = $result->xpath('//Error');
            throw new \Exception($error[0] ?? "Unknown error occurred!");
        }

        return $result;
    }

    /**
     * XML to Array.
     *
     * @param $xml
     * @return mixed
     */
    public static function xmlToArray($xml)
    {
        return json_decode(json_encode($xml), true);
    }

    /**
     * Return a error response.
     *
     * @param $e
     * @return array
     */
    private function error($e): array
    {
        return [
            'error' => $e->getMessage(),
        ];
    }

    /**
     * Return a success response.
     *
     * @param string $message optional
     * @return bool[]
     */
    private function success(string $message = ""): array
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return $response;
    }

    /**
     * Log info for debugging purpose only.
     */
    public function debug($action, $request, $response = null)
    {
        if ($this->debug_enabled) {
            $this->log($action, $request, $response);
        }
    }

    /**
     * Log information.
     */
    public function log($action, $request, $response = null)
    {
        $action .= ' (' . $this->domain . ')';
        logModuleCall('dynadot', $action, $request, $response, '', $this->params);
    }
}
