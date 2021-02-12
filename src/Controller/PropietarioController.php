<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Propietario;
use App\Entity\Auto;

class PropietarioController extends AbstractController
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

    public function index(): Response{        
        $propietarioRepo = $this->getDoctrine()->getRepository(Propietario::class);        
        $propietarios = $propietarioRepo->findAllPropietarios();        
        return $this->resJson($propietarios);        
    }

    public function searchAutos($id): Response{        
        $propietarioRepo = $this->getDoctrine()->getRepository(Propietario::class);        
        $propietario = $propietarioRepo->find($id);        
        $autosPropietario = $propietario->getAutos();

        return $this->resJson($autosPropietario);        
    }

    public function searchPropietario(Request $request): Response
    {
        $data = [
            'status' => 'error',            
            'message' => 'Parametros Incorrectos'                        
        ];  

        $apellido = $request->query->get("apellido");
        $nombre = $request->query->get("nombre");
        $documento = $request->query->get("documento");

        $propietarioRepo = $this->getDoctrine()->getRepository(Propietario::class);

        if(!empty($apellido) || !empty($nombre) || !empty($documento) ){                            
            
            $where = array();//(campo, operador, value)

            if(!empty($apellido)) array_push($where, array('apellido', 'LIKE', $apellido));
            if(!empty($nombre)) array_push($where, array('nombre', 'LIKE', $nombre));
            if(!empty($documento)) array_push($where, array('documento', 'LIKE', $documento));

            $data = $propietarioRepo->findPropietarios($where);
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
            'message' => 'Propietario no Creado'                        
        ];        

        //Comprobar y validar datos
        if($json != null){            
            $apellido = (!empty($params->apellido))? $params->apellido : null;
            $nombre = (!empty($params->nombre))? $params->nombre : null;
            $documento = (!empty($params->documento))? $params->documento : null;
            $direccion = (!empty($params->direccion))? $params->direccion : null;
            $telefono = (!empty($params->telefono))? $params->telefono : null;   
            
            if(!empty($apellido) && !empty($nombre) &&!empty($documento) && !empty($direccion) && !empty($telefono) ){                
                $dateNow = new \Datetime('now');

                $doctrine = $this->getDoctrine();
                $em = $doctrine->getManager();
                $propietarioRepo = $doctrine->getRepository(Propietario::class);

                if($id == null){
                    $propietario = new Propietario();
                    $propietario->setApellido($apellido);
                    $propietario->setNombre($nombre);
                    $propietario->setDocumento($documento);
                    $propietario->setDireccion($direccion);
                    $propietario->setTelefono($telefono);                
                    $propietario->setCreatedAt($dateNow);
                    $propietario->setUpdatedAt($dateNow);                    

                    //Verificar que no se repita el documento                    
                    $issetPropietario = $propietarioRepo->findBy(array(
                        'documento' => $documento
                    ));                   

                    if(count($issetPropietario) == 0){
                        //Guardo
                        $em->persist($propietario);
                        $em->flush();

                        $data = [
                            'status' => 'success',                            
                            'message' => 'Propietario Creado Correctamente',
                            'propietario' => $propietario
                        ];
                    }else{
                        $data = [
                            'status' => 'error',                            
                            'message' => 'El Documento ya se Encuentra Registrado'                        
                        ];
                    }
                }else{    
                    //Verifico que exista el propietario                
                    $propietario = $propietarioRepo->findOneBy(array(
                        'id' => $id
                    ));

                    if( $propietario && is_object($propietario) ){
                        //Verificar que no se repita el documento                    
                        $where=[
                            array('documento', '=', $documento),
                            array('id', '<>', $propietario->getId()),
                        ];
                        $issetPropietario = $propietarioRepo->findPropietarios($where, 'andWhere');                        

                        if(count($issetPropietario) == 0){
                            //Actualizo
                            $propietario->setApellido($apellido);
                            $propietario->setNombre($nombre);
                            $propietario->setDocumento($documento);
                            $propietario->setDireccion($direccion);
                            $propietario->setTelefono($telefono);                  
                            $propietario->setUpdatedAt($dateNow);
                            
                            $em->persist($propietario);
                            $em->flush();

                            $data = [
                                'status' => 'success',
                                'code' => 200,
                                'message' => 'Propietario Actualizado Correctamente',
                                'propietario' => $propietario
                            ];
                        }else{
                            $data = [
                                'status' => 'error',                            
                                'message' => 'El Documento ya se Encuentra Registrado'                        
                            ];
                        }
                    }else{
                        $data = [
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Id Propietario Incorrecto'                        
                        ];
                    }
                }
            }
        }        

        //Hacer la respuesta en json        
        return $this->resJson($data);
        //return new JsonResponse($data);
    }

    public function delete(Request $request, $id = null): Response{                        
        //Hacer una respuesta por default
        $data = [
            'status' => 'error',            
            'message' => 'Propietario no Encontrado'            
        ];

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $propietarioRepo = $doctrine->getRepository(Propietario::class);        

        $propietario = $propietarioRepo->findOneBy(array('id' => $id));

        if( $propietario && is_object($propietario) ){
            //Verifico si el propietario tiene autos asociados                            
            $autosPropietario = $propietario->getAutos();
            $booleanDelete = true;//Se puede Eliminar
            foreach($autosPropietario as $index => $autoPropietario){
                $transacciones = $autoPropietario->getTransacciones();
                if(count($transacciones)>0){$booleanDelete=false;break;}//No se Puede Eliminar
            }
                        
            if($booleanDelete){
                //Elimino                
                $em->remove($propietario);                
                $em->flush();

                $data = [
                    'status' => 'success',                
                    'message' => 'Propietario Eliminado Correctamente'                
                ];                
            }
            else{
                $data = [
                    'status' => 'error',            
                    'message' => 'El propietario no puede ser eliminado ya que tiene registro de transacciones'            
                ];
            }
        }else{
            $data = [
                'status' => 'error',                
                'message' => 'Id Propietario Incorrecto'                        
            ];
        }
        return $this->resJson($data);
    }
}
