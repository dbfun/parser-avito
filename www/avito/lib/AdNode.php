<?php
class AdNode {

  const PARSE_OK = 1, 
        PARSE_PHONE_FAIL = 2;

  private $url, $curl, $parseCase, $db;
  public function __construct($url, Curl $curl, stdClass $parseCase, DataBaseMysql $db) {
    $this->url = trim($url);
    $this->curl = $curl;
    $this->parseCase = $parseCase;
    $this->db = $db;
    $this->data = new stdClass();
  }
  
  private $itemId, $ref, $phoneUrl, $html, $data;
  public function load() {
    $data = $this->curl->get($this->url, false, false);
    
    preg_match('#<link rel="canonical" href="//www.avito.ru/(.+?)"#is', $data, $matches);
    $this->ref = "http://www.avito.ru/".$matches[1];

    preg_match('#<span id="item_id">(.+?)</span>#is', $data, $matches);
    $this->itemId = $matches[1];
    
    preg_match("#item_url = '(.+?)'.+?tem_phone = '(.+?)'#is", $data, $matches);
    $phone = $matches[2];
    $this->phoneUrl = "http://www.avito.ru/items/phone/". $this->itemId ."?pkey=".$this->decode($phone, $this->itemId);
    
    $this->html = str_get_html($data);
    
    $this->setDomData('name', '#seller strong', 0);
    $this->setDomData('place', '#map span', 0);
    $this->setDomData('service', '.link_inverted strong', 0);
    $this->setDomData('description', '#desc_text', 0);
    $this->setDomData('title', 'h1.h1', 0);
    $this->setDomData('price', '.description_price span span', 0);
    // $this->setDomData('', '', 0);
    // die(var_dump($this->data));

    return $this;
  }

  private function recognizePhone($imageFileName) {
    $imageFileNameResized = $imageFileName.'.jpg';
    $textFileName = $imageFileName.'.txt';
    shell_exec("convert -scale 510x80 '$imageFileName' '$imageFileNameResized'");
    shell_exec("cuneiform -l eng --singlecolumn -o '$textFileName' '$imageFileNameResized'");
    $_text = trim(file_get_contents($textFileName));
    unlink($imageFileNameResized);
    unlink($textFileName);
    unlink($imageFileName);
    $text = preg_replace('#[^0-9]+#', '', $_text);
    if (mb_strlen($text) == 11) {
      $this->parseStatus = self::PARSE_OK;
    }
    else {
      $this->parseStatus = self::PARSE_PHONE_FAIL;
    }
    return $text;
  }
  
  private static $counter = 0;
  public function save() {
    $img = $this->curl->get($this->phoneUrl, false, $this->ref);
    $imageFileName = PFactory::getDir() . "var/{$this->itemId}.png";
    file_put_contents($imageFileName, $img);
    
    $this->data->phone = $this->recognizePhone($imageFileName);
    
    // die(var_dump($this->data));
    
    
    
    
    $query = "INSERT IGNORE INTO `ads` (`case_name`, `url`, `item_id`, 
      `phone`, `name`, `place`, `service`, `description`, `title`, `price`, `parse_status`) 
      VALUES ('".addslashes($this->parseCase->name)."', '".addslashes($this->url)."', 
      '".addslashes($this->itemId)."', 
      '".addslashes($this->data->phone)."', 
      '".addslashes($this->data->name)."', 
      '".addslashes($this->data->place)."', 
      '".addslashes($this->data->service)."', 
      '".addslashes($this->data->description)."', 
      '".addslashes($this->data->title)."', 
      '".addslashes($this->data->price)."', 
      {$this->parseStatus})";
    
    printf("\r%3d%%", self::$counter++);
    $this->db->Query($query);
    return $this;
  }
  
  private function setDomData($name, $selector, $position) {
    $parsed = $this->html->find($selector, $position);
    $this->data->{$name} = is_object($parsed) ? $this->clearText($parsed->innertext) : null;
  }
  
  private function clearText($text) {
    return trim(strip_tags(preg_replace('#&nbsp;#i', ' ', $text)));
  }
  
  private function decode($key, $itemid) {
    // $key = '0e5ed6188cf616f6f2ad5m46164dc7f00c2a9436b40c64f24m16369a0c06f7ad4416mc6d85ea42946604m4f626fe88mf5d82f';
    // $itemid = 263116002;
    
    preg_match_all('#[0-9a-f]+#', $key, $pre);
    $pre = $pre[0];
    // echo var_dump($pre).PHP_EOL;

    $mixed = implode('', $itemid%2 == 0 ? array_reverse($pre) : $pre);
    // echo var_dump($mixed).PHP_EOL;
    
    $s = strlen($mixed);
    // echo var_dump($s).PHP_EOL;
    
    $r='';
    for($k=0; $k<$s; ++$k){
      if($k%3===0) {
        $r .= substr($mixed, $k, 1);
        }
      }
    return $r;
    }

}