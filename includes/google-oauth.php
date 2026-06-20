<?php
/**
 * Google OAuth Handler
 */

class GoogleOAuth {
    private $client_id = GOOGLE_CLIENT_ID;
    private $client_secret = GOOGLE_CLIENT_SECRET;
    private $redirect_uri = GOOGLE_REDIRECT_URI;
    private $auth_url = 'https://accounts.google.com/o/oauth2/auth';
    private $token_url = 'https://oauth2.googleapis.com/token';
    private $userinfo_url = 'https://www.googleapis.com/oauth2/v1/userinfo';

    public function getAuthUrl() {
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'access_type' => 'offline'
        );
        return $this->auth_url . '?' . http_build_query($params);
    }

    public function getAccessToken($code) {
        $params = array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirect_uri
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function getUserInfo($access_token) {
        $headers = array(
            'Authorization: Bearer ' . $access_token
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->userinfo_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function handleCallback() {
        if (!isset($_GET['code'])) {
            return array('success' => false, 'message' => 'No authorization code');
        }

        try {
            $token_response = $this->getAccessToken($_GET['code']);

            if (!isset($token_response['access_token'])) {
                return array('success' => false, 'message' => 'Failed to get access token');
            }

            $user_info = $this->getUserInfo($token_response['access_token']);

            if (!isset($user_info['id'])) {
                return array('success' => false, 'message' => 'Failed to get user info');
            }

            return array(
                'success' => true,
                'google_id' => $user_info['id'],
                'email' => $user_info['email'],
                'name' => $user_info['name'],
                'picture' => isset($user_info['picture']) ? $user_info['picture'] : null
            );
        } catch (Exception $e) {
            error_log('Google OAuth Error: ' . $e->getMessage());
            return array('success' => false, 'message' => 'OAuth callback failed');
        }
    }
}
?>
