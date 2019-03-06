<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class SeenListener
 *
 * @package App\EventListener
 */
class SeenListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * SeenListener constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $route = $event->getRequest()->get('_route');

        $type = null;
        if (\in_array($route, ['app_item_show', 'app_user_item_show, app_preview_item_show'])) {
            $type = 'item';
        } elseif (\in_array($route, ['app_collection_show', 'app_user_collection_show, app_preview_collection_show'])) {
            $type = 'collection';
        } elseif (\in_array($route, ['app_tag_show', 'app_user_tag_show, app_preview_tag_show'])) {
            $type = 'tag';
        } elseif (\in_array($route, ['app_album_show', 'app_user_album_show, app_preview_album_show'])) {
            $type = 'album';
        } elseif (\in_array($route, ['app_wishlist_show', 'app_user_wishlist_show, app_preview_wishlist_show'])) {
            $type = 'wishlist';
        }

        if ($type) {
            $id = $event->getRequest()->get('id');
            $sql = "UPDATE koi_$type SET seen_counter = seen_counter + 1 WHERE id = ?";
            $stmt = $this->em->getConnection()->prepare($sql);
            $stmt->bindParam(1, $id);
            $stmt->execute();
        }
    }
}
