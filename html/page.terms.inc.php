<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $page_id = "terms";

    $css_files = array("style.css");
    $page_title = $LANG['page-terms']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

    ?>

<body class="bg_gray">

    <div id="page_wrap">

        <?php

            include_once("../html/common/topbar_new.inc.php");
        ?>

        <div id="page_layout">

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
                                        <span><?php echo $LANG['page-terms']; ?></span>
                                    </div>
                                </div>

                                <?php

                                    if (file_exists("../html/terms/".$LANG['lang-code'].".inc.php")) {

                                        include_once("../html/terms/".$LANG['lang-code'].".inc.php");

                                    } else {

                                        include_once("../html/terms/en.inc.php");
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

        </div>
    </div>

</body>
</html>