<?php

namespace Arafatkn\WhmcsDynadot\Http;

class Response
{
    public $xml;
    public $json;
    public $array;
    public $object;

    public function find($path, $array = null)
    {
        if (!is_array($path)) {
            $path = explode('.', $path);
        }

        if (count($path) == 0) {
            return false;
        }

        $array = !is_array($array) ? $this->array : $array;

        if (count($path) == 1) {
            return $array[$path[0]] ?? false;
        }

        $current = array_shift($path);

        if ($current == '*') {
            foreach ($array as $value) {
                return $this->find($path, $value);
            }
        }

        return is_array($array[$current]) ? $this->find($path, $array[$current]) : false;
    }

    public function hasErrors(): bool
    {
        if (empty($this->array)) {
            return true;
        }

        if (isset($this->array['error'])) {
            return true;
        }

        $status = $this->find('//Status');

        return !$status || $status == 'error';
    }

    public function getErrors(): array
    {
        if (!$this->hasErrors() || empty($this->array)) {
            return [];
        }

        if (isset($this->array['error'])) {
            return [
                'error' => $this->array['error'],
            ];
        }

        return [
            'error' => $this->find('//Error'),
        ];
    }

    public static function fromXml($xml): Response
    {
        $response = new self();

        $response->xml = $xml;
        $response->json = json_encode($xml);
        $response->array = json_decode($response->json, true);
        $response->object = (object)$response->array;

        return $response;
    }

    public static function fromArray($array): Response
    {
        $response = new self();

        $response->array = $array;
        $response->object = (object)$response->array;
        $response->xml = null;
        $response->json = json_encode($array);

        return $response;
    }
}