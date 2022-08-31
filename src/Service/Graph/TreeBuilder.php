<?php

declare(strict_types=1);

namespace App\Service\Graph;

use App\Entity\Collection;
use App\Repository\CollectionRepository;

class TreeBuilder
{
    public function __construct(
        private readonly CollectionRepository $collectionRepository
    ) {
    }

    public function buildCollectionTree(): array
    {
        $collections = $this->collectionRepository->findAllWithChildren();
        $tree = $this->createLeaf();

        $children = array_filter($collections, function (Collection $element) {
            return null === $element->getParent();
        });

        foreach ($children as $child) {
            $tree['children'][] = $this->buildRecursively($collections, $child);
        }

        return $tree;
    }

    private function buildRecursively(array $collections, Collection $collection): array
    {
        $leaf = $this->createLeaf($collection);

        foreach ($collection->getChildren() as $child) {
            $leaf['children'][] = $this->buildRecursively($collections, $child);
        }

        return $leaf;
    }

    private function createLeaf(?Collection $collection = null): array
    {
        $name = '';
        if ($collection instanceof Collection) {
            $title = $collection->getTitle();
            $name = \strlen($title) > 21 ? substr($title, 0, 18).'...' : $title;
        }

        return [
            'id' => $collection !== null ? $collection->getId() : '',
            'name' => $name,
            'children' => [], ];
    }
}
