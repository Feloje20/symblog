<?php
namespace App\Controllers;
use App\Models\Blog;
use App\Models\Comment;
use \Respect\Validation\Validator as v;

class BlogController extends BaseController
{
    public function blogsAction($request)
    {
        if ($request->getMethod() == "POST") {
            // Valida que los campos existan y no esten vacios
            $validador = v::key('title', v::stringType()->notEmpty())
                ->key('author', v::stringType()->notEmpty())
                ->key('blog', v::stringType()->notEmpty())
                ->key('tags', v::stringType()->notEmpty());
            try {
                // Ejecuta la validacion dobre los datos enviados en el cuerpo de la solicitud
                $validador->assert($request->getParsedBody());
                $blog = new Blog();
                $blog->title = $request->getParsedBody()['title'];
                $blog->author = $request->getParsedBody()['author'];
                $blog->blog = $request->getParsedBody()['blog'];
                $blog->tags = $request->getParsedBody()['tags'];

                $files = $request->getUploadedFiles();
                $image = $files['image'] ?? null;
                
                if ($image && $image->getError() == UPLOAD_ERR_OK) {
                    $fileName = $image->getClientFilename();
                    $fileName = uniqid() . $fileName;
                    $image->moveTo("img/$fileName");
                    $blog->image = $fileName;
                } else { // Si no hay imagen pongo una por defecto
                    $blog->image = "beach.jpg";
                }
                $blog->save();
                header("Location: /");
            } catch (\Exception $e) {
                $response = "Error: " . $e->getMessage();
            }
        }
        $data = [
            "response" => $response ?? "",
        ];
        return $this->renderHTML("addBlog.twig", $data);
    }
    public function showPostAction($request)
    {
        // Obtener el ID desde la URL (query string)
        $blogId = $request->getQueryParams()['id'] ?? null;

        // Asegurarse de que el id esté presente
        if (!$blogId) {
            // Necesitas devolver una respuesta con redirección
            header("Location: /");
            exit();
        }

        $blog = Blog::find($blogId);  // Buscar el blog con el ID proporcionado
        if (!$blog) {
            header("Location: /");
            exit();
        }

        $comments = $blog->comments;  // Obtener los comentarios del blog
        $profile = $_SESSION['perfil'] ?? '';

        return $this->renderHTML('showPost.twig', [
            'blog' => $blog,
            'comments' => $comments,
            'profile' => $profile
        ]);
    }

    // Método de adición de nuevo comentario
    public function addCommentAction($request)
    {
        $validador = v::key('user', v::stringType()->notEmpty())
            ->key('comment', v::stringType()->notEmpty())
            ->key('blog_id', v::intVal()->positive());

        try {
            $validador->assert($request->getParsedBody());

            // Creamos el objeto del modelo de comentarios
            $comment = new Comment();
            $comment->user = $request->getParsedBody()['user'];
            $comment->comment = $request->getParsedBody()['comment'];
            $comment->blog_id = $request->getParsedBody()['blog_id'];
            $comment->save();

            // Usamos la respuesta actual para redirigir
            header("Location: /showPost?id=" . $request->getParsedBody()['blog_id']);
            exit; // Asegúrate de hacer un exit para detener la ejecución después de la redirección
        } catch (\Exception $e) {
            $error = "Error: " . $e->getMessage();
            var_dump($error);
            return $this->renderHTML('showPost.twig', ['response' => $error]);
        }
    }



}