<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

class update extends db_connect
{
    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    // For reply to comments v1.2

    function addColumnToCommentsTable()
    {
        $stmt = $this->db->prepare("ALTER TABLE comments ADD replyToUserId INT(11) UNSIGNED DEFAULT 0 after fromUserId");
        $stmt->execute();
    }

    // For settings reply to comments v1.2

    function addColumnToUsersTable()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowCommentReplyGCM SMALLINT(6) UNSIGNED DEFAULT 1 after allowMessagesGCM");
        $stmt->execute();
    }

    function addColumnToPostsTable()
    {
        $stmt = $this->db->prepare("ALTER TABLE posts ADD rePostsCount INT(11) UNSIGNED DEFAULT 0 after likesCount");
        $stmt->execute();
    }

    function addColumnToPostsTable2()
    {
        $stmt = $this->db->prepare("ALTER TABLE posts ADD rePostId INT(11) UNSIGNED DEFAULT 0 after fromUserId");
        $stmt->execute();
    }

    // For version 1.5 | Emoji support

    function setChatEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE messages charset = utf8mb4, MODIFY COLUMN message VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function setCommentsEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE comments charset = utf8mb4, MODIFY COLUMN comment VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function setPostsEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE posts charset = utf8mb4, MODIFY COLUMN post VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    // For version 1.6

    function setPhotosEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE photos charset = utf8mb4, MODIFY COLUMN comment VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    function addColumnToUsersTable2()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD photos_count INT(11) UNSIGNED DEFAULT 0 after likes_count");
        $stmt->execute();
    }

    // For version 1.7

    function addColumnToPostsTable3()
    {
        $stmt = $this->db->prepare("ALTER TABLE posts ADD groupId INT(11) UNSIGNED DEFAULT 0 after id");
        $stmt->execute();
    }

    function addColumnToUsersTable3()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowPosts INT(11) UNSIGNED DEFAULT 0 after allowMessages");
        $stmt->execute();
    }

    function addColumnToUsersTable4()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD account_category INT(11) UNSIGNED DEFAULT 0 after account_type");
        $stmt->execute();
    }

    function addColumnToUsersTable5()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD gifts_count INT(11) UNSIGNED DEFAULT 0 after photos_count");
        $stmt->execute();
    }

    function addColumnToUsersTable6()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD allowGiftsGCM INT(11) UNSIGNED DEFAULT 0 after allowCommentReplyGCM");
        $stmt->execute();
    }

    function addColumnToUsersTable7()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD followers_count INT(11) UNSIGNED DEFAULT 0 after gifts_count");
        $stmt->execute();
    }

    function addColumnToUsersTable8()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD following_count INT(11) UNSIGNED DEFAULT 0 after followers_count");
        $stmt->execute();
    }

    function addColumnToUsersTable9()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD friends_count INT(11) UNSIGNED DEFAULT 0 after following_count");
        $stmt->execute();
    }

    function addColumnToUsersTable10()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD last_guests_view INT(10) UNSIGNED DEFAULT 0 after last_feed_view");
        $stmt->execute();
    }

    // For version 1.8

    function addColumnToUsersTable11()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD ghost INT(10) UNSIGNED DEFAULT 0 after admob");
        $stmt->execute();
    }

    // For version 1.9

    function addColumnToUsersTable12()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD lat float(10,6) DEFAULT 0 after city_id");
        $stmt->execute();
    }

    function addColumnToUsersTable14()
    {
        $stmt = $this->db->prepare("ALTER TABLE users ADD lng float(10,6) DEFAULT 0 after lat");
        $stmt->execute();
    }

    // For version 2.1 | Emoji support

    function setGiftsEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE gifts charset = utf8mb4, MODIFY COLUMN message VARCHAR(400) CHARACTER SET utf8mb4");
        $stmt->execute();
    }

    // For version 2.2

    function addColumnToPostsTable4()
    {
        $stmt = $this->db->prepare("ALTER TABLE posts ADD urlPreviewTitle VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' after post");
        $stmt->execute();
    }

    function addColumnToPostsTable5()
    {
        $stmt = $this->db->prepare("ALTER TABLE posts ADD urlPreviewImage VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' after urlPreviewTitle");
        $stmt->execute();
    }

    function addColumnToPostsTable6()
    {
        $stmt = $this->db->prepare("ALTER TABLE posts ADD urlPreviewDescription VARCHAR(400) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' after urlPreviewImage");
        $stmt->execute();
    }

    function addColumnToPostsTable7()
    {
        $stmt = $this->db->prepare("ALTER TABLE posts ADD urlPreviewLink VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' after urlPreviewDescription");
        $stmt->execute();
    }

    // For version 2.3

    function addColumnToChatsTable()
    {
        $stmt = $this->db->prepare("ALTER TABLE chats ADD message varchar(800) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' after toUserId_lastView");
        $stmt->execute();
    }

    function addColumnToChatsTable2()
    {
        $stmt = $this->db->prepare("ALTER TABLE chats ADD messageCreateAt INT(11) UNSIGNED DEFAULT 0 after message");
        $stmt->execute();
    }

    function setDialogsEmojiSupport()
    {
        $stmt = $this->db->prepare("ALTER TABLE chats charset = utf8mb4, MODIFY COLUMN message VARCHAR(800) CHARACTER SET utf8mb4");
        $stmt->execute();
    }


    // addon

    function recalculate()
    {
        $stmt = $this->db->prepare("SELECT id FROM users");

        if ($stmt->execute()) {

            while ($row = $stmt->fetch()) {

                $account = new account($this->db, $row['id']);
                $account->updateCounters();
                unset($account);
            }
        }
    }
}
