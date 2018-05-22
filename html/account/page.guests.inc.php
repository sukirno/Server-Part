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

    $account = new account($dbo, auth::getCurrentUserId());
    $account->setLastGuestsView();
    unset($account);

    $guests = new guests($dbo, auth::getCurrentUserId());
    $guests->setRequestFrom(auth::getCurrentUserId());

    $items_all = $guests->count();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $guests->get($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                profileItem($value, $LANG, $helper);
            }

            if ($result['items'] < $items_all) {

                ?>

                <div class="more_cont">
                    <a class="more_link" href="#" onclick="Guests.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
                    <a class="loading_link" href="#" style="display: none">&nbsp;</a>
                </div>

            <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "guests";

    $css_files = array("style.css");
    $page_title = $LANG['page-guests']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-guests']; ?></span>
                                </div>
                            </div>

                            <div id="guests_cont" class="guests_cont">

                                <?php

                                    $result = $guests->get(0);

                                    $items_loaded = count($result['items']);

                                    if ($items_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            profileItem($value, $LANG, $helper);
                                        }

                                        if ($items_all > 20) {

                                            ?>

                                                <div class="more_cont">
                                                    <a class="more_link" href="#" onclick="Guests.more('<?php echo $result['itemId']; ?>'); return false;"><?php echo $LANG['action-more']; ?></a>
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

            window.Guests || ( window.Guests = {} );

            Guests.more = function (offset) {

                $('a.more_link').hide();
                $('a.loading_link').show();

                $.ajax({
                    type: 'POST',
                    url: '/account/guests',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.more_cont').remove();

                        if (response.hasOwnProperty('html')){

                            $("div.guests_cont").append(response.html);
                        }

                        items_loaded = response.items_loaded;
                        items_all = response.items_all;
                    },
                    error: function(xhr, type){

                        $('a.more_link').show();
                        $('a.loading_link').hide();
                    }
                });
            }

        </script>



    </div>
</div>

</body>
</html>

<?php

    function profileItem($profile, $LANG, $helper = null)
    {
        ?>

        <div class="post profile_item">
            <a class="profile_cont" href="/<?php echo $profile['guestUserUsername']; ?>">
                <?php

                $profilePhotoUrl = "/img/profile_default_photo.png";

                if (strlen($profile['guestUserPhoto']) != 0) {

                    $profilePhotoUrl = $profile['guestUserPhoto'];
                }
                ?>

                <img src="<?php echo $profilePhotoUrl; ?>"/>
            </a>
            <div class="post_cont">
                <a class="fullname" href="/<?php echo $profile['guestUserUsername']; ?>"><?php echo $profile['guestUserFullname']; ?></a>
                <?php if ($profile['guestUserVerify'] == 1) echo "<b original-title=\"{$LANG['label-account-verified']}\" class=\"verified\"></b>"; ?>
                <div class="addon_info">
                    @<span class="username"><?php echo $profile['guestUserUsername']; ?></span>
                </div>
                <div class="addon_info">
                    <span class="username"><?php echo $profile['timeAgo']; ?></span>
                </div>
            </div>
        </div>

        <?php
    }