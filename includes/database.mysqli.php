<?php
// no direct access
defined( 'ERISIM' ) or die( 'Bu alanı görmeye yetkiniz yok!' );  

/**
* Database connector class
* @subpackage Database
* @package Joomla
*/
class DB {
    /** @var string Internal variable to hold the query sql */
    var $_sql            = '';
    /** @var int Internal variable to hold the database error number */
    var $_errorNum        = 0;
    /** @var string Internal variable to hold the database error message */
    var $_errorMsg        = '';
    /** @var string Internal variable to hold the prefix used on all database tables */
    var $_table_prefix    = '';
    /** @var Internal variable to hold the connector resource */
    var $_resource        = '';
    /** @var Internal variable to hold the last query cursor */
    var $_cursor        = null;
    /** @var boolean Debug option */
    var $_debug            = 0;
    /** @var int The limit for the query */
    var $_limit            = 0;
    /** @var int The for offset for the limit */
    var $_offset        = 0;
    /** @var int A counter for the number of queries performed by the object instance */
    var $_ticker        = 0;
    /** @var array A log of queries */
    var $_log            = null;
    /** @var string The null/zero date string */
    var $_nullDate        = '0000-00-00 00:00:00';
    /** @var string Quote for named objects */
    var $_nameQuote        = '`';

    /**
    * Database object constructor
    * @param string Database host
    * @param string Database user name
    * @param string Database user password
    * @param string Database name
    * @param string Common prefix for all tables
    * @param boolean If true and there is an error, go offline
    */
    function __construct( $host, $user, $pass, $db='', $table_prefix='', $goOffline=true ) {
        // perform a number of fatality checks, then die gracefully
        if (!function_exists( 'mysqli_connect' )) {
            $systemError = 1;
            if ($goOffline) {
                //include ABSPATH . '/config.php';
                include ABSPATH . '/closed.php';
                exit();
            }
        }
        if (!($this->_resource = @mysqli_connect( $host, $user, $pass ))) {
            $systemError = 2;
            if ($goOffline) {
                //include ABSPATH . '/config.php';
                include ABSPATH . '/closed.php';
                exit();
            }
        }
        if ($db != '' && !mysqli_select_db($this->_resource, $db)) {
            $systemError = 3;
            if ($goOffline) {
                //include ABSPATH . '/config.php';
                include ABSPATH . '/closed.php';
                exit();
            }
        }
        $this->_table_prefix = $table_prefix;
        $this->_ticker = 0;
        $this->_log = array();
    }
    /**
     * @param int
     */
    function debug( $level ) {
        $this->_debug = intval( $level );
    }
    /**
     * @return int The error number for the most recent query
     */
    function getErrorNum() {
        return $this->_errorNum;
    }
    /**
    * @return string The error message for the most recent query
    */
    function getErrorMsg() {
        return str_replace( array( "\n", "'" ), array( '\n', "\'" ), $this->_errorMsg );
    }

    /**
     * Get a database escaped string
     *
     * @param    string    The string to be escaped
     * @param    boolean    Optional parameter to provide extra escaping
     * @return    string
     * @access    public
     * @abstract
     */
    function getEscaped( $text, $extra = false ) {
        $string = mysqli_real_escape_string( $this->_resource, $text );
        if ($extra) {
            $string = addcslashes( $string, '%_' );
        }
        return $string;
    }

    /**
    * Get a quoted database escaped string
    *
    * @param    string    A string
    * @param    boolean    Default true to escape string, false to leave the string unchanged
    * @return    string
    * @access public
    */
    function Quote( $text, $escaped = true )
    {
        return '\''.($escaped ? $this->getEscaped( $text ) : $text).'\'';
    }

    /**
     * Quote an identifier name (field, table, etc)
     * @param string The name
     * @return string The quoted name
     */
    function NameQuote( $s ) {
        $q = $this->_nameQuote;
        if (strlen( $q ) == 1) {
            return $q . $s . $q;
        } else {
            return $q[0] . $s . $q[1];
        }
    }
    /**
     * @return string The database prefix
     */
    function getPrefix() {
        return $this->_table_prefix;
    }
    /**
     * @return string Quoted null/zero date string
     */
    function getNullDate() {
        return $this->_nullDate;
    }
    /**
    * Sets the SQL query string for later execution.
    *
    * This function replaces a string identifier <var>$prefix</var> with the
    * string held is the <var>_table_prefix</var> class variable.
    *
    * @param string The SQL query
    * @param string The offset to start selection
    * @param string The number of results to return
    * @param string The common table prefix
    */
    function setQuery( $sql, $offset = 0, $limit = 0, $prefix='#__' ) {
        $this->_sql = $this->replacePrefix( $sql, $prefix );
        $this->_limit = intval( $limit );
        $this->_offset = intval( $offset );
    }

    /**
     * This function replaces a string identifier <var>$prefix</var> with the
     * string held is the <var>_table_prefix</var> class variable.
     *
     * @param string The SQL query
     * @param string The common table prefix
     * @author thede, David McKinnis
     */
    function replacePrefix( $sql, $prefix='#__' ) {
        $sql = trim( $sql );

        $escaped = false;
        $quoteChar = '';

        $n = strlen( $sql );

        $startPos = 0;
        $literal = '';
        while ($startPos < $n) {
            $ip = strpos($sql, $prefix, $startPos);
            if ($ip === false) {
                break;
            }

            $j = strpos( $sql, "'", $startPos );
            $k = strpos( $sql, '"', $startPos );
            if (($k !== FALSE) && (($k < $j) || ($j === FALSE))) {
                $quoteChar    = '"';
                $j            = $k;
            } else {
                $quoteChar    = "'";
            }

            if ($j === false) {
                $j = $n;
            }

            $literal .= str_replace( $prefix, $this->_table_prefix, substr( $sql, $startPos, $j - $startPos ) );
            $startPos = $j;

            $j = $startPos + 1;

            if ($j >= $n) {
                break;
            }

            // quote comes first, find end of quote
            while (TRUE) {
                $k = strpos( $sql, $quoteChar, $j );
                $escaped = false;
                if ($k === false) {
                    break;
                }
                $l = $k - 1;
                while ($l >= 0 && $sql[$l] == '\\') {
                    $l--;
                    $escaped = !$escaped;
                }
                if ($escaped) {
                    $j    = $k+1;
                    continue;
                }
                break;
            }
            if ($k === FALSE) {
                // error in the query - no end quote; ignore it
                break;
            }
            $literal .= substr( $sql, $startPos, $k - $startPos + 1 );
            $startPos = $k+1;
        }
        if ($startPos < $n) {
            $literal .= substr( $sql, $startPos, $n - $startPos );
        }
        return $literal;
    }
    /**
    * @return string The current value of the internal SQL vairable
    */
    function getQuery() {
        return "<pre>" . htmlspecialchars( $this->_sql ) . "</pre>";
    }
    /**
    * Execute the query
    * @return mixed A database resource if successful, FALSE if not.
    */
    function query() {
        if ($this->_limit > 0 || $this->_offset > 0) {
            $this->_sql .= "\nLIMIT $this->_offset, $this->_limit";
        }
        if ($this->_debug) {
            $this->_ticker++;
              $this->_log[] = $this->_sql;
        }
        $this->_errorNum = 0;
        $this->_errorMsg = '';
        $this->_cursor = mysqli_query( $this->_resource, $this->_sql );
        if (!$this->_cursor) {
            $this->_errorNum = mysqli_errno( $this->_resource );
            $this->_errorMsg = mysqli_error( $this->_resource ) . " SQL=$this->_sql";
            if ($this->_debug) {
                trigger_error( mysqli_error( $this->_resource ), E_USER_NOTICE );
                //echo "<pre>" . $this->_sql . "</pre>\n";
                if (function_exists( 'debug_backtrace' )) {
                    foreach( debug_backtrace() as $back) {
                        if (@$back['file']) {
                            echo '<br />'.$back['file'].':'.$back['line'];
                        }
                    }
                }
            }
            return false;
        }
        return $this->_cursor;
    }

    /**
     * @return int The number of affected rows in the previous operation
     */
    function getAffectedRows() {
        return mysqli_affected_rows( $this->_resource );
    }

    function query_batch( $abort_on_error=true, $p_transaction_safe = false) {
        $this->_errorNum = 0;
        $this->_errorMsg = '';
        if ($p_transaction_safe) {
            $si = mysqli_get_server_info();
            preg_match_all( "/(\d+)\.(\d+)\.(\d+)/i", $si, $m );
            if ($m[1] >= 4) {
                $this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
            } else if ($m[2] >= 23 && $m[3] >= 19) {
                $this->_sql = 'BEGIN WORK;' . $this->_sql . '; COMMIT;';
            } else if ($m[2] >= 23 && $m[3] >= 17) {
                $this->_sql = 'BEGIN;' . $this->_sql . '; COMMIT;';
            }
        }
        $query_split = preg_split ("/[;]+/", $this->_sql);
        $error = 0;
        foreach ($query_split as $command_line) {
            $command_line = trim( $command_line );
            if ($command_line != '') {
                $this->_cursor = mysqli_query( $command_line, $this->_resource );
                if (!$this->_cursor) {
                    $error = 1;
                    $this->_errorNum .= mysqli_errno( $this->_resource ) . ' ';
                    $this->_errorMsg .= mysqli_error( $this->_resource )." SQL=$command_line <br />";
                    if ($abort_on_error) {
                        return $this->_cursor;
                    }
                }
            }
        }
        return $error ? false : true;
    }

    /**
    * Diagnostic function
    */
    function explain() {
        $temp = $this->_sql;
        $this->_sql = "EXPLAIN $this->_sql";
        $this->query();

        if (!($cur = $this->query())) {
            return null;
        }
        $first = true;

        $buf = "<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" bgcolor=\"#000000\" align=\"center\">";
        $buf .= $this->getQuery();
        while ($row = mysqli_fetch_assoc( $cur )) {
            if ($first) {
                $buf .= "<tr>";
                foreach ($row as $k=>$v) {
                    $buf .= "<th bgcolor=\"#ffffff\">$k</th>";
                }
                $buf .= "</tr>";
                $first = false;
            }
            $buf .= "<tr>";
            foreach ($row as $k=>$v) {
                $buf .= "<td bgcolor=\"#ffffff\">$v</td>";
            }
            $buf .= "</tr>";
        }
        $buf .= "</table><br />&nbsp;";
        mysqli_free_result( $cur );

        $this->_sql = $temp;

        return "<div style=\"background-color:#FFFFCC\" align=\"left\">$buf</div>";
    }
    /**
    * @return int The number of rows returned from the most recent query.
    */
    function getNumRows( $cur=null ) {
        return mysqli_num_rows( $cur ? $cur : $this->_cursor );
    }

    /**
    * This method loads the first field of the first row returned by the query.
    *
    * @return The value returned in the query or null if the query failed.
    */
    function loadResult() {
        if (!($cur = $this->query())) {
            return null;
        }
        $ret = null;
        if ($row = mysqli_fetch_row( $cur )) {
            $ret = $row[0];
        }
        mysqli_free_result( $cur );
        return $ret;
    }
    /**
    * Load an array of single field results into an array
    */
    function loadResultArray($numinarray = 0) {
        if (!($cur = $this->query())) {
            return null;
        }
        $array = array();
        while ($row = mysqli_fetch_row( $cur )) {
            $array[] = $row[$numinarray];
        }
        mysqli_free_result( $cur );
        return $array;
    }
    /**
    * Load a assoc list of database rows
    * @param string The field name of a primary key
    * @return array If <var>key</var> is empty as sequential list of returned records.
    */
    function loadAssocList( $key='' ) {
        if (!($cur = $this->query())) {
            return null;
        }
        $array = array();
        while ($row = mysqli_fetch_assoc( $cur )) {
            if ($key) {
                $array[$row[$key]] = $row;
            } else {
                $array[] = $row;
            }
        }
        mysqli_free_result( $cur );
        return $array;
    }
    /**
    * This global function loads the first row of a query into an object
    *
    * If an object is passed to this function, the returned row is bound to the existing elements of <var>object</var>.
    * If <var>object</var> has a value of null, then all of the returned query fields returned in the object.
    * @param string The SQL query
    * @param object The address of variable
    */
    function loadObject( &$object ) {
        if ($object != null) {
            if (!($cur = $this->query())) {
                return false;
            }
            if ($array = mysqli_fetch_assoc( $cur )) {
                mysqli_free_result( $cur );
                BindArrayToObject( $array, $object, null, null, false );
                return true;
            } else {
                return false;
            }
        } else {
            if ($cur = $this->query()) {
                if ($object = mysqli_fetch_object( $cur )) {
                    mysqli_free_result( $cur );
                    return true;
                } else {
                    $object = null;
                    return false;
                }
            } else {
                return false;
            }
        }
    }
    /**
    * Load a list of database objects
    * @param string The field name of a primary key
    * @return array If <var>key</var> is empty as sequential list of returned records.
    * If <var>key</var> is not empty then the returned array is indexed by the value
    * the database key.  Returns <var>null</var> if the query fails.
    */
    function loadObjectList( $key='' ) {
        if (!($cur = $this->query())) {
            return null;
        }
        $array = array();
        while ($row = mysqli_fetch_object( $cur )) {
            if ($key) {
                $array[$row->$key] = $row;
            } else {
                $array[] = $row;
            }
        }
        mysqli_free_result( $cur );
        return $array;
    }
    /**
    * @return The first row of the query.
    */
    function loadRow() {
        if (!($cur = $this->query())) {
            return null;
        }
        $ret = null;
        if ($row = mysqli_fetch_row( $cur )) {
            $ret = $row;
        }
        mysqli_free_result( $cur );
        return $ret;
    }
    /**
    * Load a list of database rows (numeric column indexing)
    * @param string The field name of a primary key
    * @return array If <var>key</var> is empty as sequential list of returned records.
    * If <var>key</var> is not empty then the returned array is indexed by the value
    * the database key.  Returns <var>null</var> if the query fails.
    */
    function loadRowList( $key='' ) {
        if (!($cur = $this->query())) {
            return null;
        }
        $array = array();
        while ($row = mysqli_fetch_row( $cur )) {
            if ($key) {
                $array[$row[$key]] = $row;
            } else {
                $array[] = $row;
            }
        }
        mysqli_free_result( $cur );
        return $array;
    }
    /**
    * Document::db_insertObject()
    *
    * { Description }
    *
    * @param string $table This is expected to be a valid (and safe!) table name
    * @param [type] $keyName
    * @param [type] $verbose
    */
    function insertObject( $table, &$object, $keyName = NULL, $verbose=false ) {
        $fmtsql = "INSERT INTO $table ( %s ) VALUES ( %s ) ";
        $fields = array();
        foreach (get_object_vars( $object ) as $k => $v) {
            if (is_array($v) or is_object($v) or $v === NULL) {
                continue;
            }
            if ($k[0] == '_') { // internal field
                continue;
            }
            $fields[] = $this->NameQuote( $k );
            $values[] = $this->Quote( $v );
        }
        $this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
        ($verbose) && print "$sql<br />\n";
        if (!$this->query()) {
            return false;
        }
        $id = mysqli_insert_id( $this->_resource );
        ($verbose) && print "id=[$id]<br />\n";
        if ($keyName && $id) {
            $object->$keyName = $id;
        }
        return true;
    }

    /**
    * Document::db_updateObject()
    *
    * { Description }
    *
    * @param string $table This is expected to be a valid (and safe!) table name
    * @param [type] $updateNulls
    */
    function updateObject( $table, &$object, $keyName, $updateNulls=true ) {
        $fmtsql = "UPDATE $table SET %s WHERE %s";
        $tmp = array();
        foreach (get_object_vars( $object ) as $k => $v) {
            if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
                continue;
            }
            if( $k == $keyName ) { // PK not to be updated
                $where = $keyName . '=' . $this->Quote( $v );
                continue;
            }
            if ($v === NULL && !$updateNulls) {
                continue;
            }
            if( $v == '' ) {
                $val = "''";
            } else {
                $val = $this->Quote( $v );
            }
            $tmp[] = $this->NameQuote( $k ) . '=' . $val;
        }
        $this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
        return $this->query();
    }

    /**
    * @param boolean If TRUE, displays the last SQL statement sent to the database
    * @return string A standised error message
    */
    function stderr( $showSQL = false ) {
        return "DB function failed with error number $this->_errorNum"
        ."<br /><font color=\"red\">$this->_errorMsg</font>"
        .($showSQL ? "<br />SQL = <pre>$this->_sql</pre>" : '');
    }

    function insertid() {
        return mysqli_insert_id( $this->_resource );
    }

    function getVersion() {
        return mysqli_get_server_info( $this->_resource );
    }

    /**
     * @return array A list of all the tables in the database
     */
    function getTableList() {
        $this->setQuery( 'SHOW TABLES' );
        return $this->loadResultArray();
    }
    /**
     * @param array A list of valid (and safe!) table names
     * @return array A list the create SQL for the tables
     */
    function getTableCreate( $tables ) {
        $result = array();

        foreach ($tables as $tblval) {
            $this->setQuery( 'SHOW CREATE table ' . $this->getEscaped( $tblval ) );
            $rows = $this->loadRowList();
            foreach ($rows as $row) {
                $result[$tblval] = $row[1];
            }
        }

        return $result;
    }
    /**
     * @param array A list of valid (and safe!) table names
     * @return array An array of fields by table
     */
    function getTableFields( $tables ) {
        $result = array();

        foreach ($tables as $tblval) {
            $this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
            $fields = $this->loadObjectList();
            foreach ($fields as $field) {
                $result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
            }
        }

        return $result;
    }

    /**
    * Fudge method for ADOdb compatibility
    */
    function GenID( $foo1=null, $foo2=null ) {
        return '0';
    }
}

/**
* mosDBTable Abstract Class.
* @abstract
* @package Joomla
* @subpackage Database
*
* Parent classes to all database derived objects.  Customisation will generally
* not involve tampering with this object.
* @package Joomla
* @author Andrew Eddie <eddieajau@users.sourceforge.net
*/
class DBTable {
    /** @var string Name of the table in the db schema relating to child class */
    var $_tbl         = '';
    /** @var string Name of the primary key field in the table */
    var $_tbl_key     = '';
    /** @var string Error message */
    var $_error     = '';
    /** @var mosDatabase Database connector */
    var $_db         = null;

    /**
    *    Object constructor to set table and key field
    *
    *    Can be overloaded/supplemented by the child class
    *    @param string $table name of the table in the db schema relating to child class
    *    @param string $key name of the primary key field in the table
    */
    function __construct( $table, $key, $db ) {
        $this->set('_tbl', $table);
        $this->set('_tbl_key', $key);
        $this->set('_db', $db);
    }

    /**
     * Returns an array of public properties
     * @return array
     */
    function getPublicProperties() {
        static $cache = null;
        if (is_null( $cache )) {
            $cache = array();
            foreach (get_class_vars( get_class( $this ) ) as $key=>$val) {
                if (substr( $key, 0, 1 ) != '_') {
                    $cache[] = $key;
                }
            }
        }
        return $cache;
    }
    /**
     * Filters public properties
     * @access protected
     * @param array List of fields to ignore
     */
    function filter( $ignoreList=null ) {
        $ignore = is_array( $ignoreList );

        $iFilter = new InputFilter();
        foreach ($this->getPublicProperties() as $k) {
            if ($ignore && in_array( $k, $ignoreList ) ) {
                continue;
            }
            $this->$k = $iFilter->process( $this->$k );
        }
    }
    /**
     *    @return string Returns the error message
     */
    function getError() {
        return $this->_error;
    }
    /**
    * Gets the value of the class variable
    * @param string The name of the class variable
    * @return mixed The value of the class var (or null if no var of that name exists)
    */
    function get( $_property ) {
        if(isset( $this->$_property )) {
            return $this->$_property;
        } else {
            return null;
        }
    }

    /**
    * Set the value of the class variable
    * @param string The name of the class variable
    * @param mixed The value to assign to the variable
    */
    function set( $_property, $_value ) {
        $this->$_property = $_value;
    }

    /**
     * Resets public properties
     * @param mixed The value to set all properties to, default is null
     */
    function reset( $value=null ) {
        $keys = $this->getPublicProperties();
        foreach ($keys as $k) {
            $this->$k = $value;
        }
    }
    /**
    *    binds a named array/hash to this object
    *
    *    can be overloaded/supplemented by the child class
    *    @param array $hash named array
    *    @return null|string    null is operation was satisfactory, otherwise returns an error
    */
    function bind( $array, $ignore='' ) {
        if (!is_array( $array )) {
            $this->_error = strtolower(get_class( $this ))."::bind failed.";
            return false;
        } else {
            return BindArrayToObject( $array, $this, $ignore );
        }
    }

    /**
    *    binds an array/hash to this object
    *    @param int $oid optional argument, if not specifed then the value of current key is used
    *    @return any result from the database operation
    */
    function load( $oid=null ) {
        $k = $this->_tbl_key;

        if ($oid !== null) {
            $this->$k = $oid;
        }

        $oid = $this->$k;

        if ($oid === null) {
            return false;
        }
        //Note: Prior to PHP 4.2.0, Uninitialized class variables will not be reported by get_class_vars().
        $class_vars = get_class_vars(get_class($this));
        foreach ($class_vars as $name => $value) {
            if (($name != $k) and ($name != "_db") and ($name != "_tbl") and ($name != "_tbl_key")) {
                $this->$name = $value;
            }
        }

        $this->reset();

        $query = "SELECT *"
        . "\n FROM $this->_tbl"
        . "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $oid )
        ;
        $this->_db->setQuery( $query );

        return $this->_db->loadObject( $this );
    }

    /**
    *    generic check method
    *
    *    can be overloaded/supplemented by the child class
    *    @return boolean True if the object is ok
    */
    function check() {
        return true;
    }

    /**
    * Inserts a new row if id is zero or updates an existing row in the database table
    *
    * Can be overloaded/supplemented by the child class
    * @param boolean If false, null object variables are not updated
    * @return null|string null if successful otherwise returns and error message
    */
    function store( $updateNulls=false ) {
        $k = $this->_tbl_key;

        if ($this->$k) {
            $ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
        } else {
            $ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
        }
        if( !$ret ) {
            $this->_error = strtolower(get_class( $this ))."::store failed <br />" . $this->_db->getErrorMsg();
            return false;
        } else {
            return true;
        }
    }
    /**
    *    Default delete method
    *
    *    can be overloaded/supplemented by the child class
    *    @return true if successful otherwise returns and error message
    */
    function delete( $oid=null ) {
        //if (!$this->canDelete( $msg )) {
        //    return $msg;
        //}

        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = intval( $oid );
        }

        $query = "DELETE FROM $this->_tbl"
        . "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $this->$k )
        ;
        $this->_db->setQuery( $query );

        if ($this->_db->query()) {
            return true;
        } else {
            $this->_error = $this->_db->getErrorMsg();
            return false;
        }
    }

    /**
     * Checks out an object
     * @param int User id
     * @param int Object id
     */
    function checkout( $user_id, $oid=null ) {
        if (!array_key_exists( 'checked_out', get_class_vars( strtolower(get_class( $this )) ) )) {
            $this->_error = "WARNING: ".strtolower(get_class( $this ))." does not support checkouts.";
            return false;
        }
        $k = $this->_tbl_key;
        if ($oid !== null) {
            $this->$k = $oid;
        }

        $time = date( 'Y-m-d H:i:s' );
        if (intval( $user_id )) {
            $user_id = intval( $user_id );
            // new way of storing editor, by id
            $query = "UPDATE $this->_tbl"
            . "\n SET checked_out = $user_id, checked_out_time = " . $this->_db->Quote( $time )
            . "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $this->$k )
            ;
            $this->_db->setQuery( $query );

            $this->checked_out = $user_id;
            $this->checked_out_time = $time;
        } else {
            $user_id = $this->_db->Quote( $user_id );
            // old way of storing editor, by name
            $query = "UPDATE $this->_tbl"
            . "\n SET checked_out = 1, checked_out_time = " . $this->_db->Quote( $time ) . ", editor = $user_id"
            . "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $this->$k )
            ;
            $this->_db->setQuery( $query );

            $this->checked_out = 1;
            $this->checked_out_time = $time;
            $this->checked_out_editor = $user_id;
        }

        return $this->_db->query();
    }
    /**
    * Generic save function
    * @param array Source array for binding to class vars
    * @param string Filter for the order updating. This is expected to be a valid (and safe!) SQL expression
    * @returns TRUE if completely successful, FALSE if partially or not succesful
    * NOTE: Filter will be deprecated in verion 1.1
    */
    function save( $source ) {
        if (!$this->bind( $source )) {
            return false;
        }
        if (!$this->check()) {
            return false;
        }
        if (!$this->store()) {
            return false;
        }
        $this->_error = '';
        return true;
    }
    /**
    * Export item list to xml
    * @param boolean Map foreign keys to text values
    */
    function toXML( $mapKeysToText=false ) {
        $xml = '<record table="' . $this->_tbl . '"';

        if ($mapKeysToText) {
            $xml .= ' mapkeystotext="true"';
        }
        $xml .= '>';
        foreach (get_object_vars( $this ) as $k => $v) {
            if (is_array($v) or is_object($v) or $v === NULL) {
                continue;
            }
            if ($k[0] == '_') { // internal field
                continue;
            }
            $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
        }
        $xml .= '</record>';

        return $xml;
    }
}
?>