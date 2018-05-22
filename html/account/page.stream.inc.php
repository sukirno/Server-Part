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

    $stream = new stream($dbo);
    $stream->setRequestFrom(auth::getCurrentUserId());

    $inbox_all = $stream->count();
    $inbox_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $stream->get($itemId);

        $inbox_loaded = count($result['items']);

        $result['inbox_loaded'] = $inbox_loaded + $loaded;
        $result['inbox_all'] = $inbox_all;

        if ($inbox_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::post($value, $LANG, $helper);
            }

            if ($result['inbox_loaded'] < $inbox_all) {

                ?>

                    <div class="more_cont">
                        <a class="more_link" href="javascript:void(0)" onclick="Stream.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                        <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
                    </div>

                <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "stream";

    $css_files = array("style.css");
    $page_title = $LANG['page-stream']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-stream']; ?></span>
                                </div>

                            </div>

                            <div id="stream_cont" class="stream_cont">

                                <?php

                                $result = $stream->get(0);

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

                                <?php

                                if ($inbox_all > 20) {

                                    ?>

                                    <div class="more_cont">
                                        <a class="more_link" href="javascript:void(0)" onclick="Stream.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                        <a class="loading_link" href="javascript:void(0)" style="display: none">&nbsp;</a>
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
