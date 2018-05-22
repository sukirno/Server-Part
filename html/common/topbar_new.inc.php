<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */


    if (!auth::isSession()) {

        ?>

            <div id="page_topbar">

                <div class="topbar">
                    <div class="content">
                        <a href="/" class="logo"></a>

                        <div style="float: right">
                            <a href="/signup" class="topbar_item"><?php echo $LANG['topbar-signup']; ?></a>
                            <a href="/login" class="topbar_item"><?php echo $LANG['topbar-signin']; ?></a>
                        </div>
                    </div>
                </div>

            </div>
        <?php

    } else {

        $profile_top_bar = new profile($dbo, auth::getCurrentUserId());

        $topbar_notifications = new notify($dbo);
        $topbar_notifications->setRequestFrom(auth::getCurrentUserId());

        $notifications_count = $topbar_notifications->getNewCount($profile_top_bar->getLastNotifyView());

        unset($profile_top_bar);
        unset($topbar_notifications);

        ?>

            <div id="page_topbar">

                <div class="topbar">
                    <div class="content">
                        <a href="/account/wall" class="logo"></a>

                        <form method="get" action="/search/name" style="">
                            <input type="text" class="text" id="ts_input" placeholder="<?php echo $LANG['page-search']; ?>" name="query" autocomplete="off" style="">
                        </form>

                        <div style="float: right">
                            <a href="/account/wall" class="topbar_item"><?php echo $LANG['topbar-wall']; ?></a>

                            <a href="/account/notifications" class="topbar_item">
                                <?php echo $LANG['page-notifications-likes']; ?>
                                <span <?php if ($notifications_count < 1) echo "style=\"display: none\""; ?> id="notifications_counter_cont">(<span id="notifications_counter"><?php echo $notifications_count; ?></span>)</span>
                            </a>

                            <a href="/search/name" class="topbar_item"><?php echo $LANG['topbar-search']; ?></a>
                            <a href="/logout/?access_token=<?php echo auth::getAccessToken(); ?>&continue=/" class="topbar_item"><?php echo $LANG['topbar-logout']; ?></a>
                        </div>
                    </div>
                </div>

            </div>
        <?php
    }
?>