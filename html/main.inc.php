<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if ($auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header("Location: /account/wall");
    }

    $page_id = "main";

    $error = false;
    $error_message = '';

    if (!empty($_POST)) {

        $error = true;

        $user_login = isset($_POST['user_page']) ? $_POST['user_page'] : '';
        $user_fullname = isset($_POST['user_fullname']) ? $_POST['user_fullname'] : '';
        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $user_password_confirm = isset($_POST['user_confirm']) ? $_POST['user_confirm'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : 0;

        $user_login = helper::clearText($user_login);
        $user_fullname = helper::clearText($user_fullname);
        $user_password = helper::clearText($user_password);
        $user_password_confirm = helper::clearText($user_password_confirm);
        $user_email = helper::clearText($user_email);

        if (auth::getAuthenticityToken() === $token) {


        }
    }

    auth::newAuthenticityToken();

    $css_files = array("style.css");
    $page_title = APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="main_page">

    <div id="page_wrap">

        <?php

            include_once("../html/common/topbar_new.inc.php");
        ?>

        <div id="page_layout" class="no_footer_border">

            <?php

                include_once("../html/common/main_banner.inc.php");
            ?>

            <?php

                include_once("../html/common/footer_new.inc.php");
            ?>

        </div>
    </div>

</body>
</html>