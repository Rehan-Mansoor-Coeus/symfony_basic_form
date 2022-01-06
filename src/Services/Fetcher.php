<?php
namespace App\Services;

class Fetcher
{
    public function get($url){
        $result = file_get_contents($url);
        return json_decode($result , true);
    }
}