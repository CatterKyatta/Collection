<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Form\Type\Model\ProfileType;
use App\Form\Type\Security\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/profile",
     *     "fr": "/profil"
     * }, name="app_profile_index", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function index(Request $request, TranslatorInterface $translator) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        $formPassword = $this->createForm(PasswordType::class, $user);
        $formPassword->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.profile_updated'));

            return $this->redirectToRoute('app_profile_index');
        }

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.password_updated'));

            return $this->redirectToRoute('app_profile_index');
        }

        return $this->render('App/Profile/index.html.twig', [
            'lastCollectionsAdded' => $em->getRepository(Collection::class)->findBy(['owner' => $this->getUser()], ['createdAt' => 'DESC'], 5),
            'lastItemsAdded' => $em->getRepository(Item::class)->findBy(['owner' => $this->getUser()], ['createdAt' => 'DESC'], 5),
            'form' => $form->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }
}
