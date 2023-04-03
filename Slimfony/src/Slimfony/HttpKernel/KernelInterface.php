<?php

namespace Slimfony\HttpKernel;

use Slimfony\HttpFoundation\Request;
use Slimfony\HttpFoundation\Response;

interface KernelInterface
{
    public function handle(Request $request): Response;
    public static function getProjectDir(): string;
}