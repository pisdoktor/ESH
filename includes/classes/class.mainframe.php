<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );
/* 
* Provide many supporting API functions
*/
class mainFrame {
	/** @var database Internal database class pointer */
	var $_db                        = null;
	/** @var mosSession The current session */
	var $_session                    = null;
	/** @var array An array to hold global user state within a session */
	var $_userstate                    = null;
	/** @var array An array of page meta information */
	var $_head                        = null;
	/** @var boolean True if in the admin client */
	var $_isAdmin                     = false;
	/**
	* Class constructor
	* @param database A database connection object
	* @param string The url option
	* @param string The path of the mos directory
	*/
	function mainFrame( &$db, $option ) {
		$this->_db =& $db;

		if (isset( $_SESSION['session_userstate'] )) {
			$this->_userstate =& $_SESSION['session_userstate'];
		} else {
			$this->_userstate = null;
		}
		$this->_head = array();
		$this->_head['title']     = SITEHEAD;
		$this->_head['meta']     = array();
		$this->_head['custom']     = array();
		//Soner Ekledi
		$this->_head['style']   = array();
		$this->_head['script']  = array();

		$now = date( 'Y-m-d H:i:s', time() );
		$this->set( 'now', $now );
	}

	/**
	* @param string
	*/
	function setPageTitle( $title=null ) {
			$title = trim( htmlspecialchars( $title ) );
			$title = stripslashes($title);
			$this->_head['title'] = $title ? SITEHEAD . ' - '. $title : SITEHEAD;
	}
	/**
	* @param string The value of the name attibute
	* @param string The value of the content attibute
	* @param string Text to display before the tag
	* @param string Text to display after the tag
	*/
	function addMetaTag( $name, $content, $prepend='', $append='' ) {
		$name = trim( htmlspecialchars( $name ) );
		$content = trim( htmlspecialchars( $content ) );
		$prepend = trim( $prepend );
		$append = trim( $append );
		$this->_head['meta'][] = array( $name, $content, $prepend, $append );
	}
	
	//Soner Ekledi
	function addStyleSheet($href, $media=NULL, $id=NULL, $rel="stylesheet", $type="text/css") {
		$html = '<link rel="'.$rel.'" type="'.$type.'" href="'.$href.'"';
		if ($media) {
		$html.= ' media="'.$media.'"';
		}
		if ($id) {
		$html.= ' id="'.$id.'"';
		}
		$html.= ' />';
		$this->_head['style'][] = $html;
	}
	//Soner Ekledi
	function addScript($mode=0, $href=NULL, $content=NULL, $type='text/javascript') {
		if (!$mode) {
		$html = '<script src="'.$href.'"';
		$html.= ' type="'.$type.'"></script>';
		
		} else {
		$html = '<script type="'.$type.'">';
		$html.= trim($content);
		$html.= '</script>';
		}
		$this->_head['script'][] = $html;
	}
	/**
	* @param string The value of the name attibute
	* @param string The value of the content attibute to append to the existing
	* Tags ordered in with Site Keywords and Description first
	*/
	function appendMetaTag( $name, $content ) {
		$name = trim( htmlspecialchars( $name ) );
		$n = count( $this->_head['meta'] );
		for ($i = 0; $i < $n; $i++) {
			if ($this->_head['meta'][$i][0] == $name) {
				$content = trim( htmlspecialchars( $content ) );
				if ( $content ) {
					if ( !$this->_head['meta'][$i][1] ) {
						$this->_head['meta'][$i][1] = $content ;
					} else {
						$this->_head['meta'][$i][1] = $content .', '. $this->_head['meta'][$i][1];
					}
				}
				return;
			}
		}
		$this->addMetaTag( $name , $content );
	}

	/**
	* @param string The value of the name attibute
	* @param string The value of the content attibute to append to the existing
	*/
	function prependMetaTag( $name, $content ) {
		$name = trim( htmlspecialchars( $name ) );
		$n = count( $this->_head['meta'] );
		for ($i = 0; $i < $n; $i++) {
			if ($this->_head['meta'][$i][0] == $name) {
				$content = trim( htmlspecialchars( $content ) );
				$this->_head['meta'][$i][1] = $content . $this->_head['meta'][$i][1];
				return;
			}
		}
		$this->addMetaTag( $name, $content );
	}
	/**
	 * Adds a custom html string to the head block
	 * @param string The html to add to the head
	 */
	function addCustomHeadTag( $html ) {
		$this->_head['custom'][] = trim( $html );
	}
	

	/**
	* @return string
	*/
	function getHead() {
		$head = array();
		$head[] = '<title>' . $this->_head['title'] . '</title>';
        
		foreach ($this->_head['meta'] as $meta) {
			if ($meta[2]) {
				$head[] = $meta[2];
			}
            
			$head[] = '<meta name="' . $meta[0] . '" content="' . $meta[1] . '" />';
			if ($meta[3]) {
				$head[] = $meta[3];
			}
		}
        
		foreach ($this->_head['style'] as $html) {
			$head[] = $html;
		}
		foreach ($this->_head['script'] as $html) {
			$head[] = $html;
		}
		foreach ($this->_head['custom'] as $html) {
			$head[] = $html;
		}
		return implode( "\n", $head ) . "\n";
	}
	/**
	* @return string
	*/
	function getPageTitle() {
		return $this->_head['title'];
	}
    
    function showHead() {
        global $my, $option, $bolum;

        //site genel bilgileri
        $this->appendMetaTag( 'description', META_DESC );
        $this->appendMetaTag( 'keywords', META_KEYS );
        $this->addMetaTag( 'Generator', 'Soner Ekici');
        $this->addMetaTag( 'robots', 'index, follow' );
        
        //jquery eklemeleri
        $this->addStyleSheet(SITEURL.'/includes/global/css/jquery-ui.css');
        $this->addScript(0, SITEURL.'/includes/global/js/jquery.min.js');
        $this->addScript(0, SITEURL.'/includes/global/js/jquery-ui.min.js');
        
        //bootstrap eklemesi
        $this->addStyleSheet(SITEURL.'/includes/global/css/bootstrap.min.css');
        $this->addScript(0, SITEURL.'/includes/global/js/bootstrap.min.js');
        $this->addScript(0, SITEURL.'/includes/global/js/bootstrap-helper.js');
        
        //datepicker eklemesi
        $this->addStyleSheet(SITEURL.'/includes/global/css/bootstrap-datepicker3.min.css');
        $this->addScript(0, SITEURL.'/includes/global/js/bootstrap-datepicker.min.js');
        $this->addScript(0, SITEURL.'/includes/global/js/bootstrap-datepicker.tr.min.js');
        
        //validator eklemesi
        $this->addScript(0, SITEURL.'/includes/global/js/validator.js');
        $this->addScript(0, SITEURL.'/includes/global/js/tableToExcel.js');  
        
        //site genel eklemeleri
        $this->addStyleSheet(SITEURL.'/includes/global/css/global.css');
        $this->addScript(0, SITEURL.'/includes/global/js/global.js');
        
        //site menü css eklemesi
        $this->addStyleSheet(SITEURL.'/includes/global/css/cssmenu.css');    
        $this->addScript(0, SITEURL.'/includes/global/js/cssmenu.js');
        
        //site takvim eklemesi
        $this->addScript(0, SITEURL.'/includes/global/calendar/index.global.js');
        $this->addScript(0, SITEURL.'/includes/global/calendar/locales/tr.global.js');
        
        echo $this->getHead();    
    }
  /**
	* Gets the value of a user state variable
	* @param string The name of the variable
	*/
	function getUserState( $var_name ) {
		if (is_array( $this->_userstate )) {
			return getParam( $this->_userstate, $var_name, null );
		} else {
			return null;
		}
	}
	/**
	* Gets the value of a user state variable
	* @param string The name of the user state variable
	* @param string The name of the variable passed in a request
	* @param string The default value for the variable if not found
	*/
	function getUserStateFromRequest( $var_name, $req_name, $var_default=null ) {
		if (is_array( $this->_userstate )) {
			if (isset( $_REQUEST[$req_name] )) {
				$this->setUserState( $var_name, $_REQUEST[$req_name] );
			} else if (!isset( $this->_userstate[$var_name] )) {
				$this->setUserState( $var_name, $var_default );
			}

			// filter input
			$iFilter = new InputFilter();
			$this->_userstate[$var_name] = $iFilter->process( $this->_userstate[$var_name] );

			return $this->_userstate[$var_name];
		} else {
			return null;
		}
	}
	/**
	* Sets the value of a user state variable
	* @param string The name of the variable
	* @param string The value of the variable
	*/
	function setUserState( $var_name, $var_value ) {
		if (is_array( $this->_userstate )) {
			$this->_userstate[$var_name] = $var_value;
		}
	}
	/**
	* Initialises the user session
	*
	* Old sessions are flushed based on the configuration value for the cookie
	* lifetime. If an existing session, then the last access time is updated.
	* If a new session, a session id is generated and a record is created in
	* the jos_sessions table.
	*/
	function initSession() {
		global $bolum;
		// initailize session variables
		$session     =& $this->_session;
		$session     = new Session( $this->_db );

		// purge expired sessions
		$session->purge();

		// Session Cookie `name`
		$sessionCookieName     = mainFrame::sessionCookieName();
		// Get Session Cookie `value`
		$sessioncookie         = strval( getParam( $_COOKIE, $sessionCookieName, null ) );

		// Session ID / `value`
		$sessionValueCheck     = mainFrame::sessionCookieValue( $sessioncookie );

		// Check if existing session exists in db corresponding to Session cookie `value`
		// extra check added in 1.0.8 to test sessioncookie value is of correct length
		if ( $sessioncookie && strlen($sessioncookie) == 32 && $sessioncookie != '-' && $session->load($sessionValueCheck) ) {
			// update time in session table
			$session->time = time();
			$session->nerede = $bolum;
			$session->update();
		} else {
			// Remember Me Cookie `name`
			$remCookieName = mainFrame::remCookieName_User();

			// test if cookie found
			$cookie_found = false;
			if ( isset($_COOKIE[$sessionCookieName]) || isset($_COOKIE[$remCookieName]) || isset($_POST['force_session']) ) {
				$cookie_found = true;
			}

			// check if neither remembermecookie or sessioncookie found
			if (!$cookie_found) {
				// create sessioncookie and set it to a test value set to expire on session end
				setcookie( $sessionCookieName, '-', false, '/' );
			} else {
			// otherwise, sessioncookie was found, but set to test val or the session expired, prepare for session registration and register the session
				$url = strval( getParam( $_SERVER, 'REQUEST_URI', null ) );
				// stop sessions being created for requests to syndicated feeds
					$session->username     = '';
					$session->time         = time();
					
					// Generate Session Cookie `value`
					$session->generateId();

					if (!$session->insert()) {
						die( $session->getError() );
					}

					// create Session Tracking Cookie set to expire on session end
					setcookie( $sessionCookieName, $session->getCookie(), false, '/' );
			}

			// Cookie used by Remember me functionality
			$remCookieValue    = strval( getParam( $_COOKIE, $remCookieName, null ) );

			// test if cookie is correct length
			if ( strlen($remCookieValue) > 64 ) {
				// Separate Values from Remember Me Cookie
				$remUser    = substr( $remCookieValue, 0, 32 );
				$remPass    = substr( $remCookieValue, 32, 32 );
				$remID        = intval( substr( $remCookieValue, 64  ) );

				// check if Remember me cookie exists. Login with usercookie info.
				if ( strlen($remUser) == 32 && strlen($remPass) == 32 ) {
					$this->login( $remUser, $remPass, 1, $remID );
				}
			}
		}
	}

	/*
	* Function used to set Session Garbage Cleaning
	* garbage cleaning set at configured session time + 600 seconds
	* Added as of 1.0.8
	* Deprecated 1.1
	*/
	function setSessionGarbageClean() {
		/** ensure that funciton is only called once */
		if (!defined( '_JOS_GARBAGECLEAN' )) {
			define( '_JOS_GARBAGECLEAN', 1 );

			$garbage_timeout = 2400;
			@ini_set('session.gc_maxlifetime', $garbage_timeout);
		}
	}

	/*
	* Static Function used to generate the Session Cookie Name
	* Added as of 1.0.8
	* Deprecated 1.1
	*/
	function sessionCookieName() {

		if( substr( SITEURL, 0, 7 ) == 'http://' ) {
			$hash = md5( 'site' . substr( SITEURL, 7 ) );
		} elseif( substr( SITEURL, 0, 8 ) == 'https://' ) {
			$hash = md5( 'site' . substr( SITEURL, 8 ) );
		} else {
			$hash = md5( 'site' . SITEURL );
		}

		return $hash;
	}

	/*
	* Static Function used to generate the Session Cookie Value
	* Added as of 1.0.8
	* Deprecated 1.1
	*/
	static function sessionCookieValue( $id=null ) {

		$type        = SESSION_TYPE;
		$browser     = @$_SERVER['HTTP_USER_AGENT'];

		switch ($type) {
			case 2:
			// 1.0.0 to 1.0.7 Compatibility
			// lowest level security
				$value             = md5( $id . $_SERVER['REMOTE_ADDR'] );
				break;

			case 1:
			// slightly reduced security - 3rd level IP authentication for those behind IP Proxy
				$remote_addr     = explode('.',$_SERVER['REMOTE_ADDR']);
				$ip                = $remote_addr[0] .'.'. $remote_addr[1] .'.'. $remote_addr[2];
				$value             = mosHash( $id . $ip . $browser );
				break;

			default:
			// Highest security level - new default for 1.0.8 and beyond
				$ip                = $_SERVER['REMOTE_ADDR'];
				$value             = mosHash( $id . $ip . $browser );
				break;
		}

		return $value;
	}

	/*
	* Static Function used to generate the Rememeber Me Cookie Name for Username information
	* Added as of 1.0.8
	* Depreciated 1.1
	*/
	function remCookieName_User() {
		$value = mosHash( 'remembermecookieusername'. mainFrame::sessionCookieName() );

		return $value;
	}

	/*
	* Static Function used to generate the Rememeber Me Cookie Name for Password information
	* Added as of 1.0.8
	* Depreciated 1.1
	*/
	function remCookieName_Pass() {
		$value = mosHash( 'remembermecookiepassword'. mainFrame::sessionCookieName() );

		return $value;
	}

	/*
	* Static Function used to generate the Remember Me Cookie Value for Username information
	* Added as of 1.0.8
	* Depreciated 1.1
	*/
	function remCookieValue_User( $username ) {
		$value = md5( $username . mosHash( @$_SERVER['HTTP_USER_AGENT'] ) );

		return $value;
	}

	/*
	* Static Function used to generate the Remember Me Cookie Value for Password information
	* Added as of 1.0.8
	* Depreciated 1.1
	*/
	function remCookieValue_Pass( $passwd ) {
		$value     = md5( $passwd . mosHash( @$_SERVER['HTTP_USER_AGENT'] ) );

		return $value;
	}

	/**
	* Login validation function
	*
	* Username and encoded password is compare to db entries in the jos_users
	* table. A successful validation updates the current session record with
	* the users details.
	*/
	function login( $username=null, $passwd=null, $remember=0, $userid=NULL ) {
		global $bolum;

		$bypost = 0;
		$valid_remember = false;

		// if no username and password passed from function, then function is being called from login module/component
		if (!$username || !$passwd) {
			$username   = stripslashes( strval( getParam( $_POST, 'username', '' ) ) );
			$passwd     = stripslashes( strval( getParam( $_POST, 'passwd', '' ) ) );

			$bypost     = 1;

			// extra check to ensure that Joomla! sessioncookie exists 
			if (!$this->_session->session) {
				Redirect('index.php', 'Çerezler açık olmalı!' );
				return;
			}
            
			spoofCheck(NULL,1);
		}

		$row = null;
		if (!$username || !$passwd) {
			Redirect('index.php', 'Lütfen kullanıcı adı, parola alanlarını doldurunuz.');
			exit();
		} else {
			if ( $remember && strlen($username) == 32 && $userid ) {
			// query used for remember me cookie
				$harden = mosHash( @$_SERVER['HTTP_USER_AGENT'] );

				$query = "SELECT id, name, username, password"
				. "\n FROM #__users"
				. "\n WHERE id = " . (int) $userid
				;
				$this->_db->setQuery( $query );
				$this->_db->loadObject($user);

				list($hash, $salt) = explode(':', $user->password);

				$check_username = md5( $user->username . $harden );
				$check_password = md5( $hash . $harden );

				if ( $check_username == $username && $check_password == $passwd ) {
					$row = $user;
					$valid_remember = true;
				}
			} else {
			// query used for login via login module
				$query = "SELECT u.id, u.name, u.username, u.password, u.activated"
				. "\n FROM #__users AS u"
				. "\n WHERE u.username = ". $this->_db->Quote( $username )
				;

				$this->_db->setQuery( $query );
				$this->_db->loadObject( $row );
			}

			if (is_object($row)) {
				if (!$valid_remember) {
					// Conversion to new type
					if ((strpos($row->password, ':') === false) && $row->password == md5($passwd)) {
						// Old password hash storage but authentic ... lets convert it
						$salt = MakePassword(16);
						$crypt = md5($passwd.$salt);
						$row->password = $crypt.':'.$salt;

						// Now lets store it in the database
						$query    = 'UPDATE #__users'
								. ' SET password = '.$this->_db->Quote($row->password)
								. ' WHERE id = '.(int)$row->id;
						$this->_db->setQuery($query);
						if (!$this->_db->query()) {
							// This is an error but not sure what to do with it ... we'll still work for now
						}
					}

					list($hash, $salt) = explode(':', $row->password);
					$cryptpass = md5($passwd.$salt);
					if ($hash != $cryptpass) {
						if ( $bypost ) {
							Redirect('index.php', 'Hatalı kullanıcı adı ve/veya parola girdiniz');
							//mosErrorAlert('Hatalı kullanıcı adı ve/veya parola girdiniz');
						} else {
							$this->logout();
							Redirect('index.php');
						}
						exit();
					}
					//aktive edilmemiş hesap : soner ekledi
					if ($row->activated == 0) {
						Redirect('index.php', 'Hesabınız henüz aktive edilmemiş');
						exit();
					}
				}
				
				// initialize session data
				$session             =& $this->_session;
				$session->username   = $row->username;
				$session->userid     = intval( $row->id );
				$session->nerede     = $bolum;
                $session->access_type = 'site';
                $session->isadmin = $row->isadmin;
				   
				if ($row->isadmin) {
				$this->set('_isAdmin', true);
				} 
                
				$session->update();

					// delete any old front sessions to stop duplicate sessions
					$query = "DELETE FROM #__sessions"
					. "\n WHERE session != ". $this->_db->Quote( $session->session )
					. "\n AND username = ". $this->_db->Quote( $row->username )
					. "\n AND userid = " . (int) $row->id
					;
					$this->_db->setQuery( $query );
					$this->_db->query();

				// update user visit data
				$currentDate = date("Y-m-d\TH:i:s");
				
				$query = "SELECT nowvisit FROM #__users"
				. "\n WHERE id = " . (int) $session->userid
				;
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					die($this->_db->stderr(true));
				}
				
				$lastvisit = $this->_db->loadResult();

				$query = "UPDATE #__users"
				. "\n SET lastvisit = ". $this->_db->Quote( $lastvisit ) .", nowvisit = ". $this->_db->Quote( $currentDate )
				. "\n WHERE id = " . (int) $session->userid
				;
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					die($this->_db->stderr(true));
				}
				
				// set remember me cookie if selected
				$remember = strval( getParam( $_POST, 'remember', '' ) );
				if ( $remember == 'yes' ) {
					// cookie lifetime of 365 days
					$lifetime         = time() + 365*24*60*60;
					$remCookieName     = mainFrame::remCookieName_User();
					$remCookieValue = mainFrame::remCookieValue_User( $row->username ) . mainFrame::remCookieValue_Pass( $hash ) . $row->id;
					setcookie( $remCookieName, $remCookieValue, $lifetime, '/' );
				}
			} else {
				if ( $bypost ) {
					Redirect('index.php', 'Hatalı kullanıcı adı veya parola. Lütfen tekrar deneyiniz.');
					//mosErrorAlert('Hatalı kullanıcı adı veya parola. Lütfen tekrar deneyiniz.');
				} else {
					$this->logout();
					Redirect('index.php');
				}
				exit();
			}
		}
	}

	/**
	* User logout
	*
	* Reverts the current session record back to 'anonymous' parameters
	*/
	function logout() {
		$session             =& $this->_session;
        
		$session->username   = '';
		$session->userid     = '';
		$session->access_type= '';
		$session->nerede     = '';
        
		$session->update();

		// kill remember me cookie
		$lifetime         = time() - (365*24*60*60);
		$remCookieName     = mainFrame::remCookieName_User();
		setcookie( $remCookieName, ' ', $lifetime, '/' );

		@session_destroy();
	}

	
	function getUser() {
		
		$user = new Users( $this->_db );
		
		$user->id           = intval( $this->_session->userid );
		$user->username     = $this->_session->username;
		$user->sessionid    = $this->_session->session;
		$user->access_type  = $this->_session->access_type;
				
		if ($user->id) {
			$query = "SELECT u.*"
			. "\n FROM #__users AS u"
			. "\n WHERE u.id = " . (int) $user->id
			;
			$this->_db->setQuery( $query );
			$this->_db->loadObject( $my );

			$user->name         = $my->name;
			$user->email        = $my->email;
			$user->lastvisit    = $my->lastvisit;
            $user->tckimlikno   = $my->tckimlikno;
            $user->isadmin      = $my->isadmin;

		}

		return $user;
	}
	/**
	* @param string The name of the property
	* @param mixed The value of the property to set
	*/
	function set( $property, $value=null ) {
		$this->$property = $value;
	}

	/**
	* @param string The name of the property
	* @param mixed  The default value
	* @return mixed The value of the property
	*/
	function get($property, $default=null) {
		if(isset($this->$property)) {
			return $this->$property;
		} else {
			return $default;
		}
	}
}
