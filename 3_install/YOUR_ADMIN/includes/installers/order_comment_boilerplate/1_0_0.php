<?php
// use $configuration_group_id where needed

// For Admin Pages

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0
    $admin_page = 'editOrderCommentBoilerplate';
  // delete configuration menu
  $db->Execute("DELETE FROM ".TABLE_ADMIN_PAGES." WHERE page_key = '".$admin_page."' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page,
                              'BOX_TOOLS_ORDER_COMMENT_BOILERPLATE', 
                              'FILENAME_ORDER_COMMENT_BOILERPLATE',
                              '', 
                              'tools', 
                              'Y',
                              $configuration_group_id);
        
      $messageStack->add('Enabled MODULE Configuration Menu.', 'success');
    }
  }
}

/*
 * If your checking for a field
 */

   $db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_ORDER_COMMENTS . " (
                   comment_id INT(11) NOT NULL AUTO_INCREMENT,
                   sort_order INT(3) DEFAULT NULL,
                   last_modified DATETIME NULL DEFAULT NULL,
                   date_added DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00',
                   PRIMARY KEY (comment_id),
                   KEY idx_sort_order_zen (sort_order)
                 ) ENGINE = MyISAM;");

   $db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_ORDER_COMMENTS_CONTENT . " (
                   comment_id INT(11) NOT NULL DEFAULT '0',
                   language_id INT(11) NOT NULL DEFAULT '1',
                   comment_title VARCHAR(64) NOT NULL,
                   comment_content TEXT NOT NULL,
                   PRIMARY KEY  (comment_id,language_id),
                   KEY idx_comment_title_zen (comment_title)
                 ) ENGINE = MyISAM;");
