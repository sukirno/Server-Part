<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk, qascript@mail.ru
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $query = "";

    if (!isset($_GET['src'])) {

        header("Location: /");

    } else {

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
                                    <span class="title"><?php echo $LANG['page-hashtags']; ?></span>
                                </div>

                                <div id="posts_cont">

                                    <?php

                                    $result = $hashtags->search($query, 0);

                                    $inbox_loaded = count($result['posts']);

                                    if ($inbox_loaded != 0) {

                                        foreach ($result['posts'] as $key => $value) {

                                            draw::post($value, $LANG, $helper);
                                        }

                                    } else {

                                        ?>

                                        <div class="info">
                                            <?php echo $LANG['label-empty-list']; ?>
                                        </div>

                                    <?php
                                    }
                                    ?>

                                    <?php

                                    if ($inbox_all > 20) {

                                        ?>

                                        <div class="more_cont">
                                            <a class="more_link" href="javascript:void(0)" onclick="Hashtags.more('<?php echo $result['postId']; ?>', '<?php echo $query; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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
