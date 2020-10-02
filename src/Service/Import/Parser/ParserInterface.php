<?php

namespace App\Service\Import\Parser;

interface ParserInterface
{
    public function parse(string $url);
}
