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

    $profile = new profile($dbo, auth::getCurrentUserId());
    $profile->setRequestFrom(auth::getCurrentUserId());

    $items_all = $profile->getMyGroupsCount();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $profile->getMyGroups($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                community::communityItem($value, $LANG, $helper);
            }

            if ($result['items'] < $items_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="#" onclick="Groups.myGroupsMore('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "my_groups";

    $css_files = array("style.css");
    $page_title = $LANG['page-groups']." | ".APP_TITLE;

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
                                <a href="/account/groups" class="title active"><?php echo $LANG['page-groups']; ?></a>
                                <span class="divider">|</span>
                                <a href="/account/managed_groups" class="title"><?php echo $LANG['label-managed-groups']; ?></a>
                                <span class="divider">|</span>
                                <a href="/account/create_group" class="title"><?php echo $LANG['action-create-group']; ?></a>
                            </div>

                            <div id="my_groups_cont" class="my_groups_cont">

                                <?php

                                    $result = $profile->getMyGroups(0);

                                    $items_loaded = count($result['items']);

                                    if ($items_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            community::communityItem($value, $LANG, $helper);
                                        }

                                        if ($items_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="#" onclick="Groups.myGroupsMore('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                                                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                                                </div>

                                            <?php
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

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

        </script>



    </div>
</div>

</body>
</html>
