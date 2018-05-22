<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $accountId = auth::getCurrentUserId();
    $postId = helper::clearInt($request[2]);

    if (!empty($_POST)) {

        $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

        $result = array();

        $auth = new auth($dbo);

        if (!$auth->authorize($accountId, $accessToken)) {

            api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        }

        $post = new post($dbo);
        $post->setRequestFrom($accountId);

        $postInfo = $post->info($postId);

        if ($postInfo['groupId'] != 0) {

            $group = new group($dbo, $postInfo['groupId']);
            $group->setRequestFrom(auth::getCurrentUserId());

            $groupInfo = $group->get();

            if ($groupInfo['accountAuthor'] == auth::getCurrentUserId()) {

                $post->setRequestFrom($postInfo['fromUserId']);

                $result = $post->remove($postId);

            } else {

                $result = $post->remove($postId);
            }

            $group = new group($dbo, $postInfo['groupId']);
            $group->setRequestFrom(auth::getCurrentUserId());
            $posts_count = $group->getPostsCount();

        } else {

            $result = $post->remove($postId);

            $profile = new profile($dbo, $accountId);
            $posts_count = $profile->getPostsCount();
        }

        if ($posts_count == 0) {

            ob_start();

            ?>

                <div class="info">
                    <?php echo $LANG['label-empty-my-wall']; ?>
                </div>

            <?php

            $result['html'] = ob_get_clean();
        }

        ob_start();

        ?>

            <div class="info">
                <?php echo $LANG['label-post-deleted']; ?>
            </div>

        <?php

        $result['result'] = ob_get_clean();
        $result['postsCount'] = $posts_count;

        echo json_encode($result);
        exit;
    }
