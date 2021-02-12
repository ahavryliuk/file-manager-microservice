<?php

namespace FileManager\Helper;

use Carbon\Carbon;

class TypeConversionHelper
{

    /**
     * @param string|null $string
     *
     * @return Carbon|null
     */
    public static function stringToNullCarbon(?string $string): ?Carbon
    {
        return !empty($string) ? new Carbon($string) : null;
    }

    /**
     * @param string|null $jsonString
     *
     * @return array
     */
    public static function jsonToArray(?string $jsonString): array
    {
        if (empty($jsonString)) {
            return [];
        }
        $result = json_decode($jsonString, true);
        if (empty($result) || !is_array($result)) {
            return [];
        }

        return $result;
    }

}