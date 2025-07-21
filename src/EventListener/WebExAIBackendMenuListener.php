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

        $contentNode = $tree->getChild('content');

        $node = $factory
            ->createItem('my-webex-ai-parameters', ['route' => BackendWebExAIParametersController::class])
            ->setLabel('WebEx AI Tools')
            ->setLinkAttribute('title', 'Paramètres')
            ->setLinkAttribute('class', 'my-webex-ai')
            ->setCurrent(
                $this->requestStack->getCurrentRequest()->get('_controller') === BackendWebExAIParametersController::class
            )
        ;

        $contentNode->addChild($node);

        $node = $factory
            ->createItem('my-webex-ai-tools', ['route' => BackendWebExAIToolsController::class])
            ->setLabel('WebEx AI Tools')
            ->setLinkAttribute('title', 'Outils')
            ->setLinkAttribute('class', 'my-webex-ai')
            ->setCurrent(
                $this->requestStack->getCurrentRequest()->get('_controller') === BackendWebExAIToolsController::class
            )
        ;

        $contentNode->addChild($node);
    }
}
