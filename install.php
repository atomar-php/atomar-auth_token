<?php

namespace auth_token;
use atomic\core\Logger;

/**
 * Implements hook_uninstall()
 */
function uninstall() {
    // destroy tables and variables
    $sql = <<<SQL
-- TODO: implement
SQL;
    \R::begin();
    try {
        \R::exec($sql);
        \R::commit();
        return true;
    } catch (\Exception $e) {
        \R::rollback();
        Logger::log_error('Failed to un-install Auth Token', $e->getMessage());
        return false;
    }
}

/**
 * Implements hook_update_version()
 */
function update_2() {
    // create tables
    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `authtoken` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `is_flash` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_foreignkey_authtoken_user` (`user_id`),
  KEY `token` (`token`(191)),
  CONSTRAINT `c_fk_authtoken_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;

    \R::begin();
    try {
        \R::exec($sql);
        \R::commit();
        return true;
    } catch (\Exception $e) {
        \R::rollback();
        Logger::log_error('Failed to install Auth Token', $e->getMessage());
        return false;
    }
}
