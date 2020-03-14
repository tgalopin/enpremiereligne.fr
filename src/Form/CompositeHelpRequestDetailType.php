<?php

namespace App\Form;

use App\Entity\HelpRequest;
use App\Model\CompositeHelpRequestDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositeHelpRequestDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('helpType', ChoiceType::class, [
                'required' => true,
                'expanded' => true,
                'choices' => [
                    'Garder un enfant' => HelpRequest::TYPE_BABYSIT,
                    'Faire les courses et les livrer' => HelpRequest::TYPE_GROCERIES,
                ],
            ])
            ->add('childAgeRange', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Entre 0 et 1 an' => HelpRequest::AGE_RANGE_01,
                    'Entre 1 et 2 ans' => HelpRequest::AGE_RANGE_12,
                    'Entre 3 et 5 ans' => HelpRequest::AGE_RANGE_35,
                    'Entre 6 et 9 ans' => HelpRequest::AGE_RANGE_69,
                    'Entre 10 et 12 ans' => HelpRequest::AGE_RANGE_1012,
                    '13 ans et plus' => HelpRequest::AGE_RANGE_13,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeHelpRequestDetail::class,
        ]);
    }
}
