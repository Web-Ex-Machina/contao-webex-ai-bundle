<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use WEM\UtilsBundle\Classes\Encryption;

$GLOBALS['TL_DCA']['tl_settings']['fields']['ia_api_global_user'] = [
    'inputType' => 'text',
    "required" => false,
    'load_callback' => [[Encryption::class, 'decrypt_b64']],
    'save_callback' => [[Encryption::class, 'encrypt_b64']],
    'eval' => [
        'rgxp' => 'url',
        'tl_class' => 'w50'
    ],
];


$GLOBALS['TL_DCA']['tl_settings']['fields']['ia_api_global_pwd'] = [
    'exclude' => true,
    "required" => false,
    'inputType' => 'text',
    'load_callback' => [[Encryption::class, 'decrypt_b64']],
    'save_callback' => [[Encryption::class, 'encrypt_b64']],
    'eval' => [
        'tl_class' => 'w50'
    ],
];

PaletteManipulator::create()
    ->addLegend('ai_tool_legend')
    ->addField('ia_api_global_user', 'ai_tool_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('ia_api_global_pwd', 'ai_tool_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_settings');
