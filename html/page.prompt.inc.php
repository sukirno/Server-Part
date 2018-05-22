<?php

/*!
 * ifsoft.co.uk v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

$act = '';
$msg = '';

if ( isset($_GET['action']) ) {

    $act = isset($_GET['action']) ? $_GET['action'] : '';

    $act = helper::clearText($act);

    switch ($act) {

        case 'follow': {

            $msg = $LANG['label-prompt-follow'];
            break;
        }

        case 'like': {

            $msg = $LANG['label-prompt-like'];
            break;
        }

        case 'repost': {

            $msg = $LANG['label-prompt-repost'];
            break;
        }

        default: {

            $msg = "";
        }
    }

    ?>

        <div class="box-body">
            <div class="prompt_header"><?php echo $msg; ?></div>
            <div class="choice">
                <a class="primary_btn button_register" href="/signup"><?php echo $LANG['action-signup']; ?></a>
                <a class="white_btn button_login" href="/login"><?php echo $LANG['action-login']; ?></a>
            </div>
        </div>

    <?php
}
