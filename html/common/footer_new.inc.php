<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

?>

    <div id="page_footer">

        <div id="bottom_nav">
            <a href="/about"><?php echo $LANG['footer-about']; ?></a>
            <a href="/terms"><?php echo $LANG['footer-terms']; ?></a>
            <a href="/support"><?php echo $LANG['footer-support']; ?></a>
        </div>

        <div id="footer" class="clear">
            <?php echo APP_TITLE; ?> © <?php echo APP_YEAR; ?>
            <a class="lang_link" href="javascript:void(0)" onclick="App.getLanguageBox('<?php echo $LANG['page-language']; ?>'); return false;"><?php echo $LANG['lang-name']; ?></a>
        </div>

    </div>

    <script type="text/javascript" src="/js/jquery-2.1.1.min.js"></script>

    <script type="text/javascript">

        var options = {

            pageId: "<?php echo $page_id; ?>"
        }

        var account = {

            id: "<?php echo auth::getCurrentUserId(); ?>",
            username: "<?php echo auth::getCurrentUserLogin(); ?>",
            accessToken: "<?php echo auth::getAccessToken(); ?>"
        }

    </script>

    <script type="text/javascript">

        var lang_prompt_box = "<?php echo $LANG['page-prompt']; ?>";
    </script>

    <script src="/js/common.js?x=34"></script>

    <script src="/js/jquery.colorbox.js?x=30"></script>
    <script src="/js/jquery.autosize.js?x=30"></script>
    <script src="/js/jquery.cookie.js?x=30"></script>