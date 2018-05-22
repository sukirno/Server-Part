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

    $error = false;
    $error_type = 0;
    $send_status = false;
    $fullname = "";
    $username = "";
    $status = "";
    $location = "";
    $my_page = "";
    $group_category = 0;
    $year = 1916;
    $month = 1;
    $day = 1;
    $allowPosts = 1;
    $allowComments = 1;

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $allowComments = isset($_POST['allowComments']) ? $_POST['allowComments'] : '';
        $allowPosts = isset($_POST['allowPosts']) ? $_POST['allowPosts'] : '';

        $group_category = isset($_POST['group_category']) ? $_POST['group_category'] : 0;

        $day = isset($_POST['day']) ? $_POST['day'] : 0;
        $month = isset($_POST['month']) ? $_POST['month'] : 0;
        $year = isset($_POST['year']) ? $_POST['year'] : 0;

        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
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

        $username = helper::clearText($username);
        $username = helper::escapeText($username);

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

                $allowComments = 1;

            } else {

                $allowComments = 0;
            }

            if ($allowPosts === "on") {

                $allowPosts = 1;

            } else {

                $allowPosts = 0;
            }

            $group = new group($dbo);
            $group->setRequestFrom($accountId);

            $result = $group->create($username, $fullname, $group_category, $status, $my_page, $location, $year, $month, $day, $allowPosts, $allowComments);

            if ($result['error'] === false) {

                header("Location: /".$username);
                exit;

            } else {

                $error = true;

                if ($result['error_type'] == 0 || $result['error_type'] == 1) {

                    $error_type = 1;
                }

                if ($result['error_type'] == 3) {

                    $error_type = 3;
                }
            }
        }

        $error = true;
    }

    $accountInfo = $account->get();

    auth::newAuthenticityToken();

    $page_id = "create_group";

    $css_files = array("style.css");
    $page_title = $LANG['action-create-group']." | ".APP_TITLE;

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
                                    <a href="/account/groups" class="title"><?php echo $LANG['page-groups']; ?></a>
                                    <span class="divider">|</span>
                                    <a href="/account/managed_groups" class="title"><?php echo $LANG['label-managed-groups']; ?></a>
                                    <span class="divider">|</span>
                                    <a href="/account/create_group" class="title active"><?php echo $LANG['action-create-group']; ?></a>
                                </div>

                                <form action="/account/create_group" method="post" class="settings_wrap frm">

                                    <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                                    <div class="error <?php if (!$error) echo "hide"; ?>">
                                        <?php

                                            switch ($error_type) {

                                                case 1: {

                                                    echo $LANG['label-group-name-error'];

                                                    break;
                                                }

                                                default: {

                                                    echo $LANG['label-group-fullname-error'];

                                                    break;
                                                }
                                            }
                                        ?>
                                    </div>

                                    <div class="options_cont">
                                        <label for="fullname" class="noselect"><?php echo $LANG['label-group-fullname']; ?>:</label>
                                        <input type="text" id="fullname" placeholder="" name="fullname" maxlength="64" value="<?php echo $fullname; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="username" class="noselect"><?php echo $LANG['label-group-username']." - ".APP_URL."/"; ?>:</label>
                                        <input type="text" id="username" placeholder="" name="username" maxlength="64" value="<?php echo $username; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="status" class="noselect"><?php echo $LANG['label-group-status']; ?>:</label>
                                        <textarea style="width: 352px; padding: 7px;" id="status" placeholder="" name="status" maxlength="400"><?php echo $status; ?></textarea>
                                    </div>

                                    <div class="options_cont">
                                        <label for="country" class="noselect"><?php echo $LANG['label-group-location']; ?>:</label>
                                        <input type="text" id="country" placeholder="" name="location" maxlength="30" value="<?php echo $location; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="my_page" class="noselect"><?php echo $LANG['label-group-web-page']; ?>:</label>
                                        <input type="text" id="my_page" placeholder="" name="my_page" maxlength="120" value="<?php echo $my_page; ?>">
                                    </div>

                                    <div class="options_cont">
                                        <label for="group_category" class="noselect"><?php echo $LANG['label-group-category']; ?>:</label>
                                        <select id="group_category" name="group_category" class="selectBox">
                                            <?php

                                                for ($i = 0; $i < 42; $i++) {

                                                    $lang_option  = "group-category_".$i;
                                                    ?>
                                                        <option value="<?php echo $i; ?>" <?php if ($group_category == $i) echo "selected=\"selected\""; ?>><?php echo $LANG[$lang_option]; ?></option>
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

                                                for ($year = 1908; $year <= $current_year; $year++) {

                                                    if ($year == $accountInfo['year']) {

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
                                            <input style="height: 12px;" name="allowComments" <?php if ($allowComments == 1) echo "checked=\"checked\"";  ?> type="checkbox">
                                            <?php echo $LANG['label-group-allow-comments']; ?>
                                        </label>

                                        <label class="radio_privacy" style="margin-top: 20px">
                                            <input style="height: 12px;" name="allowPosts" <?php if ($allowPosts == 1) echo "checked=\"checked\"";  ?> type="checkbox">
                                            <?php echo $LANG['label-group-allow-posts']; ?>
                                        </label>
                                    </div>

                                    <div class="controls_cont">
                                        <button class="primary_btn big_btn"><?php echo $LANG['action-create-group']; ?></button>
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