<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\usuario;
use AppBundle\Entity\cerveza;
use AppBundle\Entity\usuario_beer_mapping;


class UserController extends Controller
{
    /**
    * @Route("/", name="index")
    */
    public function index(Request $request){
        $usuario = new usuario; 
        $form = $this->createFormBuilder($usuario)
                ->setAction($this->generateUrl('login'))
                ->add('user', TextType::class ) 
                ->add('pass', PasswordType::class ) 
                ->add('Login', SubmitType::class ) 
                ->getForm();

        return $this->render('usuarios/index.html.twig', [
            'loginform' => $form->createView(),
            ]);
    }
    
    /**
    * @Route("/user/views/login", name="login")
    */
    public function login(Request $request){
        
        $form = $request->request->get('form');
    
        $user = $form['user'];
        $pass = $form['pass'];
        
        if($user != null && $pass != null){
            $usuario = $this->getDoctrine()
                    ->getRepository('AppBundle:usuario')
                    ->findOneByUser($user);
            
            if($usuario != null && $usuario->getPass() == $pass){
                
                return $this->redirectToRoute('user_dashboard', array(
                        'userName' => $usuario->getNombre(),
                        'userId' => $usuario->getId()
                    ), 307);
            }else{
                return $this->render('usuarios/invalidLogin.html.twig', [
                        'reason' => "Usuario y/o contraseña no validos"
                    ]);
            }
        }else{
            return $this->render('usuarios/invalidLogin.html.twig', [
                    'reason' => "User y/o Pass no vienen en la peticion!"
                ]);
        }
    }
    
    /**
    * @Route("/user/views/register")
    */
    public function register(Request $request){
        $usuario = new usuario;
        $form = $this->createFormBuilder($usuario)
                ->add('user', TextType::class)
                ->add('pass', PasswordType::class)
                ->add('nombre', TextType::class)
                ->add('aceptar', SubmitType::class)
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $user = $form['user']->getData();
            $pass = $form['pass']->getData();
            $nombre = $form['nombre']->getData();
            
            $usuario->setNombre($nombre);
            $usuario->setPass($pass);
            $usuario->setUser($user);
            
            $userDao = $this->getDoctrine()->getManager();
            $userDao->persist($usuario);
            $userDao->flush();
            
            return $this->render('usuarios/registroCorrecto.html.twig');
        }
        
        return $this->render('usuarios/registro.html.twig', [
            'registerform' => $form->createView()]);
    }
             
    /**
    * @Route("/user/update")
    */
    public function update(Request $request){
        
    }
    
    /**
    * @Route("/user/delete")
    */
    public function delete(Request $request){
        
    }    
    
    /**
    * @Route("/user", name="user_dashboard")
    */
    public function viewCervezasPorUsuario(Request $request){
        $userName = $request->get('userName');
        $userId = $request->get('userId');
               
        $cervezas = $this->getDoctrine()->getManager()  //getRepository('AppBundle:cerveza', 'c')
                ->createQueryBuilder()
                ->select('c.id', 'c.nombre', 'c.alc', 'ubm.notes', 'ubm.id AS mappingId')
                ->from('AppBundle:cerveza', 'c')
                ->join('AppBundle:usuario_beer_mapping', 'ubm', 'WITH', 'ubm.cervezaId = c.id')
                ->where('ubm.userId = :id')
                ->setParameter('id', $userId)
                ->getQuery()
                ->getResult();

        return $this->render('usuarios/verCervezas.html.twig', [
                        'userName' => $userName,
                        'userId' => $userId,
                        'cervezas' => $cervezas,
                    ]);
    }
    
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
            return $this->addBeerToUser($userId, $beerId, $notes);
            
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

        return $this->redirectToRoute('user_dashboard', array(
               'userName' => "javi",
               'userId' => 1
        ), 307);
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
            
            //$dbManager->persist($beerMapping);
            $dbManager->flush();
            
            return $this->redirectToRoute('user_dashboard', array(
                'userName' => "javier",
                'userId' => 1
                ), 307);
        }
        
        return $this->render('cervezas/beerMappingNoteEdit.html.twig', [
            'editnoteform' => $form->createView()]);
    }
}
