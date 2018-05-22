<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    if (auth::isSession() && !isset($_SESSION['lat']) && !isset($_SESSION['lng'])) {

        $account = new account($dbo, auth::getCurrentUserId());

        $geo = new geo($dbo);

        $info = $geo->info(helper::ip_addr());

        if ($info['geoplugin_status'] == 206) {

            $result = $account->setGeoLocation($info['geoplugin_latitude'], $info['geoplugin_longitude']);

            $_SESSION['lat'] = $info['geoplugin_latitude'];
            $_SESSION['lng'] = $info['geoplugin_longitude'];

        } else {

            // 37.421011, -122.084968 | Mountain View, CA 94043, USA   ;)

            $result = $account->setGeoLocation("37.421011", "-122.084968");

            $_SESSION['lat'] = "37.421011";
            $_SESSION['lng'] = "-122.084968";
        }

        unset($geo);
        unset($account);
    }

    $distance = 100;

    $geo = new geo($dbo);
    $geo->setRequestFrom(auth::getCurrentUserId());

    $items_all = $geo->getPeopleNearbyCount($_SESSION['lat'], $_SESSION['lng'], $distance);
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $geo->getPeopleNearby($itemId, $_SESSION['lat'], $_SESSION['lng'], $distance);

        $items_loaded = count($result['items']);
        $items_all = $result['itemCount'];


        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                profileItem($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Nearby.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "nearby";

    $css_files = array("style.css", "tipsy.css");
    $page_title = $LANG['page-nearby']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="bg_gray">

<div id="page_wrap">

    <?php

        include_once("../html/common/topbar_new.inc.php");
    ?>

    <div id="page_layout">

        <?php

            include_once("../html/common/banner.inc.php");
        ?>

        <div id="page_body">

            <?php

                include_once("../html/common/sidebar.inc.php");
            ?>

            <div id="wrap3">
                <div id="wrap2">
                    <div id="wrap1">

                        <div id="content">

                            <div class="header">

                                <a href="/search/name" class="title"><?php echo $LANG['page-search']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/groups" class="title"><?php echo $LANG['label-groups']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/hashtag" class="title"><?php echo $LANG['page-hashtags']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/facebook" class="title">Facebook</a>
                                <span class="divider">|</span>
                                <a href="/search/nearby" class="title active"><?php echo $LANG['page-nearby']; ?></a>
                            </div>

                            <div id="nearby_cont" class="nearby_cont">

                                <?php

                                    $result = $geo->getPeopleNearby(0, $_SESSION['lat'], $_SESSION['lng'], $distance);

                                    $items_loaded = count($result['items']);

                                    if ($items_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            profileItem($value, $LANG, $helper);
                                        }

                                        if ($items_all > 20) {

                                            ?>

                                            <div class="more_cont">
                                                <a class="more_link" href="javascript:void(0)" onclick="Nearby.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                                            </div>

                                            <?php
                                        }

                                    } else {

                                        ?>

                                        <div class="info">
                                            <?php echo $LANG['label-empty-list']; ?>
                                        </div>

                                        <?php
                                    }
                                ?>

                            </div>

                        </div>
                    </div>
                </div>

                <?php

                    include_once("../html/common/footer_new.inc.php");
                ?>

            </div>
        </div>

        <script type="text/javascript" src="/js/jquery.tipsy.js"></script>

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

            $(document).ready(function() {

                $(".page_verified").tipsy({gravity: 'w'});
                $(".verified").tipsy({gravity: 'w'});
            });

            window.Nearby || ( window.Nearby = {} );

            Nearby.more = function (offset) {

                $('a.more_link').hide();
                $('a.loading_link').show();

                $.ajax({
                    type: 'POST',
                    url: '/search/nearby',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.more_cont').remove();

                        if (response.hasOwnProperty('html')){

                            $("div.nearby_cont").append(response.html);
                        }

                        items_loaded = response.items_loaded;
                        items_all = response.items_all;
                    },
                    error: function(xhr, type){

                        $('a.more_link').show();
                        $('a.loading_link').hide();
                    }
                });
            };

        </script>

    </div>
</div>

</body>
</html>


<?php

    function profileItem($profile, $LANG, $helper = null)
    {
        ?>

        <div class="post profile_item">
            <a class="profile_cont" href="/<?php echo $profile['username']; ?>">
                <?php

                $profilePhotoUrl = "/img/profile_default_photo.png";

                if (strlen($profile['lowPhotoUrl']) != 0) {

                    $profilePhotoUrl = $profile['lowPhotoUrl'];
                }
                ?>

                <img src="<?php echo $profilePhotoUrl; ?>"/>
            </a>
            <div class="post_cont">
                <a class="fullname" href="/<?php echo $profile['username']; ?>"><?php echo $profile['fullname']; ?></a>
                <?php if ( $profile['verify'] == 1) echo "<b original-title=\"{$LANG['label-account-verified']}\" class=\"verified\"></b>"; ?>
                <div class="addon_info">
                    @<span class="username"><?php echo $profile['username']; ?></span>
                </div>
                <div class="addon_info">
                    <span class="username"><?php echo $profile['distance']; ?>km</span>
                </div>
            </div>
        </div>

        <?php
    }