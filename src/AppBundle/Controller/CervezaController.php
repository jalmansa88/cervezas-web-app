<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use AppBundle\Entity\cerveza;

/**
 * @Route("/cervezas")
 */
class CervezaController extends Controller{
    
    /**
     * @Route("/panel", name="beer_panel")
     */
    public function panel(Request $request){
        $cervezas = $this->getDoctrine()
            ->getRepository('AppBundle:cerveza')
            ->findAll();
        
        return $this->render('cervezas/panel.html.twig', [
            'cervezas' => $cervezas,
            ]);
    }
    
    /**
    * @Route("/create")
    */
    public function create(Request $request){
        $cerveza = new cerveza; 
        $form = $this->createFormBuilder($cerveza)
                ->add('nombre', TextType::class ) 
                ->add('alc', NumberType::class ) 
                ->add('Crear nueva', SubmitType::class ) 
                ->getForm();

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){          
            $cerveza->setNombre($form['nombre']->getData());
            $cerveza->setAlc($form['alc']->getData());
            
            $dbManager = $this->getDoctrine()->getManager();
            $dbManager->persist($cerveza);
            $dbManager->flush();
            
            return $this->redirectToRoute('beer_panel', array(), 307);
        }
        
        return $this->render('cervezas/create.html.twig', [
            'createbeerform' => $form->createView(),
            ]);
    }
       
    /**
    * @Route("/update/{beerId}")
    */
    public function update($beerId, Request $request){
        $dbManager = $this->getDoctrine()->getManager();
        
        $beer = $dbManager->getRepository('AppBundle:cerveza')
                 ->find($beerId);
                
        $form = $this->createFormBuilder($beer)
                ->add('nombre', TextType::class)
                ->add('alc', NumberType::class)
                ->add('aceptar', SubmitType::class)
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $beer->setNombre($form['nombre']->getData());
            $beer->setAlc($form['alc']->getData());
            
            $dbManager->flush();
            
            return $this->redirectToRoute('beer_panel', array(), 307);
        }
        
        return $this->render('cervezas/update.html.twig', [
            'updatebeerform' => $form->createView()]);
    }
    
    /**
    * @Route("/delete/{beerId}")
    */
    public function delete($beerId, Request $request){
        $dmanager = $this->getDoctrine()->getManager();
        $tarea = $dmanager->getRepository('AppBundle:cerveza')
                ->find($beerId);
        
        $dmanager->remove($tarea);
        $dmanager->flush();
 
        return $this->redirectToRoute('beer_panel', array(), 307);
    }
    
    //    /**
//    * @Route("/user/{userId}", name="get_vervezas")
//    */
//    public function getBeerByUserIdAction($userId, Request $request){
//        $dbResult = $this->getDoctrine()->getRepository('AppBundle:cerveza')
//                ->createQueryBuilder('c')
//                ->join('AppBundle:usuario_beer_mapping', 'ubm', 'WITH', 'ubm.cervezaId = c.id')
//                ->where('ubm.userId = :id')
//                ->setParameter('id', $userId)
//                ->getQuery()
//                ->getArrayResult();
//        
////        foreach ($dbResult as $cerveza) {
////                $cervezas[] = array(
////                    'nombre' => $cerveza->getNombre(),
////                    'alc' => $cerveza->getAlc()
////                );
////        }
//        $response = new JsonResponse($dbResult);
//        $response->headers->set('Content-Type', 'application/json');
//        
//        return $response;
//    }
}
