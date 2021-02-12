<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
//use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Transaccion;
use App\Entity\Servicio;
use App\Entity\Auto;

class TransaccionController extends AbstractController
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
    
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Transaccion::class);
        $result = $repo->findAll();

        return $this->resJson($result);
    }

    public function create(Request $request): Response{
        
        //Recoger los datos por post
        //$json = $request->get('json', null);
        $json = $request->getContent();

        //Docodificar el Json
        $params = json_decode($json); // convierto array
        
        // echo "<pre>";
        // print_r($params);
        // echo "</pre>";
        // exit;

        //Hacer una respuesta por default
        $data = [
            'status' => 'error',
            'code' => 200,
            'message' => 'Transaccion no Creada'                        
        ];        

        //Comprobar y validar datos
        if($json != null && count($params)>0){   
            $dateNow = new \Datetime('now');

            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();

            $transaccion_repo = $doctrine->getRepository(Transaccion::class);
            $autoRepo = $doctrine->getRepository(Auto::class);
            $servicioRepo = $doctrine->getRepository(Servicio::class);

            $suma = 0;
            foreach($params as $index => $transaccion){
                $autoId = (!empty($transaccion->autoId))? $transaccion->autoId : null;
                $servicioId = (!empty($transaccion->servicioId))? $transaccion->servicioId : null;                  

                if(!empty($autoId) && !empty($servicioId)  ){  
                    $auto = $autoRepo->findOneBy(['id' => $autoId]);
                    $servicio = $servicioRepo->findOneBy(['id' => $servicioId]);
                
                    $transaccion = new Transaccion();
                    $transaccion->setAuto($auto);
                    $transaccion->setServicio($servicio);
                    $transaccion->setCostoServicioTransaccion($servicio->getCosto());  
                    $transaccion->setCreatedAt($dateNow);
                    $transaccion->setUpdatedAt($dateNow); 

                    $em->persist($transaccion); 
                    $transacciones[]=$transaccion;
                }
            }
            
            $suma = $suma + $servicio->getCosto();
            $em->flush();   
            $data = [
                'status' => 'success',                        
                'message' => 'Transaccion Creado Correctamente',                
                'transacciones' => $transacciones,
                'total' => $suma
            ]; 
        }        

        //Hacer la respuesta en json        
        return $this->resJson($data);
        //return new JsonResponse($data);
    }

    public function searchAutoId($autoId = null): Response
    {        
        $data = [
            'status' => 'error',            
            'message' => 'Parametro Incorrecto'                        
        ];
        
        if(is_integer(intval($autoId))){
            $autoRepo = $this->getDoctrine()->getRepository(Auto::class);
            $auto = $autoRepo->findOneBy(array('id' => $autoId));
            
            $transacciones = $auto->getTransacciones();
            
            $data = [
                'status' => 'success',            
                'transacciones' => $transacciones                        
            ];
        }

        return $this->resJson($data);        
    }
}
