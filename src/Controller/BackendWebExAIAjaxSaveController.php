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

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractBackendController;
use Contao\Database;
use Contao\PageModel;
use phpseclib3\Exception\UnsupportedOperationException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WEM\WebExAIBundle\Service\ApiAiWrapper;

#[Route('%contao.backend.route_prefix%/webex-ai/seo/save', name: 'wem-ai-ajax-save', defaults: ['_scope' => 'backend'])]
#[IsGranted('ROLE_ADMIN', message: 'Access restricted to administrators.')]
class BackendWebExAIAjaxSaveController extends AbstractBackendController
{
    public function __construct() {

    }
    public function __invoke(Request $request): JsonResponse
    {

        if ($request->isXmlHttpRequest()) {

            $id = $request->get('post_id');
            $champ = $request->get('champ');
            $value = $request->get('value');

            if (!$page = PageModel::findByid($id)) {
                return $this->json([
                    "success" => false,
                    "data" =>
                        [
                            'message' => 'Not a valid page for id : '. $id,
                            $champ => $request->get('champ')
                        ]
                ]);
            }

            switch ($champ) {
                case 'title':
                    $page->pageTitle = $value;
                    break;
                case 'description':
                    $page->description = $value;
                    break;
                default:
                    throw new UnsupportedOperationException('Not valid');
            }

            $page->tstamp = time();
            $page->save();

            return $this->json([
                "success" => true,
                "data" =>
                    [
                        'message' => $champ . 'optimized successfully',
                        $champ => $request->get('champ')
                    ]
            ]);
        } else {
            throw new UnsupportedOperationException('Not valid');
        }
    }
}
