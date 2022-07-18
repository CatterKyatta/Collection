<?php

declare(strict_types=1);

namespace App\Service\Log\Logger;

use App\Entity\Collection;
use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Enum\DatumTypeEnum;
use App\Enum\LogTypeEnum;
use App\Enum\VisibilityEnum;
use App\Service\Log\Logger;

class CollectionLogger extends Logger
{
    public function getClass(): string
    {
        return Collection::class;
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function getCreateLog(LoggableInterface $collection): ?Log
    {
        if (!$this->supports($collection)) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_CREATE, $collection);
    }

    public function getDeleteLog(LoggableInterface $collection): ?Log
    {
        if (!$this->supports($collection)) {
            return null;
        }

        return $this->createLog(LogTypeEnum::TYPE_DELETE, $collection);
    }

    public function getUpdateLog(LoggableInterface $collection, array $changeset, array $relations = []): ?Log
    {
        if (!$this->supports($collection)) {
            return null;
        }
        $mainPayload = [];
        foreach ($changeset as $property => $change) {
            if (\in_array($property, ['title', 'childrenTitle', 'itemsTitle', 'visibility'])) {
                $function = 'get'.ucfirst($property);
                $mainPayload[] = [
                    'title' => $collection->getTitle(),
                    'property' => $property,
                    'old' => $changeset[$property][0],
                    'new' => $collection->$function(),
                ];
            } elseif ('image' === $property) {
                $mainPayload[] = [
                    'title' => $collection->getTitle(),
                    'property' => 'image',
                ];
            } elseif ('parent' === $property) {
                $old = $changeset['parent'][0] instanceof Collection ? $changeset['parent'][0] : null;
                $new = $collection->getParent() instanceof Collection ? $collection->getParent() : null;

                $mainPayload[] = [
                    'property' => 'parent',
                    'old_id' => $old ? $old->getId() : null,
                    'old_title' => $old ? $old->getTitle() : null,
                    'new_id' => $new ? $new->getId() : null,
                    'new_title' => $new ? $new->getTitle() : null,
                    'title' => $collection->getTitle(),
                ];
            }
        }

        if (empty($mainPayload)) {
            return null;
        }

        return $this->createLog(
            LogTypeEnum::TYPE_UPDATE,
            $collection,
            $mainPayload
        );
    }

    public function formatPayload(string $class, array $payload): ?string
    {
        if (!$this->supportsClass($class)) {
            return null;
        }

        $property = $payload['property'];
        $label = $this->translator->trans('label.'.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property)));
        switch ($property) {
            case 'visibility':
                return $this->translator->trans('log.collection.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => '<strong>'.$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['new']]).'</strong>',
                    '%old%' => '<strong>'.$this->translator->trans('global.visibilities.'.VisibilityEnum::VISIBILITIES_TRANS_KEYS[$payload['old']]).'</strong>',
                ]);
            case 'image':
                return $this->translator->trans('log.collection.image_updated', [
                    '%property%' => "<strong>$label</strong>",
                ]);
            case 'parent':
                $defaultValue = $this->translator->trans('log.collection.default_parent');
                $old = $payload['old_title'] ? $payload['old_title'] : $defaultValue;
                $new = $payload['new_title'] ? $payload['new_title'] : $defaultValue;

                return $this->translator->trans('log.collection.parent_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%new%' => "<strong>$old</strong>",
                    '%old%' => "<strong>$new</strong>",
                ]);
            case 'datum_added':
                switch ($payload['datum_type']) {
                    case DatumTypeEnum::TYPE_FILE:
                        return $this->translator->trans('log.item.file_added', [
                            '%label%' => '<strong>'.$payload['datum_label'].'</strong>',
                        ]);
                    case DatumTypeEnum::TYPE_IMAGE:
                        return $this->translator->trans('log.item.image_added', [
                            '%label%' => '<strong>'.$payload['datum_label'].'</strong>',
                        ]);
                    case DatumTypeEnum::TYPE_SIGN:
                        return $this->translator->trans('log.item.sign_added', [
                            '%label%' => '<strong>'.$payload['datum_label'].'</strong>',
                            '%value%' => '<strong>'.$payload['datum_value'].'</strong>',
                        ]);

                    default:
                        return $this->translator->trans('log.item.property_added', [
                            '%label%' => '<strong>'.$payload['datum_label'].'</strong>',
                            '%value%' => '<strong>'.$payload['datum_value'].'</strong>',
                        ]);
                }
                // no break
            case 'datum_removed':
                switch ($payload['datum_type']) {
                    case DatumTypeEnum::TYPE_FILE:
                        return $this->translator->trans('log.item.file_removed', [
                            '%label%' => '<strong>'.$payload['datum_label'].'</strong>',
                        ]);
                    case DatumTypeEnum::TYPE_IMAGE:
                        return $this->translator->trans('log.item.image_removed', [
                            '%label%' => '<strong>'.$payload['datum_label'].'</strong>',
                        ]);
                    case DatumTypeEnum::TYPE_SIGN:
                        return $this->translator->trans('log.item.sign_removed', [
                            '%label%' => '<strong>'.$payload['datum_label'].'</strong>',
                            '%value%' => '<strong>'.$payload['datum_value'].'</strong>',
                        ]);

                    default:
                        return $this->translator->trans('log.item.property_removed', [
                            '%label%' => '<strong>'.$payload['datum_label'].'</strong>',
                            '%value%' => '<strong>'.$payload['datum_value'].'</strong>',
                        ]);
                }
                // no break
            default:
                $defaultValue = $this->translator->trans('log.default_value');
                $old = $payload['old'] ? $payload['old'] : $defaultValue;
                $new = $payload['new'] ? $payload['new'] : $defaultValue;

                return $this->translator->trans('log.collection.property_updated', [
                    '%property%' => "<strong>$label</strong>",
                    '%old%' => "<strong>$old</strong>",
                    '%new%' => "<strong>$new</strong>",
                ]);
        }
    }
}
