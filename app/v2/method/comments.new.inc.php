<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

if (!empty($_POST)) {

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : '';
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
    $commentText = isset($_POST['commentText']) ? $_POST['commentText'] : '';

    $replyToUserId = isset($_POST['replyToUserId']) ? $_POST['replyToUserId'] : 0;

    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);

    $itemId = helper::clearInt($itemId);

    $commentText = helper::clearText($commentText);

    $commentText = preg_replace( "/[\r\n]+/", " ", $commentText); //replace all new lines to one new line
    $commentText  = preg_replace('/\s+/', ' ', $commentText);        //replace all white spaces to one space

    $commentText = helper::escapeText($commentText);

    $replyToUserId = helper::clearInt($replyToUserId);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    if (strlen($commentText) != 0) {

        $post = new post($dbo);
        $post->setRequestFrom($accountId);

        $postInfo = $post->info($itemId);

        $blacklist = new blacklist($dbo);
        $blacklist->setRequestFrom($postInfo['fromUserId']);

        if ($blacklist->isExists($accountId)) {

            echo json_encode($result);
            exit;
        }

        if ($postInfo['allowComments'] == 0) {

            exit;
        }

        $comments = new comments($dbo);
        $comments->setRequestFrom($accountId);

        $notifyId = 0;

        $result = $comments->create($itemId, $commentText, $notifyId, $replyToUserId);
    }

    echo json_encode($result);
    exit;
}
