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

    $email = '';

    $error = false;
    $error_message = '';
    $sent = false;

    if ( isset($_GET['sent']) ) {

        $sent = isset($_GET['sent']) ? $_GET['sent'] : 'false';

        if ($sent === 'success') {

            $sent = true;

        } else {

            $sent = false;
        }
    }

    if (!empty($_POST)) {

        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $email = helper::clearText($email);
        $email = helper::escapeText($email);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
            $error_message[] = $LANG['msg-error-unknown'];
        }

        if (!helper::isCorrectEmail($email)) {

            $error = true;
            $error_message[] = $LANG['msg-email-incorrect'];
        }

        if ( !$error && !$helper->isEmailExists($email) ) {

            $error = true;
            $error_message[] = $LANG['msg-email-not-found'];
        }

        if (!$error) {

            $accountId = $helper->getUserIdByEmail($email);

            if ($accountId != 0) {

                $account = new account($dbo, $accountId);

                $accountInfo = $account->get();

                if ($accountInfo['error'] === false && $accountInfo['state'] != ACCOUNT_STATE_BLOCKED) {

                    $clientId = 0; // Desktop version

                    $restorePointInfo = $account->restorePointCreate($email, $clientId);

                    ob_start();

                    ?>

                    <html>
                    <body>
                    This is link <a href="<?php echo APP_URL;  ?>/restore/?hash=<?php echo $restorePointInfo['hash']; ?>"><?php echo APP_URL;  ?>/restore/?hash=<?php echo $restorePointInfo['hash']; ?></a> to reset your password.
                    </body>
                    </html>

                    <?php

                    $from = SMTP_EMAIL;

                    $to = $email;

                    $html_text = ob_get_clean();

                    $subject = APP_TITLE." | Password reset";

                    $mail = new phpmailer();

                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = SMTP_HOST;                               // Specify main and backup SMTP servers
                    $mail->SMTPAuth = SMTP_AUTH;                               // Enable SMTP authentication
                    $mail->Username = SMTP_USERNAME;                      // SMTP username
                    $mail->Password = SMTP_PASSWORD;                      // SMTP password
                    $mail->SMTPSecure = SMTP_SECURE;                            // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = SMTP_PORT;                                    // TCP port to connect to

                    $mail->From = $from;
                    $mail->FromName = APP_TITLE;
                    $mail->addAddress($to);                               // Name is optional

                    $mail->isHTML(true);                                  // Set email format to HTML

                    $mail->Subject = $subject;
                    $mail->Body    = $html_text;

                    $mail->send();
                }
            }

            $sent = true;
            header("Location: /remind/?sent=success");
        }
    }

    auth::newAuthenticityToken();

    $page_id = "restore";

    $css_files = array("style.css");
    $page_title = $LANG['page-restore']." | ".APP_TITLE;

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
                        <?php echo $LANG['page-restore']; ?>
                    </div>
                </div>

                <?php

                    if ( $sent ) {

                        ?>

                            <div class="msg">
                                <?php echo $LANG['msg-reset-password-sent']; ?>
                            </div>

                        <?php

                    } else {

                        if ( $error ) {

                            ?>

                            <div class="error">
                                <?php echo $LANG['msg-email-not-found']; ?>
                            </div>

                            <?php
                        }

                        ?>

                            <div class="frm">
                                <form action="/remind" method="post" id="restore_form">
                                    <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">
                                    <div class="frm_header">
                                        <label class="noselect" for="user_email"><?php echo $LANG['label-email']; ?>:</label>
                                    </div>
                                    <input autocomplete="off" type="text" id="user_email" class="frm_input" maxlength="64" name="email" value="<?php echo $email; ?>">
                                    <div class="">
                                        <button type="submit" class="frm_btn primary_btn big_btn"><?php echo $LANG['action-next']; ?></button>
                                    </div>
                                </form>
                            </div>

                        <?php
                    }
                ?>

            </div>

            <?php

                include_once("../html/common/footer_new.inc.php");
            ?>

        </div>
    </div>

</body>
</html>