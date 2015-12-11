<?php
/**
 * Fichier du controlleur principal pour les catégories de dangers dans Digirisk / Controller file for danger categories for Digirisk
 *
 * @author Evarisk development team <dev@evarisk.com>
 * @version 6.0
 */

/**
 * Classe du controlleur principal pour les catégories de dangers dans Digirisk / Controller class for danger categories for Digirisk
 *
 * @author Evarisk development team <dev@evarisk.com>
 * @version 6.0
 */
class constructor_model_ctr_01 {

	/**
	 * Ce constructeur crée les attributs de cette objet selon le modèle.
	 * Ensuite il leur affècte les valeurs trouvées
	 * @param Array or StdClass $object
	 * @return void
	 */
	public function __construct( $object ) {

		foreach( $this->model as $field_name => $field_def ) {
			if( !isset( $field_def['type'] ) || is_array( $field_def['type'] ) ) {

				foreach( $field_def as $sub_file_name => $sub_field_def ) {
					if( !empty( $sub_field_def['function'] ) ) {
						$this->$field_name = call_user_func( $sub_field_def['function'], $this );
					}
				}
			}
			else {
				// On lui affècte la valeur par défaut
				$this->$field_name = isset( $field_def['default'] ) ? $field_def[ 'default' ] : null;

				// Il faut tester si c'est une fonction WordPress ou pas
				// 				if( function_exists( $field_def['function'] ) && !empty( $this->id ) )
				// 					$this->$field_name = call_user_func( $field_def['function'], $this->id );

				// Si c'est un objet
				if ( is_object( $object ) ) {
					// Si la valeur est trouvé dans le modèle et dans l'objet
					if( !empty( $object->$field_def['field'] ) ) {
						$this->$field_name = $object->$field_def['field'];
						settype( $this->$field_name, $field_def['type'] );
					}
					else {
						if( $field_def['required'] ) {
							$this->$field_name = "Is required !";
						}
					}
				}
				else {
					// Si la valeur est trouvé dans le modèle et dans l'objet
					if( is_array( $object ) && array_key_exists( $field_name, $object ) ) {
						$this->$field_name = $object[$field_name];
						settype( $this->$field_name, $field_def['type'] );
					}
					else {
						if( $field_def['required'] ) {
							$this->$field_name = "Is required !";
						}
					}
				}
			}
		}
	}

	/**
	 * Remplissage des champs du model principal avec les données de base de wordpress / Fill the fields of main model with wordpress data
	 *
	 * @return array L'objet remplit avec les données existante / The filled object
	 */
	public function do_wp_object() {
		$object = array();

		foreach( $this->model as $field_name => $field_def ) {
			if( !empty( $field_def['field'] ) )
				$object[ $field_def[ 'field' ] ] = $this->$field_name;
		}

		return $object;
	}

	/**
	 * Remplissage des champs optionnel du modèle selon la configuration / Fill the value for optionnal model fields
	 *
	 * @param array $object L'objet avec lequel on doit remplir dans le cas d'une création ou d'une mise à jour / The object to use for filling in case of creation or update
	 * @param array $full_meta La liste complète des metas associées à l'objet / The complete meta list associated to element
	 * @param string $field_name Le nom du champs dont il faut remplir la valeur / Le field name to fill value
	 * @param array $field_def Un tableau de définition du champs / An array with the field definition
	 * @param object $internal_meta La meta spécifique pour le type de l'objet / The specific meta for object type
	 *
	 * @return mixed La valeur du champs / The field value
	 */
	function fill_value( $object, $full_meta, $field_name, $field_def, $internal_meta ) {
		$value = null;

		$object_option = $object;
		/**	On ne récupère que les options de l'objet courant / Only get current object's options */
		if ( is_array( $object ) && isset( $object[ 'option' ] ) && is_array( $object[ 'option' ] ) ) {
			$object_option = $object[ 'option' ];
		}
		else if ( isset( $object->option ) ) {
			$object_option = $object->option;
		}

		if( !isset( $field_def[ 'type' ] ) || is_array( $field_def[ 'type' ] ) ) {
			$sub_internal_meta = isset( $internal_meta->$field_name ) ? $internal_meta->$field_name : null;
			foreach( $field_def as $sub_field_name => $sub_field ) {
				$object_to_use = is_array( $object_option ) && !empty( $object_option[ $field_name ] ) ? $object_option[ $field_name ] : $object_option;

				$value[ $sub_field_name ] = $this->fill_value( $object_to_use, $full_meta, $sub_field_name, $sub_field, $sub_internal_meta);
			}
		}
		else {
			/**	Remplissage avec la valeur par défaut / Fill with the default value */
			$value = isset( $field_def['default'] ) ? $field_def['default'] : null;

			/** Si la valeur existe dans la meta propre sous forme json on remplit avec cette valeur / If the value exists into json meta fill with this value  */
			$value = isset( $internal_meta->$field_name ) ? $internal_meta->$field_name : $value;

			/**	Si la valeur est stockée dans une meta seule / If the value if stored into a single meta	*/
			if ( isset( $field_def[ 'field_type' ] ) && ( 'meta' == $field_def[ 'field_type' ] ) ) {
				if ( is_object( $full_meta ) && isset( $full_meta->$field_def[ 'field' ] ) ) {
					$meta_to_use = $full_meta->$field_def[ 'field' ];
				}
				else if ( isset( $full_meta[ $field_def[ 'field' ] ] ) ) {
					$meta_to_use = $full_meta[ $field_def[ 'field' ] ];
				}

				if ( isset( $meta_to_use ) ) {
					if ( 1 == count( $meta_to_use ) && isset( $meta_to_use[ 0 ] ) ) {
						$value = $meta_to_use[ 0 ];
					}
					else {
						$value = $meta_to_use;
					}
				}
			}

			/** Si on a définit une valeur (dans le cas de la création ou de la mise à jour) / If we defined the value (in creation case or in update case ) */
			$value = is_array( $object_option ) && isset( $object_option[ $field_name ] ) ? $object_option[ $field_name ] : $value;

			/**	Forçage du type pour la valeur / Force the value type	*/
			settype( $value, $field_def[ 'type' ] );
		}

		return $value;
	}

	/**
	 * GETTER - Récupération de la définition du modèle de l'objet courant / Get the model definition of current object
	 *
	 * @return array Modèle de l'objet courant / Current object model
	 */
	public function get_array_option() {
		return $this->array_option;
	}

	/**
	 * GETTER - Récupération de la définition du modèle de l'objet courant / Get the model definition of current object
	 *
	 * @return array Modèle de l'objet courant / Current object model
	 */
	public function get_model() {
		return $this->model;
	}

	/**
	 * SETTER - Enregistrement des données associées a un objet avec rangement selon les types de champs / Save information associated to an object regarding field type
	 *
	 * @param Object $object Définition complète de l'objet à enregistrer / Complete object definition to save
	 * @param string $function Le nom de la fonction à appeler / The name of function to call
	 * @param string $meta_key La meta clé à mêttre à jour / The meta key to update
	 */
	public function save_meta_data( $object, $function, $meta_key ) {
		/** Read the object model option */
		$array_option = $object->get_array_option();

		foreach ( $object->option as $field_name => $field ) {
			if( !isset( $array_option[ $field_name ]['type'] ) || is_array( $array_option[ $field_name ]['type'] ) ) {
				foreach( $field as $sub_field_name => $sub_field ) {
					if ( isset( $array_option[ $sub_field_name ][ 'field_type' ] ) && ( 'meta' == $array_option[ $sub_field_name ][ 'field_type' ] ) ) {
						/** Update a single meta for specific field */
						call_user_func( $function, $object->id, $array_option[ $sub_field_name ][ 'field' ] , $object->option[ $field_name ][ $sub_field_name ] );
						unset( $object->option[ $field_name ][ $sub_field_name ] );
					}
				}
			}
			else if ( isset( $array_option[ $field_name ][ 'field_type' ] ) && ( 'meta' == $array_option[ $field_name ][ 'field_type' ] ) ) {
				/** Update a single meta for specific field */
				call_user_func( $function, $object->id, $array_option[ $field_name ][ 'field' ], $object->option[ $field_name ] );
				unset( $object->option[ $field_name ] );
			}
		}

		/**	Update the main meta with json datas	*/
		call_user_func( $function, $object->id, $meta_key, json_encode( $object->option ) );
	}

}