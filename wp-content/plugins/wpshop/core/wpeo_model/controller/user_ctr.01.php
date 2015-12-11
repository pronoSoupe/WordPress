<?php


/**
 * CRUD Functions pour les utilisateurs
 * @author Jimmy Latour
 * @version 0.1
 */
class user_ctr_01 {
	protected $model_name = 'user_mdl_01';
	protected $meta_key = '_wpeo_user';
	protected $base = 'user';
	protected $version = '0.1';

	/**
	 * Instanciation du controleur principal pour les éléments de type "user" dans wordpress / Instanciate main controller for "user" elements' type into wordpress
	 */
	public function __construct() {
		/**	Ajout des routes personnalisées pour les éléments de type "user" / Add specific routes for "user" elements' type	*/
		add_filter( 'json_endpoints', array( &$this, 'callback_register_route' ) );
	}

	public function update( $data ) {
		$object = $data;

		if( is_array( $data ) ) {
			$object = new $this->model_name( $data, $this->meta_key );
		}

		wp_update_user( $object->do_wp_object() );

		/** On insert ou on met à jour les meta */
		if( !empty( $object->option ) ) {
			$this->save_meta_data( $object );
		}

		return $object;
	}

	public function create( $data ) {
		$object = $data;

		if( is_array( $data ) ) {
			$object = new $this->model_name( $data, $this->meta_key );
		}

		$object->id = wp_insert_user( $object->do_wp_object() );

			/** On insert ou on met à jour les meta */
		if( !empty( $object->option ) ) {
			$this->save_meta_data( $object );
		}

		return $object;
	}

	public function delete( $id ) {
		wp_delete_user( $id );
	}

	public function show( $id, $cropped = false ) {
 		$user = get_user_by( 'id', $id );

		$user = new $this->model_name( $user, $this->meta_key, $cropped );

		return $user;
	}

	public function index( $args_where = array( ), $cropped = false ) {
		$array_model = array();

		$array_user = get_users( $args_where );

		if( !empty( $array_user ) ) {
			foreach( $array_user as $key => $user ) {
				$array_model[$key] = new $this->model_name( $user, $this->meta_key, $cropped );
			}
		}

		return $array_model;
	}


	/**
	 * SETTER - Enregistrement des données associées a un objet avec rangement selon les types de champs / Save information associated to an object regarding field type
	 *
	 * @param Object $object Définition complète de l'objet à enregistrer / Complete object definition to save
	 */
	private function save_meta_data( $object ) {
		/** Read the object model option */
		$array_option = $object->get_array_option();

		foreach ( $object->option as $field_name => $field ) {
			if( !isset( $array_option[ $field_name ]['type'] ) || is_array( $array_option[ $field_name ]['type'] ) ) {
				foreach( $field as $sub_field_name => $sub_field ) {
					if ( isset( $array_option[ $sub_field_name ][ 'field_type' ] ) && ( 'meta' == $array_option[ $sub_field_name ][ 'field_type' ] ) ) {
						/** Update a single meta for specific field */
						update_user_meta( $object->id, $array_option[ $sub_field_name ][ 'field' ] , $object->option[ $field_name ][ $sub_field_name ] );
						unset( $object->option[ $field_name ][ $sub_field_name ] );
					}
				}
			}
			else if ( isset( $array_option[ $field_name ][ 'field_type' ] ) && ( 'meta' == $array_option[ $field_name ][ 'field_type' ] ) ) {
				/** Update a single meta for specific field */
				update_user_meta( $object->id, $array_option[ $field_name ][ 'field' ] , $object->option[ $field_name ] );
				unset( $object->option[ $field_name ] );
			}
		}

		/**	Update the main meta with json datas	*/
		update_user_meta( $object->id, $this->meta_key, json_encode( $object->option ) );
	}

	/**
	 * Ajoute les routes par défaut pour les éléments de type POST dans wordpress / Add default routes for POST element type into wordpress
	 *
	 * @param array $array_route Les routes existantes dans l'API REST de wordpress / Existing routes into Wordpress REST API
	 *
	 * @return array La liste des routes personnalisées ajoutées aux routes existantes / The personnalized routes added to existing
	 */
	public function callback_register_route( $array_route ) {
		/** Récupération de la liste complète des éléments / Get all existing elements */
		$array_route['/' . $this->version . '/get/' . $this->base ] = array(
				array( array( $this, 'index' ), WP_JSON_Server::READABLE | WP_JSON_Server::ACCEPT_JSON )
		);

		/** Récupération d'un élément donné / Get a given element */
		$array_route['/' . $this->version . '/get/' . $this->base . '/(?P<id>\d+)'] = array(
				array( array( $this, 'show' ), WP_JSON_Server::READABLE |  WP_JSON_Server::ACCEPT_JSON )
		);

		/** Mise à jour d'un élément / Update an element */
		$array_route['/' . $this->version . '/post/' . $this->base . ''] = array(
				array( array( $this, 'update' ), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
		);

		/** Suppression d'un élément / Delete an element */
		$array_route['/' . $this->version . '/delete/' . $this->base . '/(?P<id>\d+)'] = array(
				array( array( $this, 'delete' ), WP_JSON_Server::DELETABLE | WP_JSON_Server::ACCEPT_JSON ),
		);

		return $array_route;
	}


}
