<?php

/**
 * Dynamically add the permission check and parent table
 */
if (Input::get('do') == 'reference') {
    $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_reference';
}