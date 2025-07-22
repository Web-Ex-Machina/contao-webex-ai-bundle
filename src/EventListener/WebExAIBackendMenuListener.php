<?php

declare(strict_types=1);

/*
 * WebEx AI Bundle for Contao Open Source CMS
 * @author     Web Ex Machina
 *
 * @see        https://github.com/Web-Ex-Machina/contao-webex-ai-bundle/
 * @license    https://www.apache.org/licenses/LICENSE-2.0 Apache 2.0
 */

namespace  WEM\WebExAIBundle\EventListener;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\MenuEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use WEM\WebExAIBundle\Controller\BackendWebExAIToolsController;
use WEM\WebExAIBundle\Controller\BackendWebExAIParametersController;

#[AsEventListener(ContaoCoreEvents::BACKEND_MENU_BUILD, priority: -255)]
final readonly class WebExAIBackendMenuListener
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function __invoke(MenuEvent $event): void
    {
        $factory = $event->getFactory();
        $tree = $event->getTree();

        if ($tree->getName() !== 'mainMenu') {
            return;
        }

        $GLOBALS['TL_CSS'][] = '/bundles/webexai/css/menu-style.css';

        $name = "webex-ai";

        $categoryNode = $factory
            ->createItem($name)
            ->setLabel('WebEx AI')
            ->setUri('/contao?mtg='.$name)
            ->setLinkAttribute('class', 'group-'.$name)
            ->setLinkAttribute('title', 'Collapse node')
            ->setLinkAttribute('data-action', 'contao--toggle-navigation#toggle:prevent')
            ->setLinkAttribute('data-contao--toggle-navigation-category-param', $name)
            ->setLinkAttribute('aria-controls', $name)
            ->setLinkAttribute('data-turbo-prefetch', 'false')
            ->setChildrenAttribute('id', $name)
        ;
        $tree->addChild($categoryNode);

        $node = $factory
            ->createItem($name . 'parameters', ['route' => BackendWebExAIParametersController::class])
            ->setLabel('Paramètres')
            ->setLinkAttribute('title', 'Paramètres')
            ->setLinkAttribute('class', $name . 'parameters')
            ->setCurrent(
                $this->requestStack->getCurrentRequest()->get('_controller') === BackendWebExAIParametersController::class
            )
        ;

        $categoryNode->addChild($node);

        $node = $factory
            ->createItem($name . 'tools', ['route' => BackendWebExAIToolsController::class])
            ->setLabel('Outils')
            ->setLinkAttribute('title', 'Outils')
            ->setLinkAttribute('class', $name . 'tools')
            ->setCurrent(
                $this->requestStack->getCurrentRequest()->get('_controller') === BackendWebExAIToolsController::class
            )
        ;

        $categoryNode->addChild($node);
    }
}
