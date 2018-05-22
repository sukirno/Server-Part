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

    $profile = new profile($dbo, auth::getCurrentUserId());

    if (isset($_GET['action'])) {

        $notifications = new notify($dbo);
        $notifications->setRequestFrom(auth::getCurrentUserId());

        $notifications_count = $notifications->getNewCount($profile->getLastNotifyView());

        echo $notifications_count;
        exit;
    }

    $blacklist = new blacklist($dbo);
    $blacklist->setRequestFrom(auth::getCurrentUserId());

    $items_all = $blacklist->myActiveItemsCount();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $blacklist->get($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="#" onclick="BlackList.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "blacklist";

    $css_files = array("style.css");
    $page_title = $LANG['page-blacklist']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-blacklist']; ?></span>
                                </div>
                            </div>

                            <div id="blacklist_cont" class="blacklist_cont">

                                <?php

                                    $result = $blacklist->get(0);

                                    $items_loaded = count($result['items']);

                                    if ($items_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            draw($value, $LANG, $helper);
                                        }

                                        if ($items_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="#" onclick="BlackList.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
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

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

        </script>

    </div>
</div>

</body>
</html>

<?php

    function draw($item, $LANG, $helper) {

        ?>

                <div class="post post_item" data-id="<?php echo $item['id']; ?>">
                    <div class="profile_cont">
                        <a href="/<?php echo $item['blockedUserUsername']; ?>">
                            <img src="<?php if (strlen($item['blockedUserPhotoUrl']) != 0) { echo $item['blockedUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                        </a>
                    </div>
                    <div class="post_content">

                        <div class="post_title">
                            <a href="/<?php echo $item['blockedUserUsername']; ?>"><span class="post_fullname"><?php echo $item['blockedUserFullname']; ?></span> <s>@</s><b class="post_username"><?php echo $item['blockedUserUsername']; ?></b></a>
                        </div>

                        <div class="likes_notification">
                            <a href="javascript:void(0);" onclick="BlackList.remove('<?php echo $item['id']; ?>', '<?php echo $item['blockedUserUsername']; ?>', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['action-unblock']; ?></a>
                        </div>

                        <div class="post_footer">
                            <?php

                                $time = new language(NULL, $LANG['lang-code']);
                            ?>
                            <span class="time"><?php echo $time->timeAgo($item['createAt']); ?></span>
                        </div>
                    </div>
                </div>

        <?php
    }

?>