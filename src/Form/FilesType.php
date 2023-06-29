<?php

    // src/Form/FileUploadType.php
    namespace App\Form;
    
    use App\Entity\Files;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\Extension\Core\Type\HiddenType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\File;
    
    class FileUploadType extends AbstractType
    {
      public function buildForm(FormBuilderInterface $builder, array $options)
      {
          $builder
            ->add('upload_file', FileType::class, [
              'label' => 'Veuillez choisir le fichier à télecharger',
              'label_attr' => [
                'class' => 'block mb-2 text-sm font-medium text-gray-50'
              ],
              'attr' => [
                'class' => 'block w-full text-sm text-gray-900 border border-gray-300
                rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none
                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400'
              ],
              'mapped' => false,
              'required' => true,
              'constraints' => [
                new File([
                  'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
                    'text/x-comma-separated-values',
                    'text/comma-separated-values',
                    'text/x-csv',
                    'text/csv',
                    'text/plain',
                    'application/octet-stream',
                    'application/vnd.ms-excel',
                    'application/x-csv',
                    'application/csv',
                    'application/excel',
                    'application/vnd.msexcel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                  ],
                  'mimeTypesMessage' =>
                  "",
                  'maxSize' => '300k',
                ])
              ],
            ])
            ->add('send', SubmitType::class)
            ->add('type', HiddenType::class);
      }
    }
