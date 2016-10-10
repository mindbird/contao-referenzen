<?php
$GLOBALS ['TL_DCA'] ['tl_module'] ['palettes'] ['reference_list'] = '{title_legend},name,headline,type;{archiv_legend},reference_archiv,reference_category,jumpTo,reference_random,reference_filter_disabled,numberOfItems,perPage,imgSize,referenceTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['numberOfItems'] ['eval'] ['mandatory'] = false;
$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['reference_archiv'] = array(
    'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['reference_archiv'],
    'default' => '',
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_reference_archive.title',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'w50',
        'submitOnChange' => true
    ),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['reference_category'] = array(
    'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['reference_category'],
    'default' => '',
    'exclude' => true,
    'inputType' => 'select',
    'eval' => array(
        'mandatory' => true,
        'tl_class' => 'w50',
        'options_callback' => array('Reference\Tables\ReferenceTables', 'optionsCallbackCategory'),
        'includeBlankOption' => true
    ),
    'sql' => "varchar(10) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['reference_random'] = array(
    'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['reference_random'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array(
        'tl_class' => 'w50 m12'
    ),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['reference_filter_disabled'] = array(
    'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['reference_filter_disabled'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array(
        'tl_class' => 'w50 m12'
    ),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['palettes'] ['reference_detail'] = '{title_legend},name,headline,type;{image_legend},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields']['referenceTpl'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['referenceTpl'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => array('Reference\Tables\ReferenceTables', 'getReferenceTemplates'),
    'eval' => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
    'sql' => "varchar(64) NOT NULL default ''"
);
