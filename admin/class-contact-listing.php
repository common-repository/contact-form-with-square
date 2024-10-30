<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ContactForm_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Contact Form', 'lm-contact-square' ), //singular name of the listed records
			'plural'   => __( 'Contact Forms', 'lm-contact-square' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}


	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */

		public static function get_customers( $per_page = 5, $page_number = 1 ) {

			global $wpdb;

			$sql = "SELECT * FROM {$wpdb->prefix}lm_contact";

			if ( ! empty( $_REQUEST['orderby'] ) ) {
				$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
				$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
			}

			$sql .= " LIMIT $per_page";
			$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

			$result = $wpdb->get_results( $sql, 'ARRAY_A' );

			return $result;
		}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */

		public static function delete_customer( $id ) {
			global $wpdb;

			$wpdb->delete(
				"{$wpdb->prefix}lm_contact",
				[ 'id' => $id ],
				[ '%d' ]
			);
		}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
		public static function record_count() {
			global $wpdb;

			$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}lm_contact";

			return $wpdb->get_var( $sql );
		}


	/** Text displayed when no customer data is available */
		public function no_items() {
			return _e( 'No customers avaliable.', 'lm-contact-square' );
		}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
		public function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'id':
				case 'first_name':
				case 'last_name':
				case 'email':
				case 'phone':
				case 'date_time':
				case 'transaction_id':
				     return $item[ $column_name ];	
				default:
					return print_r( $item, true ); //Show the whole array for troubleshooting purposes
			}
		}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
		function column_action( $item ) {

			$delete_nonce = wp_create_nonce( 'lm_view_customer' );
			return sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">View</a>', esc_attr( $_REQUEST['page'] ), 'view', absint( $item['id'] ), $delete_nonce 
		      );
		}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */

		function get_columns() {
			$columns = [
				'id' => __( 'ID', 'lm-contact-square' ),
				'first_name' => __( 'First Name', 'lm-contact-square' ),
				'last_name' => __( 'Last Name', 'lm-contact-square' ),
				'email'    => __( 'Email', 'lm-contact-square' ),
				'phone'    => __( 'Phone', 'lm-contact-square' ),
				'date_time'    => __( 'Date/Time', 'lm-contact-square' ),
				'action'    => __( 'Action', 'lm-contact-square' )
			];

			return $columns;
		}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'id' => array( 'id', true ),
				'email' => array( 'email', false )
			);

			return $sortable_columns;
		}

    /**
	 * Return All the details for specific customer.
	 */
	    public static function view_details($user_id) {
			global $wpdb;

			$sql = "SELECT * FROM {$wpdb->prefix}lm_contact where id=".$user_id;

			return $wpdb->get_row( $sql, 'ARRAY_A' );
		}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
		public function prepare_items() {

			$per_page     = $this->get_items_per_page( 'customers_per_page', 10 );
			$current_page = $this->get_pagenum();
			$total_items  = self::record_count();

			$this->set_pagination_args( [
				'total_items' => $total_items, //WE have to calculate the total number of items
				'per_page'    => $per_page //WE have to determine how many items to show on a page
			] );
	       
	        $columns = $this->get_columns();
			$hidden = array();
	        $sortable = $this->get_sortable_columns();
	        $this->_column_headers = array($columns, $hidden, $sortable);
			$this->items = self::get_customers( $per_page, $current_page );
		}

}