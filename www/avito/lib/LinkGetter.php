<?php
class LinkGetter {

  private $parseCase, $curl, $page = 1, $baseHost, $baseUrl, $baseQuery = array();
  public function __construct(stdClass $parseCase, Curl $curl) {
    $this->parseCase = $parseCase;
    $this->curl = $curl;
    
    $urlParts = parse_url($this->parseCase->url);
    $this->baseHost = (isset($urlParts['scheme']) ? $urlParts['scheme'].'://' : null) . $urlParts['host'];
    $this->baseUrl = $this->baseHost . $urlParts['path'];
    if (isset($urlParts['query'])) {
      parse_str($urlParts['query'], $this->baseQuery);
    }
  }
  
  private $pageUrls = array(), $urlIndex = 0;
  public function get() {
    if ($this->hasNext()) return $this->getNext();
    if ($this->getNextPage() && $this->hasNext()) return $this->getNext();
    return false;
  }
  
  private function hasNext() {
    return isset($this->pageUrls[$this->urlIndex]);
  }
  
  private function getNext() {
    $ret = $this->pageUrls[$this->urlIndex];
    $this->urlIndex++;
    return $ret;
  }

  private function getNextPage() {
    $this->pageUrls = array();
    $this->urlIndex = 0;
    
    if ($this->page > $this->parseCase->pages) return false;
    $query = http_build_query(
      array_merge(
        $this->baseQuery, 
        $this->page == 1 ? array() : array('p' => $this->page)
        )
      );
    
    $url = $this->baseUrl . ($query ? '?'.$query : null);
    
    $data = $this->curl->get($url, false, false);

    $this->page++;
    
    $html = str_get_html($data);
    foreach($html->find('h3.title a') as $dom) {
      $this->pageUrls[] = $this->baseHost . $dom->href;
    }
    return true;
  }

}