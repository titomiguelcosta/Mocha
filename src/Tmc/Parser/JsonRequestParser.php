<?php

namespace Tmc\Parser;

class JsonRequestParser
{

    protected $json;

    public function __construct($content)
    {
        $this->json = $this->validate($content);
    }

    public function getJson()
    {
        return $this->json;
    }

    protected function validate($content)
    {
        $json = json_decode($content, true);

        if (!is_array($json)) {
            throw new \RuntimeException('Content is not in json or unable to parse it.');
        } else if (!array_key_exists('responses', $json)) {
            throw new \RuntimeException('Responses key in the body is missing.');
        }

        return $this->fixJson($json['responses']);
    }

    protected function fixJson(array $responses)
    {
        foreach ($responses as &$response) {
            if (!array_key_exists('status_code', $response)) {
                $response['status_code'] = 200;
            }

            if (!array_key_exists('description', $response)) {
                $response['description'] = 'OK';
            }

            if (!array_key_exists('headers', $response) || !is_array($response['headers'])) {
                $response['headers'] = array(
                    'Content-Type' => 'application/json'
                );
            }

            if (!array_key_exists('body', $response)) {
                $response['body'] = '';
            }
        }

        return $responses;
    }

}
