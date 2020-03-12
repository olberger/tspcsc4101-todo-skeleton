<?php

namespace App\Form;

use App\Entity\Todo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TodoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        $builder
        ->add('title')
        ->add('completed');

        if ( array_key_exists('data', $options) ) {
            // Check if the Todo::project must be modifiable in the form
            $todo = $options['data'];
            // if this is a new todo being created
            if ( $todo && ! $todo->getId() ) {
                // and if we aren't already in the context of a project 
                if (! $todo->getProject() ) {
                    // add the possibility to add it to chosen project
                    $builder->add('project');
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        //dump($resolver);
        $resolver->setDefaults([
            'data_class' => Todo::class,
        ]);
    }
}
