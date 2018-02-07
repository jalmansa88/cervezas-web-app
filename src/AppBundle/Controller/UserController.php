<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\usuario;

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

        return $this->render('cervezas/index.html.twig', [
            'loginform' => $form->createView()
            ]);
    }
    
    /**
    * @Route("/views/login", name="login")
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
                return $this->render('cervezas/verCervezas.html.twig', [
                        'name' => $usuario->getNombre(),
                        'user' => $usuario->getUser()
                    ]);
            }else{
                return $this->render('cervezas/invalidLogin.html.twig', [
                        'reason' => "Usuario y/o contraseña no validos"
                    ]);
            }
        }else{
            return $this->render('cervezas/invalidLogin.html.twig', [
                    'reason' => "User y/o Pass no vienen en la peticion!"
                ]);
        }
    }
    
    /**
     * @Route("/views/user", name="user_dashboard")
     */
    public function viewCervezasPorUsuario(Request $request){
        $id = $request->query->get('id');
    }
    
    /**
    * @Route("/user/register")
    */
    public function register(Request $request){
        
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
}
