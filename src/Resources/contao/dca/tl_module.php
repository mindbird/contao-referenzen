<?php

$GLOBALS ['TL_DCA'] ['tl_module'] ['palettes'] ['reference_list'] = '{title_legend},name,headline,type;{archiv_legend},reference_archiv,reference_category,reference_random,reference_filter_disabled,imgSize,jumpTo;{template_legend},referenceTpl,referenceFilterTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

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
        'Mindbird\Contao\Reference\Tables\Reference',
        'optionsCallbackReferenceCategory'
    ),
    'eval' => array(
        'tl_class' => 'w50',
        'includeBlankOption' => true
    ),
    'sql' => "varchar(10) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields']['jumpTo']['eval']['tl_class'] = 'w50 m12';

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['reference_random'] = array(
    'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['reference_random'],
    'inputType' => 'checkbox',
    'eval' => array(
        'tl_class' => 'w50 m12'
    ),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields'] ['reference_filter_disabled'] = array(
    'label' => &$GLOBALS ['TL_LANG'] ['tl_module'] ['reference_filter_disabled'],
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
    'inputType' => 'select',
    'options_callback' => array('Mindbird\Contao\Reference\Tables\Reference', 'getReferenceTemplates'),
    'eval' => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
    'sql' => "varchar(64) NOT NULL default ''"
);

$GLOBALS ['TL_DCA'] ['tl_module'] ['fields']['referenceFilterTpl'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['referenceFilterTpl'],
    'inputType' => 'select',
    'options_callback' => array('Mindbird\Contao\Reference\Tables\Reference', 'getReferenceFilterTemplates'),
    'eval' => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
    'sql' => "varchar(64) NOT NULL default ''"
);
