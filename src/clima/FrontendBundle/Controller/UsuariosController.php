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
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {            
            $arrControles = $request->request->All();
            $arUsuario = new \clima\FrontendBundle\Entity\User();            
            $factory = $this->get('security.encoder_factory');                        
            $encoder = $factory->getEncoder($arUsuario);
            $password = $encoder->encodePassword($arrControles['TxtPassword'], $arUsuario->getSalt());
            $arUsuario->setPassword($password);                        
            $arUsuario->setUsername($arrControles['TxtUsuario']);
            $arUsuario->setNombreCorto($arrControles['TxtNombreCorto']);
            $arUsuario->setEmail($arrControles['TxtUsuario']);            
            $em->persist($arUsuario);
            $em->flush();
            return $this->redirect($this->generateUrl('login'));
        }
        return $this->render('climaFrontendBundle:Security:nuevo.html.twig');
    }
    
    public function editarAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $arUsuario = new \clima\FrontendBundle\Entity\User();
        $arUsuario = $em->getRepository('climaFrontendBundle:User')->find($id);
        
        if ($request->getMethod() == 'POST') {            
            $arrControles = $request->request->All();
            $factory = $this->get('security.encoder_factory');                        
            $encoder = $factory->getEncoder($arUsuario);            
            $password = $encoder->encodePassword($arrControles['TxtPassword'], $arUsuario->getSalt());
                                    
            if ($arUsuario->getPassword() == $arrControles['TxtPassword'] )
            {    
                $arUsuario->setUsername($arrControles['TxtUsuario']);
                $arUsuario->setNombreCorto($arrControles['TxtNombreCorto']);
                $arUsuario->setEmail($arrControles['TxtUsuario']);            
                $em->persist($arUsuario);
                $em->flush();
                return $this->redirect($this->generateUrl('clima_usuarios_listar'));
            }
            else
            {
                $arUsuario->setPassword($password);
                $arUsuario->setUsername($arrControles['TxtUsuario']);
                $arUsuario->setNombreCorto($arrControles['TxtNombreCorto']);
                $arUsuario->setEmail($arrControles['TxtUsuario']);            
                $em->persist($arUsuario);
                $em->flush();
                return $this->redirect($this->generateUrl('clima_usuarios_listar'));
            }
        }
        return $this->render('climaFrontendBundle:Security:editar.html.twig', array(
                    'arUsuario' => $arUsuario));
    }
}