<?php

class Installer {

  private $db;
  public function __construct() {
    require_once(__DIR__ . '/../lib/PFactory.php');
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
          $config = PFactory::getConfig();
          die(var_dump($config));
          break;
        throw new Exception("DB Error");
      }
    }
    
    $sql = file_get_contents(PFactory::getDir(). 'install/install.sql');
    // $this->db->Query($sql);
  }
  
  public function selfRun() {
    $installer = new Installer();
    $installer->run();
  }

}

Installer::selfRun();