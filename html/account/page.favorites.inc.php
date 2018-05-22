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

    $stream = new stream($dbo);
    $stream->setRequestFrom(auth::getCurrentUserId());

    $items_all = $stream->getFavoritesCount();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $stream->getFavorites($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::post($value, $LANG, $helper);
            }

            if ($result['items'] < $items_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="#" onclick="Favorites.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "favorites";

    $css_files = array("style.css");
    $page_title = $LANG['page-favorites']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-favorites']; ?></span>
                                </div>
                            </div>

                            <div id="favorites_cont" class="favorites_cont">

                                <?php

                                    $result = $stream->getFavorites(0);

                                    $items_loaded = count($result['items']);

                                    if ($items_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            draw::post($value, $LANG, $helper);
                                        }

                                        if ($items_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="#" onclick="Favorites.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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
