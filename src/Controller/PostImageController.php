<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;

class PostImageController
{
    public function __invoke(Post $post,Request $request){
        $file = $request->files->get('file');

    }

}