<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $profileId = $helper->getUserId($request[0]);

    $profile = new profile($dbo, $profileId);

    $profile->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $profile->get();

    if ($profileInfo['error'] === true) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    if ($profileInfo['accountType'] == ACCOUNT_TYPE_GROUP || $profileInfo['accountType'] == ACCOUNT_TYPE_PAGE) {

        include_once("../html/group.inc.php");
        exit;
    }

    $myPage = false;

    if (auth::getCurrentUserId() == $profileId) {

        $myPage = true;

        $account = new account($dbo, $profileId);
        $account->setLastActive();
        unset($account);

    } else {

        if (auth::getCurrentUserId() != 0) {

            $guests = new guests($dbo, $profileId);
            $guests->setRequestFrom(auth::getCurrentUserId());

            $guests->add(auth::getCurrentUserId());
        }
    }

    $accessMode = 0;

    if ($profileInfo['follow'] === true || $myPage) {

        $accessMode = 1;
    }

    $wall = new post($dbo);
    $wall->setProfileId($profileId);
    $wall->setRequestFrom(auth::getCurrentUserId());

    $posts_all = $profileInfo['postsCount'];
    $posts_loaded = 0;

    if (!empty($_POST)) {

        $postId = isset($_POST['postId']) ? $_POST['postId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $postId = helper::clearInt($postId);
        $loaded = helper::clearInt($loaded);

        $result = $wall->get($profileInfo['id'], $postId, $accessMode);

        $posts_loaded = count($result['posts']);

        $result['posts_loaded'] = $posts_loaded + $loaded;
        $result['posts_all'] = $posts_all;

        if ($posts_loaded != 0) {

            ob_start();

            foreach ($result['posts'] as $key => $value) {

                draw::post($value, $LANG, $helper, false);
            }

            if ($result['posts_loaded'] < $posts_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="javascript:void(0)" onclick="Wall.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['postId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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

    $css_files = array("style.css", "tipsy.css", "gifts.css");
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

                        <div class="profile_cover" style="background-image: url(<?php echo $profileCoverUrl; ?>); background-position: <?php echo $profileInfo['coverPosition']; ?>">

                            <?php

                                if ($myPage) {

                                    ?>

                                        <div class="profile_cover_actions" style="">
                                            <div class="cover_actions_content" style="text-align: right;">
                                                <span style="float: left"><?php echo $LANG['label-reposition-cover']; ?></span>
                                                <a style="color: white" href="javascript:void(0)" onclick="Cover.save('<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo $LANG['action-save']; ?></a>
                                                <a style="color: white" href="javascript:void(0)" onclick="Profile.changeCover('<?php echo $LANG['action-change-cover']; ?>'); return false;"><?php echo $LANG['action-change']; ?></a>
                                                <a style="color: white" href="javascript:void(0)" onclick="Cover.delete('<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo $LANG['action-remove']; ?></a>
                                                <a style="color: white" href="javascript:void(0)" onclick="Cover.cancel(); return false;"><?php echo $LANG['action-cancel']; ?></a>
                                            </div>
                                        </div>

                                        <div class="profile_cover_start" style="<?php if (strlen($profileInfo['normalCoverUrl']) == 0 ) echo "display: none;" ?>">
                                            <div class="cover_actions_content" style="text-align: right;">
                                                <a style="color: white; margin: 0" href="javascript:void(0)" onclick="Cover.edit(); return false;"><?php echo $LANG['action-edit']; ?></a>
                                            </div>
                                        </div>

                                        <div class="profile_add_cover" style="<?php if (strlen($profileInfo['normalCoverUrl']) != 0 ) echo "display: none;" ?>">
                                            <span class="cover_button" onclick="Profile.changeCover('<?php echo $LANG['action-change-image']; ?>'); return false;" style="float: none; margin: 8px"><?php echo $LANG['page-profile-upload-cover']; ?></span>
                                        </div>

                                    <?php
                                }
                            ?>

                        </div>

                        <div class="profile_avatar_section noselect" style="">

                            <a href="/<?php echo $profileInfo['username'].$photo; ?>" data-img="<?php echo $profileInfo['normalPhotoUrl'] ?>" class="profile_img_wrap">

                                <img class="user_image" src="<?php echo $profilePhotoUrl; ?>">
                                <?php

                                if ($myPage) {

                                    ?>
                                    <span class="change_image" onclick="Profile.changePhoto('<?php echo $LANG['action-change-photo']; ?>'); return false;"><?php echo $LANG['action-change-photo']; ?></span>
                                    <?php
                                }
                                ?>
                            </a>

                            <div class="user_header">
                                <a href="/<?php echo $profileInfo['username']; ?>"><?php echo $profileInfo['fullname']; ?></a>
                                <?php

                                if ($profileInfo['verify'] == 1) {

                                    ?>
                                    <span class="page_verified" original-title="<?php echo $LANG['label-account-verified']; ?>"></span>
                                    <?php
                                }
                                ?>
                            </div>

                            <div class="user_username">
                                @<?php echo $profileInfo['username']; ?>
                            </div>

                        </div>

                        <div class="profile_wrap" style="">

                            <div class="profile_info_wrap">

                                <div id="addon_block">
                                    <?php

                                        if (auth::isSession() && $myPage) {

                                            ?>

                                            <a href="/account/settings/profile" class="flat_btn noselect"><?php echo $LANG['action-edit-profile']; ?></a>

                                            <?php
                                        }

                                        if (!$myPage) {

                                            ?>

                                            <div class="js_actions_block">

                                                <a href="javascript:void(0)" onclick="Profile.getReportBox('<?php echo $request[0]; ?>', '<?php echo $LANG['page-profile-report']; ?>'); return false;" class="flat_btn js_report_btn noselect" style="padding: 0">
                                                    <img style="width: 26px; height: 26px;" class="msg_img_preview" src="/img/report_icon.png">
                                                </a>

                                                <?php

                                                if (auth::getCurrentUserId() != 0) {

                                                    ?>

                                                    <a href="javascript:void(0)" onclick="Profile.getGiftsBox('<?php echo $request[0]; ?>', '<?php echo $LANG['dlg-select-gift']; ?>'); return false;" class="flat_btn js_gifts_btn noselect" style="padding: 0">
                                                        <img style="width: 26px; height: 26px;" class="msg_img_preview" src="/img/gifts.png">
                                                    </a>

                                                    <?php

                                                    if ($profileInfo['allowMessages'] == 1 || ($profileInfo['allowMessages'] == 0 && $profileInfo['follower'] === true)) {

                                                        ?>
                                                        <a href="/account/chat/?chat_id=0&user_id=<?php echo $profileInfo['id']; ?>" class="flat_btn js_message_btn noselect" style="padding: 0">
                                                            <img style="width: 26px; height: 26px;" class="msg_img_preview" src="/img/message.png">
                                                        </a>
                                                        <?php
                                                    }


                                                    if ($profileInfo['blocked']) {

                                                        ?>

                                                        <a href="javascript:void(0)" data-action="unblock" onclick="Profile.getBlockBox('<?php echo $request[0]; ?>', '<?php echo $LANG['page-profile-block']; ?>'); return false;" class="flat_btn js_block_btn noselect"><?php echo $LANG['action-unblock']; ?></a>

                                                        <?php

                                                    } else {

                                                        ?>

                                                        <a href="javascript:void(0)" data-action="block" onclick="Profile.getBlockBox('<?php echo $request[0]; ?>', '<?php echo $LANG['page-profile-block']; ?>'); return false;" class="flat_btn js_block_btn noselect"><?php echo $LANG['action-block']; ?></a>

                                                        <?php
                                                    }
                                                }
                                                ?>

                                            </div>

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

                                            <?php
                                        }
                                    ?>
                                </div>

                                <div class="profile_data_wrap">

                                    <div class="user_active">
                                        <?php

                                            if ($profileInfo['online']) {

                                                echo "Online";

                                            } else {

                                                if ($profileInfo['lastAuthorize'] == 0) {

                                                    echo "Offline";

                                                } else {

                                                    echo $profileInfo['lastAuthorizeTimeAgo'];
                                                }
                                            }
                                        ?>
                                    </div>

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

                                        if (strlen($profileInfo['fb_page']) > 0) {

                                            ?>

                                                <div class="user_link">
                                                    <a rel="nofollow" target="_blank" href="<?php echo $profileInfo['fb_page']; ?>"><?php echo $profileInfo['fb_page']; ?></a>
                                                </div>

                                            <?php
                                        }
                                    ?>

                                    <?php

                                        if (strlen($profileInfo['instagram_page']) > 0) {

                                            ?>

                                                <div class="user_link">
                                                    <a rel="nofollow" target="_blank" href="<?php echo $profileInfo['instagram_page']; ?>"><?php echo $profileInfo['instagram_page']; ?></a>
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

                                    <?php

                                        if (!$myPage) {

                                            ?>

                                            <?php
                                        }
                                    ?>

                                </div>

                            </div>
                        </div>

                        <div class="profile_form_wrap">

                            <div  class="remotivation_block" style="display:none">
                                <h1><?php echo $LANG['msg-post-sent']; ?></h1>
                                <?php

                                if ($myPage) {

                                    ?>
                                        <button onclick="Profile.showPostForm(); return false;" class="primary_btn"><?php echo $LANG['action-another-post']; ?></button>

                                    <?php

                                }

                                ?>

                            </div>

                            <?php

                                if ($myPage) {

                                    ?>

                                        <form onsubmit="Profile.post('<?php echo $profileInfo['username']; ?>'); return false;" class="profile_question_form" action="/<?php echo $profileInfo['username']; ?>/post" method="post">
                                            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">
                                            <input autocomplete="off" type="hidden" name="postImg" value="">
                                            <textarea name="postText" maxlength="1000" placeholder="<?php echo $LANG['label-placeholder-post']; ?>"></textarea>
                                            <div class="form_actions">
                                                <span id="word_counter">1000</span>
                                                <div class="main_actions" style="float: left; margin-left: 10px">
                                                    <label for="mode_checkbox" class="noselect"><?php echo $LANG['label-for-followers']; ?></label>
                                                    <input id="mode_checkbox" name="mode_checkbox" type="checkbox" style="margin-top: 7px;">
                                                </div>

                                                <div class="main_actions">
                                                    <a href="javascript:void(0)" onclick="Profile.deletePostImg(event); return false;" class="post_img_delete"><?php echo $LANG['action-delete-image']; ?></a>
                                                    <a onclick="Profile.changePostImg('<?php echo $LANG['action-change-image']; ?>'); return false;" class="add_image_to_post" style="">
                                                        <img src="/img/camera.png">
                                                    </a>
                                                </div>
                                                <button class="primary_btn" value="ask"><?php echo $LANG['action-post']; ?></button>
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

                            <div class="header" style="padding: 0; border-bottom: 1px solid #e1e8ed;">
                                <div class="title">
                                    <ul class="profile_tab_container">
                                        <li style="width: 116px">
                                            <a class="tab_button tab_button_active" href="">
                                                <span style="display: block;" class="tab_title"><?php echo $LANG['page-posts']; ?></span>
                                                <span id="stat_posts_count" class="tab_addon"><?php echo $profileInfo['postsCount']; ?></span>
                                            </a>
                                        </li>
                                        <li style="width: 116px">
                                            <a class="tab_button" href="/<?php echo $profileInfo['username']; ?>/gallery">
                                                <span style="display: block;" class="tab_title"><?php echo $LANG['label-photos']; ?></span>
                                                <span id="stat_photos_count" class="tab_addon"><?php echo $profileInfo['photosCount']; ?></span>
                                            </a>
                                        </li>
                                        <li style="width: 116px">
                                            <a class="tab_button" href="/<?php echo $profileInfo['username']; ?>/following">
                                                <span style="display: block;" class="tab_title"><?php echo $LANG['page-following']; ?></span>
                                                <span class="tab_addon"><?php echo $profileInfo['friendsCount']; ?></span>
                                            </a>
                                        </li>
                                        <li style="width: 116px">
                                            <a class="tab_button" href="/<?php echo $profileInfo['username']; ?>/followers">
                                                <span style="display: block;" class="tab_title"><?php echo $LANG['page-followers']; ?></span>
                                                <span id="stat_followers_count" class="tab_addon"><?php echo $profileInfo['followersCount']; ?></span>
                                            </a>
                                        </li>

                                        <li style="width: 116px">
                                            <a class="tab_button" href="/<?php echo $profileInfo['username']; ?>/gifts">
                                                <span style="display: block;" class="tab_title"><?php echo $LANG['page-gifts']; ?></span>
                                                <span id="stat_gifts_count" class="tab_addon"><?php echo $profileInfo['giftsCount']; ?></span>
                                            </a>
                                        </li>

                                    </ul>
<!--                                    <span>--><?php //echo $LANG['label-answers']; ?><!--</span>-->
                                </div>
                            </div>

                            <div class="answers_cont">

                                <?php

                                    $result = $wall->get($profileInfo['id'], 0, $accessMode);

                                    $posts_loaded = count($result['posts']);

                                    if ($posts_loaded != 0) {

                                        foreach ($result['posts'] as $key => $value) {

                                            draw::post($value, $LANG, $helper, false);
                                        }

                                        if ($posts_all > 20) {

                                            ?>

                                            <div class="more_cont">
                                                <a class="more_link" href="javascript:void(0)" onclick="Wall.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['postId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                                            </div>

                                        <?php
                                        }

                                    } else {

                                        $text = $LANG['label-empty-wall'];

                                        if ( $myPage ) {

                                            $text = $LANG['label-empty-my-wall'];
                                        }

                                        ?>

                                        <div class="info">
                                            <?php echo $text; ?>
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

                var max_char = 1000;

                var count = $("textarea[name=postText]").val().length;

                $("span#word_counter").empty();
                $("span#word_counter").html(max_char - count);

                event.preventDefault();
            });

            $(document).ready(function() {

                $(".page_verified").tipsy({gravity: 'w'});
                $(".verified").tipsy({gravity: 'w'});
            });

            window.Profile || ( window.Profile = {} );

            Profile.getGiftsBox = function(username, title) {

                var url = "/" + username + "/select_gifts/?action=get-box";
                $.colorbox({width:"604px", href: url, title: title, top: "50px",});
            };

        </script>

    </div>

</body>
</html>

