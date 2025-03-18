<?php

namespace App\Controllers;

use App\Models\Blog;
use App\Models\Comment;

class IndexController extends BaseController
{
    // Mostrar la vista de index
    public function indexAction()
    {
        $blogs = Blog::with('comments')->orderBy('created_at', 'desc')->get();
        $profile = $_SESSION['perfil'] ?? '';
    
        return $this->renderHTML('index_view.twig', [
            'blogs' => $blogs, 
            'profile' => $profile
        ]);
    }
    
    // Mostrar la vista de about
    public function aboutAction()
    {
        $tags = Blog::getAllTags();
        $profile = $_SESSION['perfil'] ?? '';
        return $this->renderHTML('about.twig', ['tags' => $tags, 'profile' => $profile]);
    }

    // Mostrar la vista de admin
    public function adminAction()
    {
        $tags = Blog::getAllTags();
        $profile = $_SESSION['perfil'] ?? '';
        return $this->renderHTML('admin.twig', ['tags' => $tags, 'profile' => $profile]);
    }
}
