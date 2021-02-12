<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Servicio;

class ServicioController extends AbstractController
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
        $repo = $this->getDoctrine()->getRepository(Servicio::class);
        $result = $repo->findAll();

        return $this->resJson($result);        
    }
}
