<?php

class Parser {
  public function __construct() {
    require_once(__DIR__ . '/lib/PFactory.php');
    PFactory::init();
    
    require_once(__DIR__ . '/lib/AdNode.php');
    require_once(__DIR__ . '/lib/LinkGetter.php');
    require_once(__DIR__ . '/lib/Curl.php');
    require_once(__DIR__ . '/lib/simple_html_dom.php');
  }
  
  private $linkGetter;
  public function parse() {
    $this->curl = new cURL(true, PFactory::getDir() . "var/cook.txt");
    
    $this->linkGetter = new LinkGetter(PFactory::getConfig()->parse, $this->curl);
    $i = 0;
    while($url = $this->linkGetter->get()) {
      $i++;
      // $url = 'http://www.avito.ru/tula/predlozheniya_uslug/assenizatorskaya_mashina_263116002'; //конкретный товар.
      
      $this->AdNode = new AdNode($url, $this->curl, PFactory::getConfig()->parse, PFactory::getDbo());
      $this->AdNode->
        load()->
        save();
      
      // echo $i.' > '.$url.PHP_EOL;
      }
  }
  
  private $parser;
  public function selfRun() {
    $parser = new parser();
    $parser->parse();
  }

}

Parser::selfRun();

