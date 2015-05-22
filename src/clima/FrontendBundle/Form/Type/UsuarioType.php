<?php
namespace helpdesk\SoporteBundle\Form\Type;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
        ->add('username','text')
        ->add('nombreCorto','text') 
        ->add('salt','text')
        ->add('password','text')
        ->add('role', 'choice', array('choices' => array('admin' => 'Administrador','user'=>'usuario')))
        ->add('email', 'text', array('attr' => array('class' => 'email-box', 'style' => 'width: 260px')))   
        ->add('save', 'submit', array('label'  => 'Guardar'));
       
    }
 
    public function getName()
    {
        return 'usuario';
    }
}
