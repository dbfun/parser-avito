<?php

class Curl {
  private $headers, $userAgent, $compression, $cookieFile, $proxy;
  public function __construct ($cookies = true, $cookie, $compression = 'gzip', $proxy = null) {
    $this->headers[] = 'Accept: image/png,image/*;q=0.8,*/*;q=0.5';
    $this->headers[] = 'Connection: Keep-Alive';
    $this->userAgent = array('Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
    $this->compression = $compression;
    $this->proxy = $proxy;
    $this->cookies = $cookies;
    if ($this->cookies === true) $this->setCookieFile($cookie);
  }

  public function setProxy(stdClass $proxy) {
    $this->proxy = $proxy;
  }
  
  public function setCookieFile($cookieFile) {
    if (file_exists($cookieFile)) {
      $this->cookieFile=$cookieFile;
    } else {
      fopen($cookieFile, 'wb+') || $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
      $this->cookieFile = $cookieFile;
      fclose($this->cookieFile);
    }
  }
  
  private function error($text) {
    die($text);
  }
  
  public function get($url, $theader = false, $ref) {
    shuffle($this->userAgent);
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
    if ($theader) curl_setopt($process, CURLOPT_HEADER, 1);
    else curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_USERAGENT, $this->userAgent[0]);
    curl_setopt($process, CURLOPT_REFERER, $ref);
    if ($this->cookies == true) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookieFile);
    if ($this->cookies == true) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookieFile);
    curl_setopt($process,CURLOPT_ENCODING , $this->compression);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    if (isset($this->proxy))
    {
      curl_setopt($process, CURLOPT_PROXY, $this->proxy->server);
      curl_setopt($process, CURLOPT_PROXYPORT, $this->proxy->port);
      curl_setopt($process, CURLOPT_HTTPPROXYTUNNEL, 1);
    }
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($process);

    // echo curl_error($process);
    // print_r(curl_getinfo($process));

    curl_close($process);
    return $return;
  }
}