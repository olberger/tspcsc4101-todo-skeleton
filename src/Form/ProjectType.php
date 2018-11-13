<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('todos', CollectionType::class, array(
                'entry_type' => TodoType::class,
                // We don't follow the advice of the docs and thus need changing that label later in the JS
                // This allows us to detect when the TodoType form will be a subform
                //'entry_options' => array('label' => false), 
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
