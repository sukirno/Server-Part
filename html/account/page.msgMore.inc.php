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

    $chat_id = 0;
    $user_id = 0;

    if (!empty($_POST)) {

        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        $chat_id = isset($_POST['chat_id']) ? $_POST['chat_id'] : 0;
        $message_id = isset($_POST['message_id']) ? $_POST['message_id'] : 0;
        $messages_loaded = isset($_POST['messages_loaded']) ? $_POST['messages_loaded'] : 0;

        $user_id = helper::clearInt($user_id);
        $chat_id = helper::clearInt($chat_id);
        $message_id = helper::clearInt($message_id);
        $messages_loaded = helper::clearInt($messages_loaded);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $messages = new messages($dbo);
        $messages->setRequestFrom(auth::getCurrentUserId());

        if ($chat_id == 0) {

            $chat_id = $messages->getChatId(auth::getCurrentUserId(), $user_id);
        }

        if ($chat_id != 0) {

            $result = $messages->getPreviousMessages($chat_id, $message_id);

            ob_start();

            foreach (array_reverse($result['messages']) as $key => $value) {

                draw($value, $LANG, $helper);

                $messages_loaded++;
            }

            $result['html'] = ob_get_clean();
            $result['items_all'] = $messages->messagesCountByChat($chat_id);
            $result['items_loaded'] = $messages_loaded;

            if ($messages_loaded < $result['items_all']) {

                ob_start();

                ?>

                    <div class="more_cont">
                        <a class="more_link" href="#" onclick="Messages.more('<?php echo $chat_id ?>', '<?php echo $user_id ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                        <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                    </div>

                <?php

                $result['html2'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

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