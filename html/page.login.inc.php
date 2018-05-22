<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (auth::isSession()) {

        header("Location: /account/wall");
    }

    $user_username = '';

    $error = false;
    $error_message = '';

    if (!empty($_POST)) {

        $user_username = isset($_POST['user_username']) ? $_POST['user_username'] : '';
        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $user_username = helper::clearText($user_username);
        $user_password = helper::clearText($user_password);

        $user_username = helper::escapeText($user_username);
        $user_password = helper::escapeText($user_password);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            $access_data = array();

            $account = new account($dbo);

            $access_data = $account->signin($user_username, $user_password);

            unset($account);

            if ($access_data['error'] === false) {

                $account = new account($dbo, $access_data['accountId']);

                switch ($account->getState()) {

                    case ACCOUNT_STATE_BLOCKED: {

                        break;
                    }

                    default: {

                        $account->setState(ACCOUNT_STATE_ENABLED);

                        $clientId = 0; // Desktop version

                        $auth = new auth($dbo);
                        $access_data = $auth->create($access_data['accountId'], $clientId);

                        if ($access_data['error'] === false) {

                            auth::setSession($access_data['accountId'], $user_username, $account->getAccessLevel($access_data['accountId']), $access_data['accessToken']);
                            auth::updateCookie($user_username, $access_data['accessToken']);

                            unset($_SESSION['oauth']);
                            unset($_SESSION['oauth_id']);
                            unset($_SESSION['oauth_name']);
                            unset($_SESSION['oauth_email']);
                            unset($_SESSION['oauth_link']);

                            $account->setLastActive();

                            header("Location: /");
                        }
                    }
                }

            } else {

                $error = true;
            }
        }
    }

    auth::newAuthenticityToken();

    $page_id = "login";

    $css_files = array("style.css");
    $page_title = $LANG['page-login']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");
    ?>

<body>

    <div id="page_wrap">

        <?php

            include_once("../html/common/topbar_new.inc.php");
        ?>

        <div id="page_layout" class="no_footer_border">

            <?php

                include_once("../html/common/banner.inc.php");
            ?>

            <div id="page_auth">

                <div class="header">
                    <div class="title">
                        <?php echo $LANG['page-login']; ?>
                    </div>
                </div>

                <div class="center">
                    <a class="btn icon-btn btn-large btn-facebook" href="/facebook/login">
                            <span class="icon-container">
                                <i class="icon icon-facebook"></i>
                            </span>
                            <span>
                                <?php echo $LANG['action-login-with']." ".$LANG['label-facebook']; ?>
                            </span>
                    </a>
                </div>

                <?php

                    if ( $error ) {

                        ?>

                            <div class="error">
                                <?php echo $LANG['msg-error-authorize']; ?>
                            </div>

                        <?php
                    }
                ?>

                <div class="frm">
                    <form action="/login" method="post" id="login_form">
                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">
                        <div class="frm_header">
                            <label class="noselect" for="user_username"><?php echo $LANG['label-username']; ?>:</label>
                        </div>
                        <input autocomplete="off" type="text" id="user_username" class="frm_input" maxlength="24" name="user_username" value="<?php echo $user_username; ?>">
                        <div class="frm_header">
                            <label class="noselect" for="user_password"><?php echo $LANG['label-password']; ?>:</label>
                        </div>
                        <input autocomplete="off" type="password" id="user_password" class="frm_input" maxlength="20" name="user_password" value="">
                        <div class="">
                            <button type="submit" class="frm_btn primary_btn big_btn"><?php echo $LANG['action-login']; ?></button>
                        </div>
                    </form>
                </div>

                <div class="frm_footer">
                    <a href="/remind"><?php echo $LANG['action-forgot-password']; ?></a>
                </div>

            </div>

            <?php

                include_once("../html/common/footer_new.inc.php");
            ?>

        </div>
    </div>

</body>
</html>