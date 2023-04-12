<?php

namespace App;

use Slimfony\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public static function getProjectDir(): string
    {
        return dirname(__DIR__);
    }
}
