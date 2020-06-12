<?php

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase {

  protected function executeInstall()
  {
    global $db, $sniffer;
    $db->Execute("DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = 'Order Comment Boilerplate'");
    $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'ORDER_COMMENT_BOILERPLATE_VERSION'");

    zen_deregister_admin_pages(['editOrderCommentBoilerplate']);
    zen_register_admin_page('editOrderCommentBoilerplate', 'BOX_TOOLS_ORDER_COMMENT_BOILERPLATE', 'FILENAME_ORDER_COMMENT_BOILERPLATE', '', 'tools', 'Y', 50);

    $db->Execute("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "order_comments (
                    comment_id INT(11) NOT NULL AUTO_INCREMENT,
                    sort_order INT(3) NOT NULL DEFAULT '0',
                    last_modified DATETIME NULL DEFAULT NULL,
                    date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (comment_id),
                    KEY idx_sort_order_zen (sort_order)
                  ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";");

    $db->Execute("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "order_comments_content (
                    comment_id INT(11) NOT NULL DEFAULT '0',
                    language_id INT(11) NOT NULL DEFAULT '1',
                    comment_title VARCHAR(64) NOT NULL,
                    comment_content TEXT NOT NULL,
                    PRIMARY KEY  (comment_id,language_id),
                    KEY idx_comment_title_zen (comment_title)
                  ) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";");
  }

  protected function executeUninstall()
  {
    global $db;
    zen_deregister_admin_pages(['editOrderCommentBoilerplate']);
    $db->Execute("DROP TABLE " . DB_PREFIX . "order_comments", DB_PREFIX . "order_comments_content");
  }

}
