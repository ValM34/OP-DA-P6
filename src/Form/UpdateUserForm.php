<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class UpdateUserForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('lastName', TextType::class, ['label' => 'Nom', 'required' => false])
      ->add('firstName', TextType::class, ['label' => 'Prénom', 'required' => false])
      ->add('avatar', FileType::class, [
        'label' => 'Ajouter une image (jpg, jpeg, png)',
        'mapped' => false,
        'required' => false,
        'constraints' => [
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
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
