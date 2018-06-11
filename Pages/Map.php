<?php

    namespace IdnoPlugins\CleverCustomize\Pages {

        use Idno\Core\Webmention;
        use Idno\Entities\Notification;
        use Idno\Entities\User;

        class Map extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only

                $date_string = $this->getInput('date');
                if ($date_string == null) {
                    $date_string = date('Y-m-d');
                }

                // Check for an empty site
                if (!\Idno\Entities\User::get()) {
                    $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'begin/');
                }

                // Set the homepage owner for single-user sites
                if (!$this->getOwner() && \Idno\Core\Idno::site()->config()->single_user) {
                    $owners = \Idno\Entities\User::get(['admin' => true]);
                    if (count($owners) === 1) {
                        $this->setOwner($owners[0]);
                    } else {
                        \Idno\Core\Idno::site()->logging()->warning('Expected exactly 1 admin user for single-user site; got '.count($owners));
                    }
                }

                if (!empty(\Idno\Core\Idno::site()->config()->description)) {
                    $description = \Idno\Core\Idno::site()->config()->description;
                } else {
                    $description = 'An independent social website, powered by Known.';
                }
                $description = $description . ': Map'; 
                $title = $description;
                
                $token = \Idno\Core\Idno::site()->config()->geo_token;
                $url = 'https://geo.cleverdevil.io/api/range?token=' . $token;
                $url = $url . '&start=' . $date_string;
                $points = file_get_contents($url); 
                
                $t = \Idno\Core\Idno::site()->template();
                $t->__(array(
                    'title'        => $title,
                    'description'  => $description,
                    'content'      => array('all'),
                    'body'         => $t->__(array(
                        'date'     => $date_string,
                        'points'   => $points
                    ))->draw('pages/map'),
                ))->drawPage();
            }

        }

    }
