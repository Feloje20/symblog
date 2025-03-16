<?php

namespace App\Controllers;

use App\Models\Usuario; 
use Respect\Validation\Validator as v;

class UserController extends BaseController
{
    public function userAction($request)
    {
        // Verificar si el formulario ha sido enviado por POST
        if ($request->getMethod() == "POST") {
            $validador = v::key('user', v::stringType()->notEmpty())
                ->key('password', v::stringType()->notEmpty())
                ->key('email', v::email()->notEmpty());

            try {
                // Validar los datos del formulario
                $validador->assert($request->getParsedBody());

                // Crear un nuevo objeto de Usuario
                $user = new Usuario(); // Aquí asegúrate que 'Usuario' es el modelo correcto
                $user->nombre = $request->getParsedBody()['nombre'];
                $user->user = $request->getParsedBody()['user'];
                $user->password = $request->getParsedBody()['password'];
                $user->email = $request->getParsedBody()['email'];
                $user->profile = 'usuario';

                // Guardar el nuevo usuario en la base de datos
                $user->save();
                // Redirigir al usuario a la página de inicio o login
                header("Location: /login");
                exit; // Asegúrate de llamar a exit después de redirigir

            } catch (\Exception $e) {
                // En caso de error, mostrar el mensaje de error
                $response = "Error: " . $e->getMessage();
            }
        }

        // Preparar los datos para renderizar la vista
        $data = [
            "response" => $response ?? "", // Mostrar el mensaje de error o éxito
        ];

        // Renderizar la vista de registro
        return $this->renderHTML("register.twig", $data);
    }
}
