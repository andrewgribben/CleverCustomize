<?php

    namespace IdnoPlugins\CleverCustomize\Pages {

        use Idno\Core\Webmention;
        use Idno\Entities\Notification;
        use Idno\Entities\User;

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

                $query          = $this->getInput('q');
                $offset         = (int)$this->getInput('offset');
                $types          = $this->getInput('types');
                $friendly_types = array();

                // Check for an empty site
                if (!\Idno\Entities\User::get()) {
                    $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'begin/');
                }

                // Set the homepage owner for single-user sites
                if (!$this->getOwner() && \Idno\Core\Idno::site()->config()->single_user) {
                    $owners = \Idno\Entities\User::get(['admin' => true]);
                    if (!empty($owners) && count($owners) === 1) {
                        $this->setOwner($owners[0]);
                    } else {
                        $number = 0;
                        if (!empty($owners)) {
                            $number = count($owners);
                        }
                        \Idno\Core\Idno::site()->logging()->warning("Expected exactly 1 admin user for single-user site; got $number");
                    }
                }

                if (!empty($this->arguments[0])) { // If we're on the friendly content-specific URL
                    if ($friendly_types = explode('/', $this->arguments[0])) {
                        $friendly_types = array_filter($friendly_types);
                        if (empty($friendly_types) && !empty($query)) {
                            $friendly_types = array('all');
                        }
                        $types = array();
                        // Run through the URL parameters and set content types appropriately
                        foreach ($friendly_types as $friendly_type) {
                            if ($friendly_type == 'all') {
                                $types = \Idno\Common\ContentType::getRegisteredClasses();
                                break;
                            }
                            if ($content_type_class = \Idno\Common\ContentType::categoryTitleToClass($friendly_type)) {
                                $types[] = $content_type_class;
                            }
                        }
                    }
                } else {
                    // If user has content-specific preferences, do something with $friendly_types
                    if (empty($query)) {
                        $types = \Idno\Core\Idno::site()->config()->getHomepageContentTypes();
                    }
                }


                if (!empty(\Idno\Core\Idno::site()->config()->description)) {
                    $description = \Idno\Core\Idno::site()->config()->description;
                } else {
                    $description = 'An independent social website, powered by Known.';
                }



                if (!empty(\Idno\Core\Idno::site()->config()->homepagetitle)) {
                    $title = \Idno\Core\Idno::site()->config()->homepagetitle;
                } else {
                    $title = \Idno\Core\Idno::site()->config()->title;
                }



                
                $search = array();
                $search['publish_status'] = 'published';
                
                $privateSearch = array();
                $pubSearch['publish_status'] = 'published';
                $pubSearch['access'] = 'PUBLIC';
                /* find recent photos */
                $photos = \Idno\Common\Entity::getFromX(array(
                    'IdnoPlugins\Photo\Photo'
                ), $search, array(), 15);
                /* find recent likes, reposts, and bookmarks */
                $interactions = \Idno\Common\Entity::getFromX(array(
                  'IdnoPlugins\Reactions\Like',
                  'IdnoPlugins\Reactions\Repost',
                  'IdnoPlugins\Event\RSVP',
                ), $search, array(), 10);

                /* find recent likes, reposts, and bookmarks */
                $stati= \Idno\Common\Entity::getFromX(array(
                  'IdnoPlugins\Status\Status',
                  'IdnoPlugins\Text\Entry',
                  'IdnoPlugins\Status\Reply',
                  'IdnoPlugins\Like\Like',

                ), $pubSearch, array(), 5);
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
                ), $search, array(), 10);

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
                    'content'     => $friendly_types,
                    'body'        => $t->__(array(
                        'photos'        => $photos,
                        'interactions'  => $interactions,
                        'stati'  => $stati,
                        'watched'       => $watched,
                        'checkins'      => $checkins,
                        'statuses'      => $statuses,
                        'posts'         => $posts,
                        'items'        => $feed,
                        'contentTypes' => $create,
                        'offset'       => $offset,
                        'count'        => $count,
                        'subject'      => $query,
                        'content'      => $friendly_types
                    ))->draw('pages/overview'),

                ))->drawPage();
            }

            function webmentionContent($source, $target, $source_response, $source_mf2)
            {
                // if this is a single-user site, let's forward on the root mention
                // to their user page

                \Idno\Core\Idno::site()->logging()->info("received homepage mention from $source");

                if (\Idno\Core\Idno::site()->config()->single_user) {
                    $user = \Idno\Entities\User::getOne(['admin' => true]);
                    if ($user) {
                        \Idno\Core\Idno::site()->logging()->debug("pass on webmention to solo user: {$user->getHandle()}");
                        $userPage = \Idno\Core\Idno::site()->getPageHandler($user->getURL());
                        if ($userPage) {
                            return $userPage->webmentionContent($source, $target, $source_response, $source_mf2);
                        } else {
                            \Idno\Core\Idno::site()->logging()->debug("failed to find a Page to serve route " . $user->getURL());
                        }
                    } else {
                        \Idno\Core\Idno::site()->logging()->debug("query for an admin-user failed to find one");
                    }
                } else {
                    \Idno\Core\Idno::site()->logging()->debug("disregarding mention to multi-user site");
                }

                return false;
            }
        }

    }
