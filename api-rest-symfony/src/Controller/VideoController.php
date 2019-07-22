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
use Knp\Component\Pager\PaginatorInterface;

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

    public function create(Request $request, JwtAuth $jwtAuth, $id=null)
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

                    // Recoger campos de texto
                    $id = $request->get('id',null);
                    if($id != null )
                    {
                        $video = $bd->getRepository(Video::class)->findOneBy(
                            [
                                'id' => $id,
                                'user' => $user
                            ]);

                        if(!is_object($video))
                        {
                            $data = [
                                'status'=> 'error',
                                'code' => 400,
                                'messague'=> 'Video No Encontrado'
                            ];
                        }
                        else
                        {   
                            $video->setTitle($title);
                            $video->setDescription($description);
                            $video->setUrl($url);
                            $video->setStatus('normal');
                            $video->setUpdatedAt(new \Datetime('now'));
                            
                            $bd->flush();
                            
                            $data = [
                                'status'=> 'success',
                                'code' => 200,
                                'messague'=> 'Editado Video',
                                'ide' => $video
                            ];
                        }
                    }
                    else
                    {
                        $bd->persist($videonew);
                        $bd->flush();
    
                        $data = [
                            'status'=> 'success',
                            'code' => 200,
                            'messague'=> 'Video Agregado',
                            'video' =>  $videonew
                        ];
                    }
                    
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

    public function videos(Request $request, JwtAuth $jwtAuth, PaginatorInterface $paginator)
    {
        // Recoger la cabecera de Auutenticacion
        $token = $request->headers->get('Authorization');

        // Comprobar el Token

        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken)
        {
            // Si es valido, conseguir identidad del Usuario

            $user_repo = $this->getDoctrine()->getRepository(User::class);

            //  Conseguir User del Token
            $userToken =  $jwtAuth->checkToken($token, true);

            $user = $user_repo->findBy([
                'id' => $userToken->sub
            ]);

            // Configurar el bundle de Paginacion
                /* Ver Documentacion knp_pagination */

            // Hacer una consulta para paginar
            $em    = $this->getDoctrine()->getManager();

            $dql   = "SELECT v FROM App\Entity\Video v WHERE v.user = {$userToken->sub} ORDER BY v.id DESC";

            $query = $em->createQuery($dql);

            // Recoger el parametro de la URL

            $page = $request->query->getInt('page', 1);

            $item_per_page = 5;

            // Llamar al objeto de la Paginacion

            $pagination = $paginator->paginate($query, $page, $item_per_page);

            $total= $pagination->getTotalItemCount();

            // Preparar array de datos para devolver
            $data = [
                'status'=> 'success',
                'code' => 200,
                'messague'=> 'PAginacion',
                'total_item_count' => $total,
                'page_actual' => $page,
                'item_per_page' => $item_per_page,
                'total_page' =>  ceil($total / $item_per_page),
                'videos' => $pagination,
                'user' => $userToken->sub
             ];
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

    public function video(Request $request, JwtAuth $jwtAuth, $id = null)
    {
        // Sacar el Token y Comprobar si es correccto
        $token = $request->headers->get('Authorization', null);
        $id = $request->get('id', null);
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken && $id != null)
        {
            // Sacar Identitad del Usuario
            $userToken = $jwtAuth->checkToken($token, true);

            $user_repo = $this->getDoctrine()->getRepository(User::class);
            $userBd = $user_repo->findOneBy([
                'id' => $userToken->sub
            ]);
     
            // Sacar el obejto de video en base al ID

            $video_repo = $this->getDoctrine()->getRepository(Video::class);

            $videoUser = $video_repo->findBy([
                'id' => $id,
                'user' => $userBd
            ]);
            
            // Veridicar si el video existe y sis es propiedad de Usuario 

            if(count($videoUser) == 0)
            {
                $data = [
                    'status'=> 'error',
                    'messague'=> 'Video No encontrado'
                 ];
            }
            else
            {
                $data = [
                    'status'=> 'success',
                    'messague'=> 'Video del usuario',
                    'video' => $videoUser
                 ];
            }

        }
        else
        {
            $data = [
                'status'=> 'error',
                'messague'=> 'Token No valido / Id No enviado'
             ];
        }

         return $this->restjson($data);
    }

    public function delete(Request $request, JwtAuth $jwtAuth, $id = null)
    {
        // Sacar el Token y Comprobar si es correccto
        $token = $request->headers->get('Authorization', null);
        $id = $request->get('id', null);
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken && $id != null)
        {
            // Sacar Identitad del Usuario
            $userToken = $jwtAuth->checkToken($token, true);

            $user_repo = $this->getDoctrine()->getRepository(User::class);
            $userBd = $user_repo->findOneBy([
                'id' => $userToken->sub
            ]);
     
            // Sacar el obejto de video en base al ID

            $video_repo = $this->getDoctrine()->getRepository(Video::class);

            $videoUser = $video_repo->findBy([
                'id' => $id,
                'user' => $userBd
            ]);
            
            // Veridicar si el video existe y sis es propiedad de Usuario 

            if(count($videoUser) == 0)
            {
                $data = [
                    'status'=> 'error',
                    'messague'=> 'Este video No es tuyo o No existe'
                 ];
            }
            else
            {
                $videotest = $video_repo->findOneBy(['id'=>$id]);
                // Es lo mismo que videoUser, pero por alguna razon
                // toca hacer una consulta con findOneBy
                $bd = $this->getDoctrine()->getManager();
                $bd->remove($videotest);
                $bd->flush();

                $data = [
                    'status'=> 'success',
                    'code' => 200,
                    'messague'=> 'Video Eliminado',
                    'video' => $videotest
                 ];
            }

        }
        else
        {
            $data = [
                'status'=> 'error',
                'messague'=> 'Token No valido / Id No enviado'
             ];
        }

         return $this->restjson($data);
    }
}
