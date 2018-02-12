<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\usuario;

class UserController extends Controller
{
    /**
    * @Route("/", name="index")
    */
    public function index(Request $request){
        $session = $request->getSession();
        $userName = $session->get('userName');
        $userId = $session->get('userId');
        
        if($userName != null && $userId != null ){
            return $this->redirectToRoute('user_dashboard', array(
                        'userName' => $userName,
                        'userId' => $userId
                ), 307);
        }else{            
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
    }
    
    /**
    * @Route("/user/views/login", name="login")
    */
    public function login(Request $request){
        $form = $request->request->get('form');
                            
        if($form['user'] != null && $form['pass'] != null){
            return $this->processUserAndPass($form['user'], $form['pass']);
        }else{
            return $this->render('usuarios/loginMessage.html.twig', [
                    'header' => "¡Login Incorrecto",
                    'message' => "User y/o password no vienen en la peticion!",
                    'button' => "Reintentar"
                ]);;
        }
    }
    
    private function processUserAndPass($user, $pass){
        $usuario = $this->getDoctrine()
                ->getRepository('AppBundle:usuario')
                ->findOneByUser($user);

        if($usuario != null && $usuario->getPass() == $pass){
                $session = new Session();

                $session->set('userName', $usuario->getNombre());
                $session->set('userId', $usuario->getId());

            return $this->redirectToRoute('user_dashboard', array(
                    'userName' => $usuario->getNombre(),
                    'userId' => $usuario->getId()
                ), 307);
        }else{
            return $this->render('usuarios/loginMessage.html.twig', [
                    'header' => "¡Login Incorrecto",
                    'message' => "Usuario y/o password incorrectos",
                    'button' => "Reintentar"
                ]);
        }
    }
    
    /**
    * @Route("/user/{userId}", name="user_dashboard")
    */
    public function viewCervezasPorUsuario($userId, Request $request){
        $userName = $request->get('userName');
        
        if($userName == null){
            $userName = $request->getSession()->get('userName');
        }
        
        if($userName == null){
            return $this->render('usuarios/loginMessage.html.twig', [
                    'header' => "¡Sesion invalida!",
                    'message' => "La sesion no es correcta",
                    'button' => "Volver al login"
                ]);;
        }
        
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
            $userExist = $this->getDoctrine()
                    ->getRepository('AppBundle:usuario')
                    ->findBy(array('user' => $form['user']->getData()));
            
            if($userExist != null){
                return $this->render('usuarios/loginMessage.html.twig', [
                    'header' => "¡Usuario ya registrado!",
                    'message' => "Elige un Username diferente",
                    'button' => "Volver al Login"
                ]);
            }
            
            $usuario->setNombre($form['nombre']->getData());
            $usuario->setPass($form['pass']->getData());
            $usuario->setUser($form['user']->getData());
            
            $userDao = $this->getDoctrine()->getManager();
            $userDao->persist($usuario);
            $userDao->flush();
            
            return $this->render('usuarios/registroCorrecto.html.twig');
        }
        
        return $this->render('usuarios/registro.html.twig', [
            'registerform' => $form->createView()]);
    }
             
    /**
    * @Route("/user/{userId}/update")
    */
    public function update($userId, Request $request){
                       $dbManager = $this->getDoctrine()->getManager();
        
        $usuario = $dbManager->getRepository('AppBundle:usuario')
                 ->find($userId);
                
        $form = $this->createFormBuilder($usuario)
                ->add('pass', PasswordType::class, array(
                        'required' => false,
                        'empty_data' => $usuario->getPass(),
                        'attr' => array(
                            'placeholder' => 'Nueva pass'
                        )
                    ))
                ->add('nombre', TextType::class, array(
                        'required' => false,
                        'empty_data' => $usuario->getNombre(),
                        'attr' => array(
                            'placeholder' => 'Nuevo Nombre'
                        )
                    ))
                ->add('actualizar', SubmitType::class)
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){            
            $usuario->setNombre($form['nombre']->getData());
            $usuario->setPass($form['pass']->getData());
                      
            $dbManager->flush();
            
            return $this->redirectToRoute('user_dashboard', array(
                    'userName' => $usuario->getNombre(),
                    'userId' => $userId
                ), 307);
        }
        
        return $this->render('usuarios/update.html.twig', [
            'updateuserform' => $form->createView()]);
    }
    
    /**
    * @Route("/user/{userId}/delete")
    */
    public function delete($userId, Request $request){
 
    }
    
        /**
    * @Route("/user/{id}/logout")
    */
    public function logout(Request $request){
        $request->getSession()->clear();
        
        return $this->render('usuarios/loginMessage.html.twig', [
            'header' => "Desconnectando..",
            'message' => "Te has desconectado satisfactoriamente",
            'button' => "Volver al Login"
            ]);
    } 
}
