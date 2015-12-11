<?php


/**
 * CRUD Functions pour les posts
 * @author Jimmy Latour
 * @version 0.1
 */
class post_ctr_01 {
	protected $model_name = 'post_mdl_01';
	protected $post_type = 'post';
	protected $meta_key = '_wpeo_post';
	protected $base = 'post';
	protected $version = '0.1';

	/**
	 * Instanciation du controleur principal pour les éléments de type "post" dans wordpress / Instanciate main controller for "post" elements' type into wordpress
	 */
	public function __construct() {
		/**	Ajout des routes personnalisées pour les éléments de type "post" / Add specific routes for "post" elements' type	*/
		add_filter( 'json_endpoints', array( &$this, 'callback_register_route' ) );
	}

	public function update( $data ) {
		$object = $data;

		if( is_array( $data ) ) {
			$object = new $this->model_name( $data, $this->meta_key );
			$object->type = $this->post_type;
		}

		wp_update_post( $object->do_wp_object() );

		/** On insert ou on met à jour les meta */
		if( !empty( $object->option ) ) {
			$object->save_meta_data( $object, 'update_post_meta', $this->meta_key );
		}

		/** On insert les terms */
		if ( !empty( $object->taxonomy ) ) {
			foreach( $object->taxonomy as $taxonomy => $array_value ) {
				if( !empty( $taxonomy ) && !empty( $array_value ) ) {
					wp_set_object_terms( $object->id, $array_value, $taxonomy );
				}
				else if( !empty( $taxonomy ) && empty( $array_value ) ) {
					wp_set_object_terms( $object->id, '', $taxonomy );
				}
			}
		}

		return $object;
	}

	public function create( $data ) {
		$object = $data;

		if( is_array( $data ) ) {
			$object = new $this->model_name( $data, $this->meta_key );
			$object->type = $this->post_type;
		}

		$object->id = wp_insert_post( $object->do_wp_object() );

		/** On insert ou on met à jour les meta */
		if( !empty( $object->option ) ) {
			$object->save_meta_data( $object, 'update_post_meta', $this->meta_key );
		}

		/** On insert les terms */
		if ( !empty( $object->taxonomy ) ) {
			foreach( $object->taxonomy as $taxonomy => $array_value ) {
				if( !empty( $taxonomy ) && !empty( $array_value ) ) {
					wp_set_object_terms( $object->id, $array_value, $taxonomy );
				}
			}
		}

		return $object;
	}

	public function delete($id) {
		wp_trash_post($id);

		$object = $this->show($id);

	}

	/**
	 * Récupère un élément et le retourne construit selon le modèle / Get an element and build datas with the model
	 *
	 * @param integer $id L'identifiant de l'élément que l'on souhaite avoir / Element's identifier we want to get
	 * @param boolean $cropped Optionnal La fonction doit elle retourner le modèle entier ou uniquement les données principales / The function must return the entire model or only main datas
	 *
	 * @return Object L'objet construit selon le modèle définit / Builded object by model structure
	 */
	public function show( $id, $cropped = false ) {
 		$post = get_post( $id );
		$post = new $this->model_name( $post, $this->meta_key, $cropped );

		return $post;
	}

	public function index( $args_where = array( 'parent_id' => 0 ), $cropped = false ) {
		$array_model = array();

		$args = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> $this->post_type,
			'posts_per_page' 	=> -1,
		);

		$args = array_merge( $args, $args_where );
		$array_post = get_posts( $args );

		if( !empty( $array_post ) ) {
			foreach( $array_post as $key => $post ) {
				$array_model[$key] = new $this->model_name( $post, $this->meta_key, $cropped );
			}
		}

		return $array_model;
	}


	/**
	 * GETTER - Récupère tous les terms d'un post depuis sa définition dans le modèle / Get all term in post by the model definition
	 *
	 * @param mixed $object
	 * @return array|WP_Error Array of term_id
	 */
	public static function eo_get_object_terms( $object ) {
		$list_term 		= array();
		$array_model 	= $object->get_model();

		if( !empty( $array_model ) && !empty( $array_model['taxonomy'] ) ) {
			foreach( $array_model['taxonomy'] as $key => $value ) {
				$list_term[$key] = wp_get_object_terms( $object->id, $key, array( 'fields' => 'ids' ) );
			}
		}

		return $list_term;
	}

	/**
	 * GETTER - Récupération du type de l'élément courant / Get the current element type
	 *
	 * @return string Le type d'élément courant / The current element type
	 */
	public function get_post_type() {
		return $this->post_type;
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
