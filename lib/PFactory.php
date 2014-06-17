<?php

abstract class PFactory {

  /**
   * Init common classes
   *
   */
   
  private static $dir, $config;
  public static function init()
  {
    self::$dir = __DIR__ . '/../';
    require_once(self::$dir . '/lib/DataBaseMysql.php');
    if (!file_exists(self::$dir.'config.json')) throw new Exception("No config file 'config.json'! Copy 'config_dist.json' to 'config.json'");
    self::$config = json_decode(file_get_contents(self::$dir.'config.json'));
    if (!is_object(self::$config)) throw new Exception("Invalid JSON config file 'config.json'! Compare with file 'config_dist.json'");
    if (!isset(self::$config->mysql, self::$config->parse)) throw new Exception("Invalid JSON config file 'config.json'! Compare with file 'config_dist.json'");
  }
  
  /**
	 * Get library directory.
	 *
	 */
  
  public static function getDir()
  {
    return self::$dir;
  }  
  
  /**
	 * Get library directory.
	 *
	 */
  
  public static function getConfig()
  {
    return self::$config;
  }

  /**
	 * Get a database object.
	 *
	 */

  public static $database = null;
	public static function getDbo()
	{
		if (!self::$database)
		{
			self::$database = DataBaseMysql::getDBO(self::$config->mysql);
		}

		return self::$database;
	}
  
}

