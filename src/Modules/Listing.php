<?php

namespace Mindbird\Contao\Reference\Modules;

use Contao\BackendTemplate;
use Contao\Environment;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Model;
use Contao\Model\Collection;
use Contao\Module;
use Contao\PageModel;
use Contao\System;
use Mindbird\Contao\Reference\Models\Reference;
use Mindbird\Contao\Reference\Models\ReferenceArchive;
use Mindbird\Contao\Reference\Models\ReferenceCategory;

class Listing extends Module
{

    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_reference_list';

    protected $strTemplateReferenceList = 'reference_list';

    public function generate(): string
    {
        if (TL_MODE === 'BE') {
            $objTemplate = new BackendTemplate ('be_wildcard');

            $objTemplate->wildcard = '### REFERENZEN LISTE ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }
        if ($this->referenceTpl) {
            $this->strTemplateReferenceList = $this->referenceTpl;
        }

        return parent::generate();
    }

    protected function compile(): void
    {
        // Read jump to page details
        $page = PageModel::findByIdOrAlias($this->jumpTo);

        $searchString = '';
        $filterCategory = 0;
        // Check if filter should be displayed
        if (!$this->reference_filter_disabled && !$this->reference_category) {
            $filterCategory = Input::get('filterCategory');
            $this->generateFilter($filterCategory);
        } elseif ($this->reference_category) {
            $searchString = '';
            $filterCategory = $this->reference_category;
        }

        // Get items to calculate total number of items
        $references = Reference::findItems($this->reference_archiv, $searchString, $filterCategory);

        // Pagination
        $limit = 0;
        $offset = 0;
        $total = 0;

        // Set limit to maximum number of items
        if ($this->numberOfItems > 0) {
            $limit = $this->numberOfItems;
            $total = $this->numberOfItems;
        } elseif ($references) {
            $total = $references->count();
        }

        // If per page is set and maximum number of items greater than per page use Pagination
        if ($references && $this->perPage > 0 && ($limit === 0 || $this->numberOfItems > $this->perPage)) {
            $offset = $this->generatePagination($total);
        }

        // Order
        $referenceArchive = ReferenceArchive::findByPk($this->reference_archiv);

        switch ($referenceArchive->sort_order) {
            case 2:
                $strOrder = 'sorting ASC';
                break;
            case 1:
            default:
                $strOrder = $this->reference_random ? 'RAND()' : 'title ASC';
                break;
        }

        $references = Reference::findItems($this->reference_archiv, $searchString, $filterCategory, $offset, $this->perPage,
            $strOrder);

        if ($references) {
            $this->Template->references = $this->getReferences($references, $page);
        } else {
            $this->Template->references = 'Mit den ausgewählten Filterkriterien sind keine Einträge vorhanden.';
        }
    }

    /**
     * @param Collection|Model $references
     * @param PageModel|null $page
     * @return string
     */
    protected function getReferences($references, $page = null): string
    {
        $strHTML = '';
        while ($references->next()) {
            //zugehörige Kategorien holen
            $arrCategorieIds = unserialize($references->category);
            $arrCategorieData = $this->getReferenceCategoriesAsArray($arrCategorieIds);
            $strCategorieClasses = $this->generateCategorieClassString($arrCategorieData);
            $template = new FrontendTemplate ($this->strTemplateReferenceList);

            $image = FilesModel::findByPk($references->image);
            if ($image) {
                \Controller::addImageToTemplate($template, array(
                    'singleSRC' => $image->path,
                    'size' => deserialize($this->imgSize),
                    'alt' => $references->title
                ));
            }

            $template->title = $references->title;
            $template->teaser = $references->teaser;
            $template->description = $references->description;
            $template->categories = $arrCategorieData;
            $template->categorie_classes = $strCategorieClasses;

            if ($page) {
                $urlGenerator = System::getContainer()->get('contao.routing.url_generator');
                $template->link = $urlGenerator->generate(
                    $page->id . '/{referenceId}',
                    [
                        'referenceId' => $references->id,
                        'auto_item' => 'referenceId'
                    ]
                );
            }
            $strHTML .= $template->parse();
        }

        return $strHTML;
    }

    /**
     * @param array $idArray
     */
    protected function getReferenceCategoriesAsArray($idArray = array())
    {

        $returnArr = [];
        $objCategories = ReferenceCategory::findMultipleByIds($idArray);

        if ($objCategories != null) {
            while ($objCategories->next()) {
                $returnArr[$objCategories->id] = $objCategories->title;
            }
        }

        // TODO: hier gehts weiter
        return $returnArr;
    }

    protected function generateCategorieClassString($catArray = array())
    {
        if (!\is_array($catArray) || \count($catArray) < 1) {
            return '';
        }

        $classArray = [];

        foreach ($catArray as $key => $title) {
            $classArray[] = \StringUtil::generateAlias($title);
        }

        return implode(' ', $classArray);
    }

    /**
     * @return mixed
     */
    protected function generateFilter($filterCategory): void
    {
        $templateFilter = new FrontendTemplate('reference_list_filter');

        // Filter category
        $templateFilter->strLink = Environment::get('base') . static::addToUrl('filterCategory=ID', true);

        // Get Categories
        static::loadLanguageFile('tl_reference_category');
        $categories = ReferenceCategory::findBy('pid', $this->reference_archiv, array(
            'order' => 'title ASC'
        ));
        $strOptions = '<option value="0">' . $GLOBALS ['TL_LANG'] ['tl_reference_category'] ['category'] [0] . '</option>';
        $arrOptions = array();

        if ($categories) {
            while ($categories->next()) {
                $strOptions .= '<option value="' . $categories->id . '"' . ($filterCategory != $categories->id ? '' : ' selected') . '>' . $categories->title . '</option>';
                $arrOptions[] = [
                    'title' => $categories->title,
                    'id' => $categories->id,
                    'active' => ($filterCategory != $categories->id) ? false : true
                ];
            }
        }
        $templateFilter->strCategoryOptions = $strOptions;
        $templateFilter->arrCategories = $arrOptions;
        $this->Template->strFilter = $templateFilter->parse();
    }

    /**
     * @param $total
     * @return array
     */
    protected function generatePagination($total): int
    {
        $intPage = Input::get('page') ?: 1;
        $offset = ($intPage - 1) * $this->perPage;

        // Add pagination menu
        $objPagination = new \Pagination ($total, $this->perPage);
        $this->Template->pagination = $objPagination->generate();

        return $offset;
    }
}
