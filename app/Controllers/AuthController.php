<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Controllers\BaseController;
use \Respect\Validation\Validator as v;

class AuthController extends BaseController
{
    // Método de login del usuario
    public function loginAction($reqMethod)
    {
        $profile = $_SESSION['perfil'] ?? '';
        $data['profile'] = $profile;

        if ($reqMethod->getMethod() == 'POST') {
            $validator = v::key('email', v::stringType()->notEmpty())
                ->key('password', v::stringType()->notEmpty());
            try {
                $validator->assert($reqMethod->getParsedBody());
                $user = Usuario::where('email', $reqMethod->getParsedBody()['email'])->first();
                if ($user && $reqMethod->getParsedBody()['password'] === $user->password) {
                    $_SESSION['user'] = $user->user; 
                    $_SESSION['perfil'] = 'usuario';
                    header('Location: /');
                    exit();
                } else {
                    // Aquí asignas el error correctamente
                    $data['error'] = 'Usuario o contraseña incorrectos';
                }
                
            } catch (\Exception $e) {
                // Aquí asignas el error correctamente
                $data['error'] = 'Error: ' . $e->getMessage();
            }
        } else {
            return $this->renderHTML('login.twig', ['data' => $data]);
        }
        
    
        // Asegúrate de pasar los datos a la vista
        return $this->renderHTML('login.twig', ['data' => $data]);
    }
    
    // Método de deslogeo
    public function logoutAction()
    {
        session_start();
        session_destroy();
        session_abort();
        session_unset();
        header('Location: /');
    }
}
