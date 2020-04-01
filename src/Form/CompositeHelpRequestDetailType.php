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
                    'label.babysitting' => HelpRequest::TYPE_BABYSIT,
                    'label.shopping-and-delivery' => HelpRequest::TYPE_GROCERIES,
                ],
            ])
            ->add('childAgeRange', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'label.between-0-1' => HelpRequest::AGE_RANGE_01,
                    'label.between-1-2' => HelpRequest::AGE_RANGE_12,
                    'label.between-3-5' => HelpRequest::AGE_RANGE_35,
                    'label.between-6-9' => HelpRequest::AGE_RANGE_69,
                    'label.between-10-12' => HelpRequest::AGE_RANGE_1012,
                    'label.13-and-over' => HelpRequest::AGE_RANGE_13,
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
