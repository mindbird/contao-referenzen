<?php

namespace Mindbird\Contao\Reference\Models;

use Contao\Model;

class Reference extends Model {
	protected static $strTable = 'tl_reference';
	public static function findItems($intPid, $categoryId = 0, $strOrder = 'title ASC') {
		$options = array ();
		$options ['column'] [] = 'pid = ?';
		$options ['value'] [] = $intPid;

		if ($categoryId > 0) {
			$options ['column'] [] = 'category LIKE ?';
			$options ['value'] [] = '%"' . $categoryId . '"%';
		}

		$options ['order'] = $strOrder;
		
		return static::find ( $options );
	}
}