<?php
namespace App\Controllers;
use Laminas\Diactoros\Response\TextResponse;
use App\Models\Comment;
use App\Models\Blog;

class BaseController {

    protected $templateEngine;

    public function __construct() {
        $loader = new \Twig\Loader\FilesystemLoader("../views");

        $this->templateEngine = new \Twig\Environment($loader, [
            "debug" => true,
            "cache" => false
        ]);
    }

    public function renderHTML($fileName, $data = []) {
        // Obtener los últimos 5 comentarios por defecto
        $allComments = Comment::orderBy('created_at', 'desc')->limit(5)->get();

        // Obtener la nube de tags por defecto
        $tags = Blog::getAllTags();

        // Fusionar los comentarios y los tags con los datos pasados a la vista
        $data['allComments'] = $allComments;
        $data['tags'] = $tags;

        // Renderizar la plantilla con los datos (comentarios, tags + los que pasas)
        return new TextResponse($this->templateEngine->render($fileName, $data));
    }
}

