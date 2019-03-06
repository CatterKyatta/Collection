<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Log;
use App\Entity\Tag;
use App\Form\Type\Entity\TagType;
use App\Service\ContextHandler;
use App\Service\PaginatorFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TagController
 *
 * @package App\Controller
 */
class TagController extends AbstractController
{
    /**
     * @Route("/tags", name="app_tag_index", methods={"GET"})
     * @Route("/user/{username}/tags", name="app_user_tag_index", methods={"GET"})
     * @Route("/preview/tags", name="app_preview_tag_index", methods={"GET"})
     *
     * @param Request $request
     * @param ContextHandler $contextHandler
     * @param PaginatorFactory $paginatorFactory
     * @return Response
     */
    public function index(Request $request, ContextHandler $contextHandler, PaginatorFactory $paginatorFactory) : Response
    {
        $context = $contextHandler->getContext();
        $page = $request->query->get('page', 1);
        $search = $request->query->get('search', null);
        $itemsCount = $this->getDoctrine()->getRepository(Item::class)->count([]);
        $tagsCount = $this->getDoctrine()->getRepository(Tag::class)->countTags($search, $context);

        return $this->render('App/Tag/index.html.twig', [
            'results' => $this->getDoctrine()->getRepository(Tag::class)->countItemsByTag($itemsCount, $page, $search, $context),
            'search' => $search,
            'tagsCount' => $tagsCount,
            'paginator' => $paginatorFactory->generate($tagsCount, 10)
        ]);
    }

    /**
     * @Route("/tags/{id}", name="app_tag_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Route("/user/{username}/tags/{id}", name="app_user_tag_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Route("/preview/tags/{id}", name="app_preview_tag_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Entity("tag", expr="repository.findById(id)")
     *
     * @param Tag $tag
     * @return Response
     */
    public function show(Tag $tag) : Response
    {
        return $this->render('App/Tag/show.html.twig', [
            'tag' => $tag,
            'relatedTags' => $this->getDoctrine()->getRepository(Tag::class)->findRelatedTags($tag)
        ]);
    }

    /**
     * @Route("/tags/{id}/edit", name="app_tag_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Tag $tag
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Tag $tag, TranslatorInterface $translator) : Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_edited', ['%tag%' => '&nbsp;<strong>'.$tag->getLabel().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_tag_show', ['id' => $tag->getId()]);
        }

        return $this->render('App/Tag/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
    }

    /**
     * @Route("/tags/{id}/delete", name="app_tag_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Tag $tag
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Tag $tag, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($tag);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.tag_deleted', ['%tag%' => '&nbsp;<strong>'.$tag->getLabel().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_tag_index');
    }

    /**
     * @Route("/tags/autocomplete/{search}", name="app_tag_autocomplete", methods={"GET"})
     *
     * @param string $search
     * @return JsonResponse
     */
    public function autocomplete(string $search) : JsonResponse
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findLike($search);
        $data = [];
        foreach ($tags as $tag) {
            $data[] = $tag->getLabel();
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/tags/{id}/history", name="app_tag_history", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @param Tag $tag
     * @return Response
     */
    public function history(Tag $tag) : Response
    {
        return $this->render('App/Tag/history.html.twig', [
            'tag' => $tag,
            'logs' => $this->getDoctrine()->getRepository(Log::class)->findBy([
                'objectId' => $tag->getId(),
                'objectClass' => $this->getDoctrine()->getManager()->getClassMetadata(\get_class($tag))->getName(),
            ], [
                'loggedAt' => 'DESC',
                'type' => 'DESC'
            ])
        ]);
    }
}
