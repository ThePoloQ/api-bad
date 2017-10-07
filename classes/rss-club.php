<?php

include_once(__DIR__.'/simple_html_dom.php');

class ActuClub  {
  private $PAGESITE_URL = "http://www.badmintonhautsdefrance.fr/category/responsables-clubs/";
  private $RSS_TITLE = 'Actu bad Clubs - Haut de France';
  private $RSS_LINK = 'https://poloq.eu/ffbad/actu-club';
  private $RSS_DESC = 'Actu bad Clubs - Haut de France';
  private $RSS_LANG = 'fr';

  private $CLASS_NEWPOST = '.post-wrap';
  private $CLASS_TITLE = 'h2.title';
  private $CLASS_CONTENT = '.entry';
  private $CLASS_AUTHOR = '.meta_author';
  private $CLASS_DATE = '.meta_date';
  private $CLASS_LINK = 'a.readmore';
  private $html_content;
  
  public function __construct($url,$class_newpost,$class_title,$class_content,$class_author,$class_date,$class_link){
    (isset($url)) ? $this->PAGESITE_URL = $url : null ;
    (isset($class_newpost)) ? $this->CLASS_NEWPOST = $class_newpost : null ;
    (isset($class_title)) ? $this->CLASS_TITLE = $class_title : null ;
    (isset($class_content)) ? $this->CLASS_CONTENT = $class_content : null ;
    (isset($class_author)) ? $this->CLASS_AUTHOR = $class_author : null ;
    (isset($class_date)) ? $this->CLASS_DATE = $class_date : null ;
    (isset($class_link)) ? $this->CLASS_LINK = $class_link : null ;
    
    $this->html_content = file_get_html($this->PAGESITE_URL);
    
  }
  
  public function getPosts() {
    foreach($this->html_content->find( $this->CLASS_NEWPOST) as $post) {

        $item['title'] = trim($post->find($this->CLASS_TITLE, 0)->plaintext);
        $item['content'] = trim($post->find($this->CLASS_CONTENT, 0)->innertext);
        $item['author'] = trim($post->find($this->CLASS_AUTHOR, 0)->plaintext);
        $item['date'] = trim($post->find($this->CLASS_DATE, 0)->plaintext);
        $item['link'] = trim($post->find($this->CLASS_LINK, 0)->href);        

        $posts[] = $item;
    }
    
    return $posts;
  }
  
  public function toRss($items){
    
    if (!isset($items) || count($items) < 1) return "";
    
    $html = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $html .= "<rss version=\"2.0\">\n";
    $html .= "    <channel>\n";
    $html .= "        <title>".$this->RSS_TITLE."</title>\n";
    $html .= "        <link>".$this->RSS_LINK."</link>\n";
    $html .= "        <description>".$this->RSS_DESC."</description>\n";
    $html .= "        <language>".$this->RSS_LANG."</language>\n";
    $html .= "        <copyright>Copyright (C) 2017 PoloQ</copyright>\n";
    
    foreach ($items as $item){
      $html .= "        <item>\n";
      $html .= "            <title>".$item['title']."</title>\n";
      $html .= "            <description><![CDATA[".html_entity_decode($item['content'])."]]></description>\n";
      $html .= "            <link>".htmlentities($item['link'], ENT_QUOTES, 'UTF-8')."</link>\n";
      $html .= "            <author>".$item['author']."</author>\n";
      //$html .= "            <pubDate>".$item['date']."</pubDate>\n";
      $html .= "        </item>\n";
    }
    
    $html .= "    </channel>\n";
    $html .= "</rss>\n";
   
    return $html; 
  }
  
}
