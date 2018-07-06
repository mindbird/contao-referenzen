<?php

namespace Mindbird\Contao\Reference\Modules;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\Controller;
use Contao\Database;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;
use Mindbird\Contao\Reference\Models\Reference;

class Detail extends Module {
	
	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'mod_reference_detail';
	
	public function generate() {
		if (TL_MODE === 'BE') {
			$template = new BackendTemplate ( 'be_wildcard' );
				
			$template->wildcard = '### REFERENZEN DETAILS ###';
			$template->title = $this->headline;
			$template->id = $this->id;
			$template->link = $this->name;
			$template->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
				
			return $template->parse ();
		}
	
		return parent::generate ();
	}
	
	protected function compile() {
        $db = Database::getInstance();
		$referenceId = Input::get ( 'referenceId' );
		$reference = Reference::findByPk ( $referenceId );
		if ($reference) {
			global $objPage;
			$objPage->pageTitle = $reference->title;
			
			$template = new FrontendTemplate ( 'reference_detail' );

			$image = FilesModel::findByPk ( $reference->image );
            if ($image) {
                Controller::addImageToTemplate($template, array(
                    'singleSRC' => $image->path,
                    'size' => deserialize ( $this->imgSize ),
                    'alt' => $reference->title
                ));
            }

			// Get Categories
			$categories = deserialize ( $reference->category );
            $category = '';
			if (count ( $categories ) > 0) {
				$referenceCategories = $db->prepare ( "SELECT * FROM tl_reference_category WHERE id IN(" . implode ( ',', $categories ) . ")" )->execute (  );
				while ( $referenceCategories->next () ) {
					$arrCategory [] = $referenceCategories->title;
				}
				$category = implode ( ', ', $arrCategory );
			}

            $content = '';
            $contentElement = ContentModel::findPublishedByPidAndTable($referenceId, 'tl_reference');
            if ($contentElement !== null)
            {
                while ($contentElement->next())
                {
                    $content .= $this->getContentElement($contentElement->current());
                }
            }

            $template->content = $content;
			$template->title = $reference->title;
			$template->teaser = $reference->teaser;
            $template->description = $reference->description;
            $template->category = $category;
			
			$this->Template->referenceHtml = $template->parse ();
		}
	}
}
