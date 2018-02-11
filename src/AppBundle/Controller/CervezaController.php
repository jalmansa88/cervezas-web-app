<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/cervezas")
 */
class CervezaController extends Controller
{
    
    /**
    * @Route("/create")
    */
    public function create(Request $request){
        
    }
    
    /**
    * @Route("/user/{userId}", name="get_vervezas")
    */
    public function getBeerByUserIdAction($userId, Request $request){
        $dbResult = $this->getDoctrine()->getRepository('AppBundle:cerveza')
                ->createQueryBuilder('c')
                ->join('AppBundle:usuario_beer_mapping', 'ubm', 'WITH', 'ubm.cervezaId = c.id')
                ->where('ubm.userId = :id')
                ->setParameter('id', $userId)
                ->getQuery()
                ->getArrayResult();
        
//        foreach ($dbResult as $cerveza) {
//                $cervezas[] = array(
//                    'nombre' => $cerveza->getNombre(),
//                    'alc' => $cerveza->getAlc()
//                );
//        }
        $response = new JsonResponse($dbResult);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
        
    /**
    * @Route("/update")
    */
    public function update(Request $request){
        
    }
    
    /**
    * @Route("/delete")
    */
    public function delete(Request $request){
        
    }
}
