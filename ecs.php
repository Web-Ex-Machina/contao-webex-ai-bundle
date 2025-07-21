<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;

use Contao\EasyCodingStandard\Set\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()->withSets([SetList::CONTAO])
    ->withConfiguredRule(HeaderCommentFixer::class, [
        'header' =>
"WebEx AI Bundle for Contao Open Source CMS
@author     Web Ex Machina

@see        https://github.com/Web-Ex-Machina/contao-webex-ai-bundle/
@license    https://www.apache.org/licenses/LICENSE-2.0 Apache 2.0"])

    ->withPaths([
    __DIR__ . '/src',
])
->withSkip([
    ArrayOpenerAndCloserNewlineFixer::class,
    __DIR__ . '/migrations',
    __DIR__ . '/vendor',
    __DIR__ . '/var',
    __DIR__ . '/config/jwt',
    __DIR__ . '/config/secrets',
    __DIR__ . '/config/bundles.php',
])

    // NOTE : common intègre les règles : arrays, spaces, namespaces, docblocks, controlStructures, phpunit, comments
->withPreparedSets(
    psr12: true,
    common: true,
    strict: true,
);