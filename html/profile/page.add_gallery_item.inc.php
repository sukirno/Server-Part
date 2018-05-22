<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $toUserId = $helper->getUserId($request[0]);
    $accountId = auth::getCurrentUserId();
    $accessToken = auth::getAccessToken();

    if (!$auth->authorize($accountId, $accessToken)) {

        exit;
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $itemImg = isset($_POST['itemImg']) ? $_POST['itemImg'] : '';
        $itemPreviewImg = isset($_POST['itemPreviewImg']) ? $_POST['itemPreviewImg'] : '';
        $itemOriginImg = isset($_POST['itemOriginImg']) ? $_POST['itemOriginImg'] : '';

        $itemImg = helper::clearText($itemImg);
        $itemImg = helper::escapeText($itemImg);

        $itemPreviewImg = helper::clearText($itemPreviewImg);
        $itemPreviewImg = helper::escapeText($itemPreviewImg);

        $itemOriginImg = helper::clearText($itemOriginImg);
        $itemOriginImg = helper::escapeText($itemOriginImg);

        $result = array("error" => true,
                        "error_description" => "token");

        if (auth::getAuthenticityToken() !== $token) {

            echo json_encode($result);
        }

        $photos = new photos($dbo);
        $photos->setRequestFrom($accountId);
        $result = $photos->add(0, "", $itemOriginImg, $itemPreviewImg, $itemImg);

        ob_start();

        draw($result['photo'], $LANG, $helper);

        $result['html'] = ob_get_clean();

//        $profile = new profile($dbo, $accountId);

//        $result['postsCount'] = $profile->getPostsCount();

        echo json_encode($result);
        exit;
    }

    function draw($photo, $LANG, $helper) {

        ?>

        <div class="photo gallery_item" data-id="<?php echo $photo['id']; ?>">
            <div class="action_box">
                <?php

                if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $photo['fromUserId']) {

                    ?>

                    <div class="action_remove" onclick="Photo.remove('<?php echo $photo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"></div>

                    <?php

                } else {

                    ?>

                    <div class="action_report" onclick="Photo.getReportBox('<?php echo $photo['fromUserUsername']; ?>', '<?php echo $photo['id']; ?>', '<?php echo $LANG['action-report']; ?>'); return false;"></div>

                    <?php
                }
                ?>
            </div>
            <div class="gallery_img">
                <img data-img="<?php echo $photo['imgUrl']; ?>" src="<?php echo $photo['previewImgUrl']; ?>">
            </div>
            <div class="gallery_content">
                <span class="time" style="display: block"><?php echo $photo['timeAgo']; ?></span>
            </div>
        </div>

        <?php
    }
