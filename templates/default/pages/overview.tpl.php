<div class="container">

  <div class="overview">

<div class="overview-content col-xs-12 col-md-6 col-md-push-3">
                  <div  class="overview-articles row">
<?php
$count = 0;
foreach ($posts as $post) {
  if (++$count == 2) break;
?>

  <div class="idno-object idno-content"> 
  <div class=" idno-entry">
  <?= $post->draw() ?>
  </div>
 <a href="/pages/events">More Articles > </a>
 </div>
<?php
}
?>

  </div>
  </div>

      <!-- content -->
      <div class="overview-aside col-xs-4 col-md-3 col-md-pull-6">

            <!-- photos -->
            <div class="overview-photos">
            <h2>
            <a href="/photos" >
            Photos 
            </a>
            </h2>
<?php
$count = 0;
foreach ($photos as $photo) {
  $thumbs = [];
  if ($photo['body'] != null) {
    array_push($thumbs, strip_tags($photo['body'], '<img>'));
  } else if ($photo['body'] != null) {
    foreach ($photo['body'] as $thumb) {
      array_push($thumbs, $thumb['url']);
    }
  } else {
    continue;
  }
  foreach ($thumbs as $thumb) {
    $src = (string) reset(simplexml_import_dom(DOMDocument::loadHTML($thumb))->xpath("//img/@src"));

/* print_r($src); */

?>
          <div class="side-thumb">
                <a href="<?= $photo->getURL() ?>">
             <img alt="<?= $photo['title'] ?>" src="<?= $src ?>" alt=""> 
              </a>

              </div><?php
  }

}
?>
      </div>



  </div>

<div class="overview-self col-xs-8 col-md-3">

<?php

    if (!empty($vars['contentTypes'])) {

        if (\Idno\Core\Idno::site()->canWrite()) {
            echo $this->draw('content/create');
            if (!empty(\Idno\Core\Idno::site()->session()->currentUser()->robot_state)) {
                echo $this->draw('robot/wizard');
            }
        }

    } else {

        echo $this->draw('pages/home/blurb');

    }

?>
        <div class="overview-microblog">
<div class="">
      <a href="/content/statusupdates/"><h2>Social</h2></a>
  </div>
            <?php
            foreach ($statuses as $status) {
            ?>
                <div class="status">
                <div class="content">
<?php
                    if (!empty($status->inreplyto)) {
                    ?>
                        <?php
                        if (is_array($status->inreplyto)) {
                        ?>
                        <a href="<?= $status->inreplyto[0] ?>"><i class="fa fa-reply"></i> </a>
                        <?php
                        } else {
                        ?>
                        <a href="<?= $status->inreplyto ?>"><i class="fa fa-reply"></i> </a>
                        <?php
                        }
                        ?> 
                    <?php
                    }
                    ?>
                    <?= $icon ?><?= $status->getBody() ?></div>
                    <a class="u-url url" href="<?= $status->getURL() ?>">
                        <time class="dt-published" datetime="<?= date(DATE_ISO8601, $status->created) ?>"><?= strftime('%d %b %Y', $status->created) ?></time> 
                    </a>
                </div>
            <?php
            }
            ?>
        </div>
  <!-- interactions -->
  <div class="overview-interactions">
 <div class="">
    <a href="/content/likes/reposts/rsvp/"> <h2>Shared</h2>
</a>  </div>
<?php
foreach ($interactions as $interaction) {
  $class = '';
  $icon = '';
  if (!empty($interaction->likeof)) {
    $class = 'u-like-of';
    $icon = '<i class="fa fa-thumbs-up"></i>';
    $intText = $interaction->description;
    $intLink = $interaction->likeof;
  } elseif (!empty($interaction->repostof)) {
    $class = 'u-repost-of';
    $icon = '<i class="fa fa-retweet"></i>';
    $intText = $interaction->description;
    $intLink = $interaction->repostof;
  } elseif (!empty($interaction->rsvp)) {
    $class = 'u-repost-of';
    $icon = '<i class="fa fa-calendar"></i>';
    $intText = $interaction->body;
    $intLink = $interaction->inreplyto;
  }else {
    $class = 'u-bookmark-of';
    $icon = '<i class="fa fa-bookmark"></i>';
    $intText = $interaction->description;
    $intLink = $interaction->bookmarkof;

  }
?>
  <div class="interaction">
  <?= $icon ?>
  <a href="<?= $intLink ?>" class="<?= $class ?> p-name" target="_blank">
  <?= htmlentities(strip_tags( $intText)) ?>
  </a>
  </div>
<?php
}
?>
  </div>


  <!-- checkins -->
  <div class="overview-checkins">
 <a href="/content/locations/"> <h2>Places</h2>
 </a><?php
foreach ($checkins as $checkin) {
?>
  <div class="checkin">
  <a href="<?= $checkin->getURL() ?>">
  <i class="fa fa-map-pin"></i><?= $checkin->placename ?>
  </a>
  </div>
<?php
}
?>
  </div>
</div>
</div>
</div>
