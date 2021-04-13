<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Inventory;
use App\Form\Type\Entity\InventoryType;
use App\Service\InventoryHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryController extends AbstractController
{
    #[Route(
        path: ['en' => '/inventories/add', 'fr' => '/inventaires/ajouter'],
        name: 'app_inventory_add', methods: ['GET', 'POST']
    )]
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $inventory = new Inventory();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(InventoryType::class, $inventory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($inventory);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.inventory_added', ['%inventory%' => '&nbsp;<strong>'.$inventory->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_inventory_show', ['id' => $inventory->getId()]);
        }

        return $this->render('App/Inventory/add.html.twig', [
            'form' => $form->createView(),
            'collections' => $this->getDoctrine()->getRepository(Collection::class)->findAll()
        ]);
    }

    #[Route(
        path: ['en' => '/inventories/{id}/delete', 'fr' => '/inventaires/{id}/supprimer'],
        name: 'app_inventory_delete', requirements: ['id' => '%uuid_regex%'], methods: ['DELETE']
    )]
    public function delete(Request $request, Inventory $inventory, TranslatorInterface $translator) : Response
    {
        $form = $this->createDeleteForm('app_inventory_delete', $inventory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventory);
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.inventory_deleted', ['%inventory%' => '&nbsp;<strong>'.$inventory->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_tools_index');
    }

    #[Route(
        path: ['en' => '/inventories/{id}/check', 'fr' => '/inventaires/{id}/cocher'],
        name: 'app_inventory_check', requirements: ['id' => '%uuid_regex%'], methods: ['POST']
    )]
    public function check(Request $request, Inventory $inventory, InventoryHandler $inventoryHandler) : Response
    {
        $inventoryHandler->setCheckedValue($inventory, $request->request->get('id'), $request->request->get('checked'));
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'htmlForNavPills' => $this->render('App/Inventory/_nav_pills.html.twig', ['inventory' => $inventory])->getContent()
        ]);
    }

    #[Route(
        path: ['en' => '/inventories/{id}', 'fr' => '/inventaires/{id}'],
        name: 'app_inventory_show', methods: ['GET']
    )]
    public function show(Inventory $inventory) : Response
    {
        return $this->render('App/Inventory/show.html.twig', [
            'inventory' => $inventory
        ]);
    }
}
