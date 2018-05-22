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
    $profile->setRequestFrom(auth::getCurrentUserId());

    $friends_all = $profile->getFriendsCount();
    $friends_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['id']) ? $_POST['id'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $id = helper::clearInt($id);
        $loaded = helper::clearInt($loaded);

        $result = $profile->getFollowings($itemId);

        $friends_loaded = count($result['friends']);

        $result['friends_loaded'] = $friends_loaded + $loaded;
        $result['friends_all'] = $friends_all;

        if ($friends_loaded != 0) {

            ob_start();

            foreach ($result['friends'] as $key => $value) {

                draw::profileItem($value, $LANG, $helper);
            }

            if ($result['friends_loaded'] < $friends_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Following.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['id']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "friends";

    $css_files = array("style.css", "tipsy.css");
    $page_title = $LANG['page-friends']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-friends']; ?></span>
                                </div>
                            </div>

                            <div id="friends_cont" class="friends_cont">

                                <?php

                                $profile = new profile($dbo, auth::getCurrentUserId());
                                $profile->setRequestFrom(auth::getCurrentUserId());

                                    $result = $profile->getFollowings(0);

                                    $friends_loaded = count($result['friends']);

                                    if ($friends_loaded != 0) {

                                        foreach ($result['friends'] as $key => $value) {

                                            draw::profileItem($value, $LANG, $helper);
                                        }

                                        if ($friends_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="javascript:void(0)" onclick="Following.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['id']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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

            var friends_all = <?php echo $friends_all; ?>;
            var friends_loaded = <?php echo $friends_loaded; ?>;

            $(document).ready(function() {

                $(".page_verified").tipsy({gravity: 'w'});
                $(".verified").tipsy({gravity: 'w'});
            });

        </script>

    </div>
</div>

</body>
</html>
