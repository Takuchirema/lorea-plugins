<?php

global $FEDERATED_CONSTRUCTORS;
$FEDERATED_CONSTRUCTORS = array();

class FederatedObject {

	/**
	 * Load xml
	 */
	public static function find($webid) {
		$options = array('metadata_name' => 'atom_id',
				 'metadata_value' => $webid,
				 'owner_guid' => ELGG_ENTITIES_ANY_VALUE);
                $entities = elgg_get_entities_from_metadata($options);
                if ($entities) {
                        return $entities[0];
                }
	}
	public static function create($params) {
		global  $FEDERATED_CONSTRUCTORS;
		$type = $params['type'];
		$entity = FederatedObject::find($params['id']);
		error_log("CREATE:".$FEDERATED_CONSTRUCTORS[$type].":".$params['id']);
		return call_user_func($FEDERATED_CONSTRUCTORS[$type], $params, $entity);
	}
	public static function register_constructor($type, $callback) {
		global  $FEDERATED_CONSTRUCTORS;
		$FEDERATED_CONSTRUCTORS[$type] = $callback;
	}
}
