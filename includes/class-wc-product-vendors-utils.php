<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Utility Class.
 *
 * All miscellanous convenient functions wrapper.
 *
 * @category Utility
 * @package  WooCommerce Product Vendors/Utils
 * @version  2.0.0
 */
class WC_Product_Vendors_Utils {
	/**
	 * Empty private constructor to prevent instantiation
	 *
	 * @access private
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Conditional check if current user is a vendor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $user_id id of the user to check
	 * @return bool
	 */
	public static function is_vendor( $user_id = null ) {
		// if param not passed use current user
		if ( null === $user_id ) {
			$current_user = wp_get_current_user();

			$user_id = $current_user->ID;
		}

		// check if user is a shop vendor
		if ( self::is_manager_vendor( $user_id ) ||
			self::is_admin_vendor( $user_id ) ||
			self::is_pending_vendor( $user_id )
		) {
			
			return true;
		}

		return false;
	}

	/**
	 * Conditional check if current user is a pending vendor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $user_id id of the user to check
	 * @return bool
	 */
	public static function is_pending_vendor( $user_id = null ) {
		// if param not passed use current user
		if ( null === $user_id ) {
			$current_user = wp_get_current_user();
		
		} else {
			$current_user = new WP_User( $user_id );
		}

		if ( is_object( $current_user ) && in_array( 'wc_product_vendors_pending_vendor', $current_user->roles ) ) {
			
			return true;
		}

		return false;
	}

	/**
	 * Conditional check if current user is an admin vendor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $user_id id of the user to check
	 * @return bool
	 */
	public static function is_admin_vendor( $user_id = null ) {
		// if param not passed use current user
		if ( null === $user_id ) {
			$current_user = wp_get_current_user();
		
		} else {
			$current_user = new WP_User( $user_id );
		}

		if ( is_object( $current_user ) && in_array( 'wc_product_vendors_admin_vendor', $current_user->roles ) ) {

			return true;
		}

		return false;
	}

	/**
	 * Conditional check if current user is a manager vendor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $user_id id of the user to check
	 * @return bool
	 */
	public static function is_manager_vendor( $user_id = null ) {
		// if param not passed use current user
		if ( null === $user_id ) {
			$current_user = wp_get_current_user();
		
		} else {
			$current_user = new WP_User( $user_id );
		}

		if ( is_object( $current_user ) && in_array( 'wc_product_vendors_manager_vendor', $current_user->roles ) ) {
			
			return true;
		}

		return false;
	}

	/**
	 * Sanitizes multilevel array
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param array $item
	 * @param mix $key
	 * @return bool
	 */
	public static function sanitize_multi_array( $item, $key ) {
		$item = sanitize_text_field( $item );

		return $item;		
	}

	/**
	 * Sanitizes commission
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $commission
	 * @return string $commission
	 */
	public static function sanitize_commission( $commission ) {
		if ( '' === trim( $commission ) || is_null( trim( $commission ) ) ) {
			return '';
		}

		// strip all percentages and make positive whole number
		return absint( str_replace( '%', '', trim( $commission ) ) );
	}

	/**
	 * Gets the data from a specific vendor the passed in user is managing
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array $vendor_data
	 */
	public static function get_vendor_data_from_user() {
		return self::get_vendor_data_by_id( WC_Product_Vendors_Utils::get_logged_in_vendor( 'id' ) );
	}

	/**
	 * Get sold by info for the vendor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $post_id
	 * @return array mixed
	 */
	public static function get_sold_by_link( $post_id = null ) {
		if ( null === $post_id ) {
			return;
		}

		$name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$link = get_permalink( woocommerce_get_page_id( 'shop' ) );

		$term = wp_get_post_terms( $post_id, WC_PRODUCT_VENDORS_TAXONOMY );

		if ( ! empty( $term ) ) {
			$link = get_term_link( $term[0], WC_PRODUCT_VENDORS_TAXONOMY );

			$name = $term[0]->name;
		}

		return array( 'link' => $link, 'name' => $name );
	}

	/**
	 * Gets the data from a specific vendor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $vendor_id
	 * @return array $vendor_data
	 */
	public static function get_vendor_data_by_id( $vendor_id ) {
		$vendor_data = get_term_meta( absint( $vendor_id ), 'vendor_data', true );

		$vendor_term = get_term_by( 'id', $vendor_id, WC_PRODUCT_VENDORS_TAXONOMY );

		if ( $vendor_data && $vendor_term ) {
			$vendor_data['term_id']          = $vendor_term->term_id;
			$vendor_data['name']             = $vendor_term->name;
			$vendor_data['slug']             = $vendor_term->slug;
			$vendor_data['term_group']       = $vendor_term->term_group;
			$vendor_data['term_taxonomy_id'] = $vendor_term->term_taxonomy_id;
			$vendor_data['taxonomy']         = $vendor_term->taxonomy;
			$vendor_data['description']      = $vendor_term->description;
			$vendor_data['parent']           = $vendor_term->parent;
			$vendor_data['count']            = $vendor_term->count;			
		}

		return $vendor_data;
	}

	/**
	 * Gets all vendor data the passed in user is managing
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $user_id
	 * @return array $vendor_data
	 */
	public static function get_all_vendor_data( $user_id = null ) {
		// if param not passed use current user
		if ( null === $user_id ) {
			$current_user = wp_get_current_user();

			$user_id = $current_user->ID;
		}

		$terms = get_terms( WC_PRODUCT_VENDORS_TAXONOMY, array( 'hide_empty' => false ) );

		$vendor_data = array();

		$vendors = array();

		// loop through to see which one has assigned passed in user
		foreach( $terms as $term ) {
			$vendor_data = get_term_meta( $term->term_id, 'vendor_data', true );

			if ( ! empty( $vendor_data['admins'] ) ) {
				$admin_ids = array_filter( array_map( 'absint', explode( ',', $vendor_data['admins'] ) ) );

				if ( in_array( $user_id, $admin_ids ) ) {
					$vendor_data['term_id']          = $term->term_id;
					$vendor_data['name']             = $term->name;
					$vendor_data['slug']             = $term->slug;
					$vendor_data['term_group']       = $term->term_group;
					$vendor_data['term_taxonomy_id'] = $term->term_taxonomy_id;
					$vendor_data['taxonomy']         = $term->taxonomy;
					$vendor_data['description']      = $term->description;
					$vendor_data['parent']           = $term->parent;
					$vendor_data['count']            = $term->count;

					$vendors[ $term->slug ] = $vendor_data;
				}
			}
		}

		return $vendors;
	}

	/**
	 * Gets the vendor login cookie
	 *
	 * @access private
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return mix
	 */
	private static function _get_vendor_login_cookie() {
		if ( ! empty( $_COOKIE[ 'wcpv_vendor_name_' . COOKIEHASH ] ) ) {
			return $_COOKIE[ 'wcpv_vendor_name_' . COOKIEHASH ];
		}

		return false;
	}

	/**
	 * Authenticates if user is assigned to a vendor and can manage it
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $user_id
	 * @param string $vendor_slug
	 * @return bool
	 */
	public static function auth_vendor_user( $user_id = null, $vendor_slug = null ) {
		// if param not passed use current user
		if ( null === $user_id ) {
			$current_user = wp_get_current_user();

			$user_id = $current_user->ID;
		}

		// if param not passed get from cookie
		if ( null === $vendor_slug ) {
			$vendor_slug = self::_get_vendor_login_cookie();
		}

		// if term does not exist
		if ( 0 === self::is_valid_vendor_name( $vendor_slug ) || null === self::is_valid_vendor_name( $vendor_slug ) ) {
			return false;
		}

		if ( self::is_admin_vendor( $user_id ) || self::is_manager_vendor( $user_id ) ) {
			$term = get_term_by( 'slug', sanitize_text_field( $vendor_slug ), WC_PRODUCT_VENDORS_TAXONOMY );

			if ( null === $term || false === $term ) {
				return false;
			}

			$vendor_data = get_term_meta( $term->term_id, 'vendor_data', true );

			// if user is listed as one of the admins
			if ( ! empty( $vendor_data['admins'] ) && in_array( $user_id, array_filter( array_map( 'absint', explode( ',', $vendor_data['admins'] ) ) ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if vendor name is valid
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $vendor_name
	 * @return mixed
	 */
	public static function is_valid_vendor_name( $vendor_name = null ) {
		return term_exists( $vendor_name, WC_PRODUCT_VENDORS_TAXONOMY );
	}

	/**
	 * Checks if user can manage product
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $user_id
	 * @param int $product_id
	 * @return bool
	 */
	public static function can_user_manage_product( $user_id = null, $product_id = null ) {
		// if param not passed use current user
		if ( null === $user_id ) {
			$current_user = wp_get_current_user();

			$user_id = $current_user->ID;
		}

		// if param not passed use current post
		if ( null === $product_id ) {
			global $post;

			$product_id = is_object( $post ) ? $post->ID : null;
		}

		$product_terms = wp_get_object_terms( $product_id, WC_PRODUCT_VENDORS_TAXONOMY, array( 'fields' => 'ids' ) );

		if ( null === $product_terms || empty( $product_terms ) ) {
			return false;
		}

		if ( $product_terms[0] === self::get_logged_in_vendor( 'id' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the vendor slug/id of the current user
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $type the type to return
	 * @return mixed
	 */
	public static function get_logged_in_vendor( $type = 'slug' ) {
		$current_user = wp_get_current_user();

		$user_id = $current_user->ID;
		
		$vendor_slug = self::_get_vendor_login_cookie();
		
		// if cookie is set and user can manage this vendor
		if ( self::auth_vendor_user() ) {
			if ( 'slug' === $type ) {
				return $vendor_slug;

			} elseif ( 'id' === $type ) {
				$term = get_term_by( 'slug', $vendor_slug, WC_PRODUCT_VENDORS_TAXONOMY );

				if ( is_object( $term ) ) {
					return $term->term_id;
				}
			} elseif ( 'name' === $type ) {
				$term = get_term_by( 'slug', $vendor_slug, WC_PRODUCT_VENDORS_TAXONOMY );

				if ( is_object( $term ) ) {
					return $term->name;
				}				
			}
		}

		return false;
	}

	/**
	 * Checks if the page is a edit product page
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public static function is_edit_product_page() {
		global $pagenow, $typenow;

		if ( 'product' !== $typenow && 'post.php' !== $pagenow && empty( $_GET['action'] ) && $_GET['action'] !== 'edit' ) {
			return false;
		}

		return true;
	}

	/**
	 * Get all products that belong to a vendor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $term the slug of the term
	 * @return array $ids product ids
	 */
	public static function get_vendor_product_ids( $term = '' ) {
		$ids = array();

		if ( empty( $term ) ) {
			$term = self::get_logged_in_vendor();
		}
		
		if ( ! empty( $term ) ) {
			$args = array(
				'post_type' => 'product',
				'fields'    => 'ids',
				'tax_query' => array(
					array(
						'taxonomy' => WC_PRODUCT_VENDORS_TAXONOMY,
						'field'    => 'slug',
						'terms'    => $term,
					),
				),				
			);

			$query = new WP_Query( $args );

			wp_reset_postdata();

			$ids = $query->posts;
		}

		return $ids;
	}

	/**
	 * Get vendor rating based on average of product ratings
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $term the slug of the term
	 * @return string $avg_rating
	 */
	public static function get_vendor_rating( $term ) {
		$product_ids = self::get_vendor_product_ids( $term );

		$avg_rating = 0;

		$product_count = 0;

		foreach( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id );

			// check if product has rating
			if ( $product->get_rating_count() > 0 ) {
				$avg_rating += $product->get_average_rating();

				$product_count++;
			}
		}

		if ( $product_count > 0 ) {
			$avg_rating = number_format( $avg_rating / $product_count, 2 );
		}

		return $avg_rating;
	}

	/**
	 * Get vendor rating html
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $term the slug of the term
	 * @return mixed
	 */
	public static function get_vendor_rating_html( $term ) {
		$rating = self::get_vendor_rating( $term );

		$rating_html = '<small style="display:block;">' . esc_html__( 'Average Vendor Rating', 'woocommerce-product-vendors' ) . '</small>';

		$rating_html .= '<div class="wcpv-star-rating star-rating" title="' . sprintf( esc_attr__( 'Rated %s out of 5', 'woocommerce-product-vendors' ), $rating ) . '">';

		$rating_html .= '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . $rating . '</strong> ' . esc_html__( 'out of 5', 'woocommerce-product-vendors' ) . '</span>';

		$rating_html .= '</div>';

		return apply_filters( 'wcpv_vendor_get_rating_html', $rating_html, $rating );	
	}

	/**
	 * Formats the order and payout dates to be consistent
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $date
	 * @return string $date
	 */
	public static function format_date( $date ) {
		if ( '0000-00-00 00:00:00' !== $date ) {
			$date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $date ) );
		}

		return apply_filters( 'wcpv_date_format', $date );
	}

	/**
	 * Gets the vendor id from product
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $product_id
	 * @return int $vendor_id
	 */
	public static function get_vendor_id_from_product( $product_id = null ) {
		if ( null === $product_id ) {
			return null;
		}

		$term = wp_get_object_terms( $product_id, WC_PRODUCT_VENDORS_TAXONOMY, array( 'fields' => 'ids' ) );

		if ( is_wp_error( $term ) || empty( $term ) ) {
			return null;
		}

		return $term[0];
	}

	/**
	 * Checks if the given product is a vendor product
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $product_id
	 * @return object $term
	 */
	public static function is_vendor_product( $product_id = null ) {
		if ( null === $product_id ) {
			return false;
		}

		$term = wp_get_object_terms( $product_id, WC_PRODUCT_VENDORS_TAXONOMY, array( 'fields' => 'all' ) );

		if ( ! empty( $term ) ) {
			return $term;
		}

		return false;
	}

	/**
	 * Gets the list of vendors
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array objects $vendors
	 */
	public static function get_vendors() {
		return get_terms( WC_PRODUCT_VENDORS_TAXONOMY );
	}

	/**
	 * Gets the commission for a product
	 * Search order is: product variation level -> product parent level -> vendor level -> general vendors level
	 *	
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $product_id
	 * @param array $vendor_data
	 * @return array mixed
	 */
	public static function get_product_commission( $product_id, $vendor_data ) {
		$product = wc_get_product( $product_id );

		// check if product is a variation
		if ( 'variation' === $product->product_type || $product->is_type( 'variable' ) ) {
			// look for variation commission first
			$commission = get_post_meta( $product_id, '_wcpv_product_commission', true );

			if ( ! empty( $commission ) || '0' == $commission ) {
				return array( 'commission' => $commission, 'type' => $vendor_data['commission_type'] );
			
			// try to get the commission from the parent product
			} else {
				$parent_id = wp_get_post_parent_id( $product_id );

				$commission = get_post_meta( $parent_id, '_wcpv_product_commission', true );

				if ( ! empty( $commission ) || '0' == $commission ) {
					return array( 'commission' => $commission, 'type' => $vendor_data['commission_type'] );
				}
			}

		} else {
			$commission = get_post_meta( $product_id, '_wcpv_product_commission', true );

			if ( ! empty( $commission ) || '0' == $commission ) {
				return array( 'commission' => $commission, 'type' => $vendor_data['commission_type'] );
			}
		}

		// if no commission is set in variation or parent product level
		// check commission from vendor level
		if ( ! empty( $vendor_data['commission'] ) && '0' == $vendor_data['commission'] ) {
			return array( 'commission' => $vendor_data['commission'], 'type' => $vendor_data['commission_type'] );
		}

		// if no commission is set in vendor level check store default commission
		$commission      = get_option( 'wcpv_vendor_settings_default_commission' );
		$commission_type = get_option( 'wcpv_vendor_settings_default_commission_type' );

		if ( ! empty( $commission ) || '0' == $commission ) {
			return array( 'commission' => $commission, 'type' => $commission_type );
		}

		// else return no commission
		return 0;
	}

	/**
	 * Gets the list of vendor data from an order
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param object $order
	 * @return array $vendor_data
	 */
	public static function get_vendors_from_order( $order = null ) {
		global $wpdb;
		
		if ( null === $order ) {
			return null;
		}
		
		$vendor_data = array();

		$items = $order->get_items( 'line_item' );

		if ( ! empty( $items ) ) {
			
			// get all product ids
			foreach( $items as $item_id => $item ) {
				$sql = "SELECT `meta_value`";
				$sql .= " FROM {$wpdb->prefix}woocommerce_order_itemmeta";
				$sql .= " WHERE `order_item_id` = %d";
				$sql .= " AND `meta_key` = %s";

				// get the product id of the order item
				$product_id = $wpdb->get_var( $wpdb->prepare( $sql, $item_id, '_product_id' ) );

				// get vendor id from product id
				$vendor_id = self::get_vendor_id_from_product( $product_id );

				// get vendor data
				$vendor_data[ $vendor_id ] = self::get_vendor_data_by_id( $vendor_id );
			}
		}

		return $vendor_data;
	}

	/**
	 * Converts array to string comma delimited
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param array $query
	 * @return array $query
	 */
	public static function convert2string( $query ) {
		return implode( ',', $query );
	}

	/**
	 * Unserializes the variation attributes
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param array $query
	 * @return array $query
	 */
	public static function unserialize_attributes( $query ) {
		if ( ! empty( $query['variation_attributes'] ) ) {
			$attributes = maybe_unserialize( $query['variation_attributes'] );

			$attr_names = '';

			foreach( $attributes as $attr => $value ) {
				$attr_names .= $attr . ':' . $value . '  ';
			}

			$query['variation_attributes'] = $attr_names;
		}

		return $query;
	}

	/**
	 * Checks whether the commission table exists
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public static function commission_table_exists() {
		global $wpdb;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '" . WC_PRODUCT_VENDORS_COMMISSION_TABLE . "'" ) !== WC_PRODUCT_VENDORS_COMMISSION_TABLE ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the payout schedule frequency
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return string $frequency
	 */
	public static function payout_schedule_frequency() {
		$frequency = get_option( 'wcpv_vendor_settings_payout_schedule' );

		return $frequency;
	}

	/**
	 * Gets per product shipping matching rule
	 *
	 * @param mixed $product_id
	 * @param mixed $package
	 * @return false|null
	 */
	public static function get_pp_shipping_matching_rule( $product_id, $package, $standalone = true ) {
		global $wpdb;

		$product_id = apply_filters( 'wcpv_per_product_shipping_get_matching_rule_product_id', $product_id );

		$country  = $package['destination']['country'];
		$state    = $package['destination']['state'];
		$postcode = $package['destination']['postcode'];

		// Define valid postcodes
		$valid_postcodes = array( '', $postcode );

		// Work out possible valid wildcard postcodes
		$postcode_length   = strlen( $postcode );
		$wildcard_postcode = $postcode;

		for ( $i = 0; $i < $postcode_length; $i ++ ) {
			$wildcard_postcode = substr( $wildcard_postcode, 0, -1 );
			$valid_postcodes[] = $wildcard_postcode . '*';
		}

		// Rules array
		$rules = array();

		// Get rules matching product, country and state
	    $matching_rule = $wpdb->get_row(
	    	$wpdb->prepare(
	    		"
	    		SELECT * FROM " . WC_PRODUCT_VENDORS_PER_PRODUCT_SHIPPING_TABLE . "
	    		WHERE product_id = %d
	    		AND rule_country IN ( '', %s )
	    		AND rule_state IN ( '', %s )
	    		AND rule_postcode IN ( '" . implode( "','", $valid_postcodes ) . "' )
	    		ORDER BY rule_order
	    		LIMIT 1
	    		" , $product_id, strtoupper( $country ), strtoupper( $state )
	    	)
	    );

	    return $matching_rule;
	}

	/**
	 * Get fulfillment status of an order item
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $order_item_id
	 * @return string $status
	 */
	public static function get_fulfillment_status( $order_item_id ) {
		global $wpdb;

		if ( empty( $order_item_id ) ) {
			return;
		}

		$sql = "SELECT `meta_value` FROM {$wpdb->prefix}woocommerce_order_itemmeta";
		$sql .= " WHERE `order_item_id` = %d";
		$sql .= " AND `meta_key` = %s";

		$status = $wpdb->get_var( $wpdb->prepare( $sql, $order_item_id, '_fulfillment_status' ) );

		return $status;
	}

	/**
	 * Set fulfillment status of an order item
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $order_item_id
	 * @param string $status
	 * @return bool
	 */
	public static function set_fulfillment_status( $order_item_id, $status ) {
		global $wpdb;

		$sql = "UPDATE {$wpdb->prefix}woocommerce_order_itemmeta";
		$sql .= " SET `meta_value` = %s";
		$sql .= " WHERE `order_item_id` = %d AND `meta_key` = %s";

		$status = $wpdb->get_var( $wpdb->prepare( $sql, $status, $order_item_id, '_fulfillment_status' ) );

		return true;
	}

	/**
	 * Clears all reports transients
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public static function clear_reports_transients() {
		global $wpdb;

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%wcpv_reports%'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%wcpv_unfulfilled_products%'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%book_dr%'" );

		return true;
	}
}