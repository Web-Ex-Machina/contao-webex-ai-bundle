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
use Contao\CoreBundle\Exception\NotFoundException;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('%contao.backend.route_prefix%/webex-ai/{id}/pages', name: 'wem-ai-tools-pages', defaults: ['_scope' => 'backend'])]
#[IsGranted('ROLE_ADMIN', message: 'Access restricted to administrators.')]
class BackendWebExAIToolsPagesController extends AbstractBackendController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }
    public function __invoke(Request $request, int $id): Response
    {

        $rootPage = PageModel::findBy(
            ['type = ?','id = ?'],
            ["root", $id],
            ['table' => 'tl_page']
        );

        if (!$rootPage){
            throw new NotFoundException(sprintf('Not a valide root page ', $request->get('root_page_id')));
        }
        $GLOBALS['TL_JAVASCRIPT'][] = '/bundles/webexai/js/jquery-3.7.1.min.js';
        $GLOBALS['TL_JAVASCRIPT'][] = '/bundles/webexai/js/ajax-ai.js';
        $GLOBALS['TL_CSS'][] = '/bundles/webexai/css/style.css';

        $pages = PageModel::findByPid($rootPage->id);

        return $this->render('@Contao/webex_ai_bundle/seo_tools_pages.html.twig', [
            'version' => 'WebEx AI Tools 0.0.1',
            'pages' => $pages,
            'title' => $this->translator->trans('tools_roots_title', [], 'WebExAiBundle'),
            'headline' => $this->translator->trans('tools_roots_headline', [], 'WebExAiBundle') ,
        ]);
    }
}
