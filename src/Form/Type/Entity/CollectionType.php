<?php

declare(strict_types=1);

namespace App\Form\Type\Entity;

use App\Entity\Collection;
use App\Entity\Template;
use App\Enum\DisplayModeEnum;
use App\Enum\SortingDirectionEnum;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\Base64ToImageTransformer;
use App\Repository\CollectionRepository;
use App\Repository\DatumRepository;
use App\Repository\TemplateRepository;
use App\Service\FeatureChecker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as SymfonyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    public function __construct(
        private Base64ToImageTransformer $base64ToImageTransformer,
        private FeatureChecker $featureChecker,
        private CollectionRepository $collectionRepository,
        private TemplateRepository $templateRepository,
        private DatumRepository $datumRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entity = $builder->getData();

        $itemsSortingChoices = [
            'form.item_sorting.default_value' => null
        ];
        $itemsSortingChoices = array_merge($itemsSortingChoices, $this->datumRepository->findAllLabelsInCollection($entity));

        $builder
            ->add('title', TextType::class, [
                'attr' => ['length' => 255],
                'required' => true,
            ])
            ->add('childrenTitle', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false,
            ])
            ->add('itemsTitle', TextType::class, [
                'attr' => ['length' => 255],
                'required' => false,
            ])
            ->add('visibility', ChoiceType::class, [
                'choices' => array_flip(VisibilityEnum::getVisibilityLabels()),
                'required' => true,
            ])
            ->add('itemsDisplayMode', ChoiceType::class, [
                'choices' => array_flip(DisplayModeEnum::getDisplayModeLabels()),
                'required' => true,
            ])
            ->add('itemsSortingProperty', ChoiceType::class, [
                'choices' => $itemsSortingChoices,
                'required' => true,
            ])
            ->add('itemsSortingDirection', ChoiceType::class, [
                'choices' => array_flip(SortingDirectionEnum::getSortingDirectionLabels()),
                'required' => true,
            ])
            ->add('parent', EntityType::class, [
                'class' => Collection::class,
                'choice_label' => 'title',
                'choices' => $this->collectionRepository->findAllExcludingItself($entity),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'empty_data' => '',
                'required' => false,
            ])
            ->add('data', SymfonyCollectionType::class, [
                'entry_type' => DatumType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add(
                $builder->create('file', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'model_transformer' => $this->base64ToImageTransformer,
                ])
            )
        ;

        if ($this->featureChecker->isFeatureEnabled('templates')) {
            $builder->add('template', EntityType::class, [
                'class' => Template::class,
                'choice_label' => 'name',
                'choices' => $this->templateRepository->findAll(),
                'expanded' => false,
                'multiple' => false,
                'choice_name' => null,
                'required' => false,
                'mapped' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collection::class,
        ]);
    }
}
