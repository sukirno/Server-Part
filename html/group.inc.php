<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $groupId = $profileInfo['id'];

    $myPage = false;

    if (auth::getCurrentUserId() == $profileInfo['accountAuthor']) {

        $myPage = true;
    }

    $accessMode = 0;

    if ($profileInfo['follow'] === true || $myPage) {

        $accessMode = 1;
    }

    $group = new group($dbo, $groupId);
    $group->setRequestFrom(auth::getCurrentUserId());

    $groupInfo = $group->get();

    $posts_all = $profileInfo['postsCount'];
    $posts_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $group->getPosts($itemId);

        $posts_loaded = count($result['posts']);

        $result['posts_loaded'] = $posts_loaded + $loaded;
        $result['posts_all'] = $posts_all;

        if ($posts_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                community::post($value, $LANG, $helper, false);
            }

            if ($result['posts_loaded'] < $posts_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Group.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $profileCoverUrl = $profileInfo['normalCoverUrl'];

    if (strlen($profileCoverUrl) == 0) {

        if ($myPage) {

            $profileCoverUrl = "/img/cover_add.png";

        } else {

            $profileCoverUrl = "/img/cover_none.png";
        }
    }

    $profilePhotoUrl = APP_URL."/img/profile_default_photo.png";
    $photo = '';

    if (strlen($profileInfo['bigPhotoUrl']) != 0) {

        $profilePhotoUrl = $profileInfo['bigPhotoUrl'];
        $photo = "/photo";
    }

    auth::newAuthenticityToken();

    $page_id = "profile";

    $css_files = array("style.css", "tipsy.css", "group.css");
    $page_title = $profileInfo['fullname']." | ".APP_HOST."/".$profileInfo['username'];

    include_once("../html/common/header.inc.php");
?>

<body class="bg_gray">

    <div id="page_wrap">

    <?php

        include_once("../html/common/topbar_new.inc.php");
    ?>

    <div id="page_layout" class="profile_page">

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

                        <div class="profile_wrap" style="">

                            <a href="/<?php echo $profileInfo['username'].$photo; ?>" data-img="<?php echo $profileInfo['normalPhotoUrl'] ?>" class="profile_img_wrap">

                                <img class="user_image" src="<?php echo $profilePhotoUrl; ?>">
                                <?php

                                if ($myPage) {

                                    ?>
                                    <span class="change_image" onclick="Group.changePhoto('<?php echo $groupInfo['username']; ?>', '<?php echo $LANG['action-change-photo']; ?>'); return false;"><?php echo $LANG['action-change-photo']; ?></span>
                                    <?php
                                }
                                ?>
                            </a>

                            <div class="profile_info_wrap">

                                <div id="stat">
                                    <a href="/<?php echo $groupInfo['username']; ?>/followers">
                                        <span id="stat_followers_count" class="digit"><?php echo $groupInfo['followersCount']; ?></span>
                                        <span class="stat_label"><?php echo $LANG['page-followers'] ?></span>
                                    </a>
                                    <div class="stat_separator"></div>
                                    <a href="javascript:void(0)">
                                        <span id="stat_posts_count" class="digit"><?php echo $groupInfo['postsCount']; ?></span>
                                        <span class="stat_label"><?php echo $LANG['page-posts'] ?></span>
                                    </a>
                                </div>

                                <div class="user_header">
                                    <a href="/<?php echo $profileInfo['username']; ?>"><?php echo $profileInfo['fullname']; ?></a>
                                    <?php

                                    if ($profileInfo['verify'] == 1) {

                                        ?>
                                        <span class="page_verified" original-title="<?php echo $LANG['label-account-verified']; ?>" style="top: -1px"></span>
                                        <?php
                                    }
                                    ?>
                                </div>


                                <div class="profile_data_wrap">

                                    <?php

                                        if (strlen($profileInfo['location']) > 0) {

                                            ?>

                                                <div class="user_location">
                                                    <?php echo $profileInfo['location']; ?>
                                                </div>

                                            <?php
                                        }
                                    ?>

                                    <?php

                                        if (strlen($profileInfo['my_page']) > 0) {

                                            ?>

                                                <div class="user_link">
                                                    <a rel="nofollow" target="_blank" href="<?php echo $profileInfo['my_page']; ?>"><?php echo $profileInfo['my_page']; ?></a>
                                                </div>

                                            <?php
                                        }
                                    ?>

                                    <?php

                                        if (strlen($profileInfo['status']) > 0) {

                                            ?>

                                                <div class="user_status">
                                                    <?php echo $profileInfo['status']; ?>
                                                </div>

                                            <?php
                                        }
                                    ?>

                                </div>

                                <div class="user_actions">
                                    <div id="addon_block" style="float: none;">
                                        <?php

                                        if (auth::isSession() && $myPage) {

                                            ?>

                                            <a href="/<?php echo $groupInfo['username']; ?>/settings" class="flat_btn noselect"><?php echo $LANG['page-settings']; ?></a>

                                            <?php
                                        }
                                        ?>

                                        <div class="js_follow_block">
                                            <a class="<?php if ($profileInfo['follow']) {echo "white_btn";} else { echo "green_btn"; } ?> js_follow_btn" href="javascript:void(0)" onclick="Users.follow('<?php echo $request[0]; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;">
                                                <?php

                                                if ($profileInfo['follow']) {

                                                    echo $LANG['action-unfollow'];

                                                } else {

                                                    echo $LANG['action-follow'];
                                                }
                                                ?>
                                            </a>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="profile_form_wrap">

                            <div  class="remotivation_block" style="display:none">
                                <h1><?php echo $LANG['msg-post-sent']; ?></h1>
                                <?php

                                if (auth::getCurrentUserId() != 0 && ($myPage || $groupInfo['allowPosts'] == 1)) {

                                    ?>
                                        <button onclick="Profile.showPostForm(); return false;" class="primary_btn"><?php echo $LANG['action-another-post']; ?></button>

                                    <?php

                                }

                                ?>

                            </div>

                            <?php

                            if (auth::getCurrentUserId() != 0 && ($myPage || $groupInfo['allowPosts'] == 1)) {

                                    ?>

                                        <form onsubmit="Profile.post('<?php echo $profileInfo['username']; ?>'); return false;" class="profile_question_form" action="/<?php echo $profileInfo['username']; ?>/post" method="post">
                                            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">
                                            <input autocomplete="off" type="hidden" name="postImg" value="">
                                            <textarea name="postText" maxlength="400" placeholder="<?php echo $LANG['label-placeholder-post']; ?>"></textarea>
                                            <div class="form_actions">
                                                <span id="word_counter">400</span>
                                                <div class="main_actions">
                                                    <a href="javascript:void(0)" onclick="Profile.deletePostImg(event); return false;" class="post_img_delete"><?php echo $LANG['action-delete-image']; ?></a>
                                                    <a onclick="Profile.changePostImg('<?php echo $LANG['action-change-image']; ?>'); return false;" class="add_image_to_post" style="">
                                                        <img src="/img/camera.png">
                                                    </a>
                                                </div>
                                                <button class="primary_btn" value="action"><?php echo $LANG['action-post']; ?></button>
                                                <div class="img_container" style="">
                                                    <img class="post_img_preview" style="" src=""/>
                                                </div>
                                            </div>
                                        </form>

                                    <?php
                                }
                            ?>

                        </div>

                        <div id="content">

                            <div class="answers_cont">

                                <?php

                                    $result = $group->getPosts(0);

                                    $posts_loaded = count($result['items']);

                                    if ($posts_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            community::post($value, $LANG, $helper, false);
                                        }

                                        if ($posts_all > 20) {

                                            ?>

                                            <div class="more_cont">
                                                <a class="more_link" href="javascript:void(0)" onclick="Group.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                                            </div>

                                        <?php
                                        }

                                    } else {

                                        ?>

                                        <div class="info">
                                            <?php echo $LANG['label-empty-page']; ?>
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

        <script type="text/javascript" src="/js/jquery.tipsy.js"></script>
        <script type="text/javascript" src="/js/draggable_background.js"></script>
        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

        <script type="text/javascript">

            var posts_all = <?php echo $posts_all; ?>;
            var posts_loaded = <?php echo $posts_loaded; ?>;

            var auth_token = "<?php echo auth::getAuthenticityToken(); ?>";

            <?php

                if ($myPage) {

                    ?>
                        var myPage = true;
                    <?php

                    if (strlen($profileInfo['normalCoverUrl']) != 0) {

                        ?>

                            var CoverExists = true;

                        <?php

                    } else {

                        ?>

                            var CoverExists = false;

                        <?php
                    }

                    if (strlen($profileInfo['bigPhotoUrl']) != 0) {

                        ?>
                            var PhotoExists = true;
                        <?php

                    } else {

                        ?>
                            var PhotoExists = false;
                        <?php
                    }
                }
             ?>

            $("textarea[name=postText]").autosize();

            $("textarea[name=postText]").bind('keyup mouseout', function() {

                var max_char = 400;

                var count = $("textarea[name=postText]").val().length;

                $("span#word_counter").empty();
                $("span#word_counter").html(max_char - count);

                event.preventDefault();
            });

            $(document).ready(function() {

                $(".page_verified").tipsy({gravity: 'w'});
                $(".verified").tipsy({gravity: 'w'});
            });

        </script>

    </div>

</body>
</html>

