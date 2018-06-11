<?php

    namespace IdnoPlugins\CleverCustomize\Pages {

        class Overview extends \Idno\Common\Page
        {

            function getContent()
            {
                
                if (!empty(\Idno\Core\Idno::site()->config()->description)) {
                    $description = \Idno\Core\Idno::site()->config()->description;
                } else {
                    $description = 'An independent social website, powered by Known.';
                }
                $description = $description . ": Overview";

                if (!empty(\Idno\Core\Idno::site()->config()->homepagetitle)) {
                    $title = \Idno\Core\Idno::site()->config()->homepagetitle;
                } else {
                    $title = \Idno\Core\Idno::site()->config()->title;
                }
                $title = $title . ": Overview";

                $search = array();
                $search['publish_status'] = 'published';
                
                $privateSearch = array();
                $pubSearch['publish_status'] = 'published';
                $pubSearch['access'] = 'PUBLIC';
                /* find recent photos */
                $photos = \Idno\Common\Entity::getFromX(array(
                    'IdnoPlugins\Photo\Photo'
                ), $search, array(), 40);

                /* find recent likes, reposts, and bookmarks */
                $interactions = \Idno\Common\Entity::getFromX(array(
                  'IdnoPlugins\Like\Like',
                ), $search, array(), 20);

                /* find recent likes, reposts, and bookmarks */
                $stati= \Idno\Common\Entity::getFromX(array(
                  'IdnoPlugins\Status\Status',
                  'IdnoPlugins\Text\Entry',
                  'IdnoPlugins\Status\Reply',
                  'IdnoPlugins\Like\Like',

                ), $pubSearch, array(), 20);
                /* find recent watched shows and movies */
                $watched = \Idno\Common\Entity::getFromX(array(
                    'IdnoPlugins\Watching\Watching'
                ), $search, array(), 15);

                /* find recent checkins */
                $checkins = \Idno\Common\Entity::getFromX(array(
                    'IdnoPlugins\Checkin\Checkin'
                ), $search, array(), 20);

                /* find recent status update activity */
                $statuses = \Idno\Common\Entity::getFromX(array(
                    'IdnoPlugins\Status\Status', 
                    'IdnoPlugins\Status\Reply'
                ), $search, array(), 40);

                /* find recent "posts" (posts, recipes, reviews) */
                $posts = \Idno\Common\Entity::getFromX(array(
                    'IdnoPlugins\Text\Entry',
                    'IdnoPlugins\Recipe\Recipe',
                    'IdnoPlugins\Review\Review'
                ), $search, array(), 10);

                $t = \Idno\Core\Idno::site()->template();
                $t->__(array(
                    'title'       => $title,
                    'description' => $description,
                    'body'        => $t->__(array(
                        'photos'        => $photos,
                        'interactions'  => $interactions,
                        'stati'  => $stati,
                        'watched'       => $watched,
                        'checkins'      => $checkins,
                        'statuses'      => $statuses,
                        'posts'         => $posts
                    ))->draw('pages/overview'),

                ))->drawPage();
            }

        }

    }
