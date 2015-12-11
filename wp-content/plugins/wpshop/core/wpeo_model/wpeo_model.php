<?php
/**
 * Plugin Name: Model Eoxia
 * Description:
 * Version: 0.1
 * Author: Eoxia <dev@eoxia.com>
 * Author URI: http://www.eoxia.com/
 * License: GPL2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Bootstrap file for plugin. Do main includes and create new instance for plugin components
 *
 * @author Eoxia <dev@eoxia.com>
 * @version 0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'constructor_model_ctr_01' ) ) {
/** Define */
	DEFINE( 'WPEO_MODEL_VERSION', 0.1 );
	DEFINE( 'WPEO_MODEL_DIR', basename( dirname( __FILE__ ) ) );
	DEFINE( 'WPEO_MODEL_PATH', str_replace( "\\", "/", plugin_dir_path( __FILE__ ) ) );
	DEFINE( 'WPEO_MODEL_URL', str_replace( str_replace( "\\", "/", ABSPATH), site_url() . '/', WPEO_MODEL_PATH ) );

/** Require */
	require_once( WPEO_MODEL_PATH . '/core/taxonomy_meta.controller.01.php' );

	require_once( WPEO_MODEL_PATH . '/controller/constructor_model_ctr.01.php' );

	require_once( WPEO_MODEL_PATH . '/model/post_mdl.01.php' );
	require_once( WPEO_MODEL_PATH . '/model/comment_mdl.01.php' );
	require_once( WPEO_MODEL_PATH . '/model/user_mdl.01.php' );
	require_once( WPEO_MODEL_PATH . '/model/term_mdl.01.php' );

	require_once( WPEO_MODEL_PATH . '/controller/post_ctr.01.php' );
	require_once( WPEO_MODEL_PATH . '/controller/comment_ctr.01.php' );
	require_once( WPEO_MODEL_PATH . '/controller/user_ctr.01.php' );
	require_once( WPEO_MODEL_PATH . '/controller/term_ctr.01.php' );
}


?>