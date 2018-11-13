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
            ->add('completed')
        ;
        // We explicitely avoid setting 'entry_options' to array('label' => false) in the ProjectType
        // so that the label we get here isn't null when we're in a collection of sub forms
        // where its value is '__name__value__'
        if ($options['label'] != '__name__label__') {
            $builder->add('project');
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
