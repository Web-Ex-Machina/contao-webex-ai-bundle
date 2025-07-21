<?php

declare(strict_types=1);

/*
 * WebEx AI Bundle for Contao Open Source CMS
 * @author     Web Ex Machina
 *
 * @see        https://github.com/Web-Ex-Machina/contao-webex-ai-bundle/
 * @license    https://www.apache.org/licenses/LICENSE-2.0 Apache 2.0
 */

namespace  WebExMachina\WebExAIBundle\EventListener;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\MenuEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use WebExMachina\WebExAIBundle\Controller\BackendWebExAIController;

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
            ->createItem('my-webex-ai', ['route' => BackendWebExAIController::class])
            ->setLabel('WebEx AI Menu')
            ->setLinkAttribute('title', 'Title')
            ->setLinkAttribute('class', 'my-webex-ai')
            ->setCurrent(
                $this->requestStack->getCurrentRequest()->get('_controller') === BackendWebExAIController::class
            )
        ;

        $contentNode->addChild($node);
    }
}
