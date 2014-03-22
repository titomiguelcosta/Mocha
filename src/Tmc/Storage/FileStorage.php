<?php

namespace Tmc\Storage;

use Tmc\Storage\StorageInterface;

class FileStorage implements StorageInterface
{

    protected $dirname;

    public function __construct($dirname)
    {
        if (!is_dir($dirname)) {
            if (!mkdir($dirname)) {
                throw new \RuntimeException(sprintf('Unable to open dir "%s".', $dirname));
            }
        } elseif (!is_writable($dirname)) {
            throw new \RuntimeException(sprintf('Unable to write in dir "%s".', $dirname));
        }

        $this->dirname = $dirname;
    }

    public function save($token, array $responses)
    {
        $filename = $this->getFilename($token);

        $this->update($token, $responses);

        return $filename;
    }

    public function play($token)
    {
        $filename = $this->getFilename($token);
        $content = file_get_contents($filename);

        if (false === $content) {
            throw new \RuntimeException(sprintf('Could not read the contents of the file "%s".', $filename));
        }

        $json = json_decode($content, true);

        $position = $json['meta']['current'];
        $responses = $json['responses'];

        if (!array_key_exists($position, $json['responses'])) {
            throw new \RuntimeException('Unavailable response.');
        }

        if (!$this->update($token, $responses, 1 + $position)) {
            throw new \RuntimeException(sprintf('unable to update the contents of the file.'));
        }

        return $responses[$position];
    }

    public function debug($token)
    {
        
    }

    protected function getFilename($token)
    {
        return $this->dirname . '/' . $token . '.mocha';
    }

    protected function update($token, array $responses, $current = 0)
    {
        return false !== file_put_contents($this->getFilename($token), json_encode(array(
                    'meta' => array(
                        'current' => $current,
                        'total' => count($responses)
                    ),
                    'responses' => $responses
        )));
    }

}
