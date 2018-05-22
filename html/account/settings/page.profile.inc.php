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

    $account = new account($dbo, $accountId);

    $error = false;
    $send_status = false;
    $fullname = "";

    if (auth::isSession()) {

        $ticket_email = "";
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $allowComments = isset($_POST['allowComments']) ? $_POST['allowComments'] : '';
        $allowMessages = isset($_POST['allowMessages']) ? $_POST['allowMessages'] : '';

        $gender = isset($_POST['gender']) ? $_POST['gender'] : 0;

        $day = isset($_POST['day']) ? $_POST['day'] : 0;
        $month = isset($_POST['month']) ? $_POST['month'] : 0;
        $year = isset($_POST['year']) ? $_POST['year'] : 0;

        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $location = isset($_POST['location']) ? $_POST['location'] : '';
        $facebook_page = isset($_POST['facebook_page']) ? $_POST['facebook_page'] : '';
        $instagram_page = isset($_POST['instagram_page']) ? $_POST['instagram_page'] : '';

        $allowComments = helper::clearText($allowComments);
        $allowComments = helper::escapeText($allowComments);

        $allowMessages = helper::clearText($allowMessages);
        $allowMessages = helper::escapeText($allowMessages);

        $gender = helper::clearInt($gender);

        $day = helper::clearInt($day);
        $month = helper::clearInt($month);
        $year = helper::clearInt($year);

        $fullname = helper::clearText($fullname);
        $fullname = helper::escapeText($fullname);

        $status = helper::clearText($status);
        $status = helper::escapeText($status);

        $location = helper::clearText($location);
        $location = helper::escapeText($location);

        $facebook_page = helper::clearText($facebook_page);
        $facebook_page = helper::escapeText($facebook_page);

        $instagram_page = helper::clearText($instagram_page);
        $instagram_page = helper::escapeText($instagram_page);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            if ($allowComments === "on") {

                $account->setAllowComments(1);

            } else {

                $account->setAllowComments(0);
            }

            if ($allowMessages === "on") {

                $account->setAllowMessages(1);

            } else {

                $account->setAllowMessages(0);
            }

//            $account->edit($fullname);

            if (helper::isCorrectFullname($fullname)) {

                $account->edit($fullname);
            }

            $account->setSex($gender);
            $account->setBirth($year, $month, $day);
            $account->setStatus($status);
            $account->setLocation($location);

            if (helper::isValidURL($facebook_page)) {

                $account->setFacebookPage($facebook_page);

            } else {

                $account->setFacebookPage("");
            }

            if (helper::isValidURL($instagram_page)) {

                $account->setInstagramPage($instagram_page);

            } else {

                $account->setInstagramPage("");
            }

            header("Location: /account/settings/profile/?error=false");
            exit;
        }

        header("Location: /account/settings/profile/?error=true");
        exit;
    }

    $accountInfo = $account->get();

    auth::newAuthenticityToken();

    $page_id = "settings_profile";

    $css_files = array("style.css");
    $page_title = $LANG['page-profile-settings']." | ".APP_TITLE;

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
                                    <span class="title"><?php echo $LANG['page-profile-settings']; ?></span>
                                </div>

                                <form action="/account/settings/profile" method="post" class="settings_wrap frm">

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

                                                default: {

                                                    ?>

                                                        <div class="msg">
                                                            <b><?php echo $LANG['label-thanks']; ?></b>
                                                            <br>
                                                            <?php echo $LANG['label-settings-saved']; ?>
                                                        </div>

                                                    <?php

                                                    break;
                                                }
                                            }
                                        }
                                    ?>

                                    <div class="error <?php if (!$error) echo "hide"; ?>">
                                        <?php echo $LANG['ticket-send-error']; ?>
                                    </div>

                                    <div class="settings_privacy">
                                        <label class="settings_privacy_title"><?php echo $LANG['label-posts-privacy']; ?></label>

                                        <label class="radio_privacy" style="margin-top: 20px">
                                            <input style="height: 12px;" name="allowComments" <?php if ($accountInfo['allowComments'] == 1) echo "checked=\"checked\"";  ?> type="checkbox">
                                            <?php echo $LANG['label-comments-allow']; ?>
                                        </label>
                                    </div>

                                    <div class="settings_privacy">
                                        <label class="settings_privacy_title"><?php echo $LANG['label-messages-privacy']; ?></label>

                                        <label class="radio_privacy" style="margin-top: 20px">
                                            <input style="height: 12px;" name="allowMessages" <?php if ($accountInfo['allowMessages'] == 1) echo "checked=\"checked\"";  ?> type="checkbox">
                                            <?php echo $LANG['label-messages-allow']; ?>
                                        </label>
                                    </div>

                                    <div class="options_cont">
                                        <label for="fullname" class="noselect"><?php echo $LANG['label-fullname']; ?></label>
                                        <input type="text" id="fullname" placeholder="" name="fullname" maxlength="64" value="<?php echo $accountInfo['fullname']; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="status" class="noselect"><?php echo $LANG['label-status']; ?></label>
                                        <textarea style="width: 352px; padding: 7px;" id="status" placeholder="" name="status" maxlength="400"><?php echo $accountInfo['status']; ?></textarea>
                                    </div>

                                    <div class="options_cont">
                                        <label for="country" class="noselect"><?php echo $LANG['label-location']; ?></label>
                                        <input type="text" id="country" placeholder="" name="location" maxlength="30" value="<?php echo $accountInfo['location']; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="facebook_page" class="noselect"><?php echo $LANG['label-facebook-link']; ?></label>
                                        <input type="text" id="facebook_page" placeholder="" name="facebook_page" maxlength="120" value="<?php echo $accountInfo['fb_page']; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="instagram_page" class="noselect"><?php echo $LANG['label-instagram-link']; ?></label>
                                        <input type="text" id="instagram_page" placeholder="" name="instagram_page" maxlength="120" value="<?php echo $accountInfo['instagram_page']; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="gender" class="noselect"><?php echo $LANG['label-gender']; ?></label>
                                        <select id="gender" name="gender" class="selectBox">
                                            <option value="0" <?php if ($accountInfo['sex'] != SEX_FEMALE && $accountInfo['sex'] != SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-unknown']; ?></option>
                                            <option value="1" <?php if ($accountInfo['sex'] == SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-male']; ?></option>
                                            <option value="2" <?php if ($accountInfo['sex'] == SEX_FEMALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-female']; ?></option>
                                        </select>
                                    </div>

                                    <div class="options_cont" style="width: 368px;">
                                        <label class="noselect"><?php echo $LANG['label-birth-date']; ?></label>

                                        <select id="day" name="day" class="selectBox" style="width: 90px;">

                                            <?php

                                                for ($day = 1; $day <= 31; $day++) {

                                                    if ($day == $accountInfo['day']) {

                                                        echo "<option value=\"$day\" selected=\"selected\">$day</option>";

                                                    } else {

                                                        echo "<option value=\"$day\">$day</option>";
                                                    }
                                                }
                                            ?>

                                        </select>

                                        <select id="month" name="month" class="selectBox" style="width: 181px;">
                                            <option value="0" <?php if ($accountInfo['month'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['month-jan']; ?></option>
                                            <option value="1" <?php if ($accountInfo['month'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['month-feb']; ?></option>
                                            <option value="2" <?php if ($accountInfo['month'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['month-mar']; ?></option>
                                            <option value="3" <?php if ($accountInfo['month'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['month-apr']; ?></option>
                                            <option value="4" <?php if ($accountInfo['month'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['month-may']; ?></option>
                                            <option value="5" <?php if ($accountInfo['month'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['month-june']; ?></option>
                                            <option value="6" <?php if ($accountInfo['month'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['month-july']; ?></option>
                                            <option value="7" <?php if ($accountInfo['month'] == 7) echo "selected=\"selected\""; ?>><?php echo $LANG['month-aug']; ?></option>
                                            <option value="8" <?php if ($accountInfo['month'] == 8) echo "selected=\"selected\""; ?>><?php echo $LANG['month-sept']; ?></option>
                                            <option value="9" <?php if ($accountInfo['month'] == 9) echo "selected=\"selected\""; ?>><?php echo $LANG['month-oct']; ?></option>
                                            <option value="10" <?php if ($accountInfo['month'] == 10) echo "selected=\"selected\""; ?>><?php echo $LANG['month-nov']; ?></option>
                                            <option value="11" <?php if ($accountInfo['month'] == 11) echo "selected=\"selected\""; ?>><?php echo $LANG['month-dec']; ?></option>
                                        </select>

                                        <select id="year" name="year" class="selectBox" style="width: 90px;">

                                            <?php

                                                $current_year = date("Y");

                                                for ($year = 1915; $year <= $current_year; $year++) {

                                                    if ($year == $accountInfo['year']) {

                                                        echo "<option value=\"$year\" selected=\"selected\">$year</option>";

                                                    } else {

                                                        echo "<option value=\"$year\">$year</option>";
                                                    }
                                                }
                                            ?>

                                        </select>
                                    </div>

                                    <div class="controls_cont">
                                        <button class="primary_btn big_btn"><?php echo $LANG['action-save']; ?></button>
                                    </div>

                                    <div class="settings_links">

                                        <h1><?php echo $LANG['label-profile-settings-links']; ?>:</h1>

                                        <a href="/account/balance" class="settings_link">
                                            <div class="link_title"><?php echo $LANG['page-balance']; ?></div>
                                            <div class="link_description"><?php echo $LANG['action-get-credits']; ?></div>
                                        </a>

                                        <a href="/account/settings/blacklist" class="settings_link">
                                            <div class="link_title"><?php echo $LANG['label-blacklist']; ?></div>
                                            <div class="link_description"><?php echo $LANG['label-blacklist-desc']; ?></div>
                                        </a>

                                        <a href="/account/settings/services" class="settings_link">
                                            <div class="link_title"><?php echo $LANG['label-services']; ?></div>
                                            <div class="link_description"><?php echo $LANG['action-connect-profile']; ?></div>
                                        </a>

                                        <a href="/account/settings/profile/password" class="settings_link">
                                            <div class="link_title"><?php echo $LANG['label-password']; ?></div>
                                            <div class="link_description"><?php echo $LANG['action-change-password']; ?></div>
                                        </a>

                                        <a href="/account/settings/profile/deactivation" class="settings_link">
                                            <div class="link_title"><?php echo $LANG['label-profile']; ?></div>
                                            <div class="link_description"><?php echo $LANG['action-deactivation-profile']; ?></div>
                                        </a>

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

                $('textarea[name=status]').autosize();

            </script>

        </div>
    </div>

</body>
</html>