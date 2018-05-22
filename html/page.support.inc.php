<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $page_id = "support";

    $error = false;
    $send_status = false;
    $email = "";
    $subject = "";
    $about = "";

    if (auth::isSession()) {

        $ticket_email = "";
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
        $about = isset($_POST['about']) ? $_POST['about'] : '';

        $subject = helper::clearText($subject);
        $about = helper::clearText($about);
        $email = helper::clearText($email);

        $subject = helper::escapeText($subject);
        $about = helper::escapeText($about);
        $email = helper::escapeText($email);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!helper::isCorrectEmail($email)) {

            $error = true;
        }

        if (empty($about)) {

            $error = true;
        }

        if (empty($subject)) {

            $error = true;
        }

        if (!$error) {

            $accountId = auth::getCurrentUserId();
            $clientId = 0; //Desktop version;

            $support = new support($dbo);
            $support->createTicket($accountId, $email, $subject, $about, $clientId);

            $send_status = true;
        }
    }

    auth::newAuthenticityToken();

    $css_files = array("style.css");
    $page_title = $LANG['page-support']." | ".APP_TITLE;

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
                            <div id="support">

                                <div class="header">
                                    <div class="title">
                                        <span><?php echo $LANG['page-support']; ?></span>
                                    </div>
                                </div>

                                <?php

                                    if ($send_status) {

                                        ?>

                                            <div class="msg">
                                                <?php echo $LANG['ticket-send-success']; ?>
                                            </div>

                                        <?php

                                    } else {

                                        ?>

                                            <form action="/support" method="post" class="support_wrap frm">

                                                <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                                                <div class="ticket_title_label">
                                                    <b><?php echo $LANG['label-support-sub-title']; ?></b>
                                                </div>

                                                <div class="error <?php if (!$error) echo "hide"; ?>">
                                                    <?php echo $LANG['ticket-send-error']; ?>
                                                </div>

                                                <div class="ticket_email">
                                                    <label for="email" class="noselect"><?php echo $LANG['label-email']; ?></label>
                                                    <input type="email" id="email" placeholder="" name="email" maxlength="64" value="<?php echo $email; ?>">
                                                </div>

                                                <div class="ticket_title">
                                                    <label for="subject" class="noselect"><?php echo $LANG['label-subject']; ?></label>
                                                    <input type="text" id="subject" placeholder="" name="subject" maxlength="164" value="<?php echo $subject; ?>">
                                                </div>

                                                <div class="ticket_detailed">
                                                    <label for="about" class="noselect"><?php echo $LANG['label-support-message']; ?></label>
                                                    <textarea id="about" name="about" maxlength="800"><?php echo $about; ?></textarea>

                                                    <div class="ticket_controls">
                                                        <button class="primary_btn big_btn"><?php echo $LANG['action-send']; ?></button>
                                                    </div>
                                                </div>

                                            </form>
                                        <?php
                                    }
                                ?>

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