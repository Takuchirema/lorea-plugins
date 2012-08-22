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

	/**
	 * Test function
	 *
	 * @return bool
	 * @since 1.8.0
	 */
	public function testAssembly() {
		return true;
	}
}
