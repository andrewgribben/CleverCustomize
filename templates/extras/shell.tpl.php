<?php

header('Content-type: application/json');
header("Access-Control-Allow-Origin: *");

unset($vars['body']);

$json = array();
$json['version'] = "https://jsonfeed.org/version/1";

if (empty($vars['title'])) {
  if (!empty($vars['description'])) {
    $json['title'] = implode(' ',array_slice(explode(' ', strip_tags($vars['description'])),0,10));
  } else {
    $json['title'] = 'Known site';
  }
} else {
  $json['title'] = $vars['description'];
}

if (empty($vars['base_url'])) {
  $json['home_page_url'] = $this->getCurrentURLWithoutVar('_t');
} else {
  $json['home_page_url'] = $this->getURLWithoutVar($vars['base_url'], '_t');
}

$json['feed_url'] = $this->getCurrentURL();

if (!empty(\Idno\Core\Idno::site()->config()->description)) {
  $json['description'] = \Idno\Core\Idno::site()->config()->getDescription();
}

if (!empty(\Idno\Core\Idno::site()->config()->hub)) {
  $json['hubs'] = array();
  $hub = array();
  $hub['type'] = 'WebSub';
  $hub['url'] = \Idno\Core\Idno::site()->config()->hub;
  array_push($json['hubs'], $hub);
}

// In case this isn't a feed page, find any objects
if (empty($vars['items']) && !empty($vars['object'])) {
  $vars['items'] = array($vars['object']);
}

// If we have a feed, add the items
$json['items'] = array();
if (!empty($vars['items'])) {
  foreach($vars['items'] as $item) {
    if (!($item instanceof \Idno\Common\Entity)) {
      continue;
    }
    $title = $item->getTitle();

    
    if (empty($title)) {
      if ($description = $item->getShortDescription(5)) {
        $title = $description;
        
      } else {
        $title = 'New ' . $item->getContentTypeTitle();
        
      }
    }
    $feedItem = array();


    $feedItem['title'] = strip_tags($title);
    $feedItem['url'] = $item->getSyndicationURL();
    $feedItem['id'] = $item->getUUID();
    $feedItem['date_published'] = date('c', $item->created);

    $owner = $item->getOwner();
    if (!empty($owner)) {
      $feedItem['author'] = array();
      $feedItem['author']['name'] = $item->getAuthorName();
      $feedItem['author']['url'] = $item->getAuthorURL();
      $feedItem['author']['avatar'] = $owner->getIcon();
    }

    $feedItem['_meta'] = $item->getMetadataForFeed();
    $feedItem['content_html'] = $item->draw(true);




    if ($item instanceof \IdnoPlugins\Like\Like) {
	    

      $feedItem['external_url'] = $item->getBody();
      $feedItem['url'] = $item->getURL();
      unset($feedItem['content_text']);
      	    	    
      	

      // If Repost show 
      if (!empty($item->repostof)) {
        $feedItem['title'] = '♻️ ' . $feedItem['title'];
      } 
      
      //If Like show thumbsup
      else if (!empty($item->likeof)) {
        $feedItem['title'] = '👍 ' . $feedItem['title'];
        
      } 
      
      else {
        $feedItem['title'] = '🔖 ' . $feedItem['title'];     
        
      }
    } 
    
    // SHould we perhaps look sat status for a reply?
    
    else if ($item instanceof \IdnoPlugins\Status\Reply) {
      if (strpos($item->inreplyto[0], "micro.blog") !== false) { 
        $mb_username = explode('/', $item->inreplyto[0])[3];
        $feedItem['content_html'] = '<a href="https://micro.blog/' . $mb_username . '">@' . $mb_username . '</a> ' . $item->getDescription();
        $feedItem['external_url'] = $item->inreplyto[0];
        unset($feedItem['content_text']);
      } else {
	      
	    $feedItem['external_url'] = $item->inreplyto[0]; 
	    $feedItem['content_html'] = '<a href="' . $feedItem['external_url'] . '">' . $feedItem['title'] . '</a> ';
	           
      }
    } 
    
    
    
    else if ($item instanceof \IdnoPlugins\Status\Status) {
	    

      if ($item->inreplyto) {
        if (strpos($item->inreplyto[0], "micro.blog") !== false) {
          $mb_username = explode('/', $item->inreplyto[0])[3];
          $feedItem['content_html'] = '<a href="https://micro.blog/' . $mb_username . '">@' . $mb_username . '</a> ' . $item->getDescription();
          $feedItem['external_url'] = $item->inreplyto[0];
        } else if (strpos($item->inreplyto[1], "micro.blog") !== false){
		  $mb_username = explode('/', $item->inreplyto[1])[3];
		  
          $feedItem['content_html'] = '<a href="https://micro.blog/' . $mb_username . '">@' . $mb_username . '</a><a href="'. $item->inreplyto[1] . '"  class="u-in-reply-to"></a> <a href="' . $item->inreplyto[0] . '" class="u-in-reply-to">' . $feedItem['title'] . '</a> ';
                    
          
          $feedItem['external_url'] = $item->inreplyto[0];
          
          
	    } else {
	    $feedItem['external_url'] = $item->inreplyto[0]; 
	    $feedItem['content_html'] = '<a href="' . $feedItem['external_url'] . '">' . $feedItem['content_html'] . '</a> ';
	             }


      }    
      unset($feedItem['content_text']);
      unset($feedItem['title']);
      
 
    } 
    

    
    else if ($item instanceof \IdnoPlugins\Checkin\Checkin) {
      continue;
      $feedItem['title'] = "&#x1F30E; " . $item->getTitle();
      unset($feedItem['content_text']);
      unset($feedItem['content_html']);
    } 
    
    
    
    else if ($item instanceof \IdnoPlugins\Text\Entry) {
      //$feedItem['title'] = '&#x1F4C4; <a href="' . $item->getUUID() . '">' . $item->getTitle() . '</a>';
      $feedItem['title'] = '<a href="' . $item->getUUID() . '">' . $item->getTitle() . '</a>';
    } 
    
    
    
    else if ($item instanceof \IdnoPlugins\Recipe\Recipe) {
      $feedItem['title'] = '&#x1F373; Recipe: <a href="' . $item->getUUID() . '">' . $item->getTitle() . '</a>';
    } 
    
    
    
    else if ($item instanceof \IdnoPlugins\Review\Review) {
      $stars = "";
      for ($i = 0; $i < $item->getRating(); $i++) $stars .= "&#9733;";
      $feedItem['title'] = '&#x1F914; Review: <a href="' . $item->getUUID() . '">' . $item->getTitle() . '</a> - ' . $stars;
    } 
    
    
    
    else if ($item instanceof \IdnoPlugins\Watching\Watching) {
      if ($item->getWatchType() == 'tv') {
        $feedItem['title'] = '&#x1F4FA; Watched: <a href="' . $item->mediaURL . '">' . $item->title . '</a>';
      } else if ($item->getWatchType() == 'movie') {
        $feedItem['title'] = '&#x1F3AC; Watched: <a href="' . $item->mediaURL . '">' . $item->title . '</a>';
      }
    } 
    
    
    else if ($item instanceof \IdnoPlugins\Event\RSVP) {
      $feedItem['title'] = '&#128140; I have RSVP\'d "' . $item->rsvp . '" to <a href="' . $item->inreplyto[0] . '">an event</a>. ' . $item->body; 
      $feedItem['external_url'] = $item->inreplyto[0];
      unset($feedItem['content_text']);
      unset($feedItem['content_html']);
    } 
    
    
    
    else if ($item instanceof \IdnoPlugins\Event\Event) {
      $feedItem['title'] = '&#128197; Event: <a href="' . $item->getURL() . '">' . $item->getTitle() . '</a>';
      unset($feedItem['content_text']);
      unset($feedItem['content_html']);
    } 

    if ($attachments = $item->getAttachments()) {
      $feedItem['attachments'] = array();
      foreach($attachments as $attachment) {
        $attachmentItem = array();
        $attachmentItem['url'] = $attachment['url'];
        $attachmentItem['mime_type'] = $attachment['mime-type'];
        $attachmentItem['size_in_bytes'] = $attachment['length'];
        array_push($feedItem['attachments'], $attachmentItem);
      }
    }

    if ($tags = $item->getTags()) {
      $feedItem['tags'] = $tags;
    }

    array_push($json['items'], $feedItem);
  }
}

echo json_encode($json, JSON_UNESCAPED_SLASHES);
