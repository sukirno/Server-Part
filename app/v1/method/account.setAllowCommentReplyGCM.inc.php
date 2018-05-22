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

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $allowCommentReplyGCM = isset($_POST['allowCommentReplyGCM']) ? $_POST['allowCommentReplyGCM'] : 0;

    $allowCommentReplyGCM = helper::clearInt($allowCommentReplyGCM);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $result = array("error" => false,
                    "error_code" => ERROR_SUCCESS);

    $account = new account($dbo, $accountId);

    $account->setAllowCommentReplyGCM($allowCommentReplyGCM);

    $result['allowCommentReplyGCM'] = $account->getAllowCommentReplyGCM();

    echo json_encode($result);
    exit;
}
