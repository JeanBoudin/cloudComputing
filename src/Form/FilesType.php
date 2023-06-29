<?php

    // src/Form/FileUploadType.php
    namespace App\Form;
    
    use App\Entity\Files;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\Extension\Core\Type\HiddenType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\File;
    
    class FilesType extends AbstractType
    {
      public function buildForm(FormBuilderInterface $builder, array $options)
      {
          $builder
            ->add('upload_path', FileType::class, [
              'label' => 'Veuillez choisir le fichier à télécharger',
              'mapped' => false,
              'required' => true,
              'constraints' => [
                new File([
                  'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
                    'image/jpg',
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'application/pdf',
                  ],
                  'maxSize' => '300k',
                ])
              ],
            ])
              ->add('filename', TextType::class)

              ->add('send', SubmitType::class);
      }
    }
