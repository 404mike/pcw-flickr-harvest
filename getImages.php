<?php

class GetImages
{
  
  private $csvFiles;
  private $filepath = "/home/mij/www/flickr/images/";
  
  public function __construct()
  {
    $this->getCsv();
    
    foreach ($this->csvFiles as $csv) {
      $this->getImages($csv);
    }
  }
  
  public function getCsv()
  {
    $files = glob("*.csv");
    foreach ($files as $filepath) {
      if ($handle = fopen($filepath, "r")) {
        $this->csvFiles[] = $filepath;
      }
    }
  }
  
  public function getImages($csv)
  {
    $dirName = str_replace('.csv', '', $csv);
    mkdir($this->filepath . $dirName, 0700);
    mkdir($this->filepath . $dirName . "/master", 0700);
    mkdir($this->filepath . $dirName . "/web_ready", 0700);
    
    $filename = $csv;
    
    if (!file_exists($filename) || !is_readable($filename))
      return FALSE;
    
    $header = NULL;
    $data   = array();
    if (($handle = fopen($filename, 'r')) !== FALSE) {
      while (($row = fgetcsv($handle, 10000000, ',')) !== FALSE) {
        if (!$header)
          $header = $row;
        else
          @$data[] = array_combine($header, $row);
      }
      fclose($handle);
    }
    
    array_splice($data, 0, 3);
    
    foreach ($data as $d) {
      
      $url = $d['UC4'];
      
      $filenameArr   = explode('/', $d['UC4']);
      $imagefilename = end($filenameArr);
      
      $master = $this->filepath . $dirName . "/master/" . $imagefilename;
      $web    = $this->filepath . $dirName . "/web_ready/" . $imagefilename;
      
      $aContext  = array(
        'http' => array(
          'proxy' => 'cache.llgc.org.uk:80',
          'request_fulluri' => true
        )
      );
      $cxContext = stream_context_create($aContext);
      
      file_put_contents($web, file_get_contents($url, False, $cxContext));
      file_put_contents($master, file_get_contents($url, False, $cxContext));
      
      echo "\n$d[UC4]";
    }
  }
  
}

new GetImages();