<?php


namespace App\service;


class Slugify
{
public function  generate(string $input) : string
{   $badLetter=['é','è','à','ç','ù',' ','\''];
    $betterLetter=['e','e','a','c','u','-'];
    $word=str_replace($badLetter,$betterLetter,$input);
    $word = preg_replace('/[^A-Za-z0-9\-]/', '', $word);
    return  rtrim($word,'-');
}
}