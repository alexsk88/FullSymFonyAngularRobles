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
        $params = json_decode($json);
        
        // $sintrue = json_decode($json);
        // echo '<br>';
        // $contrue = json_decode($json, true); 
        // echo '<br>';echo '<br>';
        // echo strval($sintrue->name);

        // echo strval($contrue->name);
        // die();
        // Devolver un rta por defectos

        $data = [
            'status'=> 'error',
            'code' => 200,
            'message' => 'El Usuario No se ha creado, no se ha accedido a ninnguna funcion',
            'params' => $params,
            'name' => $params->name
        ];

        //return $this->restjson($data);
        // Validar datos
        if($json != null)
        {
            // Si la validacion es correcta
            $name = (!empty($params->name)) ? $params->name : null;
            /* Si paramas->name no es vacio tome el valor de el , si no pasela a NULL */
            $surname = (!empty($params->surname)) ? $params->surname : null;
            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;

            $validator = Validation::createValidator();

            $validate_email = $validator->validate($email,[
                new Email()
            ]);


            if(!empty($name) && 
               !empty($surname) &&
               !empty($email) &&
               !empty($password) &&
               count($validate_email) == 0)
            {
                // Crear Objeto del Usuario

                $user = new User();
                $user->setName($name);
                $user->setSurname($surname);
                $user->setEmail($email);
                $user->setRole('ROLE_ADMIN');
                $user->setCreatedAt(new \DateTime('now'));
                // Cifrar Password
                $pwd = hash('sha256', $password);
                $user->setPassword($pwd);

                $doctrine = $this->getDoctrine();
                $bd = $doctrine->getManager();
                
                $repo_user = $doctrine->getRepository(User::class);
                
                $isset_email = $repo_user->findBy(array(
                    'email'=> $email
                ));

                // Control de Duplicado
                if(count($isset_email) == 0)
                {
                    // Save datos

                    $bd->persist($user);
                    // Guardar TEMPORALMENTE en el ORM de symfony

                    $bd->flush();
                    // Haga la consulta insert y guardelo

                    $data = [
                        'status'=> 'suceess',
                        'code' => 200,
                        'message' => 'Usuario Guardado Correctamente',
                    ];
                }
                else
                {
                    $data = [
                        'status'=> 'suceess',
                        'code' => 200,
                        'message' => 'Email YA UTILIZADO',
                    ];
                } 
            }
            else {
                $data = [
                    'status'=> 'error',
                    'code' => 200,
                    'message' => 'Datos NOOOO Validos',
                ];
            }
        }
        else 
        {
            $data = [
                'status'=> 'error',
                'code' => 200,
                'message' => 'No hay datos enviados'
            ];
        }

        // Devolver una respuesta con la acccion
        return $this->restjson($data);

    }

    public function login(Request $request, JwtAuth $jwtAuth)
    {
        // Recibir los datos 
        $json = $request->get('json',null);
        $params = json_decode($json);


        // Array por defecto
        $data = [
            'status'=> 'error',
            'code' => 200,
            'message' => 'Error al loggearse'
        ];
        // Comprobar y valida datos

        if($json != null)
        {
            $email = (!empty($params->email)) ? $params->email: null;
            $password = (!empty($params->password)) ? $params->password: null;
            $getToken = (!empty($params->getToken)) ? $params->getToken: null;

            $validator = Validation::createValidator();

            $validate_email = $validator->validate($email,[
                new Email()
            ]);

            // return new JsonResponse($params);
            if(!empty($email) && 
               !empty($password) &&
               count($validate_email) == 0)
            {
                // Cifrar la passsword

                $pwd = hash('sha256', $password);

                // Si todo es valido, llamaremos service para comprar
                if($getToken !=  null)
                {
                    $singup = $jwtAuth->signup($email, $pwd, $getToken);
                }
                else 
                {
                    $singup = $jwtAuth->signup($email, $pwd);                
                }

                return new JsonResponse($singup);
            }
            else 
            {
                $data = [
                    'status'=> 'error',
                    'code' => 200,
                    'message' => 'Email No Valido'
                ];
            }
            
        }
        // Devolver RTA
        return $this->restjson($data);
     
    }

    public function edit(Request $request, JwtAuth $jwtAuth)
    {
        // Recoger cabecera de Autenticacion
        $token = $request->headers->get('Authorization');

        // Recojer Datos de Usuario para Update
        
        $json = $request->get('json', null);
        $params = json_decode($json); 


        // Crear un metodo CheckToken en JWT

        $authCheck = $jwtAuth->checkToken($token);

        if($authCheck)
        {
            // Coseguir Entity Mananger
            $doctrine = $this->getDoctrine();

            $bd = $doctrine->getManager();

            // Conseguir Datos de usuario 
            $UserAuth= $jwtAuth->checkToken($token, true);

            $user_repo = $doctrine->getRepository(User ::class);
            $userbd = $user_repo->findOneBy([
                'id' => $UserAuth->sub
            ]);

            // Validar datos 
            if($json != null)
            {
                $email = (!empty($params->email)) ? $params->email: null;
                $name = (!empty($params->name)) ? $params->name: null;
                $surname = (!empty($params->surname)) ? $params->surname: null;
    
                $validator = Validation::createValidator();
                $validate_email = $validator->validate($email,[
                    new Email()
                ]);
    
                
                if(!empty($email) && 
                   !empty($name) &&
                   !empty($surname) &&
                   count($validate_email) == 0)
                {
                    // Comprobar duplicados
                    $userDuplicado = $user_repo->findBy([
                        'email' => $email
                    ]);
                    // var_dump($userDuplicado); die();
                    if(count($userDuplicado) == 0 || $UserAuth->email == $email)
                    {
                        // Update User
                        $userbd->setEmail($email);
                        $userbd->setName($name);
                        $userbd->setSurname($surname);
                        // Sava User

                        $bd->persist($userbd);
                        $bd->flush();

                        $data = [
                            'status'=> 'success',
                            'messague'=> 'Usuario Actulizado',
                            'user' => $userbd
                            ];
                    }
                    else
                    {
                        $data = [
                            'status'=> 'error',
                            'messague'=> 'Este email Ya esta en uso',
                            ];
                    }
                    
                }
                else
                {
                    $data = [
                        'status'=> 'error',
                        'messague'=> 'Datos no Validos / o email no valido',
                        ];
                }
            }
            else
            {
                $data = [
                    'status'=> 'error',
                    'messague'=> 'No se enviaron datos',
                    ];
            }
        }
        else
        {
            $data = [
                'status'=> 'error',
                'messague'=> 'El Token es Invalido so',
             ];
        }

        return $this->restjson($data);
    }
}
