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
    $user_email = '';
    $user_fullname = '';

    $error = false;
    $error_message = '';

    if (!empty($_POST)) {

        $error = false;

        $user_username = isset($_POST['username']) ? $_POST['username'] : '';
        $user_fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $user_password = isset($_POST['password']) ? $_POST['password'] : '';
        $user_email = isset($_POST['email']) ? $_POST['email'] : 0;
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $user_username = helper::clearText($user_username);
        $user_fullname = helper::clearText($user_fullname);
        $user_password = helper::clearText($user_password);
        $user_email = helper::clearText($user_email);

        $user_username = helper::escapeText($user_username);
        $user_fullname = helper::escapeText($user_fullname);
        $user_password = helper::escapeText($user_password);
        $user_email = helper::escapeText($user_email);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
            $error_token = true;
            $error_message[] = $LANG['msg-error-unknown'];
        }

        if (!helper::isCorrectLogin($user_username)) {

            $error = true;
            $error_username = true;
            $error_message[] = $LANG['msg-login-incorrect'];
        }

        if ($helper->isLoginExists($user_username)) {

            $error = true;
            $error_username = true;
            $error_message[] = $LANG['msg-login-taken'];
        }

        if (!helper::isCorrectFullname($user_fullname)) {

            $error = true;
            $error_fullname = true;
            $error_message[] = $LANG['msg-fullname-incorrect'];
        }

        if (!helper::isCorrectPassword($user_password)) {

            $error = true;
            $error_password = true;
            $error_message[] = $LANG['msg-password-incorrect'];
        }

        if (!helper::isCorrectEmail($user_email)) {

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-email-incorrect'];
        }

        if ($helper->isEmailExists($user_email)) {

            $error = true;
            $error_email = true;
            $error_message[] = $LANG['msg-email-taken'];
        }

        if (!$error) {

            $account = new account($dbo);

            $result = array();
            $result = $account->signup($user_username, $user_fullname, $user_password, $user_email, $LANG['lang-code']);

            if ($result['error'] === false) {

                $clientId = 0; // Desktop version

                $auth = new auth($dbo);
                $access_data = $auth->create($result['accountId'], $clientId);

                if ($access_data['error'] === false) {

                    auth::setSession($access_data['accountId'], $user_username, $account->getAccessLevel($access_data['accountId']), $access_data['accessToken']);
                    auth::updateCookie($user_username, $access_data['accessToken']);

                    $language = $account->getLanguage();

                    $account->setState(ACCOUNT_STATE_ENABLED);

                    $account->setLastActive();

                    //Facebook connect

                    if (isset($_SESSION['oauth']) && $_SESSION['oauth'] === 'facebook' && $helper->getUserIdByFacebook($_SESSION['oauth_id']) == 0) {

                        $account->setFacebookId($_SESSION['oauth_id']);

                        $time = time();
                        $fb_id = $_SESSION['oauth_id'];

                        $img = @file_get_contents('https://graph.facebook.com/'.$fb_id.'/picture?type=large');
                        $file =  TEMP_PATH.$time.".jpg";
                        @file_put_contents($file, $img);

                        $imglib = new imglib($dbo);
                        $response = $imglib->createPhoto($file);
                        unset($imglib);

                        if ($response['error'] === false) {

                            $account->setPhoto($response);
                        }

                        unset($_SESSION['oauth']);
                        unset($_SESSION['oauth_id']);
                        unset($_SESSION['oauth_name']);
                        unset($_SESSION['oauth_email']);
                        unset($_SESSION['oauth_link']);

                    } else {

                        $account->setFacebookId("");
                    }

                    header("Location: /account/wall");
                    exit;
                }

            } else {

                $error = true;
            }
        }
    }

    if (isset($_SESSION['oauth']) && empty($user_username) && empty($user_email)) {

        $user_fullname = $_SESSION['oauth_name'];
        $user_email = $_SESSION['oauth_email'];
    }

    auth::newAuthenticityToken();

    $page_id = "signup";

    $css_files = array("style.css");
    $page_title = $LANG['page-signup']." | ".APP_TITLE;

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
                        <?php echo $LANG['page-signup']; ?>
                    </div>
                </div>

                <?php

                    if (isset($_SESSION['oauth'])) {

                        ?>

                            <div class="connected">

                                <?php

                                $headers = get_headers('https://graph.facebook.com/'.$_SESSION['oauth_id'].'/picture',1);

                                if (isset($headers['Location'])) {

                                    $url = $headers['Location']; // string
                                    echo "<img src=\"$url\">";

                                } else {

                                    echo "<img src=\"profile_default_photo.png\">";
                                }
                                ?>

                                <div class="connected_content">
                                    <?php

                                    switch ($_SESSION['oauth']) {

                                        case "facebook": {

                                            ?>

                                            <a target="_blank" href="https://www.facebook.com/app_scoped_user_id/<?php echo $_SESSION['oauth_id']; ?>"><?php echo $_SESSION['oauth_name']; ?></a>
                                            <div><?php echo $LANG['label-authorization-with-facebook']; ?></div>
                                            <div>
                                                <a href="/facebook"><?php echo $LANG['action-back-to-default-signup']; ?></a>
                                            </div>

                                            <?php

                                            break;
                                        }

                                        default: {

                                        break;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                        <?php

                    } else {

                        ?>

                            <div class="center">
                                <a class="btn icon-btn btn-large btn-facebook" href="/facebook/signup">
                                <span class="icon-container">
                                    <i class="icon icon-facebook"></i>
                                </span>
                                <span>
                                    <?php echo $LANG['action-signup-with']." ".$LANG['label-facebook']; ?>
                                </span>
                                </a>
                            </div>

                        <?php
                    }


                    if ($error) {

                        ?>

                            <div class="error">
                                <?php

                                    foreach ( $error_message as $key => $value) {

                                        echo $value."<br>";
                                    }
                                ?>
                            </div>

                        <?php
                    }
                ?>

                <div class="frm">
                    <form action="/signup" method="post" id="login_form">
                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">
                        <div class="frm_header">
                            <label class="noselect" for="user_username"><?php echo $LANG['label-username']; ?>:</label>
                        </div>
                        <input autocomplete="off" type="text" id="user_username" class="frm_input" maxlength="24" name="username" value="<?php echo $user_username; ?>">
                        <div class="frm_header">
                            <label class="noselect" for="user_fullname"><?php echo $LANG['label-fullname']; ?>:</label>
                        </div>
                        <input autocomplete="off" type="text" id="user_fullname" class="frm_input" maxlength="34" name="fullname" value="<?php echo $user_fullname; ?>">
                        <div class="frm_header">
                            <label class="noselect" for="user_password"><?php echo $LANG['label-password']; ?>:</label>
                        </div>
                        <input autocomplete="off" type="password" id="user_password" class="frm_input" maxlength="20" name="password" value="">
                        <div class="frm_header">
                            <label class="noselect" for="user_email"><?php echo $LANG['label-email']; ?>:</label>
                        </div>
                        <input autocomplete="off" type="text" id="user_email" class="frm_input" maxlength="64" name="email" value="<?php echo $user_email; ?>">
                        <div>
                            <br>
                            <span><?php echo $LANG['label-signup-confirm']; ?></span>
                            <a href="/terms"><?php echo $LANG['page-terms']; ?></a>
                        </div>
                        <div class="">
                            <button type="submit" class="frm_btn primary_btn big_btn"><?php echo $LANG['action-signup']; ?></button>
                        </div>
                    </form>
                </div>

            </div>

            <?php

                include_once("../html/common/footer_new.inc.php");
            ?>

        </div>
    </div>

</body>
</html>