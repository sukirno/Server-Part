<?php

    if (!auth::isSession()) {

        ?>
            <div id="side_bar">
                <ol>
                    <li id="l_fr">
                        <a href="/signup" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['topbar-signup']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/login" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['topbar-signin']; ?></span>
                        </a>
                    </li>
                </ol>
            </div>
        <?php

    } else {

        $msg = new messages($dbo);
        $msg->setRequestFrom(auth::getCurrentUserId());

        $new_messages = $msg->getNewMessagesCount();

        unset($msg);

        ?>
            <div id="side_bar">
                <ol>
                    <li id="l_fr">
                        <a href="/<?php echo auth::getCurrentUserLogin(); ?>" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-profile']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/<?php echo auth::getCurrentUserLogin(); ?>/gallery" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-gallery']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/account/friends" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-friends']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/account/messages" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-messages']; ?> <span id="messages_counter_cont" <?php if ($new_messages == 0) echo "style=\"display: none\""; ?>>(<span id="messages_counter"><?php echo $new_messages; ?></span>)</span></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/account/groups" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-groups']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/account/guests" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-guests']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/account/wall" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-news']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/account/favorites" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-favorites']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/account/settings/profile" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-settings']; ?></span>
                        </a>
                    </li>

                    <div class="more_div"></div>

                    <li id="l_fr">
                        <a href="/account/stream" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-stream']; ?></span>
                        </a>
                    </li>
                    <li id="l_fr">
                        <a href="/account/popular" class="left_row">
                            <span class="left_label inl_bl"><?php echo $LANG['sidebar-popular']; ?></span>
                        </a>
                    </li>
                </ol>
            </div>
        <?php
    }