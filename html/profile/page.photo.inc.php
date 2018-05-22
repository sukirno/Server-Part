<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $profileId = $helper->getUserId($request[0]);

    $answerExists = true;

    $profile = new profile($dbo, $profileId);

    $profile->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $profile->get();

    if ($profileInfo['error'] === true) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ( $profileInfo['state'] != ACCOUNT_STATE_ENABLED ) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    $page_id = "photo";

    $css_files = array("style.css");
    $page_title = $profileInfo['fullname']." | ".APP_HOST."/".$profileInfo['username'];

    include_once("../html/common/header.inc.php");

?>

<body class="bg_gray">

<div id="page_wrap">

    <?php

    include_once("../html/common/topbar_new.inc.php");
    ?>

    <div id="page_layout" class="profile_page">

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

                            <?php

                                $imgUrl = "/img/profile_default_photo.png";

                                if ( strlen($profileInfo['normalPhotoUrl']) != 0 ) {

                                    $imgUrl = $profileInfo['normalPhotoUrl'];
                                }
                            ?>

                            <img class="profile-full-photo" src="<?php echo $imgUrl ?>"/>

                            <p>
                                <a href="/<?php echo $profileInfo['username']; ?>" class="flat_btn"><?php echo $LANG['action-full-profile']; ?></a>
                            </p>

                        </div>
                    </div>
                </div>

                <?php

                    include_once("../html/common/footer_new.inc.php");
                ?>

            </div>
        </div>

    </div>

</body>
</html>