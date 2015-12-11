<?php

class wpeo_taxonomy_meta {
	private static $oInstance = null;
	private $sMetaType;

	/**
	 * __consctruct
	 *
	 * Construct of class
	 *
	 * @param void
	 * @return void
	 */
	public function __construct() {
		$this->set_meta_type("eo_term_taxonomy");
	}

	/**
	 * Create default contents and elements
	 */
	public static function activation() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/**     Create a taxonomy term table for saving custom component on taxonomies  */
		$wpeoads_term_meta = $wpdb->prefix . self::get_instance()->get_meta_type() . "meta";
		$query =
		"CREATE TABLE {$wpeoads_term_meta} (
			term_taxonomy_meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			term_taxonomy_id bigint(20) unsigned NOT NULL DEFAULT '0',
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext,
		PRIMARY KEY (term_taxonomy_meta_id),
		KEY term_taxonomy_id (term_taxonomy_id),
		KEY meta_key (meta_key)
		) ENGINE=MyISAM;";
		dbDelta( $query );
	}

	/**
	 * get_instance
	 *
	 * Create the unique instance of class if not exist and return him
	 *
	 * @param void
	 * @return taxonomymeta
	 */
	public static function get_instance() {
		if(self::$oInstance == null)
			self::$oInstance = new wpeo_taxonomy_meta();

		return self::$oInstance;
	}

	/**
	 * set_meta_type
	 *
	 * Set the name of meta
	 *
	 * @param string $sMetaType name of meta
	 * @return void
	 */
	public function set_meta_type($sMetaType) {
		$this->sMetaType = $sMetaType;
	}

	/**
	 * get_meta_type
	 *
	 * get the name of meta
	 *
	 * @param void
	 * @return string $sMetaType name of meta
	 */
	public function get_meta_type() {
		return $this->sMetaType;
	}

	/**
	 * add_term_taxonomy_meta
	 *
	 * Add meta value in term_taxonomy_meta table in database
	 *
	 * @param int $term_taxonomy_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param boolean $unique
	 * @return int
	 */
	public static function add_term_taxonomy_meta($term_taxonomy_id, $meta_key, $meta_value, $unique = false) {
		global $wpdb;

		$sTable =		$wpdb->prefix . self::get_instance()->get_meta_type() . "meta";

		// Securize
		$meta_key = 	wp_unslash($meta_key);
		$meta_value = 	wp_unslash($meta_value);
		$meta_value = 	sanitize_meta($meta_key, $meta_value,  self::get_instance()->get_meta_type());

		// Check if not exist
		if( $unique && $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM $sTable WHERE meta_key=%s AND term_taxonomy_id=%d",
			$meta_key, $term_taxonomy_id)))
			return false;

		$meta_value = maybe_serialize( $meta_value );

		$result = $wpdb->insert($sTable, array(
			"term_taxonomy_id" => 		$term_taxonomy_id,
			"meta_key" =>	$meta_key,
			"meta_value" =>	$meta_value
		));

		if (!$result)
			return false;

		$mid = (int)$wpdb->insert_id;

		return $mid;
	}

	/**
	 * update_term_taxonomy_meta
	 *
	 * Update meta_value term taxonomy meta by term_taxonomy_id and $meta_key
	 * Create news if meta_key doesn't exist in table
	 *
	 * @param int $term_taxonomy_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @return boolean
	 */
	public static function update_term_taxonomy_meta($term_taxonomy_id, $meta_key, $meta_value) {
		global $wpdb;

		$sTable =		$wpdb->prefix . self::get_instance()->get_meta_type() . "meta";

		// Securize
		$meta_key = 	wp_unslash($meta_key);
		$meta_value = 	wp_unslash($meta_value);
		$meta_value = 	sanitize_meta($meta_key, $meta_value,  self::get_instance()->get_meta_type());

		// If not exist, create taxonomy_meta
		if (! $meta_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT term_taxonomy_id FROM $sTable WHERE meta_key=%s AND term_taxonomy_id = %d",
				$meta_key, $term_taxonomy_id)))
				return self::add_term_taxonomy_meta($term_taxonomy_id, $meta_key, $meta_value);

		$meta_value = maybe_serialize($meta_value);

		$data  = compact("meta_value");
		$where = array( 'term_taxonomy_id' => $term_taxonomy_id, "meta_key" => $meta_key);

		$result = $wpdb->update($sTable, $data, $where);

		if(!$result)
			return false;

		return true;
	}

	/**
	 * get_term_taxonomy_meta
	 *
	 * get meta value in term_taxonomy_meta by term_taxonomy_id and falcultative meta_key
	 *
	 * @param int $term_taxonomy_id
	 * @param string $meta_key falcultative
	 * @return boolean|stdClass
	 */
	public static function get_term_taxonomy_meta($term_taxonomy_id, $meta_key = "") {
		global $wpdb;

		$sTable =		$wpdb->prefix . self::get_instance()->get_meta_type() . "meta";

		// Securize
		$meta_key = 	wp_unslash($meta_key);

		$sEndQuery =	"";

		if($meta_key)
			$sEndQuery = "AND meta_key=%s";

		$sQuery = $wpdb->prepare("SELECT meta_key, meta_value FROM {$sTable}
						WHERE term_taxonomy_id=%d " . $sEndQuery, array($term_taxonomy_id, $meta_key));
		$metas =	$wpdb->get_results($sQuery);

		if(!$metas)
			return false;

		if ( empty( $meta_key ) ) {
			$metas_from_database = $metas;
			unset( $metas );
			foreach ( $metas_from_database as $meta ) {
				$metas[ $meta->meta_key ][] = $meta->meta_value;
			}
		}

		return $metas;
	}

	/**
	 * delete_term_taxonomy_meta
	 *
	 * Delete term_taxonomy_meta by $term_taxonomy_id and falcultative meta_key
	 *
	 * @param int $term_taxonomy_id
	 * @param string $meta_key falcultative
	 * @return boolean
	 */
	public static function delete_term_taxonomy_meta($term_taxonomy_id, $meta_key = "") {
		global $wpdb;

		$sTable =		$wpdb->prefix . self::get_instance()->get_meta_type() . "meta";

		// Securize
		$meta_key = 	wp_unslash($meta_key);

		$sEndQuery =	"";

		if($meta_key)
			$sEndQuery = "AND meta_key=%s";

		$sQuery			= "DELETE FROM $sTable WHERE term_taxonomy_id=%d $sEndQuery";

		$result =		$wpdb->query($wpdb->prepare($sQuery, array($term_taxonomy_id, $meta_key)));

		if(!$result)
			return false;

		return true;
	}

}
