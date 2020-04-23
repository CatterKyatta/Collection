<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\Base64ToMediumTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WishlistType
 *
 * @package App\Form\Type\Entity
 */
class WishlistType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Base64ToMediumTransformer
     */
    private $base64ToMediumTransformer;

    /**
     * WishlistType constructor.
     * @param Base64ToMediumTransformer $base64ToMediumTransformer
     * @param EntityManagerInterface $em
     */
    public function __construct(Base64ToMediumTransformer $base64ToMediumTransformer, EntityManagerInterface $em)
    {
        $this->base64ToMediumTransformer = $base64ToMediumTransformer;
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();

        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => false,
            ])
            ->add('parent', EntityType::class, [
                'class' => Wishlist::class,
                'choice_label' => 'name',
                'choices' => $this->em->getRepository(Wishlist::class)->findAllExcludingItself($entity),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'empty_data' => '',
                'required' => false,
            ])
            ->add(
                $builder->create('image', TextType::class, [
                    'required' => false,
                    'label' => false,
                ])->addModelTransformer($this->base64ToMediumTransformer)
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Wishlist::class
        ]);
    }
}
