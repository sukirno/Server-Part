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

    if (auth::isSession()) {

        $ticket_email = "";
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

        $old_password = helper::clearText($old_password);
        $new_password = helper::clearText($new_password);

        $old_password = helper::escapeText($old_password);
        $new_password = helper::escapeText($new_password);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if ( !$error ) {

            $account = new account($dbo, $accountId);

            if (auth::getCurrentUserLogin() === 'qascript' && APP_DEMO === true) {

                header("Location: /account/settings/profile/password/?error=demo");
                exit;
            }

            if ( helper::isCorrectPassword($new_password) ) {

                $result = array();

                $result = $account->setPassword($old_password, $new_password);

                if ( $result['error'] === false ) {

                    header("Location: /account/settings/profile/password/?error=false");
                    exit;

                } else {

                    header("Location: /account/settings/profile/password/?error=old_password");
                    exit;
                }

            } else {

                header("Location: /account/settings/profile/password/?error=new_password");
                exit;
            }
        }

        header("Location: /account/settings/profile/password/?error=true");
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "settings_password";

    $css_files = array("style.css");
    $page_title = $LANG['page-profile-password']." | ".APP_TITLE;

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
                                    <span class="title"><?php echo $LANG['page-profile-password']; ?></span>
                                </div>

                                <form action="/account/settings/profile/password" method="post" id="settings_profile_password" class="settings_wrap frm">

                                    <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                                    <?php

                                        if ( isset($_GET['error']) ) {

                                            switch ($_GET['error']) {

                                                case "true" : {

                                                    ?>

                                                        <div class="error">
                                                            <?php echo $LANG['msg-error-unknown']; ?>
                                                        </div>

                                                    <?php

                                                    break;
                                                }

                                                case "old_password" : {

                                                    ?>

                                                        <div class="error">
                                                            <?php echo $LANG['msg-password-save-error']; ?>
                                                        </div>

                                                    <?php

                                                    break;
                                                }

                                                case "new_password" : {

                                                    ?>

                                                        <div class="error">
                                                            <?php echo $LANG['msg-password-incorrect']; ?>
                                                        </div>

                                                    <?php

                                                    break;
                                                }

                                                case "demo" : {

                                                    ?>

                                                        <div class="error">
                                                            Not available! This demo account!
                                                        </div>

                                                    <?php

                                                    break;
                                                }

                                                default: {

                                                    ?>

                                                        <div class="msg">
                                                            <b><?php echo $LANG['label-thanks']; ?></b>
                                                            <br>
                                                            <?php echo $LANG['label-password-saved']; ?>
                                                        </div>

                                                    <?php

                                                    break;
                                                }
                                            }
                                        }
                                    ?>

                                    <div class="options_cont">
                                        <label for="old_password" class="noselect"><?php echo $LANG['label-old-password']; ?></label>
                                        <input type="password" id="old_password" placeholder="" name="old_password" maxlength="16" value="">
                                    </div>

                                    <div class="options_cont">
                                        <label for="new_password" class="noselect"><?php echo $LANG['label-new-password']; ?></label>
                                        <input type="password" id="new_password" placeholder="" name="new_password" maxlength="16" value="">
                                    </div>

                                    <div class="controls_cont">
                                        <button class="primary_btn big_btn"><?php echo $LANG['action-save']; ?></button>
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