<?php

namespace App\Form;

use App\Entity\Authors;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'form-control col half'],
                'label'=>'Ad'
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'form-control col half'],
                'label'=>'Soyad'
            ])
            ->add('orcId', TextType::class, [
                'attr' => ['class' => 'form-control col half'],
                'label'=>'Orc Id'
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control col half'],
                'label'=>'E-posta'
            ])
            ->add('institute', TextType::class, [
                'attr' => ['class' => 'form-control col trequarter'],
                'label'=>'Kurum'
            ])
            ->add('part', ChoiceType::class, [
                'choices'  => [
                    'yazar' => 'author' ,
                    'çevirmen' => 'translater',
                ],
                'attr' => ['class' => 'form-control col quarter'],
                'label'=>'Görevi'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Authors::class,
        ]);
    }
}
