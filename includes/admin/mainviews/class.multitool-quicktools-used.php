<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_ListTable_QuickTools' ) ) {
    require_once( 'class.multitool-listtable-quicktools.php' );
}

/**
 * Multitool_QuickTools_Used  
 *
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin/MainViews
 * @version     1.0.0
 */
class Multitool_QuickTools_Used extends Multitool_ListTable_QuickTools {

    /**
     * No items found text.
     */
    public function no_items() {
        _e( 'No applicable items found.', 'multitool' );
    }

    /**
     * Filter the main data result and only return the items that apply
     * to this report.
     *
     * @param int $current_page
     * @param int $per_page
     */
    public function get_items( $current_page, $per_page ) {
        global $wpdb;
        
        // Filter $this->items to create a dataset suitable for this view.
        $this->items = array();          
    }
}