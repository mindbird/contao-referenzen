<?php

namespace Mindbird\Contao\Reference\Tables;

use Contao\Backend;
use Contao\DataContainer;
use Mindbird\Contao\Reference\Models\ReferenceArchive;
use Mindbird\Contao\Reference\Models\ReferenceCategory;

class Reference extends Backend
{
    public function getReferenceTemplates()
    {
        return static::getTemplateGroup('reference_');
    }

    public function listReference($row)
    {
        return '<div>' . $row['title'] . '</div>';
    }

    public function onloadCallback(DataContainer $dc)
    {
        $objreferenceArchive = ReferenceArchive::findByPk($dc->id);

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

    public function optionsCallbackCategory($dc): array
    {
        $categories = ReferenceCategory::findBy ( 'pid', $dc->activeRecord->pid, array (
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

    public function optionsCallbackReferenceCategory($dc): array
    {
        $categories = ReferenceCategory::findBy ( 'pid', $dc->activeRecord->reference_archiv, array (
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
