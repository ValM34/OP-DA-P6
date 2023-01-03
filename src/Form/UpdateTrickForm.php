<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\All;

class UpdateTrickForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('category', EntityType::class, [
              'class' => Category::class,
              'choice_label' => 'name',
            ])
            ->add('image', FileType::class, [
              'label' => 'Ajouter une image (jpg, jpeg, png)',
              'mapped' => false,
              'required' => false,
              'constraints' => [
                new All([
                  new File([
                      'maxSize' => '1024k',
                      'mimeTypes' => [
                          'image/jpg',
                          'image/jpeg',
                          'image/png'
                      ],
                      'mimeTypesMessage' => 'Veuillez upload une image sous le format jpg, jpeg ou png',
                      'maxSizeMessage' => 'La taille maximale est de 1024k. Votre image fait ({{ size }} {{ suffix }}).',
                  ])
                ])
              ],
              'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
