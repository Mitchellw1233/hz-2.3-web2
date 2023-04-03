<?php

namespace App\Controller;

class BlogApiController
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