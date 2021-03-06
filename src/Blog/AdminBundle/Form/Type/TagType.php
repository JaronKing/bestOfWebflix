<?php

namespace Blog\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TagType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'attr' => array( 'class' => 'form-control' )
            ))
            ->add('description', 'textarea', array(
                'attr' => array( 'class' => 'form-control' )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Blog\AdminBundle\Entity\Tag'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blog_adminbundle_tag';
    }
}
