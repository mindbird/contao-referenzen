<?php

namespace Reference\Models;

use Contao\Model;

class ReferenceCategoryModel extends Model {

	protected static $strTable = 'tl_reference_category';

    /**
     * Find all published FAQs by their parent IDs
     *
     * @param string $container  An Name from Container
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|\FaqModel|null A collection of models or null if there are no FAQs
     */
    public static function findShowCategoriesByArchive($archive, array $arrOptions=array())
    {
        $t = static::$strTable;

        $arrColumns = array();

        if (!BE_USER_LOGGED_IN)
        {
            $arrColumns[] = "$t.show='1'";
            $arrColumns[] = "$t.pid='".$archive."'";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.title ASC";
        }

        return static::findOneBy($arrColumns, null, $arrOptions);
    }
    /**
     * Find all published FAQs by their parent IDs
     *
     * @param string $container  An Name from Container
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|\FaqModel|null A collection of models or null if there are no FAQs
     */
    public static function findRelatedCategoryByPage($pageId, array $arrOptions=array())
    {
        $t = static::$strTable;

        $arrColumns = array();

        if (!BE_USER_LOGGED_IN)
        {
            $arrColumns[] = "$t.filterPage='".$pageId."'";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.title ASC";
        }

        return static::findBy($arrColumns, null, $arrOptions);
    }

}