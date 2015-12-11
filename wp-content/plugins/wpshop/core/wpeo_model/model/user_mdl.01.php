<?php
/**
 * Fichier de définition du modèle des utilisateurs / File for user model definition
 *
 * @author Evarisk development team <dev@eoxia.com>
 * @version 6.0
 * @package Model manager
 * @subpackage Custom post type
 */

/**
 * CLasse de définition du modèle des utilisateurs / Class for user model definition
 *
 * @author Evarisk development team <dev@eoxia.com>
 * @version 6.0
 * @package Model manager
 * @subpackage Custom post type
 */
class user_mdl_01 extends constructor_model_ctr_01 {

	/**
	 * Définition du modèle principal des utilisateurs / Main definition for user model
	 * @var array Les champs principaux d'un utilisateur / Main fields for a user
	 */
	protected $model = array(
		'id' => array(
			'type'		=> 'integer',
			'field'		=> 'ID',
			'function'	=> '',
			'default'	=> 0,
			'required'	=> false,
		),
		'email' => array(
				'type'		=> 'string',
				'field'		=> 'user_email',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
		),
		'login' => array(
				'type'		=> 'string',
				'field'		=> 'user_login',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
		),
// 		'password' => array(
// 				'type'		=> 'string',
// 				'field'		=> 'user_pass',
// 				'function'	=> '',
// 				'default'	=> 0,
// 				'required'	=> false,
// 		),
		'displayname' => array(
				'type'		=> 'string',
				'field'		=> 'display_name',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
		),
		'date' => array(
				'type'		=> 'string',
				'field'		=> 'user_registered',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
		),
	);

	/**
	 * Définition du modèle pour les champs secondaires des utilisateurs / Secondary fields definition for user model
	 * @var array Les champs secondaires d'un utilisateur / Secondary field for a user
	 */
	protected $array_option = array(
		'user_info' => array(
			'firstname' => array(
				'type'		=> 'string',
				'field_type'	=> 'meta',
				'field'		=> 'first_name',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
			),
			'lastname' => array(
				'type'		=> 'string',
				'field_type'	=> 'meta',
				'field'		=> 'last_name',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
			),
			'address_id' => array(
				'type'		=> 'array',
				'field'		=> '',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
			),
			'phone' => array(
				'type'		=> 'array',
				'field'		=> '',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
			),
		),
		'user_right' => array(
			'type' => array(
				'type'		=> 'array',
				'field_type'		=> 'meta',
				'field'		=> 'roles',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
			),
			'code' => array(
				'type'		=> 'array',
				'field_type'		=> 'meta',
				'field'		=> 'allcaps',
				'function'	=> '',
				'default'	=> 0,
				'required'	=> false,
			),
		),
	);

	/**
	 * Construction de l'objet utilisateur par remplissage du modèle / Build user through fill in the model
	 *
	 * @param object $object L'object avec lequel il faut construire le modèle / The object which one to build
	 * @param string $meta_key Le nom de la "meta" contenant la définition complète de l'object sous forme json / The "meta" name containing the complete definition of object under json format
	 * @param boolean $cropped Permet de choisir si on construit le modèle complet ou uniquement les champs principaux / Allows to choose if the entire model have to be build or only main model
	 */
	public function __construct( $object, $meta_key, $cropped ) {
		/**	Instanciation du constructeur de modèle principal / Instanciate the main model constructor	*/
		parent::__construct( $object );

		/** If cropped don't get meta */
		if ( !$cropped ) {
			$internal_meta = !empty( $object ) && !empty( $object->$meta_key ) ? json_decode( $object->$meta_key ) : null;

			if( !empty( $this->array_option ) ) {
				foreach( $this->array_option as $key => $array ) {
					$this->option[ $key ] = $this->fill_value( $object, $object, $key, $array, $internal_meta );
				}
			}
		}
	}

}