<?php

namespace Reference;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Image;
use Reference\Models\ReferenceArchiveModel;
use Reference\Models\ReferenceCategoryModel;

class ReferenceTables extends Backend
{
    public function getReferenceTemplates()
    {
        return $this->getTemplateGroup('reference_');
    }

    public function listreference($row)
    {
        return '<div>' . $row['reference'] . '</div>';
    }

    public function onloadCallback(DataContainer $dc)
    {
        $objreferenceArchive = ReferenceArchiveModel::findByPk($dc->id);

        switch ($objreferenceArchive->sort_order) {
            case 2:
                $GLOBALS['TL_DCA']['tl_reference']['list']['sorting']['mode'] = 4;
                $GLOBALS['TL_DCA']['tl_reference']['list']['sorting']['fields'] = array(
                    'sorting'
                );
                $GLOBALS['TL_DCA']['tl_reference']['list']['sorting']['headerFields'] = array(
                    'title'
                );
                break;
            case 1:
            default:

                // Nothing to do
                break;
        }
    }

    public function optionsCallbackCategory($dc)
    {
        $categories = ReferenceCategoryModel::findBy ( 'pid', $dc->activeRecord->pid, array (
            'order' => 'title ASC'
        ) );
        $category = array();
        if ($categories) {
            while ($categories->next()) {
                $category[$categories->id] = $categories->title;
            }
        }

        return $category;
    }
}