<?php

namespace App\Controller;

use Slimfony\HttpFoundation\Response;
use Slimfony\Routing\AbstractController;

class BlogApiController extends AbstractController
{
    public function list(): string
    {
        return 'LIST';
    }

    public function show(int $id): Response
    {
        return $this->render('index.php', [
            'postId' => $id,
            'title' => 'Show-'.$id,
        ]);
    }

    public function edit(int $id): string
    {
        return 'EDIT POST-'.$id;
    }
}