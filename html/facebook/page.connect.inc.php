<?php

/*!
 * ifsoft.co.uk v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

require 'facebook.php';

if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

    header('Location: /');
}

if (isset($_GET['error'])) {

    header("Location: /account/settings/services");
    exit;
}

$facebook = new facebook(array(
    'appId' => FACEBOOK_APP_ID,
    'secret' => FACEBOOK_APP_SECRET,
));

$user = $facebook->getUser();

if ($user) {

    try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me');

    } catch (FacebookApiException $e) {

        header("Location: /facebook/connect");
        exit;
    }

    if (!empty($user_profile )) {

        # User info ok? Let's print it (Here we will be adding the login and registering routines)

        $fb_id = $user_profile['id'];

        $accountId = $helper->getUserIdByFacebook($fb_id);

        if ($accountId != 0) {

            //user with fb id exists in db
            header("Location: /account/settings/services/?oauth_provider=facebook&status=error");
            exit;

        } else {

            //new user

            $account = new account($dbo, auth::getCurrentUserId());
            $account->setFacebookId($fb_id);

            header("Location: /account/settings/services/?oauth_provider=facebook&status=connected");
            exit;
        }

    } else {

        # For testing purposes, if there was an error, let's kill the script
        header("Location: /account/settings/services");
        exit;
    }

} else {

    # There's no active session, let's generate one
    $login_url = $facebook->getLoginUrl(array( 'scope' => 'email, user_friends'));
    header("Location: " . $login_url);
}