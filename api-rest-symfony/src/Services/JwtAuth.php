<?php 

namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;

class JwtAuth
{
    public $manager;
    private $key;

    public function __construct($manager)
    {
        $this->manager = $manager;
        $this->key = 'key_generate_51515';
    }

    public function signup($email, $password, $getToken = null)
    {
        // Comprobar USer
        $user = $this->manager->getRepository(User::class)
            ->findOneBy([
                'email' => $email,
                'password' => $password
            ]);

        $singup = false;
        if(is_object($user))
        {
            $singup = true;
        }    
        // JWT token
        if($singup)
        {
            $token = [
                'sub' =>$user->getId(),
                'name' =>$user->getName(),
                'surname' =>$user->getSurname(),
                'email' =>$user->getEmail(),
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            ];

            $jwt = JWT::encode($token,$this->key, 'HS256');

            // Comprobar el flag de GetToken
            if(!empty($getToken))
            {
                $data = $jwt;
            }
            else
            {
                $data = $jwt = JWT::decode($jwt,$this->key, ['HS256']);
            }
        }

        else {
            $data = [
                'status'=> 'error',
                'code' => 200,
                'message' => 'El usuario NO existe, LOGGIN INCORRECTO',
            ];
        }
        return $data;
    }
}
