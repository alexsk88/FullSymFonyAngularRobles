<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use App\Services\JwtAuth;


class VideoController extends AbstractController
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
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VideoConstrollerController.php',
        ]);
    }

    public function create(Request $request, JwtAuth $jwtAuth)
    {
        // Recoger  la cabecera  de autenticacion
        $token = $request->headers->get('Authorization');

        // Comprobar Token Valido
        $checkToken = $jwtAuth->checkToken($token);

        // Si es correcto, hacer la actualizacion de Usuario
        if($checkToken)
        {
            // Recoger campos de texto
            $json = $request->get('json',null);
            $params = json_decode($json);

            // Validar Datos

            if($json !=  null)
            {
                $title = (!empty($params->title)) ? $params->title : null;
                $description = (!empty($params->description)) ? $params->description : null;
                $url = (!empty($params->url)) ? $params->url : null;

                if( !empty($title) &&
                    !empty($description) &&
                    !empty($url) )
                {

                    $doctrine = $this->getDoctrine();
                    $bd = $doctrine->getManager();

                    $userToken = $jwtAuth->checkToken($token, true);

                    $user = $doctrine->getRepository(User::class)->findOneBy([
                        'id' => $userToken->sub
                    ]);
                    
                    date_default_timezone_set('America/Bogota');
                    $videonew = new Video();
                    $videonew->setTitle($title);
                    $videonew->setUser($user);
                    $videonew->setDescription($description);
                    $videonew->setUrl($url);
                    $videonew->setStatus('normal');
                    $videonew->setCreatedAt(new \Datetime('now'));
                    $videonew->setUpdatedAt(new \Datetime('now'));

                    $bd->persist($videonew);
                    $bd->flush();

                    $data = [
                        'status'=> 'success',
                        'code' => 200,
                        'messague'=> 'Video Agregado',
                        'video' =>  $videonew
                    ];
                }
                else
                {
                    $data = [
                        'status'=> 'error',
                        'messague'=> 'Datos No validos O faltan Datos',
                    ];
                }
            }
            else
            {
                $data = [
                'status'=> 'error',
                'messague'=> 'No se han enviado Datos',
                ];
            }
        
        }
        else
        {
            $data = [
                'status'=> 'error',
                'messague'=> 'Token No valido',
             ];
        
        }

        return $this->restjson($data);
    }
}
