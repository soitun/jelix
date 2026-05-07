<?php
/**
 * @package    jelix
 * @subpackage auth
 *
 * @author     Laurent Jouanneau
 *
 * @copyright  2026 Laurent Jouanneau
 */

class jAuthPersistentLogin
{
    protected $persistentCookieName = 'jauthPersistentSession';
    protected $persistentCookiePath = '';
    protected $persistencyEnabled = false;
    protected $persistentCookieDuration = 172800; // 48h

    public function __construct(array $authConfig)
    {
        $this->persistencyEnabled = isset($authConfig['persistant_enable']) && $authConfig['persistant_enable'];
        $this->persistentCookieName = isset($authConfig['persistant_cookie_name']) ? $authConfig['persistant_cookie_name'] : 'jauthPersistentSession';
        $this->persistentCookiePath = isset($authConfig['persistant_cookie_path']) ? $authConfig['persistant_cookie_path'] : '';
        $this->persistentCookieDuration = isset($authConfig['persistant_duration']) ? intval($authConfig['persistant_duration']) * 86400 : 172800;
    }

    public function isPersistencyEnabled()
    {
        return $this->persistencyEnabled;
    }


    protected function getCookiesParts(array $cookies)
    {
        if (isset($cookies[$this->persistentCookieName])
            && is_string($cookies[$this->persistentCookieName])
            && strlen($cookies[$this->persistentCookieName])) {

            $parts = explode(':', $cookies[$this->persistentCookieName]);
            if (count($parts) == 3) {
                list($login, $series, $token) = $parts;
                if ($login == '' || $series == '' || $token == '') {
                    $this->deleteCookie();
                    return false;
                }
                return $parts;
            } else {
                $this->deleteCookie();
                return false;
            }
        }
        return false;
    }

    protected function setCookie($login, $token, $series, $expiresAt)
    {
        $value = $login . ':' . $series .':'. $token;
        setcookie($this->persistentCookieName, $value, $expiresAt, $this->persistentCookiePath, '', true, true);
    }

    protected function deleteCookie()
    {
        setcookie($this->persistentCookieName, '', time() - 3600, $this->persistentCookiePath, '', true, true);
    }

    /**
     * @param array $cookies
     * @param bool $userIsConnected
     * @return false|string the user id if the token is found and valid, false otherwise
     */
    public function checkTokenFromCookie(array $cookies, bool $userIsConnected)
    {
        if (!$this->persistencyEnabled) {
            return false;
        }

        if ($userIsConnected) {
            // opportunity to delete expired tokens
            $dao = jDao::get('jelix~jauthremembertoken');
            $dao->deleteExpiredTokens(time());
        }
        else if ($this->persistentCookieName != '') {

            $parts = $this->getCookiesParts($cookies);
            if ($parts) {
                list($login, $series, $token) = $parts;

                // search the token in the database
                $tokenHash = hash('sha256', $token);
                $seriesHash = hash('sha256', $series);

                $dao = jDao::get('jelix~jauthremembertoken');
                $rec = $dao->getByLoginAndSeries($login, $seriesHash, time());
                if ($rec && $rec->token_hash == $tokenHash) {
                     // found !!
                    // rotate the token, keeps the same series, and return the login
                    $dao->deleteByLoginAndSeries($login, $seriesHash);
                    $newToken = bin2hex(random_bytes(32));
                    $rec->token_hash = hash('sha256', $newToken);
                    $rec->expires_at = time() + $this->persistentCookieDuration;
                    $dao->insert($rec);

                    $this->setCookie($login, $newToken, $series, $rec->expires_at);
                    return $rec->login;
                }
                else if ($rec) {
                    // a token exists with the same series, so the given token is an old one : a theft is assumed. Let's delete all tokens of the user
                    $dao->deleteByLogin($login);
                } else {
                    // token not found or expired. This is the opportunity to delete expired tokens
                    $dao->deleteExpiredTokensByLogin($login, time());
                }
            }
        }
        return false;
    }

    /**
     * @param $login
     * @return int expiration date (UNIX timestamp), or 0 if cookie is not set
     *
     * @throws \Random\RandomException
     */
    public function generateCookieWithNewToken($login)
    {
        if (!$this->persistencyEnabled || !$this->persistentCookieName) {
            return 0;
        }
        // generate a new token
        $token = bin2hex(random_bytes(32));
        $series = bin2hex(random_bytes(32));

        // store into the database
        $dao = jDao::get('jelix~jauthremembertoken');
        $rec = $dao->createRecord();
        $rec->token_hash = hash('sha256', $token);
        $rec->login = $login;
        $rec->series_hash =  hash('sha256', $series);
        $rec->expires_at = time() + $this->persistentCookieDuration;
        $dao->insert($rec);

        $this->setCookie($login, $token, $series, $rec->expires_at);

        return $rec->expires_at;
    }

    public function deleteUserToken(array $cookies, $login)
    {
        if ($this->persistentCookieName != '') {
            $parts = $this->getCookiesParts($cookies);
            if ($parts) {
                list($cookieLogin, $series, $token) = $parts;

                $this->deleteCookie();

                $dao = jDao::get('jelix~jauthremembertoken');
                if ($cookieLogin == $login) {
                    $seriesHash = hash('sha256', $series);

                    // delete the entire series from the database
                    $dao->deleteByLoginAndSeries($login, $seriesHash);
                }
                else {
                    // strange, not the same login. Delete the given token
                    $dao->delete($cookieLogin, hash('sha256', $token));
                }

                // delete expired tokens
                $dao->deleteExpiredTokensByLogin($login, time());
            }
        }
    }

    public function deleteAllUserTokens($login)
    {
        $dao = jDao::get('jelix~jauthremembertoken');
        $dao->deleteByLogin($login);
    }

    public function deleteExpiredTokens()
    {
        $dao = jDao::get('jelix~jauthremembertoken');
        $dao->deleteExpiredTokens(time());
    }
}
