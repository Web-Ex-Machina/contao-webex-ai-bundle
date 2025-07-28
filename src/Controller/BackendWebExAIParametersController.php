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
use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\CoreBundle\Exception\NotFoundException;
use Contao\Input;
use Contao\PageModel;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('%contao.backend.route_prefix%/webex-ai/parameters', name: 'wem-ai-tools-parameters', defaults: ['_scope' => 'backend'])]
#[IsGranted('ROLE_ADMIN', message: 'Access restricted to administrators.')]
class BackendWebExAIParametersController extends AbstractBackendController
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }
    public function __invoke(Request $request): Response
    {
        $contaoCsrfTokenName = $this->getCsrfFormOptions()['csrf_field_name'];

        /* @var $contaoCsrfTokenManager ContaoCsrfTokenManager */
        $contaoCsrfTokenManager = $this->getCsrfFormOptions()['csrf_token_manager'];

        $contaoCsrfTokenValue = $contaoCsrfTokenManager->getDefaultTokenValue();

        // TODO : faire fonctionner ce putain de token de ses mort, il me retroune toujours false avec
        // dd( $this->isCsrfTokenValid($contaoCsrfTokenName, $request->get('REQUEST_TOKEN')));

          if ($request->getMethod() === 'POST' ) {
              $objPage = PageModel::findById($request->get('root_page_id'));
              if(!$objPage) {
                  throw new NotFoundException(sprintf('PaGe %s Do NoT eXiStS !', $request->get('root_page_id')));
              }
              $objPage->ia_api_user = ($request->get('api_user')) ? $request->get('api_user') : null;
              $objPage->ia_api_pwd = ($request->get('api_pwd')) ? $request->get('api_pwd') : null;;
              $objPage->tstamp = time();
              $objPage->save();

          }
        $rootPages = PageModel::findPublishedRootPages();



        $GLOBALS['TL_CSS'][] = '/bundles/webexai/css/style.css';

        return $this->render('@Contao/webex_ai_bundle/parameters.html.twig', [
            'version' => 'WebEx AI Tools 0.0.2',
            'rootPages' => $rootPages,
            'tokenName' => $contaoCsrfTokenName,
            'tokenValue' => $contaoCsrfTokenValue,
            'title' => $this->translator->trans('parameters_title', [], 'WebExAiBundle'),
            'headline' => $this->translator->trans('parameters_headline', [], 'WebExAiBundle') ,
        ]);
    }
}
