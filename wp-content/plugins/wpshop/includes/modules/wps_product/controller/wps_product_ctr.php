<?php
class wps_product_ctr {

	function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'callback_admin_enqueue_scripts' ) );
		
		add_shortcode( 'wps_product_caracteristics', array( $this, 'display_product_caracteristics_tab' ) );
		add_shortcode( 'wps_product_discount_chip', array( $this, 'display_discount_chip' ) );
	}

	/**
	 * CORE - Install all extra-modules in "Modules" folder
	 */
	function install_modules() {
		/**	Define the directory containing all exrta-modules for current plugin	*/
		$module_folder = WPS_PRODUCT_PATH . '/modules/';

		/**	Check if the defined directory exists for reading and including the different modules	*/
		if( is_dir( $module_folder ) ) {
			$parent_folder_content = scandir( $module_folder );
			foreach ( $parent_folder_content as $folder ) {
				if ( $folder && substr( $folder, 0, 1) != '.' ) {
					$child_folder_content = scandir( $module_folder . $folder );
					if ( file_exists( $module_folder . $folder . '/' . $folder . '.php') ) {
						$f =  $module_folder . $folder . '/' . $folder . '.php';
						include( $f );
					}
				}
			}
		}
	}

	public function callback_admin_enqueue_scripts() {
		wp_enqueue_script( 'wps_product_js', WPS_PRODUCT_URL . '/asset/js/backend.js', array( "jquery", "jquery-form" ), WPS_PRODUCT_VERSION );
	}
	
	/**
	 * Display Product's caracteristics tab in complete product sheet
	 * @param array $args
	 * @return string
	 */
	function display_product_caracteristics_tab( $args ) {
		$output = '';
		if( !empty($args) && !empty($args['pid']) ) {
			$wps_product_mdl = new wps_product_mdl();
			$product_atts_def = $wps_product_mdl->get_product_atts_def( $args['pid'] );
			if( !empty($product_atts_def) ) {
				ob_start();
				require( wpshop_tools::get_template_part( WPS_PRODUCT_DIR, WPS_PRODUCT_TEMPLATES_MAIN_DIR, "frontend", "product_caracteristics_tab") );
				$output = ob_get_contents();
				ob_end_clean();
			}
		}
		return $output;
	}

	/**
	 * Display Discount Chip
	 * @param array $args
	 * @return string
	 */
	function display_discount_chip( $args ) {
		$output = '';
		if( !empty($args) && !empty($args['pid']) ) {
			$wps_price = new wpshop_prices();
			$discount_data = wpshop_prices::check_discount_for_product( $args['pid'] );
			if( !empty($discount_data) ) {
				ob_start();
				require( wpshop_tools::get_template_part( WPS_PRODUCT_DIR, WPS_PRODUCT_TEMPLATES_MAIN_DIR, "frontend", "product_discount_chip") );
				$output = ob_get_contents();
				ob_end_clean();
			}
		}
		return $output;
	}

	/**
	 * Check if there is enough stock for asked product if manage stock option is checked
	 *
	 * @param integer $product_id The product we have to check the stock for
	 * @param unknown_type $cart_asked_quantity The quantity the end user want to add to the cart
	 *
	 * @return boolean|string  If there is enough sotck or if the option for managing stock is set to false return OK (true) In the other case return an alert message for the user
	 */
	function check_stock($product_id, $cart_asked_quantity, $combined_variation_id = '') {
		// Checking if combined variation ID exist and it is a simple option
		if( !empty($combined_variation_id) && ( strpos($combined_variation_id, '__') !== false ) ) {
			$var_id = explode( '__', $combined_variation_id);
			$combined_variation_id = $var_id[1];
		}


		if ( !empty($combined_variation_id) ) {

			$variation_metadata = get_post_meta( $combined_variation_id, '_wpshop_product_metadata', true );
			if ( isset($variation_metadata['product_stock']) ) {
				$product_id = $combined_variation_id;
			}
		}
		$product_data = wpshop_products::get_product_data($product_id, false, '"publish", "free_product"');
		
		if(!empty($product_data)) {
			$manage_stock = !empty($product_data['manage_stock']) ? $product_data['manage_stock'] : '';

			$product_post_type = get_post_type( $product_id );

			if ( $product_post_type == WPSHOP_NEWTYPE_IDENTIFIER_PRODUCT_VARIATION ) {
				$parent_def = wpshop_products::get_parent_variation( $product_id );
				if ( !empty($parent_def) && !empty($parent_def['parent_post']) ) {
					$parent_post = $parent_def['parent_post'];
					$parent_product_data = wpshop_products::get_product_data($parent_post->ID);
					$manage_stock = !empty($parent_product_data['manage_stock']) ? $parent_product_data['manage_stock'] : '';
				}
			}
			$manage_stock_is_activated = (!empty($manage_stock) && ( strtolower(__($manage_stock, 'wpshop')) == strtolower(__('Yes', 'wpshop')) )) ? true : false;
			$the_qty_is_in_stock = ( !empty($product_data['product_stock']) && $product_data['product_stock'] >= $cart_asked_quantity ) ? true : false ;

			if (($manage_stock_is_activated && $the_qty_is_in_stock) OR !$manage_stock_is_activated) {
				return true;
			}
			else {
				return __('You cannot add that amount to the cart since there is not enough stock.', 'wpshop');
			}
		}
		return false;
	}

	public static function get_inconsistent_product() {
		$price_piloting_option = get_option( 'wpshop_shop_price_piloting' );
		
		$entity_id 		= wpshop_entities::get_entity_identifier_from_code( 'wpshop_product' );
		
		$attribute_def	= wpshop_attributes::getElement( ( $price_piloting_option == 'TTC' ) ? 'product_price' : 'price_ht', "'valid'", 'code' );
		$attribute_id	= $attribute_def->id;
		
		global $wpdb;
		
		$wpdb->query('SET SESSION group_concat_max_len = 10000');
		
		$query			= "
		SELECT 		post.ID, post.post_title, attr_val_dec.value as price_attribute, GROUP_CONCAT(postmeta.meta_key, '&sep&', postmeta.meta_value, '&&' ORDER BY postmeta.meta_key) as price
			FROM 		$wpdb->posts as post
		JOIN		$wpdb->postmeta as postmeta
			ON			post.ID=postmeta.post_id
		JOIN		" . WPSHOP_DBT_ATTRIBUTE_VALUES_DECIMAL . " attr_val_dec
			ON			post.ID=attr_val_dec.entity_id
		WHERE		post.post_type='wpshop_product'
			AND			attr_val_dec.entity_type_id=%d
			AND			attr_val_dec.attribute_id=%d
			AND			postmeta.meta_key IN( '_product_price', '_wps_price_infos', '_wpshop_displayed_price', '_wpshop_product_metadata' )
		GROUP BY	post.ID";
	
		$list_product	= $wpdb->get_results( $wpdb->prepare( $query, array( $entity_id, $attribute_id ) ) );

		if( !empty( $list_product ) ) {
			foreach( $list_product as $key_product => &$product ) {
				$product->price = explode('&&,', $product->price);
				if(!empty($product->price) && is_array($product->price)) {
					$array_price = array();
					foreach($product->price as &$price) {
						if(strpos( $price, '&&' ))
							$price = substr($price, 0, -2);

						$tmp_price = explode('&sep&', $price);
						$key = $tmp_price[0];
						$price = maybe_unserialize($tmp_price[1]);
			
						/** _wpshop_product_metadata */
						if( $key == '_wpshop_product_metadata' ) {
							$array_price[$key] =  ( $price_piloting_option == 'TTC' ) ? $price['product_price'] : $price['price_ht'];
						}

						/** _wps_price_infos */
						if( $key == '_wps_price_infos' ) {
							$array_price[$key] = !empty( $price['PRODUCT_PRICE'] ) ? $price['PRODUCT_PRICE'] : '-';
						}

						if( $key == '_product_price' ) {
							$array_price[$key] = ( $price_piloting_option == 'TTC' ) ? $price : '-';
						}

						if ( $key == '_wpshop_displayed_price' ) {
							$array_price[$key] = $price;
						}
						unset($price);
					}

					$array_meta_list = array( '_product_price', '_wps_price_infos', '_wpshop_displayed_price', '_wpshop_product_metadata', );
	
					foreach( $array_meta_list as $meta_list ) {
						if( !array_key_exists( $meta_list, $array_price ) ) {
							$array_price[$meta_list] = 0;
						}
					}
			
					$product->price = $array_price;
					if( $product->price_attribute === $product->price['_wpshop_product_metadata'] ) {
						unset($list_product[$key_product]);
					}
						
				}
			}
			unset($product);
		}
		
		return $list_product;
	}
	
	/**
	 * Read the array_data table and call update_the_attribute_for_product for update the attribute value for this product
	 * 
	 * @param int $product_id The product ID
	 * @param array $array_data The array data [integer][barcode] = 0111100001
	 */
	public function update_attributes_for_product($product_id, $array_data) {
		if(!empty($array_data)) {
			foreach($array_data as $type => $array) {
				foreach($array as $name_attribute => $value_attribute) {
					$this->update_the_attribute_for_product($product_id, $type, $name_attribute, $value_attribute);
				}
			}
		}
	}
	
	/**
	 * Insert ou met à jour la value dans la table correspondante selon le product_id et le nom de l'attribut
	 * 
	 * @param int $product_id L'id du produit
	 * @param string $type Peut être varchar, integer, text, options, decimal, datetime
	 * @param string $attribute_name Le code d'un attribut
	 * @param string $attribute_value La valeur à mêttre à jour
	 */
	public function update_the_attribute_for_product($product_id, $type, $name_attribute, $value_attribute) {
		global $wpdb;
		
		/** On récupère l'id de l'entity produit */
 		$entity_type_id = wpshop_entities::get_entity_identifier_from_code(WPSHOP_NEWTYPE_IDENTIFIER_PRODUCT);
		
		$attribute_id = $wpdb->get_var($wpdb->prepare('SELECT id FROM ' . WPSHOP_DBT_ATTRIBUTE . ' WHERE code="%s"', array($name_attribute)));

		/** On vérifie s'il existe si c'est le cas, on update sinon on insert */
		if(count($wpdb->get_row($wpdb->prepare('SELECT value_id FROM ' . WPSHOP_DBT_ATTRIBUTE . '_value_' . $type . ' WHERE entity_id=%d AND attribute_id IN(SELECT id FROM ' . WPSHOP_DBT_ATTRIBUTE . ' WHERE code="%s")', array($product_id, $name_attribute)))) > 0) {
			$wpdb->query(
				$wpdb->prepare('UPDATE ' . WPSHOP_DBT_ATTRIBUTE . '_value_' . $type . ' SET value="%s" WHERE entity_id=%d AND attribute_id=%d', 
					array($value_attribute, $product_id, $attribute_id)
				)
			);
		}
		else {
			/** Insert avec toutes les informations requise */
			$wpdb->insert(WPSHOP_DBT_ATTRIBUTE . '_value_' . $type, array(
					'attribute_id' 			=> $attribute_id,
					'entity_id'	 			=> $product_id,
					'entity_type_id' 		=> $entity_type_id,
					'creation_date_value' 	=> current_time('mysql'),
					'value' 				=> $value_attribute,
				)
			);
		}
	}
}