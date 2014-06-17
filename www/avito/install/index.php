<?php

class Installer {

  private $db;
  public function __construct() {
    require_once(dirname(__FILE__) . '/../lib/PFactory.php');
    PFactory::init();
  }
  
  public function run() {
    try {
      $this->db = PFactory::getDbo();
    }
    catch (Exception $e) {
      switch ($e->getCode()) {
        case DataBaseMysql::CONNECT_ERROR:
        case DataBaseMysql::USE_DB_ERROR:
          // $config = PFactory::getConfig();
          throw new Exception("DB Connection error. Edit config.json file!");
          break;
        default:
          throw new Exception("DB Error!");
      }
    }
    
    $sql = file_get_contents(PFactory::getDir(). 'install/install.sql');
    $this->db->Query($sql);
    echo 'Ok'.PHP_EOL;
  }
  
  public function selfRun() {
    $installer = new Installer();
    $installer->run();
  }

}

Installer::selfRun();