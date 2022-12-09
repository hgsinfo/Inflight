<?php
/**
 * WC_GC_Activity_DB class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Gift Cards
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DB API class.
 *
 * @version  1.0.0
 */
class WC_GC_Activity_DB {

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-gift-cards' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-gift-cards' ), '1.0.0' );
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		//...
	}

	/**
	 * Query activiry data from the DB.
	 *
	 * @param  array  $args  {
	 *     @type  string     $return           Return array format:
	 *
	 *         - 'all': entire row casted to array,
	 *         - 'ids': ids only,
	 *         - 'objects': WC_PRL_Gift_Card_Data objects.
	 * }
	 *
	 * @return array
	 */
	public function query( $args ) {
		global $wpdb;

		$args = wp_parse_args( $args, array(
			'return'          => 'all', // 'ids' | 'objects'
			'count'           => false,
			'search'          => '',
			'type'            => '',
			'user_id'         => 0,
			'user_email'      => '',
			'object_id'       => 0,
			'gc_id'           => 0,
			'gc_code'         => '',
			'amount'          => 0,
			'date'            => 0,
			'note'            => '',
			'start_date'      => '',
			'end_date'        => '',
			'order_by'        => array( 'id' => 'ASC' ),
			'limit'           => -1,
			'offset'          => -1
		) );

		$table = $wpdb->prefix . 'woocommerce_gc_activity';

		if ( $args[ 'count' ] ) {

			$select = "COUNT( {$table}.id )";

		} else {

			if ( in_array( $args[ 'return' ], array( 'ids' ) ) ) {
				$select = $table . '.id';
			} else {
				$select = '*';
			}
		}

		// Build the query.
		$sql      = 'SELECT ' . $select . " FROM {$table}";
		$join     = '';
		$where    = '';
		$order_by = '';

		$where_clauses    = array( '1=1' );
		$where_values     = array();
		$order_by_clauses = array();

		// WHERE clauses.

		if ( $args[ 'type' ] ) {

			$types = is_array( $args[ 'type' ] ) ? $args[ 'type' ] : array( $args[ 'type' ] );
			$types = array_map( 'esc_sql', $types );

			$where_clauses[] = "{$table}.type IN ( " . implode( ', ', array_fill( 0, count( $types ), '%s' ) ) . ' )';
			$where_values    = array_merge( $where_values, $types );
		}

		if ( $args[ 'user_id' ] ) {
			$user_ids = array_map( 'absint', is_array( $args[ 'user_id' ] ) ? $args[ 'user_id' ] : array( $args[ 'user_id' ] ) );
			$user_ids = array_map( 'esc_sql', $user_ids );

			$where_clauses[] = "{$table}.user_id IN ( " . implode( ', ', array_fill( 0, count( $user_ids ), '%d' ) ) . ' )';
			$where_values    = array_merge( $where_values, $user_ids );
		}

		if ( $args[ 'user_email' ] ) {

			$user_emails = is_array( $args[ 'user_email' ] ) ? $args[ 'user_email' ] : array( $args[ 'user_email' ] );
			$user_emails = array_map( 'esc_sql', $user_emails );

			$where_clauses[] = "{$table}.user_email IN ( " . implode( ', ', array_fill( 0, count( $user_emails ), '%s' ) ) . ' )';
			$where_values    = array_merge( $where_values, $user_emails );
		}

		if ( $args[ 'gc_id' ] ) {
			$gc_ids = array_map( 'absint', is_array( $args[ 'gc_id' ] ) ? $args[ 'gc_id' ] : array( $args[ 'gc_id' ] ) );
			$gc_ids = array_map( 'esc_sql', $gc_ids );

			$where_clauses[] = "{$table}.gc_id IN ( " . implode( ', ', array_fill( 0, count( $gc_ids ), '%d' ) ) . ' )';
			$where_values    = array_merge( $where_values, $gc_ids );
		}

		if ( $args[ 'object_id' ] ) {
			$object_ids = array_map( 'absint', is_array( $args[ 'object_id' ] ) ? $args[ 'object_id' ] : array( $args[ 'object_id' ] ) );
			$object_ids = array_map( 'esc_sql', $object_ids );

			$where_clauses[] = "{$table}.object_id IN ( " . implode( ', ', array_fill( 0, count( $object_ids ), '%d' ) ) . ' )';
			$where_values    = array_merge( $where_values, $object_ids );
		}

		if ( $args[ 'search' ] ) {
			$s               = esc_sql( '%' . $args[ 'search' ] . '%' );
			$where_clauses[] = "( {$table}.gc_code LIKE %s OR {$table}.user_email LIKE %s )";
			$where_values    = array_merge( $where_values, array_fill( 0, 2, $s ) );
		}

		if ( $args[ 'start_date' ] ) {
			$start_date      = absint( $args[ 'start_date' ] );
			$where_clauses[] = "{$table}.date >= %d";
			$where_values    = array_merge( $where_values, array( $start_date ) );
		}

		if ( $args[ 'end_date' ] ) {
			$end_date        = absint( $args[ 'end_date' ] );
			$where_clauses[] = "{$table}.date < %d";
			$where_values    = array_merge( $where_values, array( $end_date ) );
		}

		// ORDER BY clauses.
		if ( $args[ 'order_by' ] && is_array( $args[ 'order_by' ] ) ) {
			foreach ( $args[ 'order_by' ] as $what => $how ) {
				$order_by_clauses[] = $table . '.' . esc_sql( strval( $what ) ) . ' ' . esc_sql( strval( $how ) );
			}
		}

		$order_by_clauses = empty( $order_by_clauses ) ? array( $table . '.id, ASC' ) : $order_by_clauses;

		// Build SQL query components.

		$where    = ' WHERE ' . implode( ' AND ', $where_clauses );
		$order_by = ' ORDER BY ' . implode( ', ', $order_by_clauses );
		$limit    = $args[ 'limit' ] > 0 ? ' LIMIT ' . absint( $args[ 'limit' ] ) : '';
		$offset   = $args[ 'offset' ] > 0 ? ' OFFSET ' . absint( $args[ 'offset' ] ) : '';
		// Assemble and run the query.

		$sql .= $join . $where . $order_by . $limit . $offset;

		/**
		 * WordPress.DB.PreparedSQL.NotPrepared explained.
		 *
		 * The sniff isn't smart enough to follow $sql variable back to its source. So it doesn't know whether the query in $sql incorporates user-supplied values or not.
		 * Whitelisting comment is the solution here. @see https://github.com/WordPress/WordPress-Coding-Standards/issues/469
		 */

		if ( $args[ 'count' ] ) {
			if ( empty( $where_values ) ) {
				$count = absint( $wpdb->get_var( $sql ) ); // @phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			} else {
				$count = absint( $wpdb->get_var( $wpdb->prepare( $sql, $where_values ) ) ); // @phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			}
			return $count;
		} else {
			if ( empty( $where_values ) ) {
				$results = $wpdb->get_results( $sql ); // @phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			} else {
				$results = $wpdb->get_results( $wpdb->prepare( $sql, $where_values ) ); // @phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			}
		}

		if ( empty( $results ) ) {
			return array();
		}

		$a = array();

		if ( 'objects' === $args[ 'return' ] ) {
			foreach ( $results as $result ) {
				$a[] = self::get( $result->id );
			}
		} elseif ( 'ids' === $args[ 'return' ] ) {
			foreach ( $results as $result ) {
				$a[] = $result->id;
			}
		} else {
			foreach ( $results as $result ) {
				$a[] = (array) $result;
			}
		}

		return $a;
	}

	/**
	 * Get a record from the DB.
	 *
	 * @param  mixed  $activity
	 * @return false|WC_GC_Activity_Data
	 */
	public function get( $activity ) {

		if ( is_numeric( $activity ) ) {
			$activity = absint( $activity );
			$activity = new WC_GC_Activity_Data( $activity );
		} elseif ( $activity instanceof WC_GC_Activity_Data ) {
			$activity = new WC_GC_Activity_Data( $activity );
		} elseif ( is_object( $activity ) ) {
			$giftcard = new WC_GC_Activity_Data( (array) $activity );
		} else {
			$activity = false;
		}

		if ( ! $activity || ! is_object( $activity ) || ! $activity->get_id() ) {
			return false;
		}

		return $activity;
	}

	/**
	 * Create a record in the DB.
	 *
	 * @param  array  $args
	 * @return false|int
	 *
	 * @throws Exception
	 */
	public function add( $args ) {

		$args = wp_parse_args( $args, array(
			'type'            => '',
			'user_id'         => 0,
			'user_email'      => '',
			'object_id'       => 0,
			'gc_id'           => 0,
			'gc_code'         => '',
			'amount'          => 0,
			'date'            => 0,
			'note'            => ''
		) );

		// Empty attributes.
		if ( empty( $args[ 'type' ] ) || empty( $args[ 'gc_id' ] ) ) {
			throw new Exception( __( 'Missing activity attributes.', 'woocommerce-gift-cards' ) );
		}

		$this->validate( $args );

		$activity = new WC_GC_Activity_Data( array(
			'type'            => $args[ 'type' ],
			'user_id'         => $args[ 'user_id' ],
			'user_email'      => $args[ 'user_email' ],
			'object_id'       => $args[ 'object_id' ],
			'gc_id'           => $args[ 'gc_id' ],
			'gc_code'         => $args[ 'gc_code' ],
			'amount'          => $args[ 'amount' ],
			'date'            => $args[ 'date' ],
			'note'            => $args[ 'note' ]
		) );

		return $activity->save();
	}

	/**
	 * Update a record in the DB.
	 *
	 * @param  mixed  $activity
	 * @param  array  $args
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function update( $activity, $args ) {

		if ( is_numeric( $activity ) ) {
			$activity = absint( $activity );
			$activity = new WC_GC_Activity_Data( $activity );
		}

		if ( is_object( $activity ) && $activity->get_id() && ! empty( $args ) && is_array( $args ) ) {

			$this->validate( $args, $activity );

			$activity->set_all( $args );

			return $activity->save();
		}

		return false;
	}

	/**
	 * Validate data.
	 *
	 * @param  array  &$args
	 * @param  WC_GC_Activity_Data  $activity
	 * @return void
	 *
	 * @throws Exception
	 */
	public function validate( &$args, $activity = false ) {

		if ( ! empty( $args[ 'type' ] ) ) {
			if ( ! in_array( $args[ 'type' ], array_keys( wc_gc_get_activity_types() ) ) ) {
				throw new Exception( __( 'Invalid activity type.', 'woocommerce-gift-cards' ) );
			}
		}

		if ( ! empty( $args[ 'user_email' ] ) && ! filter_var( $args[ 'user_email' ], FILTER_VALIDATE_EMAIL ) ) {
			/* translators: %s email string */
			throw new Exception( __( sprintf( 'User email `%s` is an invalid email.', $args[ 'user_email' ] ), 'woocommerce-gift-cards' ) );
		}

		// New Activity.
		if ( ! is_object( $activity ) || ! $activity->get_id() ) {

			// Fill in GC code if not provided.
			if ( empty( $args[ 'gc_code' ] ) ) {

				$gc_data = WC_GC()->db->giftcards->get( absint( $args[ 'gc_id' ] ) );

				if ( is_object( $gc_data ) ) {
					$args[ 'gc_code' ] = $gc_data->get_code();
				} else {
					throw new Exception( __( sprintf( 'Gift Card not found.', $args[ 'user_email' ] ), 'woocommerce-gift-cards' ) );
				}
			}

			// Set timestamp.
			$args[ 'date' ] = time();
		}
	}

	/**
	 * Delete a record from the DB.
	 *
	 * @param  mixed  $activity
	 * @return void
	 */
	public function delete( $activity ) {
		$activity = $this->get( $activity );
		if ( $activity ) {
			$activity->delete();
		}
	}

	/**
	 * Get distinct dates.
	 *
	 * @return array
	 */
	public function get_distinct_dates() {
		global $wpdb;

		$months = $wpdb->get_results(
				"
			SELECT DISTINCT YEAR( FROM_UNIXTIME( {$wpdb->prefix}woocommerce_gc_activity.`date` ) ) AS year, MONTH( FROM_UNIXTIME( {$wpdb->prefix}woocommerce_gc_activity.`date` ) ) AS month
			FROM {$wpdb->prefix}woocommerce_gc_activity
			ORDER BY {$wpdb->prefix}woocommerce_gc_activity.`date` DESC"
		);

		return $months;
	}
}
