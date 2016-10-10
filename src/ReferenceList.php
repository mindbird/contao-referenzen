<?php

namespace Reference;

use Contao\BackendTemplate;
use Contao\Environment;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\PageModel;
use Reference\Models\ReferenceArchiveModel;
use Reference\Models\ReferenceCategoryModel;
use Reference\Models\ReferenceModel;

class ReferenceList extends Module
{

    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_reference_list';

    protected $strTemplateReferenceList = 'reference_list';

    public function generate()
    {
        if (TL_MODE == 'BE') {
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

    protected function compile()
    {
        // Read jump to page details
        $objPage = PageModel::findByIdOrAlias($this->jumpTo);

        // Check if filter should be displayed
        if (!$this->reference_filter_disabled) {
            $objTemplateFilter = new FrontendTemplate('reference_list_filter');

            // Filter category
            $intFilterCategory = Input::get('filterCategory');
            $strFilterUrl = '';
            if ($intFilterCategory > 0) {
                $strFilterUrl = '&filterCategory=' . $intFilterCategory;
            }

            // Filter search
            $strSearch = Input::get('search');
            $strSearchUrl = 'search=%s';
            $objTemplateFilter->strLink = $strSearch != '' ? Environment::get('base') . $this->addToUrl(sprintf($strSearchUrl,
                        $strSearch) . '&filterCategory=ID',
                    true) : Environment::get('base') . $this->addToUrl('filterCategory=ID', true);

            // Generate letters
            $arrAlphabet = range('A', 'Z');
            $strHtml = '<a href="' . $this->addToUrl($strFilterUrl, true) . '">Alle</a> ';
            for ($i = 0; $i < count($arrAlphabet); $i++) {
                $strHtml .= '<a href="' . $this->addToUrl(sprintf($strSearchUrl, $arrAlphabet [$i]) . $strFilterUrl,
                        true) . '">' . $arrAlphabet [$i] . '</a> ';
            }
            $objTemplateFilter->strFilterName = $strHtml;

            // Get Categories
            $this->loadLanguageFile('tl_reference_category');
            $objCategories = ReferenceCategoryModel::findBy('pid', $this->reference_archiv, array(
                'order' => 'title ASC'
            ));
            $strOptions = '<option value="0">' . $GLOBALS ['TL_LANG'] ['tl_reference_category'] ['category'] [0] . '</option>';
            if ($objCategories) {
                while ($objCategories->next()) {
                    $strOptions .= '<option value="' . $objCategories->id . '"' . ($intFilterCategory != $objCategories->id ? '' : ' selected') . '>' . $objCategories->title . '</option>';
                }
            }
            $objTemplateFilter->strCategoryOptions = $strOptions;
            $this->Template->strFilter = $objTemplateFilter->parse();
        } else {
            $strSearch = '';
            $intFilterCategory = 0;
        }

        // Get items to calculate total number of items
        $references = ReferenceModel::findItems($this->reference_archiv, $strSearch, $intFilterCategory);

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
        if ($references && $this->perPage > 0 && ($limit == 0 || $this->numberOfItems > $this->perPage)) {

            // Set limit, page and offset
            $limit = $this->perPage;
            $intPage = $this->Input->get('page') ? $this->Input->get('page') : 1;
            $offset = ($intPage - 1) * $limit;

            // Add pagination menu
            $objPagination = new \Pagination ($total, $limit);
            $this->Template->strPagination = $objPagination->generate();
        }

        // Order
        $referenceArchive = ReferenceArchiveModel::findByPk($this->reference_archiv);

        switch ($referenceArchive->sort_order) {
            case 2:
                $strOrder = 'sorting ASC';
                break;
            case 1:
            default:
                $strOrder = $this->reference_random ? 'RAND()' : 'title ASC';
                break;
        }


        $references = ReferenceModel::findItems($this->reference_archiv, $strSearch, $intFilterCategory, $offset, $limit,
            $strOrder);

        if ($references) {
            $this->Template->strCompanies = $this->getReferences($references, $objPage);
        } else {
            $this->Template->strCompanies = 'Mit den ausgewählten Filterkriterien sind keine Einträge vorhanden.';
        }
    }

    /**
     * Return string/html of all companies
     *
     * @param array $arrCompanies
     *            DB query rows as array
     * @return string
     */
    protected function getReferences($references, $page = null)
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

            $template->title = $references->title;
            $template->teaser = $references->teaser;
            $template->description = $references->description;

            if ($page) {
                $template->link = $this->generateFrontendUrl($page->row(), '/referenceId/' . $references->id);
            }
            $strHTML .= $template->parse();
        }

        return $strHTML;
    }
}
