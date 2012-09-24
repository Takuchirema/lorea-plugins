<?php

class AtomRiverMapper {

	// River to atomid mapping
	public static function getRiverAtomID($river_id) {
		global $CONFIG;
		$prefix = $CONFIG->dbprefix;
		$river_id = (int)$river_id;
		$sql = "SELECT atom_id FROM {$prefix}river_atomid_mapping WHERE river_id=$river_id";
		$data = get_data_row($sql);
		if ($data)
			return $data->atom_id;
	}
	public static function getRiverID($atom_id) {
		global $CONFIG;
		$prefix = $CONFIG->dbprefix;
		$atom_id = sanitise_string($atom_id);
		$sql = "SELECT river_id FROM {$prefix}river_atomid_mapping WHERE atom_id='$atom_id'";
		$data = get_data_row($sql);
		if ($data)
			return $data->river_id;
	}
	public static function setIDMapping($river_id, $atom_id, $provenance=null) {
		global $CONFIG;
		$river_id = (int)$river_id;
		$atom_id = sanitise_string($atom_id);
		$prefix = $CONFIG->dbprefix;
		if ($provenance) {
			$provenance = sanitise_string($provenance);
			$sql = "INSERT INTO {$prefix}river_atomid_mapping (river_id, atom_id, provenance) VALUES($river_id, '$atom_id', '$provenance')";
		}
		else {
			$sql = "INSERT INTO {$prefix}river_atomid_mapping (river_id, atom_id) VALUES($river_id, '$atom_id')";
		}
		insert_data($sql);
	}
	// Elgg Callbacks
	public static function river_id($hook, $type, $return, $params) {
		$item = $params['item'];
		$atom_id = AtomRiverMapper::getRiverAtomID($item->id);
		if ($atom_id)
			return $atom_id;
		return $return;
	}
	public static function entity_id($hook, $type, $return, $params) {
		$entity = $params['entity'];
		if ($entity->atom_id)
			return $entity->atom_id;
		return $return;
	}
}
