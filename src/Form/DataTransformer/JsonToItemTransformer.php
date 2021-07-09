<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Repository\ItemRepository;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\DataTransformerInterface;

class JsonToItemTransformer implements DataTransformerInterface
{
    private ItemRepository $itemRepository;

    private Packages $assetManager;

    public function __construct(ItemRepository $itemRepository, Packages $assetManager)
    {
        $this->itemRepository = $itemRepository;
        $this->assetManager = $assetManager;
    }

    public function transform($items)
    {
        $array = [];
        foreach ($items as $item) {
            $array[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'thumbnail' => $item->getImageSmallThumbnail() ? $this->assetManager->getUrl($item->getImageSmallThumbnail()) : null,
            ];
        }

        return json_encode($array);
    }

    public function reverseTransform($json)
    {
        $items = [];
        foreach (json_decode($json, true) as $id) {
            $item = $this->itemRepository->find($id);

            if (!\in_array($item, $items, false)) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
