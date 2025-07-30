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

#[Route('%contao.backend.route_prefix%/webex-ai/seo/query', name: 'wem-ai-ajax-SEO', defaults: ['_scope' => 'backend'])]
#[IsGranted('ROLE_ADMIN', message: 'Access restricted to administrators.')]
class BackendWebExAIAjaxSEOController extends AbstractBackendController
{
    public function __construct(private readonly ApiAiWrapper $apiAiWrapper,private readonly HttpClientInterface $client) {

    }
    public function __invoke(Request $request): JsonResponse
    {

        if ($request->isXmlHttpRequest()) {
            $cache = new FilesystemAdapter();

            $id = $request->get('post_id');
            $champ = $request->get('champ');

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

            $page->loadDetails();

            $objArticle = ArticleModel::findPublishedByPidAndColumn($id, 'main')->first();
            $objContent = ContentModel::findPublishedByPidAndTable($objArticle->id, ArticleModel::getTable())->first();

            $excerpt = "";
            foreach ($objContent->getModels() as $content){
                if ($content->type == 'text') {
                    $excerpt = strip_tags($content->text);
                    break;
                }
            }

            $user = ($page->ia_api_user) ? $page->ia_api_user : ( $GLOBALS['TL_CONFIG']['ia_api_global_user'] ?? false) ;
            $password = ($page->ia_api_pwd) ? $page->ia_api_pwd : ( $GLOBALS['TL_CONFIG']['ia_api_global_pwd'] ?? false) ;

            if (!$user or !$password) {
                return $this->json([
                    "success" => false,
                    "data" =>
                        [
                            'message' => 'No API credentials found. Please check your configuration. ',
                            $champ => $request->get('champ')
                        ]
                ]);
            }

            $tokenCache = $cache->getItem('latest_token');

            if (!$tokenCache->isHit()) {

                $tokenCache->expiresAfter(60 * 5);
                $token = $this->getApiKey($user, $password);

                if (!isset($token['token'])) {
                    return $this->json([
                        "success" => false,
                        "data" =>
                            [
                                'message' => $token['message'],
                                $champ => $request->get('champ')
                            ]
                    ]);
                }

                $cache->save($tokenCache->set($token));
            } else {
                $token = $tokenCache->get();
            }

            $title = ($page->pageTitle) ? $page->pageTitle : $page->title;
            $language = ($page->language) ? $page->language : $page->rootLanguage;
            $value = match ($champ) {
                'title' => $this->apiAiWrapper->generateSeoTitle(
                    keywords: '',
                    theme: $title,
                    language: $language,
                    text: $excerpt,
                    token: $token
                ),
                'description' => $this->apiAiWrapper->generateSeoDescription(
                    keywords: '',
                    theme: $title,
                    language: $language,
                    text: $excerpt,
                    token: $token
                ),
                default => throw new UnsupportedOperationException('Not valid'),
            };

            return $this->json([
                "success" => true,
                "data" =>
                    [
                        'message' => $champ . 'optimized successfully',
                        $champ => $value
                    ]
            ]);
        } else {
            throw new UnsupportedOperationException('Not valid');
        }
    }

    public function getApiKey($username, $password): array
    {
        $body = json_encode([
            'username' => $username,
            'password' => $password,
        ], JSON_THROW_ON_ERROR);

        $reponse =  $this->client->request(
            'POST',
            'https://ai.webexmachina.fr/api/login',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $body,
            ]
        );

        return $reponse->toArray();

    }
}
