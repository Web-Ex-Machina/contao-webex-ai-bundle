<?php

declare(strict_types=1);

/*
 * Matomo Bundle for Contao Open Source CMS
 * @author     Web Ex Machina
 *
 * @see        https://github.com/Web-Ex-Machina/contao-matomo-analytics-bundle
 * @license    https://www.apache.org/licenses/LICENSE-2.0 Apache 2.0
 */

namespace WEM\WebExAIBundle\EventListener\DataContainer;

use Contao\PageModel;
use WEM\UtilsBundle\Classes\Encryption;

readonly class ModuleContainer
{
    public function __construct(
        private Encryption $encryption
    ) {

    }

    public function getPages(): array
    {

        $objContent = PageModel::findById($GLOBALS['_GET']['id']);
        // TODO : Best solution for recup the data ?

        if ($objContent->ia_remote_user !== '' && $objContent->ia_remote_pwd !== ''){
            $list = [];
            $pwd = $this->encryption->decrypt_b64($objContent->ia_remote_pwd);

            try {
                return $list;

            } catch (\Exception $e) {
                return [0 => $e->getMessage()];
            }

        }

        return [];
    }
}
