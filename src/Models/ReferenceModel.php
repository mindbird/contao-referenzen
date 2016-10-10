<?php

namespace Reference\Models;

use Contao\Model;

class ReferenceModel extends Model {
	protected static $strTable = 'tl_reference';
	public static function findItems($intPid, $strCompanyName = '', $intCategory = 0, $intOffset = 0, $intLimit = 0, $strOrder = 'company ASC') {
		$arrOptions = array ();
		$arrOptions ['column'] [] = 'pid = ?';
		$arrOptions ['value'] [] = $intPid;
		
		if ($strCompanyName != '') {
			$arrOptions ['column'] [] = 'reference LIKE ?';
			$arrOptions ['value'] [] = $strCompanyName . '%';
		}
		
		if ($intCategory > 0) {
			$arrOptions ['column'] [] = 'category LIKE ?';
			$arrOptions ['value'] [] = '%"' . $intCategory . '"%';
		}
		
		if ($intOffset > 0) {
			$arrOptions ['offset'] = $intOffset;
		}
		
		if ($intLimit > 0) {
			$arrOptions ['limit'] = $intLimit;
		}
		
		$arrOptions ['order'] = $strOrder;
		
		return static::find ( $arrOptions );
	}
}