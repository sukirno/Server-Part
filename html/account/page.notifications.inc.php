<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
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

    $profile->setLastNotifyView();

    $notifications = new notify($dbo);
    $notifications->setRequestFrom(auth::getCurrentUserId());

    $notifications_all = $notifications->getAllCount();
    $notifications_loaded = 0;

    if (!empty($_POST)) {

        $notifyId = isset($_POST['notifyId']) ? $_POST['notifyId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $notifyId = helper::clearInt($notifyId);
        $loaded = helper::clearInt($loaded);

        $result = $notifications->getAll($notifyId);

        $notifications_loaded = count($result['notifications']);

        $result['notifications_loaded'] = $notifications_loaded + $loaded;
        $result['answers_all'] = $notifications_all;

        if ($notifications_loaded != 0) {

            ob_start();

            foreach ($result['notifications'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            if ($result['notifications_loaded'] < $notifications_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="#" onclick="Notifications.moreAll('<?php echo $result['notifyId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "notifications";

    $css_files = array("style.css");
    $page_title = $LANG['page-notifications-likes']." | ".APP_TITLE;

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

        <div id="page_body" style="width: 791px;">

            <?php

                include_once("../html/common/sidebar.inc.php");
            ?>

            <div id="wrap3">
                <div id="wrap2">
                    <div id="wrap1">
                        <div id="content">

                            <div class="header">
                                <div class="title">
                                    <span><?php echo $LANG['page-notifications-likes']; ?></span>
                                </div>
                            </div>

                            <div id="notifications_cont" class="notifications_cont">

                                <?php

                                    $result = $notifications->getAll(0);

                                    $notifications_loaded = count($result['notifications']);

                                    if ($notifications_loaded != 0) {

                                        foreach ($result['notifications'] as $key => $value) {

                                            draw($value, $LANG, $helper);
                                        }

                                        if ($notifications_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="#" onclick="Notifications.moreAll('<?php echo $result['notifyId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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

            var notifications_all = <?php echo $notifications_all; ?>;
            var notifications_loaded = <?php echo $notifications_loaded; ?>;

        </script>



    </div>
</div>

</body>
</html>

<?php

    function draw($notify, $LANG, $helper) {

        switch ($notify['type']) {

            case NOTIFY_TYPE_LIKE: {

                $post = new post(NULL);
                $post->setRequestFrom(auth::getCurrentUserId());

                $post = $post->info($notify['postId']);

                ?>

                <div class="post post_item" data-id="<?php echo $notify['id']; ?>">
                    <div class="profile_cont">
                        <a href="/<?php echo $notify['fromUserUsername']; ?>">
                            <img src="<?php if ( strlen($notify['fromUserPhotoUrl']) != 0 ) { echo $notify['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                        </a>
                    </div>
                    <div class="post_cont">
                        <div class="likes_notification">
                            <a href="/<?php echo $notify['fromUserUsername']; ?>"><?php echo $notify['fromUserFullname']; ?></a>
                            <span><?php echo $LANG['label-likes-your-post'] ?></span>
                            <a href="/<?php echo $post['fromUserUsername']; ?>/post/<?php echo $notify['postId']; ?>"><?php echo $LANG['action-go-to-post']; ?> »</a>
                        </div>

                        <div class="post_footer">
                            <?php

                                $time = new language(NULL, $LANG['lang-code']);
                            ?>
                            <span class="time"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                        </div>
                    </div>
                </div>

                <?php

                break;
            }

            case NOTIFY_TYPE_FOLLOWER: {

                ?>

                <div class="post post_item" data-id="<?php echo $notify['id']; ?>">
                    <div class="profile_cont">
                        <a href="/<?php echo $notify['fromUserUsername']; ?>">
                            <img src="<?php if ( strlen($notify['fromUserPhotoUrl']) != 0 ) { echo $notify['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                        </a>
                    </div>
                    <div class="post_cont">
                        <div class="likes_notification">
                            <a href="/<?php echo $notify['fromUserUsername']; ?>"><?php echo $notify['fromUserFullname']; ?></a>
                            <span><?php echo $LANG['label-follow-your'] ?>.</span>
                        </div>

                        <div class="post_footer">
                            <?php

                                $time = new language(NULL, $LANG['lang-code']);
                            ?>
                            <span class="time"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                        </div>
                    </div>
                </div>

                <?php

                break;
            }

            case NOTIFY_TYPE_COMMENT: {

                $post = new post(NULL);
                $post->setRequestFrom(auth::getCurrentUserId());

                $post = $post->info($notify['postId']);

                ?>

                    <div class="post post_item" data-id="<?php echo $notify['id']; ?>">

                        <div class="profile_cont">
                            <a href="/<?php echo $notify['fromUserUsername']; ?>">
                                <img src="<?php if (strlen($notify['fromUserPhotoUrl']) != 0) { echo $notify['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                            </a>
                        </div>

                        <div class="post_cont">
                            <div class="likes_notification">
                                <a href="/<?php echo $notify['fromUserUsername']; ?>"><?php echo $notify['fromUserFullname']; ?></a>
                                    <span> <?php echo $LANG['label-new-comment'] ?> </span>
                                <a href="/<?php echo $post['fromUserUsername']; ?>/post/<?php echo $notify['postId']; ?>"><?php echo $LANG['action-go-to-post']; ?> »</a>
                            </div>

                            <div class="post_footer">
                                <?php

                                    $time = new language(NULL, $LANG['lang-code']);
                                ?>
                                <span class="time"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                            </div>
                        </div>
                    </div>

                <?php

                break;
            }

            case NOTIFY_TYPE_COMMENT_REPLY: {

                $post = new post(NULL);
                $post->setRequestFrom(auth::getCurrentUserId());

                $post = $post->info($notify['postId']);

                ?>

                    <div class="post post_item" data-id="<?php echo $notify['id']; ?>">

                        <div class="profile_cont">
                            <a href="/<?php echo $notify['fromUserUsername']; ?>">
                                <img src="<?php if (strlen($notify['fromUserPhotoUrl']) != 0) { echo $notify['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                            </a>
                        </div>

                        <div class="post_cont">
                            <div class="likes_notification">
                                <a href="/<?php echo $notify['fromUserUsername']; ?>"><?php echo $notify['fromUserFullname']; ?></a>
                                    <span> <?php echo $LANG['label-new-reply-to-comment'] ?> </span>
                                <a href="/<?php echo $post['fromUserUsername']; ?>/post/<?php echo $notify['postId']; ?>"><?php echo $LANG['action-go-to-post']; ?> »</a>
                            </div>

                            <div class="post_footer">
                                <?php

                                    $time = new language(NULL, $LANG['lang-code']);
                                ?>
                                <span class="time"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                            </div>
                        </div>
                    </div>

                <?php

                break;
            }

            case NOTIFY_TYPE_GIFT: {

                ?>

                    <div class="post post_item" data-id="<?php echo $notify['id']; ?>">

                        <div class="profile_cont">
                            <a href="/<?php echo $notify['fromUserUsername']; ?>">
                                <img src="<?php if (strlen($notify['fromUserPhotoUrl']) != 0) { echo $notify['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                            </a>
                        </div>

                        <div class="post_cont">
                            <div class="likes_notification">
                                <a href="/<?php echo $notify['fromUserUsername']; ?>"><?php echo $notify['fromUserFullname']; ?></a>
                                    <span> <?php echo $LANG['label-new-gift'] ?> </span>
                                <a href="/<?php echo auth::getCurrentUserLogin(); ?>/gifts"><?php echo $LANG['action-view']; ?> »</a>
                            </div>

                            <div class="post_footer">
                                <?php

                                    $time = new language(NULL, $LANG['lang-code']);
                                ?>
                                <span class="time"><?php echo $time->timeAgo($notify['createAt']); ?></span>
                            </div>
                        </div>
                    </div>

                <?php

                break;
            }
            
            default: {


                break;
            }
        }
    }

?>