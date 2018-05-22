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

    $accountId = auth::getCurrentUserId();

    $error = false;

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $password = isset($_POST['pswd']) ? $_POST['pswd'] : '';

        $password = helper::clearText($password);
        $password = helper::escapeText($password);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if ( !$error ) {

            $account = new account($dbo, $accountId);

            $result = array();

            $result = $account->deactivation($password);

            if ($result['error'] === false) {

                header("Location: /logout/?access_token=".auth::getAccessToken());
                exit;
            }
        }

        header("Location: /account/settings/profile/deactivation/?error=true");
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "settings_deactivation";

    $css_files = array("style.css");
    $page_title = $LANG['page-profile-deactivation']." | ".APP_TITLE;

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
                            <div id="settings">

                                <div class="header">
                                    <span class="title"><?php echo $LANG['page-profile-deactivation']; ?></span>
                                </div>

                                <div class="msg">
                                    <?php echo $LANG['page-profile-deactivation-sub-title']; ?>
                                </div>

                                <form action="/account/settings/profile/deactivation" method="post" class="settings_wrap frm">

                                    <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                                    <?php

                                        if ( isset($_GET['error']) ) {

                                            ?>

                                                <div class="error">
                                                    <?php echo $LANG['msg-error-deactivation']; ?>
                                                </div>

                                            <?php
                                        }
                                    ?>

                                    <div class="options_cont">
                                        <label for="pswd" class="noselect"><?php echo $LANG['label-password']; ?></label>
                                        <input type="password" autocomplete="off" id="pswd" placeholder="" name="pswd" maxlength="16" value="">
                                    </div>

                                    <div class="controls_cont">
                                        <button class="primary_btn big_btn"><?php echo $LANG['action-deactivation-profile']; ?></button>
                                        <a href="javascript:void(0)" class="flat_btn" onclick="history.back(); return false"><?php echo $LANG['action-cancel']; ?></a>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                    <?php

                        include_once("../html/common/footer_new.inc.php");
                    ?>

                </div>
            </div>

            <script type="text/javascript">

                $('textarea[name=about]').autosize();

            </script>

        </div>
    </div>

</body>
</html>