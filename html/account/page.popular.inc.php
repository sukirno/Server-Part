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

    $popular = new popular($dbo);
    $popular->setRequestFrom(auth::getCurrentUserId());

    $page_id = "popular";

    $css_files = array("style.css");
    $page_title = $LANG['page-popular']." | ".APP_TITLE;

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
                        <div id="content">

                            <div class="header">

                                <div class="title">
                                    <span><?php echo $LANG['page-popular']; ?></span>
                                </div>

                            </div>

                            <div id="stream_cont" class="stream_cont">

                                <?php

                                $result = $popular->get(0, 0);

                                $inbox_loaded = count($result['items']);

                                if ($inbox_loaded != 0) {

                                    foreach ($result['items'] as $key => $value) {

                                        draw::post($value, $LANG, $helper);
                                    }

                                } else {

                                    ?>

                                        <div class="info">
                                            <?php echo $LANG['label-empty-list']; ?>
                                        </div>

                                    <?php
                                }
                                ?>

                            </div>

                        </div>
                    </div>
                </div>

                <?php

                    include_once("../html/common/footer_new.inc.php");
                ?>

            </div>
        </div>

        <script type="text/javascript">

            var inbox_all = <?php echo $inbox_all; ?>;
            var inbox_loaded = <?php echo $inbox_loaded; ?>;

        </script>

    </div>
</div>

</body>
</html>
