<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $profileId = $helper->getUserId($request[0]);

    $user = new profile($dbo, $profileId);

    $user->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $user->get();

    if ($profileInfo['error'] === true) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    if ($profileInfo['accountType'] != ACCOUNT_TYPE_USER) {

        header("Location: /");
        exit;
    }

    $gifts = new gift($dbo);
    $gifts->setRequestFrom($profileId);

    $items_all = $gifts->count();
    $items_loaded = 0;

    if (isset($_GET['action'])) {

        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $itemId = isset($_GET['itemId']) ? $_GET['itemId'] : 0;

        $itemId = helper::clearInt($itemId);

        switch ($action) {

            case "delete": {

                $gifts->setRequestFrom(auth::getCurrentUserId());
                $result = $gifts->remove($itemId);

                echo json_encode($result);
                exit;

                break;
            }

            default: {

                break;
            }
        }
    }

    if (!empty($_POST)) {

        $id = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $id = helper::clearInt($id);
        $loaded = helper::clearInt($loaded);

        $result = $gifts->get($profileId, $id);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ( $items_loaded != 0 ) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Gifts.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "gifts";

    $css_files = array("style.css", "tipsy.css", "gifts.css");
    $page_title = $LANG['page-gifts']." | ".APP_TITLE;

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
                                <div class="title">
                                    <span><?php echo $LANG['page-gifts']; ?></span>
                                </div>
                            </div>

                            <div id="gifts_cont" class="gifts_cont">

                                <?php

                                    $result = $gifts->get($profileId, 0);

                                    $items_loaded = count($result['items']);

                                    if ($items_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            draw($value, $LANG, $helper);
                                        }

                                        if ($items_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="javascript:void(0)" onclick="Gifts.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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

            window.Gifts || ( window.Gifts = {} );

            Gifts.more = function (profile, offset) {

                $('a.more_link').hide();
                $('a.loading_link').show();

                $.ajax({
                    type: 'POST',
                    url: '/' + profile + '/gifts',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.more_cont').remove();

                        if ( response.hasOwnProperty('html') ){

                            $("div.gifts_cont").append(response.html);
                        }

                        items_loaded = response.items_loaded;
                        items_all = response.items_all;
                    },
                    error: function(xhr, type){

                        $('a.more_link').show();
                        $('a.loading_link').hide();
                    }
                });
            }

            Gifts.remove = function (profile, gift) {

                $('a.more_link').hide();
                $('a.loading_link').show();

                $.ajax({
                    type: 'GET',
                    url: '/' + profile + '/gifts',
                    data: 'itemId=' + gift + "&action=delete",
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.post[data-id='+ gift +']').remove();
                    },
                    error: function(xhr, type){


                    }
                });
            }

        </script>

    </div>
</div>

</body>
</html>

<?php

    function draw($giftInfo, $LANG, $helper = null)
    {
        ?>

        <div class="post profile_item" data-id="<?php echo $giftInfo['id']; ?>">
            <a class="profile_cont" href="/<?php echo $giftInfo['giftFromUserUsername']; ?>">
                <?php

                $profilePhotoUrl = "/img/profile_default_photo.png";

                if ( strlen($giftInfo['giftFromUserPhoto']) != 0 ) {

                    $profilePhotoUrl = $giftInfo['giftFromUserPhoto'];
                }
                ?>

                <img src="<?php echo $profilePhotoUrl; ?>"/>
            </a>
            <div class="post_content">

                <?php

                    if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $giftInfo['giftTo']) {

                        ?>
                            <div class="action_remove" onclick="Gifts.remove('<?php echo auth::getCurrentUserLogin(); ?>', '<?php echo $giftInfo['id']; ?>'); return false;"></div>
                        <?php
                    }
                ?>

                <div class="likes_notification">
                    <a href="/<?php echo $giftInfo['giftFromUserUsername']; ?>"><?php echo $giftInfo['giftFromUserFullname']; ?></a>
                </div>

                <?php

                    if (strlen($giftInfo['message']) > 0) {

                        ?>
                        <div class="post_data"><?php echo $giftInfo['message']; ?></div>
                        <?php
                    }
                ?>

                <div class="post_img" style="text-align: center;">
                    <img src="<?php echo $giftInfo['imgUrl']; ?>" style="border: none; width: 256px; height: 256px;">
                </div>

                <div class="post_footer">
                    <span class="time"><?php echo $giftInfo['timeAgo']; ?></span>
                </div>
            </div>
        </div>

        <?php
    }
