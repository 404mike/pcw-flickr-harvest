<?php

class Collections
{
  
  private $collections;
  private $output;
  private $apiKey;
  private $user_id;
  
  public function __construct()
  {
    $this->apiKey = '';
    $this->user_id = '';
    $this->output = array();
    $this->getCount();
    $this->createCsv($this->output);
  }
  
  public function getCount()
  {
    $countCall = $this->apiCall();

    $count = $countCall['photos']['total'];

      if ($count > 500) {        
        $num = ceil($count / 400);
        
        for ($i = 0; $i < $num; $i++) {
          $this->getPageItems( 'page' , $i + 1 );
        }
      } else {
        $this->getPageItems('page');
      }
  }

  public function getPageItems( $type , $page )
  {
    $pageItems = $this->apiCall( 'page' , $page );
    
    foreach($pageItems['photos']['photo'] as $items) {
      $this->getSingleItems('item' , $items['id']);
    }
  }


  public function getSingleItems($type , $itemId)
  {
    $item = $this->apiCall( 'item' , '' , $itemId );
    echo "Getting information for item $itemId\n";
    array_push($this->output, $item);
  }

  public function apiCall( $type = '' , $page = '' , $id = '')
  {
    if($type == 'item') {
      $params = array(
        'api_key' => $this->apiKey,
        'method' => 'flickr.photos.getInfo',
        'photo_id' => $id,
        'format' => 'php_serial'
      );
    }elseif ($type == 'single') {
      $params = array(
        'api_key' => $this->apiKey,
        'method' => 'flickr.photos.getSizes',
        'photo_id' => $id,
        'format' => 'php_serial'
      );
    }else{
      $params = array(
        'api_key' => $this->apiKey,
        'method' => 'flickr.people.getPhotos',
        'user_id' => $this->user_id,
        'format' => 'php_serial',
        'per_page' => 400,
        'page' => $page
      );
    }
    
    $encoded_params = array();
    
    foreach ($params as $k => $v) {
      $encoded_params[] = urlencode($k) . '=' . urlencode($v);
    }
    
    $url = "https://api.flickr.com/services/rest/?" . implode('&', $encoded_params);

    $aContext  = array(
      'http' => array(
        'proxy' => 'cache.llgc.org.uk:80',
        'request_fulluri' => true
      )
    );
    $cxContext = stream_context_create($aContext);
    
    $rsp = file_get_contents($url, False, $cxContext);
    
    $rsp_obj = unserialize($rsp);
    return $rsp_obj;
  }
  
  public function createCsv($collection)
  {
    $list = array(
      array(
        'UC1',
        'UC2',
        'UC3',
        'UC4',
        'UC5',
        'UC6',
        'UC7',
        'UC8',
        'UC9',
        'UC10',
        'UC11',
        'UC12',
        'UC13',
        'UC14',
        'UC15',
        'UC16',
        'UC17',
        'UC18',
        'UC19',
        'UC20',
        'UC21',
        'UC22',
        'UC23',
        'UC24',
        'UC25',
        'UC26',
        'UC27',
        'UC28',
        'UC29',
        'UC30',
        'UC31',
        'UC32',
        'UC33',
        'UC34',
        'original_image'
      ),
      
      array(
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        ''
      ),
      
      array(
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        ''
      ),
      
      
      array(
        'Image Identifier',
        'Parent ID*',
        'Page Order*',
        'Image/File Name',
        'Title EN',
        'Title CY',
        'Description EN',
        'Description CY',
        'Item type',
        'Tags EN',
        'Tags CY',
        'Date',
        'Owner',
        'Creator',
        'Website en',
        'Website cy',
        'What facet',
        'When facet',
        'Location (lat, lon)',
        'Location description en',
        'Location description cy',
        'Right Type 1',
        'Right Holder 1 EN',
        'Right Holder 1 CY',
        'Begin Date 1',
        'Right Type 2',
        'Right Holder 2 EN',
        'Right Holder 2 CY',
        'Begin Date 2',
        'Right Type 3',
        'Right Holder 3 EN',
        'Right Holder 3 CY',
        'Begin Date 3',
        'Addional rights',
        'Original Image'
      )
    );
    
    foreach ($collection as $item => $value) {
      
      $photo = $this->apiCall('item', '', $value['photo']['id']);
      $image = $this->apiCall('single', '', $value['photo']['id']);
      
      $image_arr = $image['sizes']['size'];
      
      $image_location        = end($image_arr);
      $image_location_source = $image_location['source'];
      $image_location_arr    = explode('/', $image_location_source);
      $imageFileName         = end($image_location_arr);
      
      $user        = $value['photo']['owner']['realname'];
      $title       = $value['photo']['title']['_content'];
      $description = $value['photo']['description']['_content'];
      $date        = $value['photo']['dates']['taken'];
      $date        = substr($date, 0, 10);

      $fullDate = explode('-', $date);
      $dateFacet = substr_replace($fullDate[0] , '0' , -1 , 4);  

      $dateFacetArr = [
        '17' => '1800',
        '18' => '1810',
        '19' => '1820',
        '20' => '1830',
        '21' => '1840',
        '22' => '1850',
        '23' => '1860',
        '24' => '1870',
        '25' => '1880',
        '26' => '1890',
        '28' => '1900',
        '29' => '1910',
        '30' => '1920',
        '31' => '1930',
        '32' => '1940',
        '33' => '1950',
        '34' => '1960',
        '35' => '1970',
        '36' => '1980',
        '37' => '1990',
        '39' => '2000',
        '40' => '2010'
      ];

      $dateFacetFinal = array_search($dateFacet , $dateFacetArr);
      
      $url = $photo['photo']['urls']['url'][0]['_content'];

      $itemTag = '';
      foreach($value['photo']['tags']['tag'] as $tag) {
        $itemTag .= $tag['raw'] . ',';
      }

      // remove trailing comma
      rtrim($itemTag , ",");
      
      array_push($list, array(
        $value['photo']['id'], // Image Identifier
        '', // Parent ID*
        '', // Page Order*
        $imageFileName, // Image/File Name
        $title, // Title EN
        $title, // Title CY
        $description, // Description EN
        $description, // Description CY
        '', // Item type
        $itemTag, // Tags EN
        '', // Tags CY
        $date, // Date
        $user, // Owner
        $user, // Creator
        $url, // Website en
        $url, // Website cy
        '', // What facet
        $dateFacetFinal, // When facet
        '', // Location (lat, lon)
        '', // Location description en
        '', // Location description cy
        '', // Right Type 1
        '', // Right Holder 1 EN
        '', // Right Holder 1 CY
        '', // Begin Date 1
        '', // Right Type 2
        '', // Right Holder 2 EN
        '', // Right Holder 2 CY
        '', // Begin Date 2
        '', // Right Type 3
        '', // Right Holder 3 EN
        '', // Right Holder 3 CY
        '', // Begin Date 3
        '', // Addional rights
        $image_location_source
      ));
    }
    
    $title = $this->user_id;
    $title = strtolower($title);
    $title = str_replace(array(
      '/',
      ',',
      '@',
      '-'
    ), '', $title);
    $title = str_replace(' ', '_', $title);
    
    $fp = fopen($title . '.csv', 'w');
    
    foreach ($list as $fields) {
      fputcsv($fp, $fields);
    }
    
    fclose($fp);    
  }
  
}

new Collections();
