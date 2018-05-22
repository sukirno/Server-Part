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

    $query = '';

    $search = new search($dbo);
    $search->setRequestFrom(auth::getCurrentUserId());

    $items_all = 0;
    $items_loaded = 0;

    if (isset($_GET['query'])) {

        $query = isset($_GET['query']) ? $_GET['query'] : '';

        $query = helper::clearText($query);
        $query = helper::escapeText($query);
    }

    if (!empty($_POST)) {

        $userId = isset($_POST['userId']) ? $_POST['userId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';
        $query = isset($_POST['query']) ? $_POST['query'] : '';

        $userId = helper::clearInt($userId);
        $loaded = helper::clearInt($loaded);

        $query = helper::clearText($query);
        $query = helper::escapeText($query);

        $result = $search->query($query, $userId);

        $items_loaded = count($result['users']);
        $items_all = $result['itemCount'];


        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ( $items_loaded != 0 ) {

            ob_start();

            foreach ($result['users'] as $key => $value) {

                draw::profileItem($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Search.more('<?php echo $result['userId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "search";

    $css_files = array("style.css", "tipsy.css");
    $page_title = $LANG['page-search']." | ".APP_TITLE;

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

                                <a href="/search/name" class="title active"><?php echo $LANG['page-search']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/groups" class="title"><?php echo $LANG['label-groups']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/hashtag" class="title"><?php echo $LANG['page-hashtags']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/facebook" class="title">Facebook</a>
                                <span class="divider">|</span>
                                <a href="/search/nearby" class="title"><?php echo $LANG['page-nearby']; ?></a>
                            </div>

                            <form id="search_form" method="get" action="/search/name" class="frm">

                                <div class="search_input_cont">
                                    <input type="text" class="text search_query" autocomplete="off" name="query" value="<?php echo $query; ?>">
                                    <div style="display: inline-block; margin-left: 8px;">
                                        <button class="primary_btn search_submit" onclick=""><?php echo $LANG['action-search']; ?></button>
                                    </div>
                                </div>

                            </form>

                            <div id="search_cont" class="search_cont">

                                <?php

                                if (strlen($query) > 0) {

                                    $result = $search->query($query, 0);

                                    $items_all = $result['itemCount'];
                                    $items_loaded = count($result['users']);

                                    if (strlen($query) > 0) {

                                    ?>

                                        <div class="header">
                                            <div class="title">
                                                <span><?php echo $LANG['label-search-result']; ?></span>
                                                <span id="search_count">(<?php echo $items_all; ?>)</span>
                                            </div>
                                        </div>

                                    <?php

                                    }

                                    if ($items_loaded != 0) {

                                        foreach ($result['users'] as $key => $value) {

                                            draw::profileItem($value, $LANG, $helper);
                                        }

                                        if ($items_all > 20) {

                                            ?>

                                            <div class="more_cont">
                                                <a class="more_link" href="javascript:void(0)" onclick="Search.more('<?php echo $result['userId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                                            </div>

                                        <?php
                                        }

                                    } else {

                                        ?>

                                            <div class="info">
                                                <?php echo $LANG['label-search-empty']; ?>
                                            </div>

                                        <?php
                                    }

                                } else {

                                    ?>

                                        <div class="info">
                                            <?php echo $LANG['label-search-prompt']; ?>
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
            var query = "<?php echo $query; ?>";

            $(document).ready(function() {

                $(".page_verified").tipsy({gravity: 'w'});
                $(".verified").tipsy({gravity: 'w'});
            });

        </script>

    </div>
</div>

</body>
</html>
