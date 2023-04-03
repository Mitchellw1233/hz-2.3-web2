<?php

namespace App\Controller;

use Slimfony\Routing\AbstractController;

class BlogApiController extends AbstractController
{
    public function list(): string
    {
        return 'LIST';
    }

    public function show(int $id): string
    {
        return 'SHOW = '.$id;
    }

    public function edit(int $id): string
    {
        return 'EDIT POST-'.$id;
    }
}