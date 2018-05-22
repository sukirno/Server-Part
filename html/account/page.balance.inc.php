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

    $page_id = "balance";

    $css_files = array("style.css");
    $page_title = $LANG['page-balance']." | ".APP_TITLE;

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
                                    <span><?php echo $LANG['page-balance']; ?></span>
                                </div>
                            </div>

                            <div id="balance_cont" class="balance_cont">

                            <p style="text-align: center; padding-top: 10px; font-size: 12px; color: #000; font-weight: bold" class="description">
                                <?php echo $LANG['page-balance-desc'] ?></span>
                            </p>

                            <p style="text-align: center; padding-top: 10px; font-size: 12px; color: #777" class="description">
                                <?php echo $LANG['label-balance'] ?> <span style="color: #333; font-size: 13px; font-weight: bold"><?php echo $account->getBalance(); ?> <?php echo $LANG['label-credits']; ?></span>
                            </p>

                                <p style="text-align: center;padding-top: 30px;">
                                    <a id="fmp-button" href="#" rel="<?php echo FORTUMO_SERVICE_ID; ?>/<?php echo auth::getCurrentUserId(); ?>">
                                        <img src="/img/pay_button.png" width="150" height="50" alt="Mobile Payments by Fortumo" border="0" />
                                    </a>
                                </p>

                            </div>

                        </div>
                    </div>
                </div>

                <?php

                    include_once("../html/common/footer_new.inc.php");
                ?>

            </div>
        </div>

        <script src="//fortumo.com/javascripts/fortumopay.js" type="text/javascript"></script>

        <script type="text/javascript">



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