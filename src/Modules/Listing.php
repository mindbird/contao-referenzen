<?php

namespace Mindbird\Contao\Reference\Modules;

use Contao\BackendTemplate;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Model;
use Contao\Model\Collection;
use Contao\Module;
use Contao\PageModel;
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

    protected $strTemplateFilter = 'reference_filter_button';

    private $categories;

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

        $GLOBALS['TL_BODY'][] = '<script src="bundles/reference/js/script.min.js"></script>';

        $categories = ReferenceCategory::findBy('pid', $this->reference_archiv);
        $this->categories = [];
        if ($categories !== null) {
            while ($categories->next()) {
                $this->categories[$categories->id] = $categories->title;
            }
        }

        return parent::generate();
    }

    protected function compile(): void
    {
        // Read jump to page details
        $page = PageModel::findByIdOrAlias($this->jumpTo);

        $filterCategory = 0;
        // Check if filter should be displayed
        if (!$this->reference_filter_disabled && !$this->reference_category) {
            $filterCategory = Input::get('filterCategory');
        } elseif ($this->reference_category) {
            $filterCategory = $this->reference_category;
        } else {
            $this->Template->filter = $this->generateFilter();
        }
        $this->Template->filter = $this->generateFilter();

        $strOrder = $this->reference_random ? 'RAND()' : 'title ASC';
        /** @var ReferenceArchive $referenceArchive */
        $referenceArchive = ReferenceArchive::findByPk($this->reference_archiv);
        if ($referenceArchive->sort_order === "2") {
                $strOrder = 'sorting ASC';
        }

        $references = Reference::findItems($this->reference_archiv, $filterCategory, $strOrder);

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
            $template = new FrontendTemplate ($this->strTemplateReferenceList);

            $image = FilesModel::findByPk($references->image);
            if ($image) {
                \Controller::addImageToTemplate($template, array(
                    'singleSRC' => $image->path,
                    'size' => deserialize($this->imgSize),
                    'alt' => $references->title
                ));
            }

            $template->reference = $references;
            $categories = unserialize($references->category);
            $categoryCss = [];
            $categoryTmp = [];
            if ($categories && count($categories) > 0) {
                foreach ($categories as $id) {
                    $categoryCss[] = 'cat-' . $id;
                    $categoryTmp[$id] = $this->categories[$id];
                }
            }
            $template->categories = $categoryTmp;
            $template->cssCategories = implode(' ', $categoryCss);

            if ($page) {
                $template->link = $page->getFrontendUrl('/' . $references->id);
            }
            $strHTML .= $template->parse();
        }

        return $strHTML;
    }

    protected function generateFilter()
    {
        $filterCategories = [];
        if (count($this->categories) > 0) {
            foreach ($this->categories as $id => $name) {
                $filterCategories[$id] = $name;
            }
        }

        $this->strTemplateFilter = 'reference_filter_button';
        if ($this->referenceFilterTpl !== '') {
            $this->strTemplateFilter = $this->referenceFilterTpl;
        }
        $template = new FrontendTemplate($this->strTemplateFilter);
        $template->categories = $filterCategories;

        $template->filterId = Input::get('filter');

        return $template->parse();
    }
}
