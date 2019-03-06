<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Type\Entity\Admin\UserType;
use App\Service\DiskUsageCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 *
 * @package App\Controller
 *
 * @Route("/admin/users")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="app_admin_user_index", methods={"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Admin/User/index.html.twig', [
            'users' => $em->getRepository(User::class)->findBy([], ['lastDateOfActivity' => 'DESC'])
        ]);
    }

    /**
     * @Route("/add", name="app_admin_user_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.user_added', ['%user%' => '&nbsp;<strong>'.$user->getUsername().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_admin_user_index', ['id' => $user->getId()]);
        }

        return $this->render('App/Admin/User/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admin_user_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param User $user
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, User $user, TranslatorInterface $translator) : Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.user_edited', ['%user%' => '&nbsp;<strong>'.$user->getUsername().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_admin_user_index', ['id' => $user->getId()]);
        }

        return $this->render('App/Admin/User/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/recompute-disk-usage", name="app_admin_user_recompute_disk_usage", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @param User $user
     * @param DiskUsageCalculator $diskUsageCalculator
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function recomputeDiskUsage(User $user, DiskUsageCalculator $diskUsageCalculator, TranslatorInterface $translator) : Response
    {
        $user->setDiskSpaceUsed($diskUsageCalculator->getSpaceUsedByUser($user));
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('notice', $translator->trans('message.done'));

        return $this->redirectToRoute('app_admin_user_index');
    }

    /**
     * @Route("/{id}/delete", name="app_admin_user_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "DELETE"})
     *
     * @param User $user
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Request $request, User $user, TranslatorInterface $translator) : Response
    {
        if ($user->isAdmin()) {
            return $this->render('App/Admin/User/delete.html.twig', [
                'user' => $user,
                'error' =>  $translator->trans('error.cannot_delete_admin_user')
            ]);
        }

        $form = $this->createFormBuilder()
            ->setMethod('DELETE')
            ->add('confirm', CheckboxType::class, ['required' => true, 'mapped' => false, 'data' => false])
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.user_deleted', ['%user%' => '&nbsp;<strong>'.$user->getUsername().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_admin_user_index');
        }

        return $this->render('App/Admin/User/delete.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
