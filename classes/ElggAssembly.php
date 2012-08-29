<?php
/**
 * Extended class for assemblies
 */
class ElggAssembly extends ElggObject {

	/**
	 * Set subtype to assembly.
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = "assembly";
	}
}
