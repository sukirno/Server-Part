<?php

	/*!
	 * ifsoft.co.uk v1.0
	 *
	 * http://ifsoft.com.ua, http://ifsoft.co.uk
	 * qascript@ifsoft.co.uk
	 *
	 * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
	 */

	$profileId = $helper->getUserId($request[0]);

	$postExists = true;

	$profile = new profile($dbo, $profileId);

	$profile->setRequestFrom(auth::getCurrentUserId());
	$profileInfo = $profile->get();

	if ($profileInfo['error'] === true) {

		include_once("../html/error.inc.php");
		exit;
	}

	if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

		include_once("../html/stubs/profile.inc.php");
		exit;
	}

	$post = new post($dbo);
	$post->setRequestFrom(auth::getCurrentUserId());

	$postId = helper::clearInt($request[2]);

	$postInfo = $post->info($postId);

	if ($postInfo['error'] === true) {

        // Missing
        $postExists = false;
	}

	if ($postExists && $postInfo['removeAt'] != 0) {

		// Missing
		$postExists = false;
	}

	if ($postExists && $profileInfo['id'] != $postInfo['fromUserId']) {

        // Missing
        $postExists = false;
    }

	$page_id = "post";

	$css_files = array("style.css", "tipsy.css");

	if ($postExists) {

		$page_title = helper::clearText($postInfo['post']);
        $page_title = helper::escapeText($page_title);

	} else {

		$page_title = $profileInfo['fullname']." | ".APP_HOST."/".$profileInfo['username'];
	}

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

						<div id="content" class="posts_cont">

							<?php

								if ($postExists) {

									if ($postInfo['groupId'] != 0) {

                                        community::post($postInfo, $LANG, $helper, true);

                                    } else {

                                        draw::post($postInfo, $LANG, $helper, true);
                                    }

								} else {

									?>

										<div class="info">
											<?php echo $LANG['label-post-missing']; ?>
										</div>

									<?php
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

        <script type="text/javascript" src="/js/jquery.tipsy.js"></script>

        <script type="text/javascript">

			var replyToUserId = 0;

            <?php

                if (auth::getCurrentUserId() == $profileInfo['id']) {

                    ?>
                        var myPage = true;
                    <?php
                }
            ?>

            $(document).ready(function() {

                $(".page_verified").tipsy({gravity: 'w'});
                $(".verified").tipsy({gravity: 'w'});
            });

        </script>

	</div>

</body>
</html>