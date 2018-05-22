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

        $message_text = isset($_POST['message_text']) ? $_POST['message_text'] : "";
        $message_img = isset($_POST['message_img']) ? $_POST['message_img'] : "";

        $user_id = helper::clearInt($user_id);
        $chat_id = helper::clearInt($chat_id);
        $message_id = helper::clearInt($message_id);

        $message_text = helper::clearText($message_text);

        $message_text = preg_replace( "/[\r\n]+/", "<br>", $message_text); //replace all new lines to one new line
        $message_text  = preg_replace('/\s+/', ' ', $message_text);        //replace all white spaces to one space

        $message_text = helper::escapeText($message_text);

        $message_img = helper::clearText($message_img);
        $message_img = helper::escapeText($message_img);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if ($access_token != auth::getAccessToken()) {

            api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        }

        $profile = new profile($dbo, $user_id);
        $profile->setRequestFrom(auth::getCurrentUserId());

        $profileInfo = $profile->get();

        if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

            echo json_encode($result);
            exit;
        }

        if ($profileInfo['allowMessages'] == 0 && $profileInfo['follower'] === false) {

            echo json_encode($result);
            exit;
        }

        $blacklist = new blacklist($dbo);
        $blacklist->setRequestFrom($user_id);

        if (!$blacklist->isExists(auth::getCurrentUserId())) {

            $messages = new msg($dbo);
            $messages->setRequestFrom(auth::getCurrentUserId());

            $result = $messages->create($user_id, $chat_id, $message_text, $message_img, 0, 0, $profileInfo['gcm_regid']);

            $messages = new messages($dbo);
            $messages->setRequestFrom(auth::getCurrentUserId());

            $result = $messages->getNextMessages($result['chatId'], $message_id);

            ob_start();

            foreach ($result['messages'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();
            $result['items_all'] = $messages->messagesCountByChat($chat_id);
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