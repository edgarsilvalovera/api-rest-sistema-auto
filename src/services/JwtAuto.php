<?php
    
namespace App\Service;

use Firebase\JWT\JWT;
use App\Entity\Auto;


class JwtAuto{

    public $manager;
    public $key;

    public function __construct($manager){
        $this->manager = $manager;
        $this->key = 'Key_App_Servicio_Mecanico_Autos_09_02_2021';
    }

    public function validarPatente($patente, $getToken = null){
        //Comprobar si el usuario existe

        //$auto_repo = $this->getDoctrine()->getRepository(Auto::class);

        $auto = $this->manager->getRepository(Auto::class)->findOneBy([
            'patente' => $patente
        ]);

        $validarPatente = false;
        if(is_object($auto)){
            $validarPatente = true;
        }

        //Si existe generar el token
        if($validarPatente){
            $token = [
                'id' => $auto->getId(),
                'patente' => $auto->getPatente(),
                'iat'   => time(),
                'exp'   => time() + (7 * 24 * 60 * 60)
            ];

            //Cmprobar el flag getToken
            $jwt = JWT::encode($token, $this->key, 'HS256');

            if(!empty($getToken)){                
                $data = $jwt;
            }else{
                $decode = JWT::decode($jwt, $this->key, ['HS256']);
                $data = $decode;
            }                
        }else{
            $data = [
                'status' => 'error',
                'message' => 'Patente No Encontrada'
            ];
        }

        //Devolver los datos
        return $data;
    }

    public function checkToken($jwt){
        $auto = false;

        try{
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }catch(\SignatureInvalidException $e){
            $auth = false;
        }


        $decoded = JWT::decode($jwt, $this->key, ['HS256']);

        if(isset($decoded) && !empty($decoded) && is_object($decoded) && isset($decoded->patente)){
            $auto = true;
        }else{
            $auto = false;
        }

        return $auto;
    }
}
?>