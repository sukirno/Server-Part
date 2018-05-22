<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $accountId = auth::getCurrentUserId();
    $photoId = helper::clearInt($request[2]);

    if (!empty($_POST)) {

        $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

        $result = array();

        $auth = new auth($dbo);

        if (!$auth->authorize($accountId, $accessToken)) {

            api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        }

        $photos = new photos($dbo);
        $photos->setRequestFrom($accountId);
        $result = $photos->remove($photoId);

        $profile = new profile($dbo, $accountId);
        $profileInfo = $profile->get();

        if ($profileInfo['photosCount'] == 0) {

            ob_start();

            ?>

                <div class="info">
                    <?php echo $LANG['label-empty-list']; ?>
                </div>

            <?php

            $result['html'] = ob_get_clean();
        }

        $result['photosCount'] = $profileInfo['photosCount'];

        echo json_encode($result);
        exit;
    }
