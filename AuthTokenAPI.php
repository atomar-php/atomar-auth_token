<?php

namespace auth_token;
use atomic\core\Auth;
use CryptoAPI;
use atomic\Atomic;

/**
* This is the internal api class that can be used by third party extensions
*/
class AuthTokenAPI
{
  /**
   * Generates a new token associated with the user account.
   * @param RedBeanPHP $user the user account the new token will run under
   * @param int $ttl the length of time in seconds the token will live. Default is config.session_ttl
   * @param int $length the character length of the generated token
   * @return mixed the generated token or false if the generation failed
   */
  public static function get_token($user, $ttl=null, $length=255) {
    $token = self::generate_token($user, $ttl, $length, false);
    if ($token !== null) {
      return $token->token;
    }
  }

  /**
   * Similar to get_token except these tokens are usable once. This is handy for offering one time verification e.g. email verification.
   *
   * @param RedBean $user the user account the new token will run under
   * @param int $ttl the length of time in seconds the token will live. Default is config.session_ttl
   * @param int $length the character length of the generated token
   * @return mixed the generated token or false if the generation failed
   */
  public static function get_flash_token($user, $ttl=null, $length=255) {
    $token = self::generate_token($user, $ttl, $length, true);
    if ($token !== null) {
      return $token->token;
    }
  }

  /**
   * Similar to get_token except these tokens are usable once. This is handy for offering one time verification e.g. email verification.
   *
   * @param RedBean $user the user account the new token will run under
   * @param int $ttl the length of time in seconds the token will live. Default is config.session_ttl
   * @return mixed the generated token object or false if the generation failed
   */
  private static function generate_token($user, $ttl=null, $length=255, $flash=false) {
    if ($ttl === null) $ttl = Atomic::$config['auth']['session_ttl'];
    if (!$user || !$user->id) return false;
    if ($ttl <= 0) return false;
    $flash = $flash ? '1' : '0';

    $token = \R::dispense('authtoken');
    $token->user = $user;
    $token->created_at = db_date();
    $token->expires_at = db_date(time()+$ttl);
    $token->token = CryptoAPI::generate_token($length);
    $token->is_flash = $flash;
    if (store($token)) {
      return $token;
    } else {
      return false;
    }
  }

    /**
     * Attempts to log in with the token
     * @param $token_string
     * @return bool true if successful otherwise false
     * @internal param string $token the token to login with
     */
  public static function login($token_string) {
    $user_id = self::validate_token($token_string);
    if ($user_id) {
      return Auth::authorize($user_id);
    } else {
      return false;
    }
  }

    /**
     * Checks if a token is valid
     * A valid token should exist, not be expired, and the user account active.
     * @param $token_string
     * @param boolean $only_active_users if set to true a user must be active for a token to validate.
     * @return mixed the user id if the token is valid otherwise false
     * @internal param string $token the token to validate
     */
  public static function validate_token($token_string, $only_active_users=true) {
    $sql_enabled = '';
    if ($only_active_users) $sql_enabled = '  AND`u`.`is_enabled`=\'1\'';
    $sql_info = <<<SQL
SELECT
  `u`.`id` AS `user_id`,
  `t`.`is_flash` AS `is_flash`,
  `t`.`id` AS `token_id`
FROM `authtoken` AS `t`
LEFT JOIN `user` AS `u` ON `u`.`id`=`t`.`user_id`
WHERE
  `t`.`token`=?
  AND `t`.`expires_at` > NOW()
$sql_enabled
LIMIT 1
SQL;
    $info = \R::getRow($sql_info, array($token_string));
    if ($info['is_flash'] == '1') {
      // delete the flash token
      $sql_delete = <<<SQL
DELETE FROM `authtoken`
WHERE `id`=?
SQL;
      \R::exec($sql_delete, array($info['token_id']));
    }
    if (!$info['user_id']) {
      return false;
    } else {
      return $info['user_id'];
    }
  }

  /**
   * Removes a token from the database thus making it invalid
   * @param string $token the token to trash
   */
  public static function trash_token($token_string) {
    $sql_trash = <<<SQL
DELETE FROM
  `authtoken`
WHERE
  `token`=?
SQL;
    \R::exec($sql_trash, array($token_string));
  }
}