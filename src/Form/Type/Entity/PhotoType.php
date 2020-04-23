<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Album;
use App\Entity\Photo;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\FileToMediumTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class PhotoType
 *
 * @package App\Form\Type\Entity
 */
class PhotoType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FileToMediumTransformer
     */
    private $fileToMediumTransformer;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * PhotoType constructor.
     * @param EntityManagerInterface $em
     * @param FileToMediumTransformer $fileToMediumTransformer
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $em, FileToMediumTransformer $fileToMediumTransformer, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->fileToMediumTransformer = $fileToMediumTransformer;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
            ])
            ->add('place', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false,
            ])
            ->add('takenAt', DateType::class, [
                'required' => false,
                'html5' => false,
                'widget' => 'single_text',
                'format' => $this->tokenStorage->getToken()->getUser()->getDateFormatForForm()
            ])
            ->add(
                $builder->create('image', FileType::class, [
                    'required' => false,
                    'label' => false,
                ])->addModelTransformer($this->fileToMediumTransformer)
            )
            ->add('album', EntityType::class, [
                'class' => Album::class,
                'choice_label' => 'title',
                'choices' => $this->em->getRepository(Album::class)->findAll(),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => true,
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Photo::class
        ]);
    }
}
