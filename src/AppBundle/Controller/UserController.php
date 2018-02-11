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
    * @Route("/")
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
            'loginform' => $form->createView()
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
        }
        
        return $this->render('usuarios/registro.html.twig',
                ['registerform' => $form->createView()]);
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
        
//        $response = json_decode($this->forward('AppBundle:Cerveza:getBeerByUserId', array(
//                'userId'  => $userId
//            )));
//
//        $cervezas = $response['cervezas'];
        $cervezas = $this->getDoctrine()->getManager()  //getRepository('AppBundle:cerveza', 'c')
                ->createQueryBuilder()
                ->select('c.id', 'c.nombre', 'c.alc', 'ubm.notes')
                ->from('AppBundle:cerveza', 'c')
                ->join('AppBundle:usuario_beer_mapping', 'ubm', 'WITH', 'ubm.cervezaId = c.id')
                ->where('ubm.userId = :id')
                ->setParameter('id', $userId)
                ->getQuery()
                ->getResult();

        return $this->render('usuarios/verCervezas.html.twig', [
                        'userName' => $userName,
                        'userId' => $userId,
                        'cervezas' => $cervezas
                    ]);
    }
}
