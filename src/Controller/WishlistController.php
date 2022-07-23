<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Wishlist;
use App\Form\Type\Entity\WishlistType;
use App\Repository\WishlistRepository;
use App\Repository\WishRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WishlistController extends AbstractController
{
    #[Route(
        path: ['en' => '/wishlists', 'fr' => '/listes-de-souhaits'],
        name: 'app_wishlist_index',
        methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/wishlists', 'fr' => '/utilisateur/{username}/listes-de-souhaits'],
        name: 'app_shared_wishlist_index',
        methods: ['GET']
    )]
    public function index(WishlistRepository $wishlistRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $wishlists = $wishlistRepository->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('App/Wishlist/index.html.twig', [
            'wishlists' => $wishlists,
        ]);
    }

    #[Route(
        path: ['en' => '/wishlists/add', 'fr' => '/listes-de-souhaits/ajouter'],
        name: 'app_wishlist_add',
        methods: ['GET', 'POST']
    )]
    public function add(Request $request, WishlistRepository $wishlistRepository, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $wishlist = new Wishlist();
        if ($request->query->has('parent')) {
            $parent = $wishlistRepository->findOneBy([
                'id' => $request->query->get('parent'),
                'owner' => $this->getUser(),
            ]);
            $wishlist
                ->setParent($parent)
                ->setVisibility($parent->getVisibility())
                ->setParentVisibility($parent->getVisibility())
                ->setFinalVisibility($parent->getFinalVisibility())
            ;
        }

        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($wishlist);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.wishlist_added', ['%wishlist%' => '&nbsp;<strong>'.$wishlist->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        return $this->render('App/Wishlist/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        path: ['en' => '/wishlists/{id}', 'fr' => '/listes-de-souhaits/{id}'],
        name: 'app_wishlist_show',
        requirements: ['id' => '%uuid_regex%'],
        methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/wishlists/{id}', 'fr' => '/utilisateur/{username}/listes-de-souhaits/{id}'],
        name: 'app_shared_wishlist_show',
        requirements: ['id' => '%uuid_regex%'],
        methods: ['GET']
    )]
    public function show(Wishlist $wishlist, WishlistRepository $wishlistRepository, WishRepository $wishRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        return $this->render('App/Wishlist/show.html.twig', [
            'wishlist' => $wishlist,
            'children' => $wishlistRepository->findBy(['parent' => $wishlist]),
            'wishes' => $wishRepository->findBy(['wishlist' => $wishlist]),
        ]);
    }

    #[Route(
        path: ['en' => '/wishlists/{id}/edit', 'fr' => '/listes-de-souhaits/{id}/editer'],
        name: 'app_wishlist_edit',
        requirements: ['id' => '%uuid_regex%'],
        methods: ['GET', 'POST']
    )]
    public function edit(Request $request, Wishlist $wishlist, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wishlist_edited', ['%wishlist%' => '&nbsp;<strong>'.$wishlist->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        return $this->render('App/Wishlist/edit.html.twig', [
            'form' => $form->createView(),
            'wishlist' => $wishlist,
        ]);
    }

    #[Route(
        path: ['en' => '/wishlists/{id}/delete', 'fr' => '/listes-de-souhaits/{id}/supprimer'],
        name: 'app_wishlist_delete',
        requirements: ['id' => '%uuid_regex%'],
        methods: ['POST']
    )]
    public function delete(Request $request, Wishlist $wishlist, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createDeleteForm('app_wish_delete', $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($wishlist);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wishlist_deleted', ['%wishlist%' => '&nbsp;<strong>'.$wishlist->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_wishlist_index');
    }
}
