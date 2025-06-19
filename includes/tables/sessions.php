<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );

class Session extends DBTable {
	/** @var int Primary key */
	var $session            = null;
	/** @var string */
	var $time               = null;
	/** @var string */
	var $userid             = null;
	/** @var string */
	var $username           = null;
	/** @var string */
	var $_session_cookie    = null;
	
	var $access_type        = null;
	
	var $nerede             = null;
	/**
	* @param database A database connector object
	*/
	function Session( &$db ) {
		$this->DBTable( '#__sessions', 'session', $db );
	}
	
	/**
	* soner ekledi
	* toplam online üye
	*//*
	function totalOnline() {
		global $my;
		$query = "SELECT COUNT(*) FROM #__sessions"
		. "\n WHERE userid>0"
		. "\n AND userid NOT IN (".$this->_db->Quote($my->id).")"
		;
		$this->_db->setQuery($query);
		
		if ($this->_db->loadResult()) {
			echo $this->_db->loadResult();
		}
	}
     */
	/**
	 * @param string Key search for
	 * @param mixed Default value if not set
	 * @return mixed
	 */
	function get( $key, $default=null ) {
		return getParam( $_SESSION, $key, $default );
	}

	/**
	 * @param string Key to set
	 * @param mixed Value to set
	 * @return mixed The new value
	 */
	function set( $key, $value ) {
		$_SESSION[$key] = $value;
		return $value;
	}

	/**
	 * Sets a key from a REQUEST variable, otherwise uses the default
	 * @param string The variable key
	 * @param string The REQUEST variable name
	 * @param mixed The default value
	 * @return mixed
	 */
	function setFromRequest( $key, $varName, $default=null ) {
		if (isset( $_REQUEST[$varName] )) {
			return mosSession::set( $key, $_REQUEST[$varName] );
		} else if (isset( $_SESSION[$key] )) {
			return $_SESSION[$key];
		} else {
			return mosSession::set( $key, $default );
		}
	}

	/**
	 * Insert a new row
	 * @return boolean
	 */
	function insert() {
		$ret = $this->_db->insertObject( $this->_tbl, $this );

		if( !$ret ) {
			$this->_error = strtolower(get_class( $this ))."::kayıt başarısız <br />" . $this->_db->stderr();
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Update an existing row
	 * @return boolean
	 */
	function update( $updateNulls=false ) {
		$ret = $this->_db->updateObject( $this->_tbl, $this, 'session', $updateNulls );

		if( !$ret ) {
			$this->_error = strtolower(get_class( $this ))."::kayıt başarısız <br />" . $this->_db->stderr();
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Generate a unique session id
	 * @return string
	 */
	function generateId() {
		$failsafe     = 20;
		$randnum     = 0;
		
		

		while ($failsafe--) {
			$randnum         = md5( uniqid( microtime(), 1 ) );
			$new_session_id = mainFrame::sessionCookieValue( $randnum );

			if ($randnum != '') {
				$query = "SELECT $this->_tbl_key"
				. "\n FROM $this->_tbl"
				. "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $new_session_id )
				;
				$this->_db->setQuery( $query );
				if(!$result = $this->_db->query()) {
					die( $this->_db->stderr( true ));
				}

				if ($this->_db->getNumRows($result) == 0) {
					break;
				}
			}
		}

		$this->_session_cookie     = $randnum;
		$this->session         = $new_session_id;
	}

	/**
	 * @return string The name of the session cookie
	 */
	function getCookie() {
		return $this->_session_cookie;
	}

	/**
	 * Purge lapsed sessions
	 * @return boolean
	 */
	function purge( $inc=1800, $and='' ) {
		global $mainframe;


			$past = time() - $inc;
            
			$query = "DELETE FROM $this->_tbl"
			. "\n WHERE ( time < '" . (int) $past . "' )"
			. $and
			;
            
            
		$this->_db->setQuery($query);

		return $this->_db->query();
	}
}
