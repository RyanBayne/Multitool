<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class.wp-list-table.php' );
}

/**
 * Multitool_ListTable_QuickTools.
 *
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin/MainViews
 * @version     1.0.0
 */
class Multitool_ListTable_QuickTools extends WP_List_Table {

	/**
	 * Max items.
	 *
	 * @var int
	 */
	protected $max_items;

    public $items = array();
    
	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct( array(
			'singular'  => __( 'Tool', 'multitool' ),
			'plural'    => __( 'Tools', 'multitool' ),
			'ajax'      => false
		) );
        
        // Perform query or set default items.
        $this->query_items();
	}

    /**
    * Setup default items. 
    * 
    * This is not required and was only implemented for demonstration purposes. 
    */
    public function query_items() {
        $quick_tools = new Multitool_QuickTools();
        $quick_tools->return_tool_info = true;
        
        $tools_info_array = array();
        
        foreach( get_class_methods( 'Multitool_QuickTools' ) as $tool ) {
            if( substr( $tool, 0, 5 ) !== "tool_" ) { continue; } 
            eval( '$tool_info = $quick_tools->$tool();');
            
            // Capability check.
            if( !current_user_can( $tool_info['capability'] ) ) { continue; }
            
            // Create a new list table item.
            $this->items[] = $tool_info;
            
            // Get the last array key and add tool (method) name.
            end( $this->items );
            $key = key( $this->items ); 
            $this->items[ $key ]['name'] = $tool;
        }
    }
    
	/**
	 * No items found text.
	 */
	public function no_items() {
		_e( 'No items found.', 'multitool' );
	}

	/**
	 * Control what table navigation is displayed.
	 *
	 * @param string $position
	 */
	public function display_tablenav( $position ) {
        // Avoid displaying the top navigation and make tidier space for sub view links.
		if ( $position != 'top' ) {
			parent::display_tablenav( $position );
		}
	}

	/**
	 * Output the report.
	 */
	public function output_result() {
		$this->prepare_items();
		echo '<div id="poststuff" class="multitool-reports-wide">';
        echo '<form id="multitool-list-table-form-quicktools" method="post">';
		$this->display();
        echo '<form>';
		echo '</div>';
	}

	/**
	 * Get column value.
	 *
	 * @param mixed $item
	 * @param string $column_name
	 */
	public function column_default( $item, $column_name ) {

		switch( $column_name ) {

			case 'header_title' :
				echo $item['title'];
			break;

			case 'header_description' :
				echo $item['description'];
			break;

			case 'header_version' :
				echo $item['version'];
			break;
 
            case 'header_category' :
                echo $item['category'];
            break;

            case 'header_action' :
            
                $nonce = wp_create_nonce( 'quicktool_action' );
                $url   = self_admin_url( 'tools.php?page=multitool-quick&_wpnonce=' . $nonce . '&toolname=' . $item['name'] );   
                echo '<a href="' . $url . '" class="button button-primary">' . __( 'Run Tool', 'multitool' ) . '</a>';
                    
            break;

		}
	}
    
    /**
    * Adds a column of checkboxes for use with bulk actions.
    */
    public function column_cb( $item ) {
        // The display is controlled within the sub view files. 
    }    

	/**
	 * Get columns.
	 *
	 * @return array
	 */
	public function get_columns() {
        
		$columns = array(
            'header_title'       => __( 'Tool Name', 'multitool' ),
			'header_description' => __( 'Description', 'multitool' ),
			'header_version'     => __( 'Version', 'multitool' ),
            'header_category'    => __( 'Category', 'multitool' ),
            'header_action'      => __( 'Run Tool', 'multitool' ),
		);

        // Sub view might offer bulk actions and require checkbox column. 
        if( isset( $this->checkbox_column ) && $this->checkbox_column === true ) {
            $cb = array( 'cb' => __( '<input type="checkbox" />', 'multitool' ) );
            $columns = array_merge( $cb, $columns );    
        }
        
		return $columns;
	}

	/**
	 * Prepare customer list items further. Does not get the items. It only
     * prepares them for the specific views presentation configuration i.e. pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$current_page          = absint( $this->get_pagenum() );
		$per_page              = apply_filters( 'multitool_admin_list_table_demo_items_per_page', 20 );

		$this->get_items( $current_page, $per_page );

        // Process bulk actions.
        //$this->process_bulk_action();
              
		/**
		 * Pagination.
		 */
		$this->set_pagination_args( array(
			'total_items' => $this->max_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $this->max_items / $per_page )
		) );
	}
    
    /**
    * Process bulk actions selected in two possible menus. 
    */
    public function process_bulk_actions() {
        // Processing is handled by each sub file.     
    }
}
