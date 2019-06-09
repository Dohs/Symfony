<?php


namespace App\service;


class Slugify
{
    public function generate(string $input) : string
    {
        setlocale(LC_ALL, 'fr_FR.UTF8');
        $result = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input);
        $result= preg_replace('/[^A-Za-z0-9\-]/', '', $result);
        $result= str_replace('-', ' ', $result);
        return $result;
    }
}