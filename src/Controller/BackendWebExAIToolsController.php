<?php

declare(strict_types=1);

/*
 * WebEx AI Bundle for Contao Open Source CMS
 * @author     Web Ex Machina
 *
 * @see        https://github.com/Web-Ex-Machina/contao-webex-ai-bundle/
 * @license    https://www.apache.org/licenses/LICENSE-2.0 Apache 2.0
 */

namespace WEM\WebExAIBundle\Controller;

use Contao\CoreBundle\Controller\AbstractBackendController;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('%contao.backend.route_prefix%/webex-ai', name: self::class, defaults: ['_scope' => 'backend'])]
#[IsGranted('ROLE_ADMIN', message: 'Access restricted to administrators.')]
class BackendWebExAIToolsController extends AbstractBackendController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }
    public function __invoke(Request $request): Response
    {
        $objContent = PageModel::findById($request->get('id'));
        $childPages = PageModel::findByPid($objContent->id);

        $GLOBALS['TL_CSS'][] = '/bundles/matomo/css/style.css';

        return $this->render('@Contao/webex_ai_bundle/seo_tools_pages.html.twig', [
            'version' => 'WebEx AI Tools 0.0.1',
            'homePage' => $objContent,
            'pages' => $childPages,
            'data' => '',
            'title' => $this->translator->trans('tools_pages_title', [], 'WebExAiBundle') . ' ' . $objContent->title,
            'headline' => $this->translator->trans('tools_pages_headline', [], 'WebExAiBundle') . ' ' . $objContent->title,
        ]);
    }
}
