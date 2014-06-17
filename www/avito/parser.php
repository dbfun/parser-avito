#!/usr/bin/shell php
<?php

class Parser {
  public function __construct() {
    require_once(dirname(__FILE__) . '/lib/PFactory.php');
    PFactory::init();
    
    require_once(PFactory::getDir() . 'lib/AdNode.php');
    require_once(PFactory::getDir() . 'lib/LinkGetter.php');
    require_once(PFactory::getDir() . 'lib/Curl.php');
    require_once(PFactory::getDir() . 'lib/simple_html_dom.php');
  }
  
  private $curl, $parseCase, $linkGetter, $proxy;
  public function parse() {
    global $argv, $argc;
    if ($argc != 4) die("Usage parser.php 'case name' 'url' number-pages".PHP_EOL);
    
    $this->parseCase = new stdClass();
    $this->parseCase->name = $argv[1];
    $this->parseCase->url = $argv[2];
    $this->parseCase->pages = $argv[3];
    
    $this->curl = new cURL(true, PFactory::getDir() . "var/cook.txt");
    
    $this->proxy = new stdClass();
    $this->proxy->server = '5.206.237.26';
    $this->proxy->port = '8080';
    $this->curl->setProxy($this->proxy);
    

    $this->linkGetter = new LinkGetter($this->parseCase, $this->curl);
    
    while($url = $this->linkGetter->get()) {
      // $url = 'http://www.avito.ru/tula/predlozheniya_uslug/assenizatorskaya_mashina_263116002'; //конкретный товар.
      
      $this->AdNode = new AdNode($url, $this->curl, $this->parseCase, PFactory::getDbo());
      $this->AdNode->
        load()->
        save();
      }
  }
  
  private $parser;
  public function selfRun() {
    $parser = new parser();
    $parser->parse();
  }

}

Parser::selfRun();

