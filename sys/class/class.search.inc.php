<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

class search extends db_connect
{

    private $requestFrom = 0;
    private $language = 'en';

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);
    }

    private function getCommunitiesCount($queryText)
    {
        $queryText = "%".$queryText."%";

        $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE state = 0 AND account_type > 0 AND (login LIKE (:query) OR fullname LIKE (:query))");
        $stmt->bindParam(':query', $queryText, PDO::PARAM_STR);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    private function getCount($queryText)
    {
        $queryText = "%".$queryText."%";

        $stmt = $this->db->prepare("SELECT count(*) FROM users WHERE state = 0 AND account_type = 0 AND (login LIKE (:query) OR fullname LIKE (:query) OR email LIKE (:query))");
        $stmt->bindParam(':query', $queryText, PDO::PARAM_STR);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function lastIndex()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM users");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn() + 1;
    }

    public function query($queryText = '', $userId = 0)
    {
        $originQuery = $queryText;

        if ($userId == 0) {

            $userId = $this->lastIndex();
            $userId++;
        }

        $users = array("error" => false,
                    "error_code" => ERROR_SUCCESS,
                    "itemCount" => $this->getCount($originQuery),
                    "userId" => $userId,
                    "query" => $originQuery,
                    "users" => array());

        $queryText = "%".$queryText."%";

        $stmt = $this->db->prepare("SELECT id, regtime FROM users WHERE state = 0 AND account_type = 0 AND (login LIKE (:query) OR fullname LIKE (:query) OR email LIKE (:query) ) AND id < (:userId) ORDER BY regtime DESC LIMIT 20");
        $stmt->bindParam(':query', $queryText, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $profile = new profile($this->db, $row['id']);
                    $profile->setRequestFrom($this->requestFrom);

                    array_push($users['users'], $profile->get());

                    $users['userId'] = $row['id'];

                    unset($profile);
                }
            }
        }

        return $users;
    }

    public function communitiesQuery($queryText = '', $itemId = 0)
    {
        $originQuery = $queryText;

        if ($itemId == 0) {

            $itemId = $this->lastIndex();
            $itemId++;
        }

        $result = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "itemsCount" => $this->getCommunitiesCount($originQuery),
                        "itemId" => $itemId,
                        "query" => $originQuery,
                        "items" => array());

        $queryText = "%".$queryText."%";

        $stmt = $this->db->prepare("SELECT id, regtime FROM users WHERE state = 0 AND account_type > 0 AND (login LIKE (:query) OR fullname LIKE (:query)) AND id < (:itemId) ORDER BY regtime DESC LIMIT 20");
        $stmt->bindParam(':query', $queryText, PDO::PARAM_STR);
        $stmt->bindParam(':itemId', $itemId, PDO::PARAM_STR);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    $group = new group($this->db, $row['id']);
                    $group->setRequestFrom($this->requestFrom);

                    array_push($result['items'], $group->get());

                    $result['itemId'] = $row['id'];

                    unset($profile);
                }
            }
        }

        return $result;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
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

