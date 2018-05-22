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

    $feed = new feed($dbo);
    $feed->setRequestFrom(auth::getCurrentUserId());

    $inbox_all = $feed->count();
    $inbox_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $feed->get($itemId);

        $inbox_loaded = count($result['items']);

        $result['inbox_loaded'] = $inbox_loaded + $loaded;
        $result['inbox_all'] = $inbox_all;

        if ($inbox_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::post($value, $LANG, $helper);
            }

            if ($result['inbox_loaded'] < $inbox_all) {

                ?>

                    <div class="more_cont">
                        <a class="more_link" href="javascript:void(0)" onclick="Feeds.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                        <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                    </div>

                <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "wall";

    $css_files = array("style.css");
    $page_title = $LANG['topbar-wall']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-wall']; ?></span>
                                </div>

                                <div class="wall_share_block">
                                    <div>
                                        <span class="profile_link"><?php echo APP_HOST."/".auth::getCurrentUserLogin(); ?></span>
                                    </div>
                                    <br>
                                    <div class="profile_link_description">
                                        <?php echo $LANG['label-empty-questions']; ?>
                                    </div>
                                    <div class="share_block">
                                        <a href="http://www.facebook.com/share.php?u=<?php echo APP_URL."/".auth::getCurrentUserLogin(); ?>">
                                            <i class="social_icon icon_fb"></i>
                                        </a>
                                        <a href="http://vk.com/share.php?url=<?php echo APP_URL."/".auth::getCurrentUserLogin(); ?>">
                                            <i class="social_icon icon_vk"></i>
                                        </a>
                                        <a href="http://twitter.com/share?url=<?php echo APP_URL."/".auth::getCurrentUserLogin(); ?>">
                                            <i class="social_icon icon_tw"></i>
                                        </a>
                                    </div>
                                </div>

                            </div>

                            <div id="wall_cont" class="wall_cont">

                                <?php

                                $result = $feed->get(0);

                                $inbox_loaded = count($result['items']);

                                if ($inbox_loaded != 0) {

                                    foreach ($result['items'] as $key => $value) {

                                        draw::post($value, $LANG, $helper);
                                    }

                                } else {

                                    ?>

                                    <div class="info">
                                        <?php echo $LANG['label-empty-feeds']; ?>
                                    </div>

                                    <?php
                                }
                                ?>

                                <?php

                                if ($inbox_all > 20) {

                                    ?>

                                    <div class="more_cont">
                                        <a class="more_link" href="javascript:void(0)" onclick="Feeds.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                        <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
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

            var inbox_all = <?php echo $inbox_all; ?>;
            var inbox_loaded = <?php echo $inbox_loaded; ?>;

        </script>

    </div>
</div>

</body>
</html>
