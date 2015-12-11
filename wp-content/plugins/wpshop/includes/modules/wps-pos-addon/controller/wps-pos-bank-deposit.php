<?php
class wps_pos_addon_bank_deposit {
	public function __construct() {
		/**	Call metaboxes	*/
		add_action( 'admin_init', array( $this, 'metaboxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'vars_js' ), 11 );
	}
	public function metaboxes() {
		add_meta_box( 'wpspos-bank-deposit-metabox', __( 'Create your bank deposit', 'wps-pos-i18n' ), array( $this, 'metabox' ), 'wpspos-bank-deposit', 'wpspos-bank-deposit-left' );
	}
	public function metabox() {
		require( wpshop_tools::get_template_part( WPSPOS_DIR, WPSPOS_TEMPLATES_MAIN_DIR, 'backend/bank_deposit', 'metabox', 'bank_deposit' ) );
	}
	/**
	 * Order - Sample :
	 * 	WP_Post Object
	 * 	(
	 * 		[ID] => 259
	 * 		[post_author] => 1
	 * 		[post_date] => 2015-11-04 16:44:51
	 * 		[post_date_gmt] => 2015-11-04 15:44:51
	 * 		[post_content] =>
	 * 		[post_title] => Commande - 04 nov 2015, 16:44:51
	 * 		[post_excerpt] =>
	 * 		[post_status] => publish
	 * 		[comment_status] => closed
	 * 		[ping_status] => closed
	 * 		[post_password] =>
	 * 		[post_name] => commande-04-nov-2015-164451
	 * 		[to_ping] =>
	 * 		[pinged] =>
	 * 		[post_modified] => 2015-11-04 16:48:25
	 * 		[post_modified_gmt] => 2015-11-04 15:48:25
	 *		[post_content_filtered] =>
	 *		[post_parent] => 1
	 *		[guid] => http://localhost/tests-wpshop1.4.0.3/?wpshop_shop_order=commande-04-nov-2015-164451
	 *		[menu_order] => 0
	 *		[post_type] => wpshop_shop_order
	 *		[post_mime_type] =>
	 *		[comment_count] => 0
	 *		[filter] => raw
	 *		[_order_postmeta] => Array
	 *			(
	 *				[order_key] => OR00057
	 *				[customer_id] => 1
	 *				[order_status] => completed
	 *				[order_date] => 2015-11-04 16:44:51
	 *				[order_shipping_date] =>
	 *				[order_invoice_ref] => FA00058
	 *				[order_currency] => euro
	 *				[order_payment] => Array
	 *					(
	 *						[customer_choice] => Array
	 *							(
	 *								[method] => checks
	 *							)
	 *						[received] => Array
	 *							(
	 *								[0] => Array
	 *									(
	 *										[method] => checks
	 *										[waited_amount] => 120.00
	 *										[status] => payment_received
	 *										[author] => 1
	 *										[payment_reference] =>
	 *										[date] => 2015-11-04 16:48:25
	 *										[received_amount] => 120.00
	 *										[comment] =>
	 *										[invoice_ref] => FA00058
	 *									)
	 *							)
	 *						[shipping_method] => default_shipping_mode
	 *					)
	 *				[cart] => Array
	 *					(
	 *						[cart_type] => normal
	 *						[order_items] => Array
	 *							(
	 *								[250] => Array
	 *									(
	 *										[item_id] => 250
	 *										[item_ref] => PDCT00000251
	 *										[item_name] => Pizza
	 *										[item_qty] => 1
	 *										[item_pu_ht] => 12.50000
	 *										[item_pu_ttc] => 15.00000
	 *										[item_ecotaxe_ht] => 0
	 *										[item_ecotaxe_tva] => 19.6
	 *										[item_ecotaxe_ttc] => 0
	 *										[item_discount_type] => 0
	 *										[item_discount_value] => 0
	 *										[item_discount_amount] => 0
	 *										[item_tva_rate] => 20
	 *										[item_tva_amount] => 2.50000
	 *										[item_total_ht] => 12.5
	 *										[item_tva_total_amount] => 2.5
	 *										[item_total_ttc] => 15
	 *										[item_meta] => Array
	 *											(
	 *												[attribute_visible] => Array
	 *													(
	 *														[product_reference] => PDCT00000251
	 *														[product_height] => 0.00000
	 *														[product_width] => 0.00000
	 *														[suppl_ments_divers__3674] => Oignons
	 *														[product_weight] => 0
	 *														[suppl_ments_charcuterie_ou_fromage_] => 0
	 *													)
	 *												[attribute_visible_listing] => Array
	 *													(
	 *														[product_reference] => PDCT00000251
	 *														[product_height] => 0.00000
	 *														[product_width] => 0.00000
	 *														[suppl_ments_divers__3674] => Oignons
	 *														[product_weight] => 0
	 *														[suppl_ments_charcuterie_ou_fromage_] => 0
	 *													)
	 *											)
	 *									)
	 *							)
	 *						[order_total_ht] => 12.5
	 *						[order_total_ttc] => 15
	 *						[order_shipping_cost] => 0
	 *						[order_grand_total] => 15
	 *						[order_amount_to_pay_now] => 15
	 *						[order_grand_total_before_discount] => 15
	 *					)
	 *				[cart_type] => normal
	 *				[order_items] => Array
	 *					(
	 *						[55__222] => Array
	 *							(
	 *								[item_id] => 55
	 *								[item_ref] => PDCT0000072
	 *								[item_name] => Salut
	 *								[item_qty] => 1
	 *								[item_pu_ht] => 46.66667
	 *								[item_pu_ttc] => 56.00000
	 *								[item_ecotaxe_ht] => 0
	 *								[item_ecotaxe_tva] => 19.6
	 *								[item_ecotaxe_ttc] => 0
	 *								[item_discount_type] => 0
	 *								[item_discount_value] => 0
	 *								[item_discount_amount] => 0
	 *								[item_tva_rate] => 20
	 *								[item_tva_amount] => 9.33333
	 *								[item_total_ht] => 46.666666666667
	 *								[item_tva_total_amount] => 9.3333333333333
	 *								[item_total_ttc] => 56
	 *								[item_meta] => Array
	 *									(
	 *										[attribute_visible] => Array
	 *											(
	 *												[product_reference] => PDCT0000072
	 *												[product_height] => 0.00000
	 *												[product_width] => 0.00000
	 *												[suppl_ments_divers__3674] => 0
	 *												[product_weight] => 0
	 *												[suppl_ments_charcuterie_ou_fromage_] => 0
	 *											)
	 *										[attribute_visible_listing] => Array
	 *											(
	 *												[product_reference] => PDCT0000072
	 *												[product_height] => 0.00000
	 *												[product_width] => 0.00000
	 *												[suppl_ments_divers__3674] => 0
	 *												[product_weight] => 0
	 *												[suppl_ments_charcuterie_ou_fromage_] => 0
	 *											)
	 *										[is_downloadable_] => Array
	 *											(
	 *												[file_url] => http://localhost/tests-wpshop1.4.0.3/wp-content/uploads//2015-11-04-164321__wpshop_discounts_category.zip
	 *											)
	 *										[variations] => Array
	 *											(
	 *												[222] => Array
	 *													(
	 *														[product_id] => 222
	 *														[post_name] => produit-avec-option-55-salut-color-6
	 *														[product_name] => Produit avec option 55 - Salut color 6
	 *														[post_title] => Produit avec option 55 - Salut color 6
	 *														[product_author_id] => 1
	 *														[product_date] => 2015-09-30 13:09:27
	 *														[product_content] =>
	 *														[product_excerpt] =>
	 *														[product_meta_attribute_set_id] => 1
	 *														[product_attribute_set_id] => 0
	 *														[product_reference] => 0
	 *														[product_height] => 0
	 *														[price_ht] => 0.00000
	 *														[declare_new] => 0
	 *														[is_downloadable_] => 0
	 *														[barcode] => 0
	 *														[cost_of_postage] => 0
	 *														[product_width] => 0
	 *														[tva] => 0.00000
	 *														[product_price] => 0.00000
	 *														[special_from] => 0000-00-00 00:00:00
	 *														[color] => Rouge
	 *														[tx_tva] => 20
	 *														[highlight_product] => 0
	 *														[special_to] => 0000-00-00 00:00:00
	 *														[manage_stock] => 0
	 *														[product_stock] => 0
	 *														[suppl_ments_divers__3674] => 0
	 *														[product_weight] => 0
	 *														[discount_amount] => 0.00000
	 *														[suppl_ments_charcuterie_ou_fromage_] => 0
	 *														[discount_rate] => 0.00000
	 *														[special_price] => 0.00000
	 *														[item_meta] => Array
	 *															(
	 *																[attribute_visible] => Array
	 *																	(
	 *																		[product_reference] => 0
	 *																		[product_height] => 0
	 *																		[product_width] => 0
	 *																		[suppl_ments_divers__3674] => 0
	 *																		[product_weight] => 0
	 *																		[suppl_ments_charcuterie_ou_fromage_] => 0
	 *																	)
	 *																[attribute_visible_listing] => Array
	 *																	(
	 *																		[product_reference] => 0
	 *																		[product_height] => 0
	 *																		[product_width] => 0
	 *																		[suppl_ments_divers__3674] => 0
	 *																		[product_weight] => 0
	 *																		[suppl_ments_charcuterie_ou_fromage_] => 0
	 *																	)
	 *																[variation_definition] => Array
	 *																	(
	 *																		[color] => Array
	 *																			(
	 *																				[UNSTYLED_VALUE] => Rouge
	 *																				[NAME] => Couleur
	 *																				[VALUE] => Rouge
	 *																				[ID] => 6
	 *																			)
	 *																	)
	 *															)
	 *														[custom_display] =>
	 *													)
	 *											)
	 *										[free_variation] => Array
	 *											(
	 *												[suppl_ments_divers__3674] => 253
	 *											)
	 *										[variation_definition] => Array
	 *											(
	 *												[color] => Array
	 *													(
	 *														[NAME] => Couleur
	 *														[UNSTYLED_VALUE] => Rouge
	 *														[VALUE] => Rouge
	 *													)
	 *											)
	 *									)
	 *								[item_is_downloadable_] => oui
	 *							)
	 *					)
	 *				[order_total_ht] => 46.666666666667
	 *				[order_total_ttc] => 120.00
	 *				[order_shipping_cost] => 64
	 *				[order_grand_total] => 120.00
	 *				[order_amount_to_pay_now] => 0.00
	 *				[order_grand_total_before_discount] => 120
	 *				[order_invoice_date] => 2015-11-04 16:48:25
	 *			)
	 *	)
	 */
	public function get_payments() {
		$args = array(
				'posts_per_page' 	=> -1,
				'post_type'			=> WPSHOP_NEWTYPE_IDENTIFIER_ORDER,
				'post_status' 		=> 'publish',
				'meta_key'			=> '_order_postmeta',
				'meta_value'		=> serialize( 'received' ) . serialize( array() ),
				'meta_compare'		=> 'NOT LIKE',
		);
		$query = new WP_Query( $args );
		
		$orders = $query->posts;
		$payments = array();
		
		foreach( $orders as $order ) {
			$order->_order_postmeta = get_post_meta( $order->ID, '_order_postmeta', true );
			foreach( $order->_order_postmeta['order_payment']['received'] as $payment_received ) {
				if( isset( $payment_received['status'] ) && $payment_received['status'] == 'payment_received' ) {
					$payments[] = '';
					end( $payments );
					$id = key( $payments );
					$payments[$id] = $this->row_model( $id, isset( $order->_order_postmeta['order_key'] ) ? $order->_order_postmeta['order_key'] : $order->_order_postmeta['order_temporary_key'], $payment_received['date'], isset( $order->_order_postmeta['cart']['order_items'] ) ? $order->_order_postmeta['cart']['order_items'] : array(), $payment_received['received_amount'], $payment_received['method'] );
				}
			}
		}
		
		return $payments;
	}
	public function row_model( $id, $order_key, $date, $products, $amount, $method ) {
		$products_simplified = array();
		if( !empty( $products ) && is_array( $products ) ) {
			foreach( $products as $product ) {
				if( isset( $product['item_meta']['variations'] ) ) {
					reset( $product['item_meta']['variations'] );
					$id_variation = key( $product['item_meta']['variations'] );
					$products_simplified[] = $product['item_meta']['variations'][$id_variation]['product_name'];
				} else {
					$products_simplified[] = $product['item_name'];
				}
			}			
		}
		return array( 'id' => $id, 'order_key' => $order_key, 'date' => $date, 'products' => $products_simplified, 'amount' => $amount, 'method' => $method );
	}
	public function vars_js() {
		wp_localize_script( 'wpspos-backend-bank-deposit-js', 'payments', $this->get_payments() );
	}
}