<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\usuario_beer_mapping;

class CervezaMappingController extends Controller{
    
    /**
     * @Route("/user/beer/delete/{beerMappingId}"), name="user_beer_delete")
     */
    public function deleteBeerFromUser($beerMappingId, Request $request){
        $dbManager = $this->getDoctrine()->getManager();
        
        $beerMapping = $dbManager->getRepository('AppBundle:usuario_beer_mapping')
                 ->find($beerMappingId);
        
        $cerveza = $dbManager->getRepository('AppBundle:cerveza')
                 ->find($beerMapping->getCervezaId());
        
        $dbManager->remove($beerMapping);
        $dbManager->flush();
        
        return $this->render('cervezas/beerMappingDeleted.html.twig', [
            'beerName' => $cerveza->getNombre(),
            'beerNote' => $beerMapping->getNotes()
        ]);
    }
    
    /**
    * @Route("/user/{userId}/beer/add"), name="user_beer_add_form")
    */
    public function generateFormToAddBeerUser($userId, Request $request){
        $dbManager = $this->getDoctrine()->getManager();
        
        if(isset($_POST["beerid"])){
            $beerId = $_POST["beerid"];
            $notes = $_POST["notes"];
            
            $this->addBeerToUser($userId, $beerId, $notes);
            
            return $this->redirectToRoute('user_dashboard', array(
               'userName' => $request->getSession()->get('userName'),
               'userId' => $userId
            ), 307);
            
        }else{
            $cervezas = $dbManager->getRepository('AppBundle:cerveza')
                     ->findAll();

            return $this->render('cervezas/beerMappingAdd.html.twig', [
                'userId' => $userId,
                'cervezas' => $cervezas
            ]);
        }
    }
    
    private function addBeerToUser($userId, $beerId, $notes){
        $beerMapping = new usuario_beer_mapping;
        
        $beerMapping->setCervezaId($beerId);
        $beerMapping->setUserId($userId);
        $beerMapping->setNotes($notes);

        $dbManager = $this->getDoctrine()->getManager();
        $dbManager->persist($beerMapping);
        $dbManager->flush();
    }
    
    /**
    * @Route("/user/beer/edit/{beerMappingId}"), name="user_beer_note_edit")
    */
    public function editBeerNote($beerMappingId, Request $request){
        $dbManager = $this->getDoctrine()->getManager();
        
        $beerMapping = $dbManager->getRepository('AppBundle:usuario_beer_mapping')
                 ->find($beerMappingId);
                
        $form = $this->createFormBuilder($beerMapping)
                ->add('notes', TextType::class)
                ->add('aceptar', SubmitType::class)
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $beerMapping->setNotes($form['notes']->getData());
            
            $dbManager->flush();
            
            return $this->redirectToRoute('user_dashboard', array(
                'userName' => $request->getSession()->get('userName'),
                'userId' => $request->getSession()->get('userId')
                ), 307);
        }
        
        return $this->render('cervezas/beerMappingNoteEdit.html.twig', [
            'editnoteform' => $form->createView()]);
    }
}
