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

	$error = false;
    $error_message = '';

    $account = new account($dbo, auth::getCurrentUserId());
    $fb_id = $account->getFacebookId();

    if (!empty($_POST)) {

    }

	$page_id = "services";

	$css_files = array("style.css");


    $page_title = $LANG['page-services']." | ".APP_TITLE;

	include_once("../html/common/header.inc.php");
?>

<body class="bg_gray">

	<div id="page_wrap">

	<?php

		include_once("../html/common/topbar_new.inc.php");
	?>

	<div id="page_layout" class="">

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
                                    <span><?php echo $LANG['page-services']; ?></span>
                                </div>
                            </div>

                            <div class="services_cont">

                                <?php

                                    $msg = $LANG['page-services-sub-title'];

                                    if (isset($_GET['status'])) {

                                        switch($_GET['status']) {

                                            case "connected": {

                                                $msg = $LANG['label-services-facebook-connected'];
                                                break;
                                            }

                                            case "error": {

                                                $msg = $LANG['label-services-facebook-error'];
                                                break;
                                            }

                                            case "disconnected": {

                                                $msg = $LANG['label-services-facebook-disconnected'];
                                                break;
                                            }

                                            default: {

                                                $msg = $LANG['page-services-sub-title'];
                                                break;
                                            }
                                        }
                                    }
                                ?>

                                <div class="msg">
                                    <?php echo $msg; ?>
                                </div>

                                <div class="services_block">
                                    <img src="/img/i_facebook.png">
                                    <div class="services_data">
                                    <?php

                                        if ($fb_id == 0) {

                                            ?>
                                                <a href="/facebook/connect/?access_token=<?php echo auth::getAccessToken(); ?>"><?php echo $LANG['action-connect-facebook']; ?></a>
                                            <?php

                                        } else {

                                            if (auth::getCurrentUserLogin() === 'qascript' && APP_DEMO === true) {

                                                ?>
                                                    <div><?php echo $LANG['label-connected-with-facebook']; ?></div>
                                                    <a href="#"><?php echo $LANG['action-disconnect']; ?> (Not available! This demo account!)</a>
                                                <?php

                                            } else {

                                                ?>
                                                    <div><?php echo $LANG['label-connected-with-facebook']; ?></div>
                                                    <a href="/facebook/disconnect/?access_token=<?php echo auth::getAccessToken(); ?>"><?php echo $LANG['action-disconnect']; ?></a>
                                                <?php
                                            }
                                        }
                                    ?>
                                    </div>
                                </div>

                            </div>

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