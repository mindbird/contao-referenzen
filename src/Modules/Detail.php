<?php

namespace Mindbird\Contao\Reference\Modules;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\Controller;
use Contao\Database;
use Contao\FilesModel;
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
		$referenceId = Input::get('auto_item');
		$reference = Reference::findByPk ( $referenceId );
		if ($reference) {
			global $objPage;
			$objPage->pageTitle = $reference->title;

			$image = FilesModel::findByPk ( $reference->image );
            if ($image) {
                Controller::addImageToTemplate($this->Template, array(
                    'singleSRC' => $image->path,
                    'size' => deserialize ( $this->imgSize ),
                    'alt' => $reference->title
                ));
            }

			// Get Categories
			$categories = deserialize ( $reference->category );
            $categoryArr = [];
			if (\count( $categories ) > 0) {
				$referenceCategories = $db->prepare ( "SELECT * FROM tl_reference_category WHERE id IN(" . implode ( ',', $categories ) . ")" )->execute (  );
				while ( $referenceCategories->next () ) {
                    $categoryArr[$referenceCategories->id] = $referenceCategories->title;
				}
			}

            $content = '';
            $contentElement = ContentModel::findPublishedByPidAndTable($referenceId, 'tl_reference');
            if ($contentElement !== null)
            {
                while ($contentElement->next())
                {
                    $content .= static::getContentElement($contentElement->current());
                }
            }

            $this->Template->content = $content;
			$this->Template->reference = $reference;
            $this->Template->categories = $categoryArr;
		}
	}
}
