<?php

namespace clima\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
class UsuariosController extends Controller {

 public function listarAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $paginator  = $this->get('knp_paginator');
        $form = $this->createFormBuilder()
            ->add('BtnEliminar', 'submit', array('label'  => 'Eliminar',))
            ->getForm();
        $form->handleRequest($request);
        // Eliminar registros
        if($form->isValid()) {
            $arrSeleccionados = $request->request->get('ChkSeleccionar');
            if(count($arrSeleccionados) > 0) {
                foreach ($arrSeleccionados AS $id) {
                    $arUsuario = new \clima\FrontendBundle\Entity\User();
                    $arUsuario = $em->getRepository('climaFrontendBundle:User')->find($id);
                    $em->remove($arUsuario);
                    $em->flush();
                }
            }
        }
        // Fin Eliminar registros
        $arUsuarios = new \clima\FrontendBundle\Entity\User();
        $query = $em->getRepository('climaFrontendBundle:User')->findAll();
        $arUsuarios = $paginator->paginate($query, $this->get('request')->query->get('page', 1),9);
              
        return $this->render('climaFrontendBundle:Security:listar.html.twig', array(
                    'arUsuarios' => $arUsuarios,
                    'form'=> $form->createView()
           
        ));
    }
   
 public function nuevoAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $arUsuario = new \clima\FrontendBundle\Entity\User();
        $formUsuario = $this->createFormBuilder($arUsuario)        
            ->add('username','email')
            ->add('password','password')
            ->add('nombreCorto','text')
            ->add('role', 'choice', array('choices' => array('' => 'Seleccione el Rol', 'admin' => 'Administrador','user'=>'Usuario')))
            ->add('nombreCorto','text')
            ->add('isActive','hidden', array('data' => 1))
            ->add('save', 'submit', array('label'  => 'Guardar'))
            ->getForm();
        $formUsuario->handleRequest($request);
        
        if ($formUsuario->isValid())
        {
            // guardar la tarea en la base de datos
            $factory = $this->get('security.encoder_factory');                        
            $encoder = $factory->getEncoder($arUsuario);            
            $password = $encoder->encodePassword($arUsuario->getPassword(), $arUsuario->getsalt());
            $arUsuario->setPassword($password);                       
            $arUsuario->setusername($formUsuario->get('username')->getData());
            $arUsuario->setnombreCorto($formUsuario->get('nombreCorto')->getData());
            
            $arUsuario->setrole($formUsuario->get('role')->getData());
            $arUsuario->setemail($formUsuario->get('username')->getData());
            $arUsuario->setisActive($formUsuario->get('isActive')->getData());
            $em->persist($arUsuario);
            $em->flush();
            return $this->redirect($this->generateUrl('clima_usuarios_listar'));
        }

        return $this->render('climaFrontendBundle:Security:nuevo.html.twig', array(
            'formUsuario' => $formUsuario->createView(),
        ));
    }
    
  public function editarAction($id) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $arUsuario = new \clima\FrontendBundle\Entity\User();
        $arUsuario = $em->getRepository('climaFrontendBundle:User')->find($id);
        $formUsuario = $this->createFormBuilder($arUsuario)        
            ->add('username','email', array('data' => $arUsuario->getemail()))
            ->add('password','password')
            ->add('nombreCorto','text', array('data' => $arUsuario->getnombreCorto()))
            ->add('role', 'choice', array('choices' => array($arUsuario->getrole() => $arUsuario->getrole(), 'admin' => 'Administrador','user'=>'Usuario')))
            ->add('nombreCorto','text', array('data' => $arUsuario->getnombreCorto()))
            ->add('isActive','text', array('data' => 1))
            ->add('save', 'submit', array('label'  => 'Guardar'))
            ->getForm();
        $formUsuario->handleRequest($request);
        
        if ($formUsuario->isValid())
        {
            // guardar la tarea en la base de datos
            $arUsuario->setusername($formUsuario->get('username')->getData());
            $arUsuario->setPassword($formUsuario->get('password')->getData());
            $arUsuario->setnombreCorto($formUsuario->get('nombreCorto')->getData());
            $arUsuario->setsalt($formUsuario->get('username')->getData());
            $arUsuario->setrole($formUsuario->get('role')->getData());
            $arUsuario->setemail($formUsuario->get('username')->getData());
            $arUsuario->setisActive($formUsuario->get('isActive')->getData());
            $em->persist($arUsuario);
            $em->flush();
            return $this->redirect($this->generateUrl('clima_usuarios_listar'));
        }

        return $this->render('climaFrontendBundle:Security:editar.html.twig', array(
            'formUsuario' => $formUsuario->createView(),
        ));
    }
}