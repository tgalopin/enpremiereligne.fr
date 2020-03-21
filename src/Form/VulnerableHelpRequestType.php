<?php

namespace App\Form;

use App\Model\VulnerableHelpRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class VulnerableHelpRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isCloseOne', CheckboxType::class, ['required' => false, 'mapped' => false])
            ->add('ccEmail', TextType::class, ['required' => true])
            ->add('firstName', TextType::class, ['required' => true])
            ->add('lastName', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('zipCode', TextType::class, ['required' => true])
            ->add('confirm', CheckboxType::class, ['required' => true, 'mapped' => false, 'constraints' => [
                new NotBlank(),
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VulnerableHelpRequest::class,
        ]);
    }
}
