<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $accountId = auth::getCurrentUserId();

    $account = new account($dbo, $accountId);

    $groupId = $helper->getUserId($request[0]);
    $groupAccountId = $helper->getUserId($request[0]);

    $group = new group($dbo, $groupId);
    $group->setRequestFrom($accountId);

    $groupInfo = $group->get();

    if ($groupInfo['state'] != ACCOUNT_STATE_ENABLED) {

        header('Location: /');
        exit;
    }

    if ($groupInfo['accountType'] == ACCOUNT_TYPE_USER) {

        header('Location: /');
        exit;
    }

    if ($groupInfo['accountAuthor'] != $accountId) {

        header('Location: /');
        exit;
    }

    $error = false;
    $send_status = false;
    $fullname = "";
    $group_category = 0;

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $allowComments = isset($_POST['allowComments']) ? $_POST['allowComments'] : '';
        $allowPosts = isset($_POST['allowPosts']) ? $_POST['allowPosts'] : '';

        $group_category = isset($_POST['group_category']) ? $_POST['group_category'] : 0;

        $day = isset($_POST['day']) ? $_POST['day'] : 0;
        $month = isset($_POST['month']) ? $_POST['month'] : 0;
        $year = isset($_POST['year']) ? $_POST['year'] : 0;

        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $location = isset($_POST['location']) ? $_POST['location'] : '';
        $my_page = isset($_POST['my_page']) ? $_POST['my_page'] : '';

        $allowComments = helper::clearText($allowComments);
        $allowComments = helper::escapeText($allowComments);

        $allowPosts = helper::clearText($allowPosts);
        $allowPosts = helper::escapeText($allowPosts);

        $group_category = helper::clearInt($group_category);

        $day = helper::clearInt($day);
        $month = helper::clearInt($month);
        $year = helper::clearInt($year);

        $fullname = helper::clearText($fullname);
        $fullname = helper::escapeText($fullname);

        $status = helper::clearText($status);
        $status = helper::escapeText($status);

        $location = helper::clearText($location);
        $location = helper::escapeText($location);

        $my_page = helper::clearText($my_page);
        $my_page = helper::escapeText($my_page);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            if ($allowComments === "on") {

                $group->setAllowComments(1);

            } else {

                $group->setAllowComments(0);
            }

            if ($allowPosts === "on") {

                $group->setAllowPosts(1);

            } else {

                $group->setAllowPosts(0);
            }

//            $account->edit($fullname);

            if (helper::isCorrectFullname($fullname)) {

                $group->edit($fullname);
            }

            $group->setBirth($year, $month, $day);
            $group->setStatus($status);
            $group->setLocation($location);
            $group->setCategory($group_category);

            if (helper::isValidURL($my_page)) {

                $group->setWebPage($my_page);

            } else {

                $group->setWebPage("");
            }

            header("Location: /".$request[0]."/settings/?error=false");
            exit;
        }

        header("Location: /".$request[0]."/settings/?error=true");
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "group_settings";

    $css_files = array("style.css");
    $page_title = $LANG['page-settings']." | ".APP_TITLE;

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
                                    <a href="/<?php echo $groupInfo['username']; ?>" class="title"><?php echo $groupInfo['fullname']; ?></a>
                                    <span class="divider">|</span>
                                    <a href="/<?php echo $groupInfo['username']; ?>/settings" class="title active"><?php echo $LANG['page-settings']; ?></a>
                                </div>

                                <form action="/<?php echo $groupInfo['username']; ?>/settings" method="post" class="settings_wrap frm">

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

                                    <div class="options_cont">
                                        <label for="fullname" class="noselect"><?php echo $LANG['label-group-fullname']; ?>:</label>
                                        <input type="text" id="fullname" placeholder="" name="fullname" maxlength="64" value="<?php echo $groupInfo['fullname']; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="status" class="noselect"><?php echo $LANG['label-group-status']; ?>:</label>
                                        <textarea style="width: 352px; padding: 7px;" id="status" placeholder="" name="status" maxlength="400"><?php echo $groupInfo['status']; ?></textarea>
                                    </div>

                                    <div class="options_cont">
                                        <label for="country" class="noselect"><?php echo $LANG['label-group-location']; ?>:</label>
                                        <input type="text" id="country" placeholder="" name="location" maxlength="30" value="<?php echo $groupInfo['location']; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="my_page" class="noselect"><?php echo $LANG['label-group-web-page']; ?>:</label>
                                        <input type="text" id="my_page" placeholder="" name="my_page" maxlength="120" value="<?php echo $groupInfo['myPage']; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="group_category" class="noselect"><?php echo $LANG['label-group-category']; ?>:</label>
                                        <select id="group_category" name="group_category" class="selectBox">
                                            <?php

                                                for ($i = 0; $i < 42; $i++) {

                                                    $lang_option  = "group-category_".$i;
                                                    ?>
                                                        <option value="<?php echo $i; ?>" <?php if ($groupInfo['accountCategory'] == $i) echo "selected=\"selected\""; ?>><?php echo $LANG[$lang_option]; ?></option>
                                                    <?php
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="options_cont" style="width: 368px;">
                                        <label class="noselect"><?php echo $LANG['label-group-date']; ?>:</label>

                                        <select id="day" name="day" class="selectBox" style="width: 90px;">

                                            <?php

                                                for ($day = 1; $day <= 31; $day++) {

                                                    if ($day == $groupInfo['day']) {

                                                        echo "<option value=\"$day\" selected=\"selected\">$day</option>";

                                                    } else {

                                                        echo "<option value=\"$day\">$day</option>";
                                                    }
                                                }
                                            ?>

                                        </select>

                                        <select id="month" name="month" class="selectBox" style="width: 181px;">
                                            <option value="0" <?php if ($groupInfo['month'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['month-jan']; ?></option>
                                            <option value="1" <?php if ($groupInfo['month'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['month-feb']; ?></option>
                                            <option value="2" <?php if ($groupInfo['month'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['month-mar']; ?></option>
                                            <option value="3" <?php if ($groupInfo['month'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['month-apr']; ?></option>
                                            <option value="4" <?php if ($groupInfo['month'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['month-may']; ?></option>
                                            <option value="5" <?php if ($groupInfo['month'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['month-june']; ?></option>
                                            <option value="6" <?php if ($groupInfo['month'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['month-july']; ?></option>
                                            <option value="7" <?php if ($groupInfo['month'] == 7) echo "selected=\"selected\""; ?>><?php echo $LANG['month-aug']; ?></option>
                                            <option value="8" <?php if ($groupInfo['month'] == 8) echo "selected=\"selected\""; ?>><?php echo $LANG['month-sept']; ?></option>
                                            <option value="9" <?php if ($groupInfo['month'] == 9) echo "selected=\"selected\""; ?>><?php echo $LANG['month-oct']; ?></option>
                                            <option value="10" <?php if ($groupInfo['month'] == 10) echo "selected=\"selected\""; ?>><?php echo $LANG['month-nov']; ?></option>
                                            <option value="11" <?php if ($groupInfo['month'] == 11) echo "selected=\"selected\""; ?>><?php echo $LANG['month-dec']; ?></option>
                                        </select>

                                        <select id="year" name="year" class="selectBox" style="width: 90px;">

                                            <?php

                                                $current_year = date("Y");

                                                for ($year = 1915; $year <= $current_year; $year++) {

                                                    if ($year == $groupInfo['year']) {

                                                        echo "<option value=\"$year\" selected=\"selected\">$year</option>";

                                                    } else {

                                                        echo "<option value=\"$year\">$year</option>";
                                                    }
                                                }
                                            ?>

                                        </select>
                                    </div>

                                    <div class="settings_privacy">
                                        <label class="settings_privacy_title"><?php echo $LANG['label-group-privacy']; ?>:</label>

                                        <label class="radio_privacy" style="margin-top: 20px">
                                            <input style="height: 12px;" name="allowComments" <?php if ($groupInfo['allowComments'] == 1) echo "checked=\"checked\"";  ?> type="checkbox">
                                            <?php echo $LANG['label-group-allow-comments']; ?>
                                        </label>

                                        <label class="radio_privacy" style="margin-top: 20px">
                                            <input style="height: 12px;" name="allowPosts" <?php if ($groupInfo['allowPosts'] == 1) echo "checked=\"checked\"";  ?> type="checkbox">
                                            <?php echo $LANG['label-group-allow-posts']; ?>
                                        </label>
                                    </div>

                                    <div class="controls_cont">
                                        <button class="primary_btn big_btn"><?php echo $LANG['action-save']; ?></button>
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