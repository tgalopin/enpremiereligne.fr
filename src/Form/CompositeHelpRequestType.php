<?php

namespace App\Form;

use App\Model\CompositeHelpRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompositeHelpRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['required' => true])
            ->add('lastName', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('zipCode', TextType::class, ['required' => true])
            ->add('jobType', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'label.work-health' => 'health',
                    'label.work-emergency' => 'emergency',
                    'label.work-care' => 'care',
                    'label.work-food' => 'food',
                    'label.work-drugs' => 'drugs',
                    'label.work-energy' => 'energy',
                    'label.work-transport' => 'transports',
                    'label.work-other' => 'other',
                ],
            ])
            ->add('details', CollectionType::class, [
                'entry_type' => CompositeHelpRequestDetailType::class,
                'allow_add' => true,
            ])
            ->add('preferParents', CheckboxType::class, ['required' => false, 'mapped' => false])
            ->add('confirm', CheckboxType::class, ['required' => true, 'mapped' => false, 'constraints' => [
                new NotBlank(),
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeHelpRequest::class,
        ]);
    }
}
