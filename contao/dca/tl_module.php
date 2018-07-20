<?php
$GLOBALS ['TL_DCA'] ['tl_module'] ['palettes'] ['reference_list'] = '{title_legend},name,headline,type;{redirect_legend},jumpTo;{archiv_legend},reference_archiv;{filter_legend},reference_category,check_page_referenz,reference_filter_disabled,only_featured;{list_legend},numberOfItems,perPage,imgSize,reference_random;{template_legend},referenceTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS ['TL_DCA'] ['tl_module'] ['palettes'] ['reference_detail'] = '{title_legend},name,headline,type,referenceTpl;{image_legend},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['numberOfItems'] ['eval'] ['mandatory'] = false;
$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['reference_archiv'] = array(
    'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['reference_archiv'],
    'default' => 0,
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
    'inputType' => 'select',
    'options_callback' => array(
        'Reference\Tables\ReferenceTables',
        'optionsCallbackReferenceCategory'
    ),
    'eval' => array(
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ),
    'sql' => "varchar(10) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['reference_filter_disabled'] = array(
    'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['reference_filter_disabled'],
    'inputType' => 'checkbox',
    'eval' => array(
        'tl_class' => 'w50 m12'
    ),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS ['TL_DCA']['tl_module']['fields']['reference_random'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['reference_random'],
    'inputType' => 'checkbox',
    'eval' => array(
        'tl_class' => 'w50 m12'
    ),
    'sql' => "char(1) NOT NULL default ''"
);



$GLOBALS ['TL_DCA'] ['tl_module'] ['fields']['referenceTpl'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['referenceTpl'],
    'inputType' => 'select',
    'options_callback' => array('Reference\Tables\ReferenceTables', 'getReferenceTemplates'),
    'eval' => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
    'sql' => "varchar(64) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields']['only_featured'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['only_featured'],
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields']['check_page_referenz'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['check_page_referenz'],
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);