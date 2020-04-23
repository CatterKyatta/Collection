<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Tag;
use App\Model\BreadcrumbElement;
use App\Service\ContextHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var ContextHandler
     */
    private ContextHandler $contextHandler;

    /**
     * AppExtension constructor.
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param EntityManagerInterface $em
     * @param ContextHandler $contextHandler
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, EntityManagerInterface $em, ContextHandler $contextHandler)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->em = $em;
        $this->contextHandler = $contextHandler;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function safeContent(string $string) : string
    {
        return $string;
    }

    /**
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public function bytes(int $bytes, int $precision = 2) : string
    {
        $base = $bytes > 0 ? log($bytes, 1024) : $bytes;

        $suffixes = ['', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi'];

        return round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)].$this->translator->trans('global.byte_abbreviation');
    }

    /**
     * @param array $breadcrumb
     * @return string
     * @throws \ReflectionException
     */
    public function renderTitle(array $breadcrumb)
    {
        $element = \array_shift($breadcrumb);

        if ($element instanceof BreadcrumbElement && isset($element->getParams()['username'])) {
            return $this->translator->trans($element->getLabel(), ['%username%' => $element->getParams()['username']]);
        }

        $element = \count($breadcrumb) === 0 ? $element : \array_pop($breadcrumb);

        if ($element instanceof BreadcrumbElement) {
            if ($element->getType() === 'action') {
                $entityElement = \array_pop($breadcrumb);

                if ($entityElement instanceof BreadcrumbElement && $entityElement->getEntity() !== null) {
                    $class = (new \ReflectionClass($entityElement->getEntity()))->getShortName();
                    return $this->translator->trans('global.entities.'.strtolower($class)).' · '.$entityElement->getLabel().' · '.$this->translator->trans($element->getLabel());
                } elseif (strpos($element->getLabel(), 'breadcrumb.') !== false) {
                    return $this->translator->trans($element->getLabel());
                }

                return $this->translator->trans('label.search').' · '.$element->getLabel();
            }

            if ($element->getType() === 'entity') {
                $class = (new \ReflectionClass($element->getEntity()))->getShortName();
                $pieces = preg_split('/(?=[A-Z])/', lcfirst($class));
                $class = implode('_', $pieces);
                $class = strtolower($class);

                return $this->translator->trans('global.entities.'.strtolower($class)) .' · '. $element->getLabel();
            }

            if ($element->getType() === 'root') {
                if ($this->contextHandler->getContext() === 'user') {
                    return $this->translator->trans($element->getLabel().'_user', ['%username%' => $this->contextHandler->getUsername()]);
                }
                return $this->translator->trans($element->getLabel());
            }
        }

        return $this->translator->trans('global.koillection');
    }

    /**
     * @param null|string $text
     * @return null|string|string[]
     */
    public function highlightTags(?string $text)
    {
        if (null === $text) {
            return $text;
        }

        $tags = $this->em->getRepository(Tag::class)->findAllForHighlight();

        $words = [];
        foreach ($tags as $tag) {
            $id = \is_string($tag['id']) ? $tag['id'] : $tag['id']->toString();
            $words[$id] = preg_quote($tag['label'], '/');
        }

        return preg_replace_callback(
            "/\b(".implode('|', $words).")\b/ui",
            function ($matches) use ($words) {
                $id = \array_search(preg_quote(strtolower($matches[1]), '/'), array_map('strtolower', $words));

                $route = $this->contextHandler->getRouteContext('app_tag_show');
                $route = $this->router->generate($route, ['id' => $id]);

                return "<a href='$route'>$matches[1]</a>";
            },
            $text
        );
    }
}