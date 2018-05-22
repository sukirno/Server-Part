<?php

/*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk, qascript@mail.ru
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

class community extends db_connect
{
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }

    static function comment($comment, $postInfo, $LANG = array()) {

        $comment['comment'] = helper::processCommentText($comment['comment']);

        $fromUserPhoto = "/img/profile_default_photo.png";

        if (strlen($comment['fromUserPhotoUrl']) != 0) {

            $fromUserPhoto = $comment['fromUserPhotoUrl'];
        }

        ?>

        <div class="comment_item" data-id="<?php echo $comment['id']; ?>">

        <span class="comment_profile">
            <a href="/<?php echo $comment['fromUserUsername']; ?>">
                <img src="<?php echo $fromUserPhoto; ?>">
            </a>
        </span>

            <div class="comment_content">

                <?php

                if (auth::getCurrentUserId() != 0) {

                    if ($postInfo['fromUserId'] == auth::getCurrentUserId() || auth::getCurrentUserId() == $comment['fromUserId']) {

                        ?>

                        <div class="comment_remove" onclick="Comments.remove('<?php echo $comment['fromUserUsername']; ?>', '<?php echo $postInfo['id']; ?>', '<?php echo $comment['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"></div>

                        <?php
                    }
                }
                ?>
                <div class="comment_title"><?php echo $comment['comment']; ?></div>
                <div class="comment_footer">
                    <span class="time"><?php echo $comment['timeAgo']; ?></span>
                    <?php

                        if ($comment['replyToUserId'] != 0) {

                            ?>
                            <span class="time action_reply_to"> <?php echo $LANG['label-to-user']; ?> <a href="/<?php echo $comment['replyToUserUsername']; ?>"><?php echo $comment['replyToFullname']; ?></a></span>
                            <?php
                        }
                    ?>
                    <?php

                        if ((auth::getCurrentUserId() != 0) && ($comment['fromUserId'] != auth::getCurrentUserId()) && ($postInfo['allowComments'] != 0) ) {

                            ?>
                                | <span class="time action_reply"><a href="javascript:void(0)" onclick="Comments.reply('<?php echo $comment['fromUserId']; ?>', '<?php echo $comment['fromUserUsername']; ?>', '<?php echo $comment['fromUserFullname']; ?>'); return false;"><?php echo $LANG['action-reply']; ?></a></span>
                            <?php
                        }
                    ?>
                </div>
            </div>
        </div>

        <?php
    }

    static function post($post, $LANG, $helper = null, $showComments = false)
    {
        $post['post'] = helper::processPostText($post['post']);

        $fromUserPhoto = "/img/profile_default_photo.png";

        if (strlen($post['fromUserPhoto']) != 0) {

            $fromUserPhoto = $post['fromUserPhoto'];
        }

        $username = $post['fromUserUsername'];
        $fullname = $post['fromUserFullname'];

        if ($post['groupId'] != 0) {

            $group = new group(null, $post['groupId']);
            $group->setRequestFrom(auth::getCurrentUserId());

            $groupInfo = $group->get();

            if ($groupInfo['accountAuthor'] == $post['fromUserId']) {

                $username = $groupInfo['username'];
                $fullname = $groupInfo['fullname'];

                if (strlen($groupInfo['lowPhotoUrl'])) {

                    $fromUserPhoto = $groupInfo['lowPhotoUrl'];
                }
            }
        }

        ?>

        <div class="post post_item" data-id="<?php echo $post['id']; ?>">

            <a class="profile_cont" href="/<?php echo $username; ?>">
                <img src="<?php echo $fromUserPhoto; ?>">
            </a>

            <div class="post_content">
                <?php

                    if ((auth::isSession() && $post['fromUserId'] == auth::getCurrentUserId()) || (isset($groupInfo) && $groupInfo['accountAuthor'] == auth::getCurrentUserId())) {

                        ?>

                        <div class="action_remove" onclick="Post.remove('<?php echo $post['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"></div>

                        <?php

                    } else {

                        if (auth::isSession() && $post['fromUserId'] != auth::getCurrentUserId()) {

                            ?>

                            <div class="action_report" onclick="Post.getReportBox('<?php echo $post['fromUserUsername']; ?>', '<?php echo $post['id']; ?>', '<?php echo $LANG['page-profile-report']; ?>'); return false;"></div>

                            <?php
                        }
                    }

                ?>
                <div class="post_title">
                    <a href="/<?php echo $username; ?>">
                        <span class="post_fullname"><?php echo $fullname; ?></span>
                        <?php

                            if (isset($groupInfo) && $groupInfo['accountAuthor'] == $post['fromUserId']) {


                            } else {

                                ?>
                                    <s>@</s><b class="post_username"><?php echo $post['fromUserUsername']; ?></b>
                                <?php
                            }
                        ?>
                    </a>
                </div>
                <div class="post_data">
                    <?php echo $post['post']; ?>
                </div>

                <?php

                if (strlen($post['imgUrl'])) {

                    ?>

                    <div class="post_img">
                        <img src="<?php echo $post['imgUrl']; ?>"/>
                    </div>
                    <?php
                }
                ?>

                <?php

                    $rePost = $post['rePost'];
                    $rePost = $rePost[0];

                    if ($post['rePostId'] != 0 && $rePost['error'] === false) {

                        if ($rePost['removeAt'] != 0) {

                            ?>

                                <div class="post post_item" data-id="<?php echo $rePost['id']; ?>" style="width: 100%;display: inline-block; border-left: 1px solid #DAE1E8; border-bottom: 0px; padding-left: 5px; margin-top: 10px; margin-bottom: 10px;">

                                    <div class="post_content">
                                        <div class="post_data">
                                            <?php echo $LANG['label-repost-error']; ?>
                                        </div>
                                    </div>
                                </div>

                            <?php

                        }  else {


                            $rePost['post'] = helper::processPostText($rePost['post']);

                            $rePostFromUserPhoto = "/img/profile_default_photo.png";

                            if (strlen($rePost['fromUserPhoto']) != 0) {

                                $rePostFromUserPhoto = $rePost['fromUserPhoto'];
                            }

                            ?>

                            <div class="post post_item" data-id="<?php echo $rePost['id']; ?>" style="width: 100%;display: inline-block; border-left: 1px solid #DAE1E8; border-bottom: 0px; padding-left: 5px; margin-top: 10px; margin-bottom: 10px;">

                                <a class="profile_cont"
                                   href="/<?php echo $rePost['fromUserUsername']; ?>/post/<?php echo $rePost['id'] ?>">
                                    <img src="<?php echo $rePostFromUserPhoto; ?>">
                                </a>

                                <div class="post_content">

                                    <div class="post_title">
                                        <a href="/<?php echo $rePost['fromUserUsername']; ?>/post/<?php echo $rePost['id'] ?>">
                                            <span
                                                class="post_fullname"><?php echo $rePost['fromUserFullname']; ?></span>
                                            <s>@</s><b
                                                class="post_username"><?php echo $rePost['fromUserUsername']; ?></b>
                                        </a>
                                    </div>
                                    <div class="post_data">
                                        <?php echo $rePost['post']; ?>
                                    </div>

                                    <?php

                                    if (strlen($rePost['imgUrl'])) {

                                        ?>

                                        <div class="post_img">
                                            <img style="max-width: 444px;" src="<?php echo $rePost['imgUrl']; ?>"/>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                </div>

                            </div>

                            <?php
                        }
                    }

                ?>

                <div class="post_footer">
                    <div class="post_like" onclick="Post.like('<?php echo $post['fromUserUsername']; ?>', '<?php echo $post['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;">
                        <i data-id="<?php echo $post['id']; ?>" class="like_icon <?php if ( $post['myLike'] ) echo "mylike"; ?>"></i>
                    </div>
                    <div class="post_like_count" data-id="<?php echo $post['id']; ?>">
                        <?php echo language::getLikes($LANG, $post); ?>
                    </div>
                    <?php

                    $time = new language(NULL, $LANG['lang-code']);
                    ?>
                    <a class="time" href="/<?php echo $post['fromUserUsername']; ?>/post/<?php echo $post['id']; ?>"><?php echo $time->timeAgo($post['createAt']); ?></a>
                    <?php
                        if (!$showComments) {

                            ?>
                            <span class="action_separator"> | </span>
                            <a class="action_comment" href="/<?php echo $post['fromUserUsername']; ?>/post/<?php echo $post['id']; ?>"><?php echo $LANG['action-comment']; ?> <?php if ($post['commentsCount'] > 0) echo "({$post['commentsCount']})"; ?></a>
                            <?php

                                if (auth::isSession() && $post['fromUserId'] != auth::getCurrentUserId()) {

                                    $re_post_id = $post['id'];

                                    if ($post['rePostId'] != 0) {

                                        $re_post_id = $post['rePostId'];
                                    }

                                    ?>
                                    <span class="action_separator"> | </span>
                                    <a class="action_share" data-id="<?php echo $post['id']; ?>" href="javascript:void(0)" onclick="Post.getRepostBox('<?php echo $post['fromUserUsername']; ?>', '<?php echo $re_post_id; ?>', '<?php echo $LANG['action-share-post']; ?>', '<?php echo $post['myRePost']; ?>'); return false;"><?php echo $LANG['action-share']; ?> <?php if ($post['rePostsCount'] > 0) echo "({$post['rePostsCount']})"; ?></a>
                                    <?php
                                }
                            ?>

                            <?php
                        }
                    ?>
                </div>
            </div>

            <?php

            if ($showComments) {

                ?>

                <div data-id="<?php echo $post['id']; ?>" class="post_comments">

                    <?php

                    $comments = new comments();
                    $comments->setLanguage($LANG['lang-code']);
                    $comments->setRequestFrom(auth::getCurrentUserId());

                    $data = $comments->getPreview($post['id']);

                    $commentsCount = $data['count'];

                    if ($commentsCount > 3) {

                        ?>
                        <a data-id="<?php echo $post['id']; ?>" onclick="Comments.more('<?php echo $post['fromUserUsername']; ?>', '<?php echo $post['id'] ?>', '<?php echo $data['commentId']; ?>'); return false;" class="get_comments_header">
                            <?php echo $LANG['action-show-all']; ?> (<?php echo $commentsCount - 3; ?>)
                        </a>
                        <?php
                    }

                    $data['comments'] = array_reverse($data['comments'], false);

                    foreach ($data['comments'] as $key => $value) {

                        draw::comment($value, $post, $LANG);
                    }

                    if (auth::getCurrentUserId() != 0) {

                        $allowComments = $post['allowComments'];

                        if ($post['groupId'] != 0) {

                            $allowComments = $post['groupAllowComments'];
                        }

                        if ($allowComments) {

                            ?>

                            <div class="comment_form" data-id="<?php echo $post['id']; ?>">

                                <span class="comment_form_profile">

                                    <?php

                                        $profile = new profile(null, auth::getCurrentUserId());
                                        $profileInfo = $profile->get();

                                        $lowPhotoUrl = "/img/profile_default_photo.png";

                                        if (strlen($profileInfo['lowPhotoUrl']) != 0) {

                                            $lowPhotoUrl = $profileInfo['lowPhotoUrl'];
                                        }

                                        ?>

                                        <img class="comment_switch" src="<?php echo $lowPhotoUrl; ?>">
                                </span>

                                <form class="" onsubmit="Comments.create('<?php echo $post['fromUserUsername']; ?>', '<?php echo $post['id'] ?>', '<?php echo auth::getAccessToken(); ?>'); return false;">
                                    <input data-id="<?php echo $post['id']; ?>" class="comment_text" name="comment_text" maxlength="140" placeholder="<?Php echo $LANG['label-placeholder-comment']; ?>" type="text" value="">
                                    <button class="primary_btn comment_send"><?Php echo $LANG['action-send']; ?></button>
                                </form>

                            </div>

                            <?php

                        } else {

                            ?>
                            <div class="comment_form">
                                <div class="comment_prompt">
                                    <?php echo $LANG['label-comments-disallow']; ?>
                                </div>
                            </div>
                            <?php
                        }

                    } else {

                        ?>

                        <div class="comment_form">
                            <div class="comment_prompt">
                                <?php echo $LANG['label-comments-prompt']; ?>
                            </div>
                        </div>

                        <?php
                    }
                    ?>

                </div>

                <?php
            }
            ?>

        </div>

        <?php
    }

    static function communityItem($community, $LANG, $helper = null)
    {
        ?>

        <div class="post profile_item">
            <a class="profile_cont" href="/<?php echo $community['username']; ?>">
                <?php

                $profilePhotoUrl = "/img/profile_default_photo.png";

                if (strlen($community['lowPhotoUrl']) != 0) {

                    $profilePhotoUrl = $community['lowPhotoUrl'];
                }
                ?>

                <img src="<?php echo $profilePhotoUrl; ?>"/>
            </a>
            <div class="post_cont">
                <a class="fullname" href="/<?php echo $community['username']; ?>"><?php echo $community['fullname']; ?></a>
                <?php if ($community['verify'] == 1) echo "<b original-title=\"{$LANG['label-account-verified']}\" class=\"verified\"></b>"; ?>
                <div class="addon_info">
                    <?php echo $LANG['page-followers']; ?>: <span class="username"><?php echo $community['followersCount']; ?></span>
                </div>
            </div>
        </div>

        <?php
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }
}

