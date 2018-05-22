<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

//    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {
//
//        header('Location: /');
//    }

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

    $photos = new photos($dbo);
    $photos->setRequestFrom($profileInfo['id']);

    $photos_all = $photos->count();
    $photos_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $photos->get($profileInfo['id'], $itemId, 0);

        $photos_loaded = count($result['photos']);

        $result['photos_loaded'] = $photos_loaded + $loaded;
        $result['photos_all'] = $photos_all;

        if ($photos_loaded != 0) {

            ob_start();

            foreach ($result['photos'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            if ($result['photos_loaded'] < $photos_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Photo.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['photoId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "gallery";

    $css_files = array("style.css", "gallery.css");
    $page_title = $LANG['page-gallery']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-gallery']; ?></span>
                                </div>

                                <?php

                                    if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $profileInfo['id']) {
                                ?>

                                <form onsubmit="Photo.add('<?php echo $profileInfo['username']; ?>'); return false;" class="profile_question_form add-img-form" action="/<?php echo $profileInfo['username']; ?>/add_gallery_item" method="post">
                                            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">
                                            <input autocomplete="off" type="hidden" name="itemPreviewImg" value="">
                                            <input autocomplete="off" type="hidden" name="itemImg" value="">
                                            <input autocomplete="off" type="hidden" name="itemOriginImg" value="">
                                            <div class="form_actions">
                                                <span id="word_counter"><?php echo $LANG['label-add-photo']; ?></span>

                                                <div class="main_actions">
                                                    <a href="javascript:void(0)" onclick="Photo.deleteGalleryImg(event); return false;" class="post_img_delete"><?php echo $LANG['action-delete-image']; ?></a>
                                                    <a onclick="Photo.changeGalleryImg('<?php echo $LANG['action-change-image']; ?>'); return false;" class="add_image_to_post" style="">
                                                        <img src="/img/camera.png">
                                                    </a>
                                                </div>
                                                <button class="primary_btn" value="ask"><?php echo $LANG['action-add-photo']; ?></button>
                                                <div class="img_container" style="">
                                                    <img class="post_img_preview" style="" src=""/>
                                                </div>
                                            </div>
                                </form>

                                <?php

                                    }
                                 ?>
                            </div>

                            <div id="gallery_cont" class="gallery_cont">

                                <?php

                                    $result = $photos->get($profileInfo['id'], 0, 0);

                                    $photos_loaded = count($result['photos']);

                                    if ($photos_loaded != 0) {

                                        foreach ($result['photos'] as $key => $value) {

                                            draw($value, $LANG, $helper);
                                        }

                                        if ($photos_all > 16) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="javascript:void(0)" onclick="Photo.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['photoId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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

        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

        <script type="text/javascript">

            var photos_all = <?php echo $photos_all; ?>;
            var photos_loaded = <?php echo $photos_loaded; ?>;

        </script>

    </div>
</div>

</body>
</html>

<?php

    function draw($photo, $LANG, $helper) {

        ?>

                <div class="photo gallery_item" data-id="<?php echo $photo['id']; ?>">
                    <div class="action_box">
                        <?php

                            if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $photo['fromUserId']) {

                                ?>

                                    <div class="action_remove" onclick="Photo.remove('<?php echo $photo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"></div>

                                <?php

                            } else {

                                ?>

                                    <div class="action_report" onclick="Photo.getReportBox('<?php echo $photo['fromUserUsername']; ?>', '<?php echo $photo['id']; ?>', '<?php echo $LANG['action-report']; ?>'); return false;"></div>

                                <?php
                            }
                        ?>
                    </div>
                    <div class="gallery_img">
                        <img data-img="<?php echo $photo['imgUrl']; ?>" src="<?php echo $photo['previewImgUrl']; ?>">
                    </div>
                    <div class="gallery_content">
                        <span class="time" style="display: block"><?php echo $photo['timeAgo']; ?></span>
                    </div>
                </div>

        <?php
    }

?>