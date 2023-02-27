<?php
$path = realpath(dirname(__FILE__));
require_once $path . "/Encrypter.php";
require_once $path . "/../classes/Common.php";

class RememberCookie
{
    const ID = 0;
    const REMEMBER_TOKEN = 1;
    const PASSWORD = 2;

    public function __construct(public readonly Common $common = new Common())
    {
    }

    public function setRememberCookie($user)
    {
        $key = Encrypter::generateKey();
        $encrypter = new Encrypter($key);

        $rememberToken = $user['remember_token'];
        if ($rememberToken === null) {
            $rememberToken = base64_encode(serialize(random_bytes(30)));
            $this->common->update(
                table: 'users',
                data: ['remember_token' => $rememberToken],
                cond: 'id = :id',
                params: ['id' => $user['id']],
                modifiedColumnName: 'updated_at'
            );
        }
        $result = $encrypter->encrypt([$user['id'], $rememberToken, $user['password']]);
        $cookieKey = 'remember_web_' . base64_encode(serialize($key));

        setcookie($cookieKey, $result, time()+3600*24*30, '/');
    }

    public static function getRememberCookieData(): ?array
    {
        foreach ($_COOKIE as $key => $cookie) {
            if (str_starts_with($key, 'remember_web_')) {
                return self::decryptCookie($key, $cookie);
            }
        }

        return null;
    }

    public static function decryptCookie($key, $cookie)
    {
        $key = substr($key, 13);
        $key = base64_decode($key);
        $key = unserialize($key);

        return (new Encrypter($key))->decrypt($cookie);
    }

    public function destroyRememberCookie()
    {
        foreach ($_COOKIE as $key => $cookie) {
            if (str_starts_with($key, 'remember_web_')) {
                $data = self::decryptCookie($key, $cookie);
                $this->common->update(
                    table: 'users',
                    data: ['remember_token' => null],
                    cond: 'id = :id',
                    params: ['id' => $data[self::ID]],
                    modifiedColumnName: 'updated_at'
                );
                setcookie($key, null, -1, '/');
            }
        }

        return null;
    }
}