<?php
/**
*/


defined( '_JEXEC' ) or die( 'Restricted access' );

class FalangVersion {
	var $_version	= '2.9.1';
    var $_versiontype	= 'pro';
    var $_date	= '2017/07/25';
	var $_status	= 'Stable';
	var $_revision	= '';
	var $_copyyears = '';

	/**
	 * This method delivers the full version information in one line
	 *
	 * @return string
	 */
    function getVersionFull(){
        return 'v' .$this->_version. ' ('.$this->_versiontype.')';
    }

    /**
     * This method delivers the short version information in one line
     *
     * @return string
     */
    function getVersionShort() {
        return $this->_version;
	}

	function getVersionType() {
		return $this->_versiontype;
	}


	/**
	 * This method delivers a special version String for the footer of the application
	 *
	 * @return string
	 */
	function getCopyright() {
		//return '&copy; ' .$this->_copyyears;
            return '';
	}

	/**
	 * Returns the complete revision string for detailed packaging information
	 *
	 * @return unknown
	 */
	function getRevision() {
		return '' .$this->_revision. ' (' .$this->_date. ')';
	}
}
