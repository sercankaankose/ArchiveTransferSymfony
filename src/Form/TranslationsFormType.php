<?php

namespace App\Form;

use App\Entity\Translations;
use App\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('locale', TextType::class, [
                'attr' => ['class' => 'form-control col small'],
                'label' => 'Dil'
            ])
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control col bigger'],
                'label' => 'Başlık'
            ])
            ->add('abstract', TextareaType::class, [
                'attr' => ['class' => 'form-control custom-textarea-class', 'style' => 'width: 100%; height: 250px;'],
                'label' => 'Özet'
            ])

            ->add('keywords', TextareaType::class, [
                'attr' => ['class' => 'form-control col keywords'],
                'label' => 'Anahtar Kelimeler'
            ]);

        $builder->get('keywords')->addModelTransformer(new ArrayToStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Translations::class,
        ]);
    }
}
