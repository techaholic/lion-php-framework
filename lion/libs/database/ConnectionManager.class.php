<?php

/**
 * This class manages database connections.
 */
class __ConnectionManager {

	/**
	 * This variables caches the XML based configuration file as an object.
	 *
	 * @access protected
	 * @static
	 * @var SimpleXMLElement
	 */
	protected static $config = NULL;

	/**
	 * This constructor initializes the class.
	 *
	 * @access protected
	 */
    protected function __construct() {
        if (is_null(self::$config)) {
			$contents = file_get_contents(APP_DIR . '/config/connections.xml');
			self::$config = new SimpleXMLElement($contents);
		}
    }

    /**
     * This function returns the specified data source.
     *
     * @access public
     * @param string $id                        the id of the desired data source
     * @return array                            the data source
     */
	public function get_data_source($id = 'default') {
		$xpath = "/configuration/connections/connection[@id='{$id}']";
		$connections = self::$config->xpath($xpath);
		$source = array('id' => $id, 'type' => '', 'host' => '', 'port' => '', 'database' => '', 'username' => '', 'password' => '');
		if (!empty($connections)) {
			foreach ($connections as $connection) {
				foreach ($connection->attributes() as $key => $value) {
					$source[(string)$key] = (string)$value;
				}
			}
		}
		return (object)$source;
	}

    /**
     * This function returns a list of data sources.
     *
     * @access public
     * @return array                            a list of data sources
     */
	public function get_data_source_list() {
		$xpath = "/configuration/connections/connection/@id";
		$ids = self::$config->xpath($xpath);
		$list = array();
		if (!empty($ids)) {
			foreach ($ids as $id) {
				$list[] = (string)$id;
			}
		}
		return $list;
	}

    /**
     * This function returns a singleton instance of this class.
     *
     * @access public
     * @static
     * @return __ConnectionManager              a singleton instance of this class
     */
	public static function instance() {
		static $singleton = NULL;
		if (is_null($singleton)) {
			$singleton = new __ConnectionManager();
		}
		return $singleton;
	}

}
?>