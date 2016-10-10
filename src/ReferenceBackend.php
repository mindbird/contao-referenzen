<?php

namespace Reference;

use Contao\Database;
use Reference\Models\ReferenceModel;
use Contao\Backend;

class ReferenceBackend extends Backend
{
    /**
     * Hook for searchable pages
     *
     * @param unknown $arrPages
     * @param number $intRoot
     * @param string $blnIsSitemap
     * @return string
     */
    public function getSearchablePages($arrPages, $intRoot = 0, $blnIsSitemap = false)
    {
        $db = Database::getInstance();
        $arrRoot = array();
        if ($intRoot > 0) {
            $arrRoot = $db->getChildRecords($intRoot, 'tl_page', true);
        }

        // Read jump to page details
        $objResult = $db->prepare("SELECT jumpTo, reference_archiv FROM tl_module WHERE type=?")->execute('reference_list');
        $arrModules = $objResult->fetchAllAssoc();

        if (count($arrModules) > 0) {
            $arrPids = array();
            foreach ($arrModules as $arrModule) {
                if (is_array($arrRoot) && count($arrRoot) > 0 && !in_array($arrModule ['jumpTo'], $arrRoot)) {
                    continue;
                }

                $objParent = \PageModel::findWithDetails($arrModule ['jumpTo']);
                // The target page does not exist
                if ($objParent === null) {
                    continue;
                }

                // The target page has not been published (see #5520)
                if (!$objParent->published) {
                    continue;
                }

                // The target page is exempt from the sitemap (see #6418)
                if ($blnIsSitemap && $objParent->sitemap == 'map_never') {
                    continue;
                }

                // Set the domain (see #6421)
                $domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

                $arrPids [] = $arrModule ['reference_archiv'];
                $references = ReferenceModel::findByPids($arrPids, 0, 0, array(
                    'order' => 'id ASC'
                ));
                while ($references->next()) {
                    $arrReferences = $references->row();
                    $arrPages [] = $domain . $this->generateFrontendUrl($objParent->row(),
                            '/referenceID/' . $arrReferences ['id'], $objParent->language);
                }
            }
        }

        return $arrPages;
    }
}
