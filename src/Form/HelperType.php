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
                    'Un seul' => 1,
                    'Jusqu\'à 2' => 2,
                    'Jusqu\'à 3' => 3,
                    'Jusqu\'à 4' => 4,
                ],
            ])
            ->add('babysitAgeRanges', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Entre 0 et 1 an' => HelpRequest::AGE_RANGE_01,
                    'Entre 1 et 2 ans' => HelpRequest::AGE_RANGE_12,
                    'Entre 3 et 5 ans' => HelpRequest::AGE_RANGE_35,
                    'Entre 6 et 9 ans' => HelpRequest::AGE_RANGE_69,
                    'Entre 10 et 12 ans' => HelpRequest::AGE_RANGE_1012,
                    '13 ans et plus' => HelpRequest::AGE_RANGE_13,
                ],
            ])
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
