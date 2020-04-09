<?php

namespace App\Form;

use App\Entity\Helper;
use App\Entity\HelpRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class HelperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['required' => true])
            ->add('lastName', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('zipCode', TextType::class, ['required' => true])
            ->add('age', NumberType::class, ['required' => true, 'html5' => true])
            ->add('haveChildren', CheckboxType::class, ['required' => false])
            ->add('canBabysit', CheckboxType::class, ['required' => false])
            ->add('babysitMaxChildren', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'label.only-one' => 1,
                    'label.up-to-two' => 2,
                    'label.up-to-three' => 3,
                    'label.up-to-four' => 4,
                ],
            ])
            ->add('babysitAgeRanges', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'label.between-0-1' => HelpRequest::AGE_RANGE_01,
                    'label.between-1-2' => HelpRequest::AGE_RANGE_12,
                    'label.between-3-5' => HelpRequest::AGE_RANGE_35,
                    'label.between-6-9' => HelpRequest::AGE_RANGE_69,
                    'label.between-10-12' => HelpRequest::AGE_RANGE_1012,
                    'label.13-and-over' => HelpRequest::AGE_RANGE_13,
                ],
            ])
            ->add('acceptVulnerable', CheckboxType::class, ['required' => false])
            ->add('canBuyGroceries', CheckboxType::class, ['required' => false])
            ->add('confirm', CheckboxType::class, ['required' => true, 'mapped' => false, 'constraints' => [
                new NotBlank(),
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Helper::class,
        ]);
    }
}
