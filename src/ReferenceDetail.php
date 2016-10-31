<?php

namespace Reference;

use Contao\Database;
use Reference\Models\ReferenceModel;
use Contao\BackendTemplate;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\Module;

class ReferenceDetail extends Module {
	
	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'mod_reference_detail';
	
	public function generate() {
		if (TL_MODE == 'BE') {
			$objTemplate = new BackendTemplate ( 'be_wildcard' );
				
			$objTemplate->wildcard = '### REFERENZEN DETAILS ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
				
			return $objTemplate->parse ();
		}
	
		return parent::generate ();
	}
	
	protected function compile() {
        $db = Database::getInstance();
		$referenceId = Input::get ( 'referenceId' );
		$reference = ReferenceModel::findByPk ( $referenceId );
		if ($reference) {
			global $objPage;
			$objPage->pageTitle = $reference->title;
			
			$template = new FrontendTemplate ( 'reference_detail' );

			$image = FilesModel::findByPk ( $reference->image );
            if ($image) {
                \Controller::addImageToTemplate($template, array(
                    'singleSRC' => $image->path,
                    'size' => deserialize ( $this->imgSize ),
                    'alt' => $reference->title
                ));
            }

			// Get Categories
			$categories = deserialize ( $reference->category );
            $strCategory = '';
			if (count ( $categories ) > 0) {
			    print ("SELECT * FROM tl_reference_category WHERE id IN(" . implode ( ',', $categories ) . ")");
				$referenceCategories = $db->prepare ( "SELECT * FROM tl_reference_category WHERE id IN(" . implode ( ',', $categories ) . ")" )->execute (  );
				while ( $referenceCategories->next () ) {
					$arrCategory [] = $referenceCategories->title;
				}
				$strCategory = implode ( ', ', $arrCategory );
			}
			$template->title = $reference->title;
			$template->teaser = $reference->teaser;
            $template->description = $reference->description;
            $template->category = $strCategory;
			
			$this->Template->referenceHtml = $template->parse ();
		}
	}
}
