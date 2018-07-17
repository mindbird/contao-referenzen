<?php

/** Backend */
$GLOBALS ['BE_MOD'] ['content'] ['reference'] = array(
    'tables' => array(
        'tl_reference_archive',
        'tl_reference',
        'tl_reference_category',
        'tl_content'
    ),
    'icon' => 'bundles/referenzen/img/icon.png'
);

/** Frontend */
array_insert($GLOBALS ['FE_MOD'] ['reference'], 1, array(
    'reference_list' => 'Reference\ReferenceList',
    'reference_detail' => 'Reference\ReferenceDetail'
));

/** Hooks */
$GLOBALS ['TL_HOOKS'] ['getSearchablePages'] [] = array(
    'Reference\ReferenceBackend',
    'getSearchablePages'
);

/** Models */
$GLOBALS['TL_MODELS']['tl_reference'] = 'Mindbird\Contao\Reference\Models\Reference';
$GLOBALS['TL_MODELS']['tl_reference_archive'] = 'Mindbird\Contao\Reference\Models\ReferenceArchive';
$GLOBALS['TL_MODELS']['tl_reference_category'] = 'Mindbird\Contao\Reference\Models\ReferenceCategory';
