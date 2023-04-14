<?php

namespace App\Util;

use Slimfony\HttpKernel\Exception\BadRequestException;

class Validator
{
    /**
     * @param array $data
     * @return bool
     */
    public static function validateRequired(array $data, array $required): bool
    {
        foreach ($required as $key) {
            if (!isset($data[$key])) {
                return false;
            }

            if (is_string($data[$key]) && empty($data[$key])) {
                return false;
            }
        }

        return true;
    }
}