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

    $showForm = true;

    $chat_id = 0;
    $user_id = 0;

    $chat_info = array("messages" => array());
    $user_info = array();
    $profile_info = array();

    $profile = new profile($dbo, auth::getCurrentUserId());
    $profile_info = $profile->get();

    $messages = new messages($dbo);
    $messages->setRequestFrom(auth::getCurrentUserId());

    if (!isset($_GET['chat_id']) && !isset($_GET['user_id'])) {

        header('Location: /');
        exit;

    } else {

        $chat_id = isset($_GET['chat_id']) ? $_GET['chat_id'] : 0;
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;

        $chat_id = helper::clearInt($chat_id);
        $user_id = helper::clearInt($user_id);

        $user = new profile($dbo, $user_id);
        $user->setRequestFrom(auth::getCurrentUserId());
        $user_info = $user->get();
        unset($user);

        if ($user_info['error'] === true) {

            header('Location: /');
            exit;
        }

        $chat_id_test = $messages->getChatId(auth::getCurrentUserId(), $user_id);

        if ($chat_id != 0 && $chat_id_test != $chat_id) {

            header('Location: /');
            exit;
        }

        if ($chat_id == 0) {

            $chat_id = $messages->getChatId(auth::getCurrentUserId(), $user_id);

            if ($chat_id != 0) {

                header('Location: /account/chat/?chat_id='.$chat_id.'&user_id='.$user_id);
                exit;
            }
        }

        if ($chat_id != 0) {

            $chat_info = $messages->get($chat_id, 0);
        }
    }

    if ($user_info['state'] != ACCOUNT_STATE_ENABLED) {

        $showForm = false;
    }

    if ($user_info['allowMessages'] == 0 && $user_info['follower'] === false) {

        $showForm = false;
    }

    $blacklist = new blacklist($dbo);
    $blacklist->setRequestFrom($user_info['id']);

    if ($blacklist->isExists(auth::getCurrentUserId())) {

        $showForm = false;
    }

    $items_all = $messages->messagesCountByChat($chat_id);
    $items_loaded = 0;

    $page_id = "chat";

    $css_files = array("style.css");
    $page_title = $LANG['page-messages']." | ".APP_TITLE;

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
                                    <span><?php echo $user_info['fullname']; ?></span>
                                </div>
                            </div>

                            <div id="messages_cont" class="messages_cont">

                                <?php

                                    $result = $chat_info;

                                    $items_loaded = count($result['messages']);

                                    if ($items_loaded != 0) {

                                        if ($items_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="#" onclick="Messages.more('<?php echo $chat_id ?>', '<?php echo $user_id ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                                                </div>

                                            <?php
                                        }

                                        foreach (array_reverse($result['messages']) as $key => $value) {

                                            draw($value, $LANG, $helper);
                                        }

                                        ?>

                                        <?php


                                        ?>

                                        <?php

                                    } else {

                                        ?>

                                            <div class="info">
                                                <?php echo $LANG['label-empty-list']; ?>
                                            </div>

                                        <?php
                                    }

                                    if ($showForm) {

                                        ?>

                                            <div class="comment_form">

                                                <span class="comment_form_profile">
                                                    <img class="comment_switch" src="<?php if (strlen($profile_info['lowPhotoUrl']) != 0 ) { echo $profile_info['lowPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                                                </span>

                                                <form class="" onsubmit="Messages.create('<?php echo $chat_id; ?>', '<?php echo $user_id; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;">
                                                    <input type="hidden" name="message_image" value="">
                                                    <input class="comment_text" style="width: 411px" name="message_text" maxlength="340" placeholder="<?php echo $LANG['label-placeholder-message']; ?>" type="text" value="">
                                                    <a onclick="Messages.changeChatImg('<?php echo $LANG['action-change-image']; ?>'); return false;" class="add_image_to_post" style="">
                                                        <img style="width: 26px; height: 26px;" class="msg_img_preview" src="/img/camera.png">
                                                    </a>
                                                    <button class="primary_btn comment_send"><?php echo $LANG['action-send']; ?></button>
                                                </form>

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

            App.chatInit('<?php echo $chat_id; ?>', '<?php echo $user_id; ?>', '<?php echo auth::getAccessToken(); ?>');

        </script>

        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

    </div>
</div>

</body>
</html>

<?php

    function draw($message, $LANG, $helper) {

        ?>

                <div class="post post_item" data-id="<?php echo $message['id']; ?>">
                    <div class="profile_cont">
                        <a href="/<?php echo $message['fromUserUsername']; ?>">
                            <img style="width: 40px; height: 40px;" src="<?php if (strlen($message['fromUserPhotoUrl']) != 0 ) { echo $message['fromUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                        </a>
                    </div>
                    <div class="post_cont" style="margin-left: 50px; min-height: 50px;">

                        <?php

                            if (strlen($message['message']) > 0) {

                                ?>
                                    <div class="post_data" style="padding-top: 0px;"><?php echo $message['message']; ?></div>
                                <?php
                            }

                            if (strlen($message['imgUrl']) > 0) {

                                ?>
                                    <div class="post_img">
                                        <img src="<?php echo $message['imgUrl']; ?>">
                                    </div>
                                <?php
                            }
                        ?>

                        <div class="post_footer">
                            <?php

                                $time = new language(NULL, $LANG['lang-code']);
                            ?>
                            <span class="time"><?php echo $time->timeAgo($message['createAt']); ?></span>
                        </div>
                    </div>
                </div>

        <?php
    }

?>