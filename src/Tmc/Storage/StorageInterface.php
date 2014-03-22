<?php

namespace Tmc\Storage;

interface StorageInterface
{
    public function save($token, array $responses);
    
    public function play($token);
    
    public function debug($token);
}
