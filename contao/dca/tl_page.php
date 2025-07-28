<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use WEM\UtilsBundle\Classes\Encryption;

$GLOBALS['TL_DCA']['tl_page']['fields']['ia_api_user'] = [
    'inputType' => 'text',
    "required" => false,
    'load_callback' => [[Encryption::class, 'decrypt_b64']],
    'save_callback' => [[Encryption::class, 'encrypt_b64']],
    'eval' => [
        'rgxp' => 'url',
        'tl_class' => 'w50'
    ],
    'sql' => "varchar(255) NULL default ''",
];


$GLOBALS['TL_DCA']['tl_page']['fields']['ia_api_pwd'] = [
    'exclude' => true,
    "required" => false,
    'inputType' => 'text',
    'load_callback' => [[Encryption::class, 'decrypt_b64']],
    'save_callback' => [[Encryption::class, 'encrypt_b64']],
    'eval' => [
        'tl_class' => 'w50'
    ],
    'sql' => "varchar(255) NULL default ''",
];

PaletteManipulator::create()
    ->addLegend('ai_tool_legend')
    ->addField('ia_api_user', 'ai_tool_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('ia_api_pwd', 'ai_tool_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette("root", 'tl_page')
    ->applyToPalette("rootfallback", 'tl_page');
