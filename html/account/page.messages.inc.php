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

    $messages = new messages($dbo);
    $messages->setRequestFrom(auth::getCurrentUserId());

    if (isset($_GET['action'])) {

        $messages_count = $messages->getNewMessagesCount();

        echo $messages_count;
        exit;
    }

    $account = new account($dbo, auth::getCurrentUserId());
    $account->setLastActive();
    unset($account);

    $chats_all = $messages->myActiveChatsCount();
    $chats_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $messages->getChats($itemId);

        $chats_loaded = count($result['chats']);

        $result['chats_loaded'] = $chats_loaded + $loaded;
        $result['chats_all'] = $chats_all;

        if ($chats_loaded != 0) {

            ob_start();

            foreach ($result['chats'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            if ($result['chats_loaded'] < $chats_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="#" onclick="Chats.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "messages";

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
                                    <span><?php echo $LANG['page-messages']; ?></span>
                                </div>
                            </div>

                            <div id="messages_cont" class="messages_cont">

                                <?php

                                    $result = $messages->getChats(0);

                                    $chats_loaded = count($result['chats']);

                                    if ($chats_loaded != 0) {

                                        foreach ($result['chats'] as $key => $value) {

                                            draw($value, $LANG, $helper);
                                        }

                                        if ($chats_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="#" onclick="Chats.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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

            var chats_all = <?php echo $chats_all; ?>;
            var chats_loaded = <?php echo $chats_loaded; ?>;

        </script>

    </div>
</div>

</body>
</html>

<?php

    function draw($chat, $LANG, $helper) {

        ?>

                <div class="post post_item" data-id="<?php echo $chat['id']; ?>">
                    <div class="profile_cont">
                        <a href="/<?php echo $chat['withUserUsername']; ?>">
                            <img src="<?php if ( strlen($chat['withUserPhotoUrl']) != 0 ) { echo $chat['withUserPhotoUrl']; } else { echo "/img/profile_default_photo.png"; } ?>">
                        </a>
                    </div>
                    <div class="post_content">

                        <div class="action_remove" onclick="Messages.removeChat('<?php echo $chat['id']; ?>', '<?php echo $chat['withUserId']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"></div>

                        <div class="addon_info">
                            <a class="fullname" href="/<?php echo $chat['withUserUsername']; ?>"><?php echo $chat['withUserFullname']; ?></a>
                            <?php if ($chat['withUserVerify'] == 1) echo "<b original-title=\"{$LANG['label-account-verified']}\" class=\"verified\"></b>"; ?>
                        </div>

                        <div class="post_data" style="padding-top: 5px">
                            <span>
                            <?php

                                if (strlen($chat['lastMessage']) == 0) {

                                    echo "Image";

                                } else {

                                    echo $chat['lastMessage'];
                                }

                                if ($chat['newMessagesCount'] != 0) {

                                    ?>

                                        <span class="messages_counter"><?php echo $chat['newMessagesCount']; ?></span>

                                    <?php
                                }
                            ?>
                            </span>
                        </div>

                        <div class="post_footer">
                            <?php

                                $time = new language(NULL, $LANG['lang-code']);
                            ?>
                            <span class="time"><?php echo $time->timeAgo($chat['lastMessageCreateAt']); ?></span>
                            <a class="right" href="/account/chat/?chat_id=<?php echo $chat['id']; ?>&user_id=<?php echo $chat['withUserId']; ?>"><?php echo $LANG['action-go-to-conversation']; ?></a>
                        </div>
                    </div>
                </div>

        <?php
    }

?>