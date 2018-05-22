<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk, qascript@mail.ru
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $page_id = "update";

    include_once("../sys/core/initialize.inc.php");

    $update = new update($dbo);
    $update->setChatEmojiSupport();
    $update->setCommentsEmojiSupport();
    $update->setPostsEmojiSupport();

    $update->setPhotosEmojiSupport();
    $update->setGiftsEmojiSupport();

    $update->setDialogsEmojiSupport();

    $css_files = array("my.css");
    $page_title = APP_TITLE;

    include_once("../html/common/admin_panel_header.inc.php");
?>

<body>

<?php

    include_once("../html/common/admin_panel_topbar.inc.php");
?>
<div class="section no-pad-bot" id="index-banner">
    <div class="container">

        <div class="row">
            <div class="col s12 m6" style="margin: 0 auto; float: none; margin-top: 100px;">
                <div class="card teal lighten-2">
                    <div class="card-content white-text">
                            <span class="card-title">
                                <strong>Success!</strong>
                                <br>
                                Your MySQL version: <?php print mysql_get_client_info(); ?>
                                <br>
                                Database refactoring success!
                            </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/init.js"></script>

</body>
</html>