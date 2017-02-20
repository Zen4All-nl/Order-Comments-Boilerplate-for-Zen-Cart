<?php
/*
 * @package Order Comment Boilerplate
 * @copyright Copyright 2008-2016 Zen4All
 * @copyright Portions 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: order_comment_boilerplate.php 1.0 2016-04-28
 */

require('includes/application_top.php');

switch ($_GET['action']) {
  case 'set_editor':
    // Reset will be done by init_html_editor.php. Now we simply redirect to refresh page properly.
    $action = '';
    zen_redirect(zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE));
    break;
  /*case 'confirmdelete':

    $db->Execute("update " . TABLE_COUPONS . "
                    set coupon_active = 'N'
                    where coupon_id='" . $_GET['cid'] . "'");
    $messageStack->add_session(SUCCESS_COUPON_DISABLED, 'success');
    zen_redirect(zen_href_link(FILENAME_COUPON_ADMIN));
    break;*/
  case 'update':
    $update_errors = 0;
    // get all HTTP_POST_VARS and validate
    $languages = zen_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $language_id = $languages[$i]['id'];
      $_POST['comment_title'][$language_id] = trim($_POST['comment_title'][$language_id]);
      if (!$_POST['comment_title'][$language_id]) {
        $update_errors = 1;
        $messageStack->add(ERROR_NO_COMMENT_TITLE . $languages[$i]['name'], 'error');
      }
      $_POST['comment_content'][$language_id] = trim($_POST['comment_content'][$language_id]);
    }
    if ($update_errors != 0) {
      $_GET['action'] = 'new';
    } else {
      $_GET['action'] = 'update_preview';
    }
    break;
  case 'update_confirm':
    if (($_POST['back_x']) || ($_POST['back_y'])) {
      $_GET['action'] = 'new';
    } else {
      $sql_data_array = array('last_modified' => 'now()');
      $languages = zen_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_marray[$i] = array('comment_title' => zen_db_prepare_input($_POST['comment_title'][$language_id]),
          'comment_content' => zen_db_prepare_input($_POST['comment_content'][$language_id])
        );
      }
      if ($_GET['oldaction'] == 'commentedit') {
        zen_db_perform(TABLE_ORDER_COMMENTS, $sql_data_array, 'update', "comment_id='" . $_GET['ocid'] . "'");
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_desc_array = array('comment_title' => zen_db_prepare_input($_POST['comment_title'][$language_id]),
            'comment_content' => zen_db_prepare_input($_POST['comment_content'][$language_id])
          );
          zen_db_perform(TABLE_ORDER_COMMENTS_CONTENT, $sql_data_desc_array, 'update', "comment_id = '" . $_GET['ocid'] . "' and language_id = '" . (int)$languages[$i]['id'] . "'");
        }
      } else {
        zen_db_perform(TABLE_ORDER_COMMENTS, $sql_data_array);
        $insert_id = $db->Insert_ID();
        $ocid = $insert_id;
        $_GET['ocid'] = $ocid;

        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_marray[$i]['comment_id'] = (int)$insert_id;
          $sql_data_marray[$i]['language_id'] = (int)$language_id;
          zen_db_perform(TABLE_ORDER_COMMENTS_CONTENT, $sql_data_marray[$i]);
        }
      }
    }
    zen_redirect(zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'ocid=' . $_GET['ocid'] . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script type="text/javascript" language="javascript" src="includes/menu.js"></script>
    <script type="text/javascript" language="javascript" src="includes/general.js"></script>
    <script type="text/javascript">
      <!--
      function init()
      {
        cssjsmenu('navbar');
        if (document.getElementById)
        {
          var kill = document.getElementById('hoverJS');
          kill.disabled = true;
        }
      }
      // -->
    </script>
    <?php if ($editor_handler != '') include ($editor_handler); ?>
  </head>
  <body onLoad="init()">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->
    <table style="width: 100%;" cellspacing="2" cellpadding="2">
      <tr>
        <!-- body_text //-->
        <?php
        switch ($_GET['action']) {
          case 'update_preview':
            ?>
            <td width="100%" valign="top">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                  <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                      <tr>
                        <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                        <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>
                    <?php echo zen_draw_form('comment', FILENAME_ORDER_COMMENT_BOILERPLATE, 'action=update_confirm&oldaction=' . $_GET['oldaction'] . '&ocid=' . $_GET['ocid'] . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?>
                    <table border="0" width="100%" cellspacing="0" cellpadding="6">
                      <?php
                      $languages = zen_get_languages();
                      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        $language_id = $languages[$i]['id'];
                        ?>
                        <tr>
                          <td align="left"><?php echo COMMENT_TITLE; ?></td>
                          <td align="left"><?php echo zen_db_prepare_input($_POST['comment_title'][$language_id]); ?></td>
                        </tr>
                        <?php
                      }
                      ?>
                      <?php
                      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        $language_id = $languages[$i]['id'];
                        ?>
                        <tr>
                          <td align="left"><?php echo COMMENT_CONTENT; ?></td>
                          <td align="left"><?php echo zen_db_prepare_input($_POST['comment_content'][$language_id]); ?></td>
                        </tr>
                        <?php
                      }
                      ?>
                      <?php
                      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                        $language_id = $languages[$i]['id'];
                        echo zen_draw_hidden_field('comment_title[' . $languages[$i]['id'] . ']', stripslashes($_POST['comment_title'][$language_id]));
                        echo zen_draw_hidden_field('comment_content[' . $languages[$i]['id'] . ']', stripslashes($_POST['comment_content'][$language_id]));
                      }
                      ?>
                      <tr>
                        <td align="left"><?php echo zen_image_submit('button_confirm.gif', COMMENT_BUTTON_CONFIRM, (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?></td>
                        <td align="left"><?php echo zen_image_submit('button_cancel.gif', COMMENT_BUTTON_CANCEL, 'name=back'); ?></td>
                      </tr>
                  </td>
              </table>
              </form>
            </td>
          </tr>
        </table>
      </td>
      <?php
      break;
    case 'commentedit':
      $languages = zen_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $comment = $db->Execute("SELECT comment_title, comment_content
                              from " . TABLE_ORDER_COMMENTS_CONTENT . "
                              where comment_id = '" . $_GET['ocid'] . "'
                              and language_id = '" . (int)$language_id . "'");

        $_POST['comment_title'][$language_id] = $comment->fields['comment_title'];
        $_POST['comment_content'][$language_id] = $comment->fields['comment_content'];
      }

    case 'new':
      ?>
    <td style="width:100%;" valign="top">
      <table style="width:100%;" cellspacing="0" cellpadding="2">
        <tr>
          <td>
            <table style="width:100%;" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <?php
            echo zen_draw_form('comment', FILENAME_ORDER_COMMENT_BOILERPLATE, 'action=update&oldaction=' . $_GET['action'] . '&ocid=' . $_GET['ocid'] . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''));
            ?>
            <table style="width:100%;" cellspacing="0" cellpadding="6">
              <?php
              $languages = zen_get_languages();
              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $language_id = $languages[$i]['id'];
                ?>
                <tr>
                  <td align="left" class="main"><?php if ($i == 0) { echo COMMENT_TITLE;} ?></td>
                  <td align="left"><?php echo zen_draw_input_field('comment_title[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($_POST['comment_title'][$language_id]), ENT_COMPAT, CHARSET, TRUE)) . '&nbsp;' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></td>
                  <td align="left" class="main" width="40%"><?php if ($i == 0) { echo COMMENT_TITLE_HELP;} ?></td>
                </tr>
                <?php
              }
              ?>
              <?php
              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $language_id = $languages[$i]['id'];
                ?>

                <tr>
                  <td align="left" valign="top" class="main"><?php if ($i == 0) { echo COMMENT_CONTENT;} ?></td>
                  <td align="left" valign="top"><?php echo zen_draw_textarea_field('comment_content[' . $languages[$i]['id'] . ']', 'physical', '24', '8', htmlspecialchars(stripslashes($_POST['comment_content'][$language_id]), ENT_COMPAT, CHARSET, TRUE), 'class="editorHook"'); ?>
                    <?php echo '&nbsp;' . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></td>
              <td align="left" valign="top" class="main"><?php if ($i == 0) { echo COMMENT_CONTENT_HELP;} ?></td>
                </tr>
                <?php
              }
              ?>
              <tr>
                <td align="left"><?php echo zen_image_submit('button_preview.gif', COUPON_BUTTON_PREVIEW); ?></td>
                <td align="left">&nbsp;&nbsp;<a href="<?php echo zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'ocid=' . $_GET['cid'] . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')); ?>"><?php echo zen_image_button('button_cancel.gif', IMAGE_CANCEL); ?></a>
                </td>
              </tr>
          </td>
      </table>
    </form>
    </td>
    </tr>
    </table>
    </td>
    <?php
    break;
  default:
    ?>
    <td width="100%" valign="top">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
          <td class="main">
            <?php
// toggle switch for editor
            echo TEXT_EDITOR_INFO . zen_draw_form('set_editor_form', FILENAME_ORDER_COMMENT_BOILERPLATE, '', 'get') . '&nbsp;&nbsp;' . zen_draw_pull_down_menu('reset_editor', $editors_pulldown, $current_editor_key, 'onChange="this.form.submit();"') .
            zen_hide_session_id() .
            zen_draw_hidden_field('action', 'set_editor') .
            '</form>';
            ?>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top">
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent"><?php echo COMMENT_ID; ?></td>
                      <td class="dataTableHeadingContent" align="center"><?php echo COMMENT_TITLE; ?></td>
                      <td class="dataTableHeadingContent">&nbsp;</td>
                    </tr>
                    <?php
                    $orderCommentQueryRaw = "SELECT oc.comment_id, oc.sort_order, oc.date_added, oc.last_modified, occ.comment_title
                               FROM " . TABLE_ORDER_COMMENTS . " oc, " . TABLE_ORDER_COMMENTS_CONTENT . " occ 
                               WHERE oc.comment_id = occ.comment_id
                               AND occ.language_id = '" . (int)$_SESSION['languages_id'] . "'
                               ORDER BY oc.sort_order ASC";
                    $maxDisplaySearchResults = (defined('MAX_DISPLAY_SEARCH_RESULTS_DISCOUNT_COUPONS') && (int)MAX_DISPLAY_SEARCH_RESULTS_ORDER_COMMENTS > 0) ? (int)MAX_DISPLAY_SEARCH_RESULTS_ORDER_COMMENTS : 20;
                    // reset page when page is unknown
                    if (($_GET['page'] == '' or $_GET['page'] == '1') and $_GET['ocid'] != '') {
                      $check_page = $db->Execute($orderCommentQueryRaw);
                      $check_count = 1;
                      if ($check_page->RecordCount() > $maxDisplaySearchResults) {
                        while (!$check_page->EOF) {
                          if ($check_page->fields['comment_id'] == $_GET['ocid']) {
                            break;
                          }
                          $check_count++;
                          $check_page->MoveNext();
                        }
                        $_GET['page'] = round((($check_count / $maxDisplaySearchResults) + (fmod_round($check_count, $maxDisplaySearchResults) != 0 ? .5 : 0)), 0);
                      } else {
                        $_GET['page'] = 1;
                      }
                    }

                    $oc_split = new splitPageResults($_GET['page'], $maxDisplaySearchResults, $orderCommentQueryRaw, $oc_query_numrows);
                    $oc_list = $db->Execute($orderCommentQueryRaw);
                    while (!$oc_list->EOF) {
                      if (((!$_GET['ocid']) || (@$_GET['ocid'] == $oc_list->fields['comment_id'])) && (!$ocInfo)) {
                        $ocInfo = new objectInfo($oc_list->fields);
                      }
                      if ((is_object($ocInfo)) && ($oc_list->fields['comment_id'] == $ocInfo->comment_id)) {
                        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, zen_get_all_get_params(array('ocid', 'action')) . 'ocid=' . $ocInfo->comment_id . '&action=commentedit') . '\'">' . "\n";
                      } else {
                        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, zen_get_all_get_params(array('ocid', 'action')) . 'ocid=' . $oc_list->fields['comment_id']) . '\'">' . "\n";
                      }
                      ?>
                      <td class="dataTableContent"><?php echo $oc_list->fields['comment_id']; ?></td>
                      <td class="dataTableContent" align="center"><?php echo $oc_list->fields['comment_title']; ?></td>
                      <td class="dataTableContent" align="right"><?php
                        if ((is_object($ocInfo)) && ($oc_list->fields['comment_id'] == $ocInfo->comment_id)) {
                          echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                        } else {
                          echo '<a href="' . zen_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&ocid=' . $oc_list->fields['comment_id'] . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '')) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                        }
                        ?>&nbsp;</td>
                </tr>
                <?php
                $oc_list->MoveNext();
              }
              ?>
              <tr>
                <td colspan="3">
                  <table style="width: 100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="smallText">&nbsp;<?php echo $oc_split->display_count($oc_query_numrows, $maxDisplaySearchResults, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUPONS); ?>&nbsp;</td>
                      <td align="right" class="smallText">&nbsp;<?php echo $oc_split->display_links($oc_query_numrows, $maxDisplaySearchResults, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], (isset($_GET['status']) ? '&status=' . $_GET['status'] : '')); ?>&nbsp;</td>
                    </tr>

                    <tr>
                      <td align="right" colspan="2" class="smallText"><?php echo '<a name="commentInsert" href="' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'page=' . $_GET['page'] . '&ocid=' . $ocInfo->comment_id . '&action=new') . '">' . zen_image_button('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>

          <?php
          $heading = array();
          $contents = array();

          switch ($_GET['action']) {
            case 'new':
              $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_COUPON . '</b>');
              $contents[] = array('text' => TEXT_NEW_INTRO);
              $contents[] = array('text' => '<br />' . COUPON_NAME . '<br />' . zen_draw_input_field('name'));
              $contents[] = array('text' => '<br />' . COUPON_AMOUNT . '<br />' . zen_draw_input_field('voucher_amount'));
              $contents[] = array('text' => '<br />' . COUPON_CODE . '<br />' . zen_draw_input_field('voucher_code'));
              $contents[] = array('text' => '<br />' . COUPON_USES_COUPON . '<br />' . zen_draw_input_field('voucher_number_of'));
              break;
            default:
              $heading[] = array('text' => '[' . $ocInfo->comment_id . ']  ' . $ocInfo->comment_title);
              if ($_GET['action'] == 'commentdelete') {
                $contents[] = array('text' => TEXT_CONFIRM_DELETE . '</br></br>' .
                  '<a href="' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'action=confirmdelete&ocid=' . $_GET['ocid'] . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'NONSSL') . '">' . zen_image_button('button_confirm.gif', 'Confirm Delete ' . TEXT_DISCOUNT_COUPON) . '</a>' .
                  '<a href="' . zen_href_link(FILENAME_COUPON_ADMIN, 'ocid=' . $cInfo->comment_id . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'NONSSL') . '">' . zen_image_button('button_cancel.gif', 'Cancel') . '</a>'
                );
              } else {
                $commentContent = $db->Execute("select comment_content
                                     from " . TABLE_ORDER_COMMENTS_CONTENT . "
                                     where comment_id = '" . $ocInfo->comment_id . "'
                                     and language_id = '" . (int)$_SESSION['languages_id'] . "'");

                $contents[] = array('text' => COMMENT_CONTENT . '&nbsp;::&nbsp; ' . $commentContent->fields['comment_content'] . '<br />' .
                  DATE_CREATED . '&nbsp;::&nbsp; ' . zen_date_short($ocInfo->date_added) . '<br />' .
                  DATE_MODIFIED . '&nbsp;::&nbsp; ' . zen_date_short($ocInfo->last_modified) . '<br /><br />' .
                  ($ocInfo->comment_id != '' ?
                          '<center><a href="' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'action=commentedit&ocid=' . $ocInfo->comment_id . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'NONSSL') . '">' . zen_image_button('button_edit.gif', BUTTON_EDIT_COMMENT) . '</a>' .
                          '<a href="' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'action=commentdelete&ocid=' . $cInfo->comment_id . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'NONSSL') . '">' . zen_image_button('button_delete.gif', BUTTON_DELETE_COMMEMT) . '</a>' : ' who ' . $ocInfo->comment_id . ' - ' . $_GET['ocid'])
                );
              }
              break;
          }
          ?>
          <td width="25%" valign="top">
            <?php
            $box = new box;
            echo $box->infoBox($heading, $contents);
            echo '            </td>' . "\n";
        }
        ?>
    </tr>
  </table>
</td>
<!-- body_text_eof //-->
</tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
