<style type="text/css">
    h1 {
        background: white;
        opacity: 0.8;
        padding: 0.5em;
        text-align: center;
        margin: 0 0 20px 0;
    }

    h2 {
        font-size: 2.0em !important;
        margin: 0;
    } 

    /* content */
    div.overview-content {
        margin-bottom: 15px;
    }    
    div.overview-content ul.picker {
        margin: 0;
        padding: 0;
        overflow: hidden;
        list-style-type: none;
    }
    div.overview-content ul.picker li a {
        color: #333;
    }
    div.overview-content ul.picker li {
        float: left;
        width: 50%;
        text-align: center;
        padding: 5px 15px;
        opacity: 0.5;
        background: white;
    }
    div.overview-content ul.picker li.active {
        opacity: 1.0;    
    }

    /* articles */
    div.overview-articles {
    }
    div.overview-articles h2 {
        background: white;
        padding: 0.5em;
        margin: 0 0 15px 0;
    }        

    /* microblog */
    div.overview-microblog {
        background: white;
        padding: 15px;
        margin-top: 15px;
    }

    div.overview-microblog div.status {
        margin: 0 0 15px 0;
        padding: 10px 20px;
        background: #f6f6f6;
    }
    div.overview-microblog div.status div.reply {
        background: #eee;
        padding: 5px 10px;
        margin: -10px -20px 5px -20px;
    }

    /* photos */
    div.overview-photos {
        width: 345px;
        background: white;
        margin: 0 20px 20px 0;
        padding: 15px;
        overflow: hidden;
    }    

    div.photo {
        float: left;
        width: 93px;
        height: 93px;
        margin: 0 0 10px 10px;
    }

    div.photo:hover {
        cursor: pointer;
        opacity: 0.75;
    }

    /* interactions */
    div.overview-interactions {
        width: 345px;
        background: white;
        margin: 0 20px 20px 0;
        padding: 15px;
word-break: break-all;
    }
div.overview-interactions .sunlit_image {
display: none!important;

}

    div.overview-interactions div.interaction {
        margin: 0 0 10px 0;
    }

    /* watched */
    div.overview-watched {
        width: 345px;
        background: white;
        margin: 0 20px 20px 0;
        padding: 15px;
        overflow: hidden;
    }
    div.watched {
        float: left;
        margin: 0 0 10px 10px;
    }
    div.watched img.poster {
        max-height: 134px;
        max-width: 94px;
    }
    div.watched img.poster:hover {
        cursor: pointer;
        opacity: 0.75;
    }

    /* places */
    div.overview-checkins {
        width: 345px;
        background: white;
        margin: 0 20px 20px 0;
        padding: 15px;
    }

    @media (min-width: 768px) {
        div.overview-content {
            width: auto;
            float: none;
        }
    }
    @media (min-width: 992px) {
        div.overview-content {
            width: auto;
            float: none;
        }
    }
    @media (min-width: 1200px) {
        div.overview-articles {
        }
        div.overview-content {
            width: 780px;
            float: right;
        }
    }
</style>

<script type="text/javascript">
function toggle(to) {
  $('.picker-microblog').toggleClass('active');
  $('.picker-articles').toggleClass('active');
  $('.overview-microblog').toggle();
  $('.overview-articles').toggle();
    }
</script>

<div class="overview">
    <div class="col-md-20 col-md-offset-0">
    
    <!-- content -->
    <div class="overview-content">

                    <h2><i class="fa fa-file"></i>Articles</h2>
        <div class="overview-articles">
<?php
$count = 0;
foreach ($posts as $post) {
  if (++$count == 11) break;
?>

                <div class="idno-object idno-content"> 
                <div class="row idno-entry">
                    <?= $post->draw() ?>
                </div>
                </div>
<?php
}
?>
        </div>

        <!-- microblog -->
        <div class="overview-microblog" style="display: none">
<?php
foreach ($statuses as $status) {
?>
                <div class="status">
<?php
  if (!empty($status->inreplyto)) {
?>
                    <div class="reply">
                        <i class="fa fa-reply"></i>
                        <?php
                        if (is_array($status->inreplyto)) {
                        ?>
                        <a href="<?= $status->inreplyto[0] ?>"><?= $status->inreplyto[0] ?></a>
                        <?php
                        } else {
                        ?>
                        <a href="<?= $status->inreplyto ?>"><?= $status->inreplyto ?></a>
                        <?php
                        }
                        ?> 
                    </div>
<?php
  }
?>
                    <div class="content"><?= $icon ?><?= $status->getBody() ?></div>
                    <a class="u-url url" href="<?= $status->getURL() ?>">
                        <time class="dt-published" datetime="<?= date(DATE_ISO8601, $status->created) ?>"><?= strftime('%d %b %Y', $status->created) ?></time> 
                    </a>
                </div>
<?php
}
?>
        </div>
    </div>

    <!-- interactions -->
    <div class="overview-interactions">
        <h2><i class="fa fa-comments"></i>Micro.blog</h2>
<?php
foreach ($stati as $interaction) {
  /* print_r($interaction); */
  $class = '';
  $icon = '';

  if (!empty($interaction->likeof)) {
    $class = 'u-like-of';
    $icon = '<i class="fa fa-star-o"></i>';
  } elseif (!empty($interaction->repostof)) {
    $class = 'u-repost-of';
    $icon = '<i class="fa fa-retweet"></i>';
  } elseif (!empty($interaction->inreplyto)) {
    $class = 'u-in-reply-to';
    $icon = '<i class="fa fa-reply"></i>';
  } 
  else {
    $class = 'u-bookmark-of';
    $icon = '<i class="fa fa-bookmark"></i>';
  }

  $body = $interaction->body;
  if (!empty($interaction->pageTitle)) {
    $body = $interaction->pageTitle;
  }
?>
        <div class="interaction">
            <?= $icon ?>
            <a href="<?= $interaction->body ?>" class="<?= $class ?> p-name" target="_blank">
                <?= htmlentities(strip_tags($body)) ?>
            </a>
        </div>
<?php
}
?>
    </div>
    <!-- photos -->
    <div class="overview-photos">
        <h2>
            <i class="fa fa-camera-retro"></i>Photos 
            <a href="/photos" target="_blank" style="float: right">
                <i class="fa fa-external-link-alt"></i>
            </a>
        </h2>
        <?php
        $count = 0;
        foreach ($photos as $photo) {
            $thumbs = [];
            if ($photo['thumbnail_small'] != null) {
                array_push($thumbs, $photo['thumbnail_small']);
            } else if ($photo['thumbs_small'] != null) {
                foreach ($photo['thumbs_small'] as $thumb) {
                    array_push($thumbs, $thumb['url']);
                }
            } else {
                continue;
            }

  foreach ($thumbs as $thumb) {
    if (++$count >= 25) break;
?>
                <a href="<?= $photo->getURL() ?>">
                    <div class="photo" style="background-image:url(<?= $thumb ?>); background-size: cover; background-position: 50%"></div>
                </a>
<?php
  }
}
?>
    </div>

    <!-- interactions -->
    <div class="overview-interactions">
        <h2><i class="fa fa-comments"></i>Interactions</h2>
<?php
foreach ($interactions as $interaction) {
  $class = '';
  $icon = '';

  if (!empty($interaction->likeof)) {
    $class = 'u-like-of';
    $icon = '<i class="fa fa-star-o"></i>';
  } elseif (!empty($interaction->repostof)) {
    $class = 'u-repost-of';
    $icon = '<i class="fa fa-retweet"></i>';
  } else {
    $class = 'u-bookmark-of';
    $icon = '<i class="fa fa-bookmark"></i>';
  }

  $body = $interaction->body;
  if (!empty($interaction->pageTitle)) {
    $body = $interaction->pageTitle;
  }
?>
        <div class="interaction">
            <?= $icon ?>
            <a href="<?= $interaction->body ?>" class="<?= $class ?> p-name" target="_blank">
                <?= htmlentities(strip_tags($body)) ?>
            </a>
        </div>
<?php
}
?>
    </div>

    <!-- watched -->
    <div class="overview-watched">
        <h2><i class="fa fa-film"></i>Watched</h2>
<?php
foreach ($watched as $watch) {
  $poster = '';
  if ($attachments = $watch->getAttachments()) {
    foreach ($attachments as $attachment) {
      $poster = '<img src="' . $attachment['url'] . '" class="poster">';
      break;
    }
  }
?>
            <div class="watched">
                <a href="<?= $watch->getURL() ?>">
                    <?= $poster ?>
                </a>
            </div>
<?php
}
?>
    </div>

    <!-- checkins -->
    <div class="overview-checkins">
        <h2><i class="fa fa-map-pin"></i>Places</h2>
<?php
foreach ($checkins as $checkin) {
?>
            <div class="checkin">
                <a href="<?= $checkin->getURL() ?>">
                    <?= $checkin->placename ?>
                </a>
            </div>
<?php
}
?>
    </div>


</div>
