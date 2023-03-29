<?php

namespace App\Controller;

class BlogApiController
{
    public function show(int $id=null)
    {
        if (is_null($id)) return;

        echo 'POST-'.$id;
    }

    public function edit(int $id)
    {
        echo 'EDIT POST-'.$id;
    }
}