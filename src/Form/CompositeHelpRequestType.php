<?php

namespace App\Form;

use App\Model\CompositeHelpRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                    'Le médical ou le paramédical' => 'health',
                    'Un service d’urgence' => 'emergency',
                    'L’aide aux personnes à domicile ou en centre' => 'care',
                    'La production ou l’approvisionnement agroalimentaire' => 'food',
                    'La production ou l’approvisionnement de médicaments' => 'drugs',
                    'La production ou l’approvisionnement en énergie' => 'energy',
                    'Le service minimum de transports en commun' => 'transports',
                    'Une autre fonction nécessaire au maintien des secteurs vitaux de la Nation' => 'other',
                ],
            ])
            ->add('details', CollectionType::class, [
                'entry_type' => CompositeHelpRequestDetailType::class,
                'allow_add' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeHelpRequest::class,
        ]);
    }
}
