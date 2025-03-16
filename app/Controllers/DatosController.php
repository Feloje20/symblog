<?php
namespace App\Controllers;

use App\Models\{Blog, Comment};


class DatosController
{
    public function guardarAction(){
        require_once __DIR__ . '/../../datos.php';
        $blogModel = Blog::getInstancia();
    
        // Recorremos los blogs del fichero datos.php para procesarlo
        foreach ($blogs as $blog) {
            $blogModel->setTitle($blog->getTitle());
            $blogModel->setAuthor($blog->getAuthor());
            $blogModel->setBlog($blog->getBlog());
            $blogModel->setImage($blog->getImage());
            $blogModel->setTags($blog->getTags());
            $blogModel->setCreated($blog->getCreated());
            $blogModel->setUpdated($blog->getUpdated());
    
            // Guardamos el blog
            $blogModel->set();
    
            // Miramos los comentarios del blog y lo almacenamos llamando a su modelo
            foreach ($blog->getComments() as $comment) {
                $commentModel = Comment::getInstancia();
                $commentModel->setUser($comment->getUser());
                $commentModel->setComment($comment->getComment());
                $commentModel->setBlog($blogModel->getId());
                $commentModel->setApproved($comment->getApproved());
                $commentModel->setCreated($comment->getCreated());
                $commentModel->setUpdated($comment->getUpdated());

                // Guardamos el comentario
                $commentModel->set();
            }
        }
        echo "Datos guardados correctamente";
    }
    
    
    
}
