<?php

namespace App\Form;

use App\Entity\Articles;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('primaryLanguage', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Birincil Dil'
            ])
            ->add('firstPage', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'ilk safya'
            ])
            ->add('lastPage', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'son safya'

            ])
            ->add('doi', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'doi'

            ])
            ->add('authors', CollectionType::class, [
                'entry_type' => AuthorFormType::class,
                'allow_add' => true,
                'by_reference' => false,
            ])

            ->add('citations', CollectionType::class, [
                'entry_type' => CitationsFormType::class,
                'allow_add' => true,
                'by_reference' => false,
            ])

            ->add('translations', CollectionType::class, [
                'entry_type' => TranslationsFormType::class,
                'allow_add' => true,
                'by_reference' => false,
            ]);
//            ->add('citationsText', TextareaType::class, [
//                'attr' => ['class' => 'form-control', 'style' => 'width: 100%; height: 550px;'],
//                'label' => 'Atıf ',
//                'mapped' => false,
//            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
