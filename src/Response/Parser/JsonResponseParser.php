<?php

namespace FINDOLOGIC\Asurm\Response\Parser;

class JsonResponseParser
{
    public static function parse(string $data): array
    {
        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }
}
