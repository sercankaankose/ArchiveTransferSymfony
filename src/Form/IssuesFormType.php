<?php

namespace App\Form;

use App\Entity\Issues;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class IssuesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('year', ChoiceType::class, [
                'required' => true,
                'choices' => array_combine(
                    range(date('Y'), 1990),
                    range(date('Y'), 1990)
                ),
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('volume', IntegerType::class, [
                'required' => false,

                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Cilt',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Cilt  0 veya daha büyük olmalıdır.',
                    ]),
                ],
            ])
            ->add('number', IntegerType::class, [
                'required' => true,

                'constraints' => [
                    new NotBlank([
                        'message' => 'Lütfen Sayı Giriniz.',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Sayı 0 veya daha büyük olmalıdır.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('fulltext', FileType::class, [
                'required' => false,

                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Lütfen geçerli bir PDF dosyası yükleyin.',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('xml', FileType::class, [
                'required' => true,

                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '10240k',
                        'mimeTypes' => [
                            'text/xml',
                            'application/xml',
                        ],
                        'mimeTypesMessage' => 'Lütfen geçerli bir XML Dosyası Yükleyiniz.',
                    ]),

                    new NotBlank([
                        'message' => 'Lütfen Xml Dosyasını Yükleyiniz.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Issues::class,
        ]);
    }
}
