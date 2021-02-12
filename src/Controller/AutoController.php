<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
//use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Psr\Log\LoggerInterface;

use App\Entity\Auto;
use App\Entity\Propietario;


use App\Service\JwtAuto;

class AutoController extends AbstractController
{
    private function resJson($data){
        //Serializar Datos con Servicio Serializer
        $json = $this->get('serializer')->serialize($data, 'json');

        //Response con httfoundation
        $response = new Response();

        //Asignar contenido a la respuesta
        $response->setContent($json);

        //Indicarle el formato de respuesta        
        $response->headers->set('Content-Type', 'application/json');

        //Devolver la respuesta
        return $response;
    }

    
    public function index(LoggerInterface $logger): Response{           
        $autoRepo = $this->getDoctrine()->getRepository(Auto::class);      
        $autos = $autoRepo->findAllAutosPropietarios();        
        return $this->resJson($autos);
    }

    public function searchAuto(Request $request): Response
    {   
        $data = [
            'status' => 'error',            
            'message' => 'Parametros Incorrectos'                        
        ];  

        $marca = $request->query->get("marca");
        $modelo = $request->query->get("modelo");
        $patente = $request->query->get("patente");               

        $autoRepo = $this->getDoctrine()->getRepository(Auto::class);
        
        if(!empty($marca) || !empty($modelo) || !empty($patente) ){                
            
            $where = array();//(campo, operador, value)
            
            if(!empty($marca)) array_push($where, array('marca', 'LIKE', $marca));
            if(!empty($modelo)) array_push($where, array('modelo', 'LIKE', $modelo));
            if(!empty($patente)) array_push($where, array('patente', 'LIKE', $patente));

            //echo "<pre>";print_r($where);echo "</pre>";exit;
            
            $data = $autoRepo->findAutosPropietarios($where);
        }
        return $this->resJson($data);
    }

    public function create(Request $request, $id = null): Response{
        //Recoger los datos por post
        //$json = $request->get('json', null);
        $json = $request->getContent();

        //Docodificar el Json
        $params = json_decode($json); // convierto array
        
        //Hacer una respuesta por default
        $data = [
            'status' => 'error',            
            'message' => 'Faltan Datos'                        
        ];        

        //Comprobar y validar datos
        if($json != null){            
            $marca = (!empty($params->marca))? $params->marca : null;
            $modelo = (!empty($params->modelo))? $params->modelo : null;
            $anio = (!empty($params->anio))? $params->anio : null;
            $patente = (!empty($params->patente))? $params->patente : null;
            $color = (!empty($params->color))? $params->color : null;   
            $propietarioId = (!empty($params->propietario->id))? $params->propietario->id : null;   
            
            if(!empty($marca) && !empty($modelo) &&!empty($anio) && !empty($patente) && !empty($color)  && !empty($propietarioId) ){                
                $dateNow = new \Datetime('now');

                $doctrine = $this->getDoctrine();
                $em = $doctrine->getManager();
                $autoRepo = $doctrine->getRepository(Auto::class);

                $propietarioRepo = $doctrine->getRepository(Propietario::class);
                $propietario = $propietarioRepo->findOneBy(['id' => $propietarioId]);

                if($id == null){
                    $auto = new Auto();
                    $auto->setMarca($marca);
                    $auto->setModelo($modelo);
                    $auto->setAnio($anio);
                    $auto->setPatente($patente);
                    $auto->setColor($color);   
                    $auto->setPropietario($propietario);
                    $auto->setCreatedAt($dateNow);
                    $auto->setUpdatedAt($dateNow); 

                    //Verificar que no se repita la patente                    
                    $isssetAuto = $autoRepo->findBy(array(
                        'patente' => $patente
                    ));

                    if(count($isssetAuto) == 0){
                        //Guardo el Auto
                        $em->persist($auto);
                        $em->flush();

                        $data = [
                            'status' => 'success',                            
                            'message' => 'Auto Creado Correctamente',
                            'auto' => $auto
                        ];
                    }else{
                        $data = [
                            'status' => 'error',                            
                            'message' => 'La Patene ya se Encuentra Registrada'                        
                        ];
                    }
                }else{    
                    //Verifico que exista el Auto                
                    $auto = $autoRepo->findOneBy(array(
                        'id' => $id
                    ));                    

                    if( $auto && is_object($auto) ){
                        //Verificar que no se repita LA PATENTE                    
                        $where=[
                            array('patente', '=', $patente),
                            array('id', '<>', $id),
                        ];
                        $isssetAuto = $autoRepo->findAutosPropietarios($where, 'andWhere');

                        if(count($isssetAuto) == 0){
                            //Actualizo
                            $auto->setMarca($marca);
                            $auto->setModelo($modelo);
                            $auto->setAnio($anio);
                            $auto->setPatente($patente);
                            $auto->setColor($color);     
                            $auto->setPropietario($propietario);                          
                            $auto->setUpdatedAt($dateNow);
                            
                            $em->persist($auto);
                            $em->flush();

                            $data = [
                                'status' => 'success',                            
                                'message' => 'Auto Actualizado Correctamente',
                                'auto' => $auto
                            ];
                        }else{
                            $data = [
                                'status' => 'error',                            
                                'message' => 'La Patene ya se Encuentra Registrada'                        
                            ];
                        }
                    }else{
                        $data = [
                            'status' => 'error',                            
                            'message' => 'Id Auto Incorrecto'                        
                        ];
                    }
                }
            }
        }        

        //Hacer la respuesta en json        
        return $this->resJson($data);
        //return new JsonResponse($data);
    }    

    public function delete(Request $request, JwtAuto $jwtAuto, $id = null){                        
        //Hacer una respuesta por default
        $data = [
            'status' => 'error',            
            'message' => 'Auto no Encontrado'            
        ];

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $autoRepo = $doctrine->getRepository(Auto::class);

        $auto = $autoRepo->findOneBy(array(
            'id' => $id
        ));

        $transacciones = $auto->getTransacciones();
        // echo "<pre>";print_r(count($transacciones));echo "</pre>";
        // exit;
        if( $auto && is_object($auto) ){
            if(count($transacciones)==0){
                //Elimino
                $em->remove($auto);
                $em->flush();

                $data = [
                    'status' => 'success',                
                    'message' => 'Auto Eliminado Correctamente'                
                ];
            }
            else{
                $data = [
                    'status' => 'error',                
                    'message' => 'El Auto No puede Ser Eliminado porque tiene registro de transacciones'                        
                ];
            }
        }else{
            $data = [
                'status' => 'error',                
                'message' => 'Id Auto Incorrecto'                        
            ];
        }

        return $this->resJson($data);
    }



    /*
    public function edit(Request $request, JwtAuto $jwtAuto){
        
        //Recoger la cabecera autenticacion
        $token = $request->headers->get('Authorization');

        //Crear método para comprobar token
        $autoCheck = $jwtAuto->checkToken($token);

        //Si es correcto hacer el update
        if($autoCheck){


            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();

            $autoRepo = $doctrine->getRepository(Auto::class);
            $isssetAuto = $autoRepo->findBy(array(
                'id' => $patente
            ));
        }

        $data = [
            'status' => 'error',            
            'message' => 'Método Updates',
            'token' => $token,
            'autoCheck' => $autoCheck

        ];

        return $this->resJson($data);
    }


    public function login(Request $request, JwtAuto $jwtAuto){
        //Recibe los datos de post
        $json = $request->get('json', null);

        //Docodificar el Json
        $params = json_decode($json); // convierto array

        //Hacer una respuesta por default
        $data = [
            'status' => 'error',
            'code' => 200,
            'message' => 'El Auto no se ha Podido Encontrar'            
        ];

        //Comprobar y validar datos
        if($json != null){            
            $marca = (!empty($params->marca))? $params->marca : null;
            $modelo = (!empty($params->modelo))? $params->modelo : null;
            $anio = (!empty($params->anio))? $params->anio : null;
            $patente = (!empty($params->patente))? $params->patente : null;
            $color = (!empty($params->color))? $params->color : null;   
            
            $getToken = (!empty($params->gettoken))? $params->gettoken : null;   
            
            if(!empty($marca) && !empty($modelo) &&!empty($anio) && !empty($patente) && !empty($color) ){
                
                //Cifro algun dato
                $cifradoPatente = hash('sha256', $patente);

                //Crear Servicio JWT
                if($getToken){
                    $data = $jwtAuto->validarPatente($patente, $getToken);
                }else{
                    $data = $jwtAuto->validarPatente($patente);
                }
            }
        }
        
        return new JsonResponse($data);
        return $this->resJson($data);
    }

    */
}
