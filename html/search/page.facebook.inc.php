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

    $account = new account($dbo, auth::getCurrentUserId());
    $accountInfo = $account->get();

    require '../html/facebook/facebook.php';

    if ($accountInfo['fb_id'] != 0) {

        $facebook = new facebook(array(
            'appId' => FACEBOOK_APP_ID,
            'secret' => FACEBOOK_APP_SECRET,
        ));

        $user = $facebook->getUser();

        if ($user) {


        } else {

            $login_url = $facebook->getLoginUrl(array('scope' => 'email, user_friends'));
            header("Location: " . $login_url);
        }
    }

    $page_id = "fb_search";

    $css_files = array("style.css", "tipsy.css");
    $page_title = $LANG['page-search']." | ".APP_TITLE;

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

                                <a href="/search/name" class="title"><?php echo $LANG['page-search']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/groups" class="title"><?php echo $LANG['label-groups']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/hashtag" class="title"><?php echo $LANG['page-hashtags']; ?></a>
                                <span class="divider">|</span>
                                <a href="/search/facebook" class="title active">Facebook</a>
                                <span class="divider">|</span>
                                <a href="/search/nearby" class="title"><?php echo $LANG['page-nearby']; ?></a>
                            </div>

                            <div id="search_cont" class="search_cont">

                                <?php

                                if ($accountInfo['fb_id'] != 0) {

                                    $user = $facebook->getUser();

                                    if ($user) {

                                        $friends = $facebook->api('/me/friends');

                                        $total_friends = 0;

                                        foreach ($friends["data"] as $value) {

                                            $user_id = $helper->getUserIdByFacebook($value['id']);

                                            if ($user_id != 0) {

                                                $total_friends++;
                                            }
                                        }

                                        if ($total_friends > 0) {

                                            ?>

                                            <div class="header" style="padding: 16px 0 16px 0; border-bottom: solid 1px #eee;">
                                                <div class="title" style="color: #777">
                                                    <span><?php echo $LANG['label-search-result']; ?></span>
                                                    <span id="search_count">(<?php echo $total_friends; ?>)</span>
                                                </div>
                                            </div>

                                            <?php

                                            foreach ($friends["data"] as $value) {

                                                $user_id = $helper->getUserIdByFacebook($value['id']);

                                                if ($user_id != 0) {

                                                    $user = new profile($dbo, $user_id);
                                                    $user->setRequestFrom(auth::getCurrentUserId());

                                                    $userInfo = $user->get();

                                                    draw::profileItem($userInfo, $LANG, $helper);

                                                    unset($userInfo);
                                                    unset($user);
                                                }
                                            }

                                        } else {

                                            ?>
                                                <div class="info">
                                                    <?php echo $LANG['label-social-search-not-found']; ?>
                                                </div>
                                            <?php
                                        }
                                    }

                                } else {

                                    ?>
                                        <div class="info">
                                            <div class="social_search"><?php echo $TEXT['label-social-search']; ?></div>
                                            <a href="/account/settings/services"><?php echo $TEXT['fb-linking']; ?></a>
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

        <script type="text/javascript">

            $(document).ready(function() {

                $(".page_verified").tipsy({gravity: 'w'});
                $(".verified").tipsy({gravity: 'w'});
            });

        </script>

    </div>
</div>

</body>
</html>
