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

    $stats = new stats($dbo);

    $report = new report($dbo);

    if (isset($_GET['act'])) {

        $act = isset($_GET['act']) ? $_GET['act'] : '';
        $token = isset($_GET['access_token']) ? $_GET['access_token'] : '';

        if (admin::getAccessToken() === $token && !APP_DEMO) {

            switch ($act) {

                case "clear" : {

                    $report->removeAllPostReports();

                    header("Location: /admin/post_reports");
                    break;
                }

                default: {

                    header("Location: /admin/post_reports");
                }
            }
        }

        header("Location: /admin/post_reports");
    }

    $page_id = "post_reports";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("my.css");
    $page_title = "Post Reports";

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
                            <div class="col s12">
                                <h4>Post Reports (Latest reports)</h4>
                            </div>
                        </div>

                        <div class="col s12">

                            <?php

                                $result = $stats->getPostsReports();

                                $inbox_loaded = count($result['reports']);

                                if ($inbox_loaded != 0) {

                                ?>

                                <div class="row">
                                    <div class="col s12">
                                        <a href="/admin/post_reports/?act=clear&access_token=<?php echo admin::getAccessToken(); ?>">
                                            <button class="btn waves-effect waves-light teal">Delete all reports<i class="material-icons right">delete</i></button>
                                        </a>
                                    </div>
                                </div>

                                <table class="bordered data-tables responsive-table">
                                    <tbody>
                                        <tr>
                                            <th class="text-left">Id</th>
                                            <th>From account</th>
                                            <th>To post</th>
                                            <th>Abuse</th>
                                            <th>Date</th>
                                        </tr>

                                    <?php

                                    foreach ($result['reports'] as $key => $value) {

                                        draw($value);
                                    }

                                    ?>

                                    </tbody>
                                </table>

                                <?php

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


        </script>

</body>
</html>

<?php

    function draw($user)
    {
        ?>

        <tr>
            <td class="text-left"><?php echo $user['id']; ?></td>
            <td><?php if ($user['abuseFromUserId'] == 0) {echo "-";} else {echo "<a href=\"/admin/profile/?id={$user['abuseFromUserId']}\">From profile Id ({$user['abuseFromUserId']})</a>";} ?></td>
            <td><?php echo "<a href=\"/admin/post/?id={$user['abuseToPostId']}\">To post Id ({$user['abuseToPostId']})</a>"; ?></td>
            <td>
                <?php

                    switch ($user['abuseId']) {

                        case 0: {

                            echo "This is spam.";

                            break;
                        }

                        case 1: {

                            echo "Hate Speech or violence.";

                            break;
                        }

                        case 2: {

                            echo "Nudity or Pornography.";

                            break;
                        }

                        default: {

                            echo "Piracy.";

                            break;
                        }
                    }
                ?>
            </td>
            <td><?php echo $user['date']; ?></td>
<!--            <td><a href="/admin/profile.php/?id=--><?php //echo $user['id']; ?><!--">Go to account</a></td>-->
        </tr>

        <?php
    }