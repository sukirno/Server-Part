<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $profileId = $helper->getUserId($request[0]);

    $postExists = true;

    $profile = new profile($dbo, $profileId);

    $profile->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $profile->get();

    if ($profileInfo['error'] === true) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    $post = new post($dbo);
    $post->setRequestFrom(auth::getCurrentUserId());

    $postId = helper::clearInt($request[2]);

    $postInfo = $post->info($postId);

    if ($postInfo['error'] === true) {

        // Missing
        $postExists = false;
    }

    if ($postExists && $postInfo['removeAt'] != 0) {

        // Missing
        $postExists = false;
    }

    if ($postExists && $profileInfo['id'] != $postInfo['fromUserId'] ) {

        // Missing
        $postExists = false;
    }

    $items_all = 0;

    if ($postExists) {

        $items_all = $postInfo['likesCount'];
    }

    $items_loaded = 0;

    if (!empty($_POST)) {

        $likeId = isset($_POST['likeId']) ? $_POST['likeId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $likeId = helper::clearInt($likeId);
        $loaded = helper::clearInt($loaded);

        $result = $post->getLikers($postInfo['id'], $likeId);

        $items_loaded = count($result['likers']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['likers'] as $key => $value) {

                draw::profileItem($value, $LANG, $helper);
            }

            if ($result['items_loaded'] < $items_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Likers.more('<?php echo $profileInfo['username']; ?>', '<?php echo $postInfo['id']; ?>', '<?php echo $result['likeId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "people";

    $css_files = array("style.css", "tipsy.css");
    $page_title = $LANG['page-likes']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-likes']; ?></span>
                                </div>
                            </div>

                            <div id="people_cont" class="people_cont">

                                <?php

                                    if ($postExists) {

                                        $result = $post->getLikers($postInfo['id'], 0);

                                        $items_loaded = count($result['likers']);

                                        if ($items_loaded != 0) {

                                            foreach ($result['likers'] as $key => $value) {

                                                draw::profileItem($value, $LANG, $helper);
                                            }

                                            if ($items_all > 20) {

                                                ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="javascript:void(0)" onclick="Likers.more('<?php echo $profileInfo['username']; ?>', '<?php echo $postInfo['id']; ?>', '<?php echo $result['likeId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                                                </div>

                                                <?php
                                            }

                                        } else {

                                            ?>

                                            <div class="info">
                                                <span>
                                                    <?php echo $LANG['label-empty-list']; ?>
                                                    <a href="/<?php echo $postInfo['fromUserUsername']; ?>/post/<?php echo $postInfo['id']; ?>"><?php echo $LANG['action-go-to-post']; ?></a>.
                                                </span>
                                            </div>

                                            <?php
                                        }

                                    } else {

                                        ?>

                                        <div class="info">
                                            <?php echo $LANG['label-post-missing']; ?>
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

        </script>

    </div>
</div>

</body>
</html>
