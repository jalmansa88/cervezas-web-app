<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class CervezaController extends Controller
{
    
    /**
    * @Route("/create")
    */
    public function create(Request $request){
        
    }
    
    /**
    * @Route("/read")
    */
    public function read(Request $request){
        
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
