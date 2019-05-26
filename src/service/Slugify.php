<?php


namespace App\service;


class Slugify
{
public function  generate(string $input) : string
{
    $word=str_replace(' ','-',$input);
    return  preg_replace('/[^A-Za-z0-9\-]/', '', $word);
}
}