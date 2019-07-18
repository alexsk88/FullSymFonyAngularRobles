<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{

    private function restjson($data)
    {
        // Conseguir servicio de Serializacion
        $json = $this->get('serializer')->serialize($data, 'json');

        // Response con HTTP Foudation
        $response = new Response();

        // Asignar contenido de la respuesta
        $response->setContent($json);

        // Indicar formato de la respuesta
        $response->headers->set('Content-Type', 'application/json');

        // Devolver una respuesta
        return $response;
    }

    public function index()
    {

        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $video_repo = $this->getDoctrine()->getRepository(Video::class);
        // Llamo los repositorios para utlizar los metodos de esas clases
        // Como un require en JS

        $users = $user_repo->findAll();
        $user = $user_repo->find(1);


        $videos = $video_repo->findAll();

        $data = [
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ];

        // foreach ($users as $user)
        // {
        //     $eje = $user->getName();
        //     echo "<h1> $eje  </h1>";

        //     foreach ($user->getVideos() as $video)
        //     {
        //         $ejero = $video->getTitle();
        //         echo "<p> $ejero  </p>";
        //     }
        // }
        // die();

        return $this->restjson($user);
    }

    public function register(Request $request)
    {
        // Recoger los datos por post
        $json = $request->get('json', null);

    
        // Decodificar JSON
        $params = json_decode($json, true);

        // Devolver un rta por defectos

        $data = [
            'status'=> 'error',
            'code' => 200,
            'message' => 'El usuario no se ha creado',
            'json' => $params
        ];

        // Validar datos

        // Si la validacion es correcta

        // Crear Objeto del Usuario

        // Control de Duplicado

        // Save datos

        // Devolver una respuesta con la acccion

        return new JsonResponse($data);
    }
}
