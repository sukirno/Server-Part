<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk, qascript@mail.ru
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $query = "";

    if (isset($_GET['src'])) {

        $query = isset($_GET['src']) ? $_GET['src'] : '';

        $query = str_replace('#', '', $query);

        $query = helper::clearText($query);
        $query = helper::escapeText($query);
    }


    $hashtags = new hashtag($dbo);

    $hashtags->setRequestFrom(auth::getCurrentUserId());
    $hashtags->setLanguage($LANG['lang-code']);

    $inbox_all = $hashtags->count($query);
    $inbox_loaded = 0;

    if (!empty($_POST)) {

        $postId = isset($_POST['postId']) ? $_POST['postId'] : '';
        $hashtag = isset($_POST['hashtag']) ? $_POST['hashtag'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $postId = helper::clearInt($postId);

        $hashtag = helper::clearText($hashtag);
        $hashtag = helper::escapeText($hashtag);

        $loaded = helper::clearInt($loaded);

        $result = $hashtags->search($hashtag, $postId);

        $inbox_loaded = count($result['posts']);

        $result['inbox_loaded'] = $inbox_loaded + $loaded;
        $result['inbox_all'] = $inbox_all;

        if ($inbox_loaded != 0) {

            ob_start();

            foreach ($result['posts'] as $key => $value) {

                draw::post($value, $LANG, $helper);
            }

            if ($result['inbox_loaded'] < $inbox_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Hashtags.more('<?php echo $result['postId']; ?>', '<?php echo $result['query']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

                <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "hashtags";

    $css_files = array("style.css");
    $page_title = $LANG['page-hashtags']." | ".APP_TITLE;

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
                                    <a href="/search/hashtag" class="title active"><?php echo $LANG['page-hashtags']; ?></a>
                                    <span class="divider">|</span>
                                    <a href="/search/facebook" class="title">Facebook</a>
                                    <span class="divider">|</span>
                                    <a href="/search/nearby" class="title"><?php echo $LANG['page-nearby']; ?></a>
                                </div>

                                <form id="search_form" method="get" action="/search/hashtag" class="frm">

                                    <div class="search_input_cont">
                                        <input type="text" class="text search_query" autocomplete="off" name="src" value="<?php echo $query; ?>">
                                        <div style="display: inline-block; margin-left: 8px;">
                                            <button class="primary_btn search_submit" onclick=""><?php echo $LANG['action-search']; ?></button>
                                        </div>
                                    </div>

                                </form>

                                <div id="posts_cont">

                                    <?php

                                    if (strlen($query) > 0) {

                                        $result = $hashtags->search($query, 0);

                                        $inbox_loaded = count($result['posts']);

                                        if (strlen($query) > 0) {

                                            ?>

                                            <div class="header">
                                                <div class="title">
                                                    <span><?php echo $LANG['label-search-result']; ?></span>
                                                    <span id="search_count">(<?php echo $inbox_all; ?>)</span>
                                                </div>
                                            </div>

                                            <?php

                                        }

                                        if ($inbox_loaded != 0) {

                                            foreach ($result['posts'] as $key => $value) {

                                                draw::post($value, $LANG, $helper);
                                            }

                                        } else {

                                            ?>

                                            <div class="info">
                                                <?php echo $LANG['label-search-empty']; ?>
                                            </div>

                                            <?php
                                        }
                                        ?>

                                        <?php

                                        if ($inbox_all > 20) {

                                            ?>

                                            <div class="more_cont">
                                                <a class="more_link" href="javascript:void(0)"
                                                   onclick="Hashtags.more('<?php echo $result['postId']; ?>', '<?php echo $query; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                                            </div>

                                            <?php
                                        }

                                    } else {

                                        ?>
                                            <div class="info">
                                                <?php echo $LANG['label-search-hashtag-prompt']; ?>
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
