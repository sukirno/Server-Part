<?php

    /*!
     * ifsoft.co.uk engine v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (!admin::isSession()) {

        header("Location: /admin/login");
    }

    $accountInfo = array();

    if (isset($_GET['itemId'])) {

        $itemId = isset($_GET['itemId']) ? $_GET['itemId'] : 0;
        $fromUserId = isset($_GET['fromUserId']) ? $_GET['fromUserId'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;

        $itemId = helper::clearInt($itemId);
        $fromUserId = helper::clearInt($fromUserId);

        if ($accessToken === admin::getAccessToken() && !APP_DEMO) {

            $post = new post($dbo);
            $post->setRequestFrom($fromUserId);
            $post->remove($itemId);
        }
    }

    $stats = new stats($dbo);

    $page_id = "stream";

    $stream = new stream($dbo);
//    $stream->setRequestFrom($accountInfo['id']);

    $inbox_all = $stream->getAllCount();
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

                draw($value, $helper);
            }

            if ($result['inbox_loaded'] < $inbox_all) {

                ?>

                <div class="row more_cont">
                    <div class="col s12">
                        <a href="javascript:void(0)" onclick="Profile.moreItems('<?php echo $result['itemId']; ?>'); return false;">
                            <button class="btn waves-effect waves-light teal more_link">View more</button>
                        </a>
                    </div>
                </div>

                <?php
            }

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }

    $css_files = array("my.css");
    $page_title = "Stream";

    include_once("../html/common/admin_panel_header.inc.php");

?>

<body>

    <?php

        include_once("../html/common/admin_panel_topbar.inc.php");
    ?>

<main class="content">
    <div class="row">
        <div class="col s12 m12 l12">

            <?php

                include_once("../html/common/admin_panel_banner.inc.php");
            ?>

            <div class="card">
                <div class="card-content">
                    <div class="row">
                        <div class="col s12">

                        <div class="row">
                            <div class="col s6">
                                <h4>Stream</h4>
                            </div>
                        </div>

                        <div class="col s12" id="items-content">

                            <?php

                                   $result = $stream->get(0);

                                    $inbox_loaded = count($result['items']);

                                    if ($inbox_loaded != 0) {

                                        foreach ($result['items'] as $key => $value) {

                                            draw($value, $helper);
                                        }

                                    } else {

                                    ?>

                                        <div class="row">
                                            <div class="col s12">
                                                <div class="card blue-grey darken-1">
                                                    <div class="card-content white-text">
                                                        <span class="card-title">List is empty.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php
                                }

                                if ($inbox_all > 20) {

                                    ?>

                                    <div class="row more_cont">
                                        <div class="col s12">
                                            <a href="javascript:void(0)" onclick="Profile.moreItems('<?php echo $result['itemId']; ?>'); return false;">
                                                <button class="btn waves-effect waves-light teal more_link">View more</button>
                                            </a>
                                        </div>
                                    </div>

                                    <?php
                                }
                            ?>
                        </div>

			</div>
		  </div>
		</div>
	  </div>
	</div>
</div>
</main>

        <?php

            include_once("../html/common/admin_panel_footer.inc.php");
        ?>

        <script type="text/javascript">

            var inbox_all = <?php echo $inbox_all; ?>;
            var inbox_loaded = <?php echo $inbox_loaded; ?>;

            window.Post || ( window.Post = {} );

            Post.remove = function (offset, fromUserId, accessToken) {

                $.ajax({
                    type: 'GET',
                    url: '/admin/stream/?id=' + offset + '&fromUserId=' + fromUserId + '&access_token=' + accessToken,
                    data: 'itemId=' + offset + '&fromUserId=' + fromUserId + "&access_token=" + accessToken,
                    timeout: 30000,
                    success: function(response){

                        $('div.item[data-id=' + offset + ']').remove();
                    },
                    error: function(xhr, type){

                    }
                });
            };

            window.Profile || ( window.Profile = {} );

            Profile.moreItems = function (offset) {

                $('a.more_link').hide();
                $('a.loading_link').show();

                $.ajax({
                    type: 'POST',
                    url: '/admin/stream',
                    data: 'itemId=' + offset + "&loaded=" + inbox_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('div.more_cont').remove();

                        if (response.hasOwnProperty('html')){

                            $("div#items-content").append(response.html);
                        }

                        inbox_loaded = response.inbox_loaded;
                        inbox_all = response.inbox_all;
                    },
                    error: function(xhr, type){

                        $('a.more_link').show();
                        $('a.loading_link').hide();
                    }
                });
            };

        </script>

</body>
</html>

<?php

    function draw($item, $helper)
    {
        ?>

            <div class="row item" data-id="<?php echo $item['id']; ?>">
                <div class="col s8">
                    <div class="card">
                        <div class="card-content">
                            <p>
                                <a href="/admin/profile/?id=<?php echo $item['fromUserId']; ?>"><?php echo $item['fromUserFullname']; ?></a>
                                </br>

                                <?php

                                    if (strlen($item['post'])) {

                                        ?>
                                            <br>
                                            <?php echo $item['post']; ?>
                                            </br>
                                        <?php
                                    }
                                ?>


                                <?php

                                    if ($item['rePostId'] != 0) {

                                        $rePost = $item['rePost'];
                                        $rePost = $rePost[0];

                                        ?>
                                            <br>
                                            <?php echo "Re-post: <a target=\"_blank\" href=\"/{$rePost['fromUserUsername']}/post/{$rePost['id']}\">See Re-post -></a>"; ?>
                                            <br>
                                        <?php
                                    }

                                ?>

                                <?php

                                    if (strlen($item['imgUrl'])) {

                                        ?>
                                        <br>
                                        <span class="post_img">
                                            <img style="width: 100%" src="<?php echo $item['imgUrl']; ?>"/>
                                        </span>
                                        <?php
                                    }
                                ?>

                                <br>
                                <span class="text-lighten-2"><?php echo $item['timeAgo']; ?></span>
                            </p>
                        </div>
                        <div class="card-action">
                            <a href="javascript: void(0)" onclick="Post.remove('<?php echo $item['id']; ?>', '<?php echo $item['fromUserId']; ?>', '<?php echo admin::getAccessToken(); ?>'); return false;">Delete</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php
    }