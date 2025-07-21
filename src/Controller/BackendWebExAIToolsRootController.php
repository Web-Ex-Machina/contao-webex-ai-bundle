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
class BackendWebExAIToolsRootController extends AbstractBackendController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }
    public function __invoke(Request $request): Response
    {
        $rootPagesAll = PageModel::findPublishedRootPages();

        $GLOBALS['TL_CSS'][] = '/bundles/matomo/css/style.css';
        $rootPages = [];
        foreach ($rootPagesAll as $rootPage) {
            $is_configured = ($rootPage['ia_api_user'] !== null && $rootPage['ia_api_pwd'] !== '');
            if ($is_configured) {
                $rootPages[] = $rootPage;
            }
        }

        return $this->render('@Contao/webex_ai_bundle/seo_tools_roots.html.twig', [
            'version' => 'WebEx AI Tools 0.0.1',
            'rootPages' => $rootPages,
            'title' => $this->translator->trans('tools_roots_title', [], 'WebExAiBundle'),
            'headline' => $this->translator->trans('tools_roots_headline', [], 'WebExAiBundle') ,
        ]);
    }
}
