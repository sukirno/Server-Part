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

    $gift_id = 0;
    $gift_info = array();

    $gifts = new gift($dbo);
    $gifts->setRequestFrom(auth::getCurrentUserId());

    if (isset($_GET['gift_id'])) {

        $gift_id = isset($_GET['gift_id']) ? $_GET['gift_id'] : 0;

        $gift_id = helper::clearInt($gift_id);

        $gift_info = $gifts->db_info($gift_id);

        if ($gift_info['error'] === true) {

            header("Location: /");
            exit;
        }
    }

    $profileId = $helper->getUserId($request[0]);

    $user = new profile($dbo, $profileId);

    $user->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $user->get();

    if ($profileInfo['error'] === true) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    if ($profileInfo['accountType'] != ACCOUNT_TYPE_USER) {

        header("Location: /");
        exit;
    }

    $account = new account($dbo, auth::getCurrentUserId());

    $balance = $account->getBalance();

    $items_all = $gifts->count();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $g_id = isset($_POST['gift_id']) ? $_POST['gift_id'] : 0;
        $g_message = isset($_POST['message']) ? $_POST['message'] : '';
        $auth_token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $g_id = helper::clearInt($g_id);

        $g_message = helper::clearText($g_message);
        $g_message = helper::escapeText($g_message);

        if ($auth_token === auth::getAuthenticityToken()) {

            if ($balance == $gift_info['cost'] || $balance > $gift_info['cost']) {

                $result = $gifts->send($gift_id, $profileId, $g_message);

                if ($result['error'] === false) {

                    $account->setBalance($balance - $gift_info['cost']);

                    header("Location: /".$profileInfo['username']."/gifts");
                    exit;
                }

            } else {

                header("Location: /account/balance");
                exit;
            }
        }
    }

    $page_id = "send_gifts";

    $css_files = array("style.css", "tipsy.css", "gifts.css");
    $page_title = $LANG['page-send-gift']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

    auth::newAuthenticityToken();

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
                        <div id="content">

                            <div class="header">
                                <div class="title">
                                    <span><?php echo $LANG['page-send-gift']; ?></span>
                                    <span class="right">
                                        <a href="/account/balance"><?php echo $LANG['label-balance']; ?> <?php echo $balance; ?> <?php echo $LANG['label-credits']; ?></a>
                                    </span>
                                </div>

                            </div>

                            <div id="gifts_cont" class="gifts_cont">

                                <div style="text-align: center">
                                    <img style="width: 256px; height: 256px;" src="<?php echo $gift_info['imgUrl']; ?>">

                                    <div style="padding: 25px;">
                                        <span style="font-weight: bold"><?php echo $gift_info['cost']; ?> <?php echo $LANG['label-credits']; ?></span>
                                    </div>
                                </div>

                                <form class="profile_question_form" action="/<?php echo $request[0]; ?>/send_gift/?gift_id=<?php echo $gift_id; ?>" method="post">
                                    <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">
                                    <input autocomplete="off" type="hidden" name="gift_id" value="<?php echo $gift_id; ?>">
                                    <textarea name="message" maxlength="250" placeholder="<?php echo $LANG['label-placeholder-gift']; ?>"></textarea>
                                    <div class="form_actions">
                                        <span id="word_counter">250</span>
                                        <button class="primary_btn" value="send"><?php echo $LANG['action-send']; ?></button>
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

                <?php

                    include_once("../html/common/footer_new.inc.php");
                ?>

            </div>
        </div>

        <script type="text/javascript" src="/js/jquery.tipsy.js"></script>

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

            $(document).ready(function() {

                $(".page_verified").tipsy({gravity: 'w'});
                $(".verified").tipsy({gravity: 'w'});
            });

            $("textarea[name=message]").autosize();

            $("textarea[name=message]").bind('keyup mouseout', function() {

                var max_char = 250;

                var count = $("textarea[name=message]").val().length;

                $("span#word_counter").empty();
                $("span#word_counter").html(max_char - count);

                event.preventDefault();
            });

        </script>

    </div>
</div>

</body>
</html>