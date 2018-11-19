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

$languages = zen_get_languages();
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if (zen_not_null($action)) {
  switch ($action) {
    case 'set_editor':
// Reset will be done by init_html_editor.php. Now we simply redirect to refresh page properly.
      $action = '';
      zen_redirect(zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE));
      break;
    case 'insert':
    case 'update':
    case 'add':
    case 'upd':
      if (isset($_POST['comment_id'])) {
        $comment_id = zen_db_prepare_input($_POST['comment_id']);
      }
      $comment_sort_order = zen_db_prepare_input($_POST['sort_order']);
      $sql_data_array = array(
        'sort_order' => (int)$comment_sort_order);
      if ($action == 'add') {
        $insert_sql_data = array('date_added' => 'now()');

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        zen_db_perform(TABLE_ORDER_COMMENTS, $sql_data_array);

        $comment_id = zen_db_insert_id();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_desc_array = array(
            'comment_id' => zen_db_prepare_input($comment_id),
            'language_id' => (int)$languages[$i]['id'],
            'comment_title' => zen_db_prepare_input($_POST['comment_title'][$language_id]),
            'comment_content' => zen_db_prepare_input($_POST['comment_content'][$language_id])
          );
          zen_db_perform(TABLE_ORDER_COMMENTS_CONTENT, $sql_data_desc_array);
        }
        $messageStack->add_session(SUCCESS_ORDER_COMMENT_INSERTED, 'success');
      } elseif ($action == 'upd') {
        $sql_data_array = array('last_modified' => 'now()','sort_order' => (int)$comment_sort_order);
        zen_db_perform(TABLE_ORDER_COMMENTS, $sql_data_array, 'update', "comment_id = '" . (int)$comment_id . "'");

        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          $sql_data_desc_array = array(
            'comment_title' => zen_db_prepare_input($_POST['comment_title'][$language_id]),
            'comment_content' => zen_db_prepare_input($_POST['comment_content'][$language_id]),
          );
          zen_db_perform(TABLE_ORDER_COMMENTS_CONTENT, $sql_data_desc_array, 'update', "comment_id = " . (int)$comment_id . " and language_id = " . (int)$languages[$i]['id']);
        }
        $messageStack->add_session(SUCCESS_ORDER_COMMENT_UPDATED, 'success');
      }
      zen_redirect(zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'ocid=' . (int)$comment_id . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '')));

      break;
    case 'deleteconfirm':

      $db->Execute("DELETE FROM " . TABLE_ORDER_COMMENTS . "
                    WHERE commment_id = " . (int)$_POST['commment_id']);
      $db->Execute("DELETE FROM " . TABLE_ORDER_COMMENTS_CONTENT . "
                    WHERE commment_id = " . (int)$_POST['commment_id']);

      $messageStack->add_session(SUCCESS_COMMENT_REMOVED, 'success');
      zen_redirect(zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] : '')));
      break;
  }
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <link rel="stylesheet" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script src="includes/menu.js"></script>
    <script src="includes/general.js"></script>
    <script>
      function init() {
          cssjsmenu('navbar');
          if (document.getElementById) {
              var kill = document.getElementById('hoverJS');
              kill.disabled = true;
          }
      }
    </script>
    <?php
    if ($editor_handler != '') {
      include ($editor_handler);
    }
    ?>
  </head>
  <body onLoad="init()">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->
    <div class="container-fluid">
      <!-- body_text //-->
      <h1><?php echo HEADING_TITLE; ?></h1>
      <?php
      if ($action == 'new') {
        $formAction = 'add';

        $parameters = array(
          'sort_order' => ''
        );

        $ocInfo = new objectInfo($parameters);
        if (isset($_GET['ocid'])) {
          $formAction = 'upd';

          $ocID = zen_db_prepare_input($_GET['ocid']);
          $commentInfo = $db->Execute("SELECT sort_order
                                       FROM " . TABLE_ORDER_COMMENTS . "
                                       WHERE comment_id = " . (int)$ocID);
          $ocInfo->updateObjectInfo($commentInfo->fields);

          $commentContents = $db->Execute("SELECT comment_title, comment_content, language_id
                                           FROM " . TABLE_ORDER_COMMENTS_CONTENT . "
                                           WHERE comment_id = " . (int)$ocID);

          $commentArray = array();
          foreach ($commentContents as $commentContent) {
            $commentArray[$commentContent['language_id']] = array(
              'comment_title' => $commentContent['comment_title'],
              'comment_content' => $commentContent['comment_content']);
          }
          $ocInfo->updateObjectInfo($commentArray);
        } elseif (zen_not_null($_POST)) {
          $ocInfo->updateObjectInfo($_POST);
        }
        ?>
        <?php
        echo zen_draw_form('new_comment', FILENAME_ORDER_COMMENT_BOILERPLATE, (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'action=' . $formAction, 'post', 'enctype="multipart/form-data" class="form-horizontal"');
        if ($formAction == 'upd') {
          echo zen_draw_hidden_field('comment_id', $ocID);
        }
        ?>
        <div class="form-group">
            <?php echo zen_draw_label(COMMENT_TITLE, 'comment_title[]', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
              <?php
              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $language_id = $languages[$i]['id'];
                ?>
              <div class="input-group">
                <span class="input-group-addon"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></span>
                <?php echo zen_draw_input_field('comment_title[' . $language_id . ']', htmlspecialchars(stripslashes($ocInfo->{$language_id}['comment_title']), ENT_COMPAT, CHARSET, TRUE), 'class="form-control"'); ?>
              </div><br>
              <?php
            }
            ?>
          </div>
          <span class="help-block"><?php echo COMMENT_TITLE_HELP; ?></span>
        </div>
        <div class="form-group">
            <?php echo zen_draw_label(COMMENT_CONTENT, 'comment_content[]', 'class="control-label col-sm-3"'); ?>
          <div class="col-sm-9 col-md-6">
              <?php
              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                $language_id = $languages[$i]['id'];
                ?>
              <div class="input-group">
                <span class="input-group-addon"><?php echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></span>
                <?php echo zen_draw_textarea_field('comment_content[' . $languages[$i]['id'] . ']', 'soft', '24', '8', htmlspecialchars(stripslashes($ocInfo->{$language_id}['comment_content']), ENT_COMPAT, CHARSET, TRUE), 'class="editorHook form-control"'); ?>
              </div><br>
              <?php
            }
            ?>
          </div>
          <span class="help-block"><?php echo COMMENT_CONTENT_HELP; ?></span>
        </div>
      <div class="form-group">
        <?php echo zen_draw_label(COMMENT_SORT_ORDER, 'sort_order', 'class="control-label col-sm-3"'); ?>
        <div class="col-sm-9 col-md-6">
          <?php echo zen_draw_input_field('sort_order', $ocInfo->sort_order, 'class="form-control"') ; ?>
        </div>
      </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary"><?php echo (($formAction == 'add') ? IMAGE_INSERT : IMAGE_SAVE); ?></button> <a href="<?php echo zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . (isset($_GET['ocid']) ? 'ocid=' . (int)$_GET['ocid'] : '')); ?>" class="btn btn-default" role="button"><?php echo IMAGE_CANCEL; ?></a>
        </div>
        <?php echo '</form>'; ?>
        <?php
      } else {
        ?>
        <div class="row">
            <?php
// toggle switch for editor
            echo zen_draw_form('set_editor_form', FILENAME_ORDER_COMMENT_BOILERPLATE, '', 'get');
            echo zen_draw_label(TEXT_EDITOR_INFO, 'reset_editor');
            echo zen_draw_pull_down_menu('reset_editor', $editors_pulldown, $current_editor_key, 'onchange="this.form.submit();"');
            echo zen_hide_session_id();
            echo zen_draw_hidden_field('action', 'set_editor');
            echo '</form>';
            ?>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 configurationColumnLeft">
            <table class="table table-hover table-striped">
              <thead>
                <tr>
                  <th class="dataTableHeadingContent"><?php echo COMMENT_ID; ?></th>
                  <th class="dataTableHeadingContent text-center"><?php echo COMMENT_TITLE; ?></th>
                  <th class="dataTableHeadingContent">&nbsp;</th>
                </tr>
              </thead>
              <tbody>
                  <?php
// Split Page
                  $orderCommentQueryRaw = "SELECT oc.comment_id, oc.sort_order, oc.date_added, oc.last_modified, occ.comment_title
                                           FROM " . TABLE_ORDER_COMMENTS . " oc,
                                                " . TABLE_ORDER_COMMENTS_CONTENT . " occ
                                           WHERE oc.comment_id = occ.comment_id
                                           AND occ.language_id = " . (int)$_SESSION['languages_id'] . "
                                           ORDER BY oc.sort_order, oc.comment_id";

                  // reset page when page is unknown
                  if (($_GET['page'] == '' || $_GET['page'] == '1') && $_GET['ocid'] != '') {
                    $check_page = $db->Execute($orderCommentQueryRaw);
                    $check_count = 1;
                    if ($check_page->RecordCount() > MAX_DISPLAY_SEARCH_RESULTS) {
                      foreach ($check_page as $item) {
                        if ($item['comment_id'] == $_GET['ocid']) {
                          break;
                        }
                        $check_count++;
                      }
                      $_GET['page'] = round((($check_count / MAX_DISPLAY_SEARCH_RESULTS) + (fmod_round($check_count, MAX_DISPLAY_SEARCH_RESULTS) != 0 ? .5 : 0)), 0);
                    } else {
                      $_GET['page'] = 1;
                    }
                  }

                  $oc_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orderCommentQueryRaw, $oc_query_numrows);
                  $oc_list = $db->Execute($orderCommentQueryRaw);
                  foreach ($oc_list as $oc_item) {
                    if ((!isset($_GET['ocid']) || (isset($_GET['ocid']) && ((int)$_GET['ocid'] == $oc_item['comment_id']))) && !isset($ocInfo) && (substr($action, 0, 3) != 'new')) {
                      $ocInfo = new objectInfo($oc_item);
                    }
                    if (isset($ocInfo) && is_object($ocInfo) && ($oc_item['comment_id'] == $ocInfo->comment_id)) {
                      ?>
                    <tr class="dataTableRowSelected" onclick="document.location.href = '<?php echo zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'page=' . $_GET['page'] . '&ocid=' . $ocInfo->comment_id . '&action=new'); ?>'">
                        <?php
                      } else {
                        ?>
                    <tr class="dataTableRow" onclick="document.location.href = '<?php echo zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'page=' . $_GET['page'] . '&ocid=' . $oc_item['comment_id']); ?>'">
                        <?php
                      }
                      ?>
                    <td class="dataTableContent"><?php echo $oc_item['comment_id']; ?></td>
                    <td class="dataTableContent text-center"><?php echo $oc_item['comment_title']; ?></td>
                    <td class="dataTableContent text-right">
                        <?php
                        if (isset($ocInfo) && is_object($ocInfo) && ($oc_item['comment_id'] == $ocInfo->comment_id)) {
                          echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif');
                        } else {
                          ?>
                        <a href="<?php echo zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'page=' . $_GET['page'] . '&ocid=' . $oc_item['comment_id']); ?>"><?php echo zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?></a>
                        <?php
                      }
                      ?>
                    </td>
                  </tr>
                  <?php
                }
                ?>
              </tbody>
            </table>
          </div>
          <?php
          $heading = array();
          $contents = array();

          switch ($action) {
            case 'delete' : // deprecated
            case 'del' :
              $heading[] = array('text' => '<h4>[' . $ocInfo->comment_id . ']  ' . $ocInfo->comment_title . '</h4>');

              $contents = array('form' => zen_draw_form('comments', FILENAME_ORDER_COMMENT_BOILERPLATE, 'page=' . $_GET['page'] . '&action=deleteconfirm') . zen_draw_hidden_field('commment_id', $ocInfo->comment_id));
              $contents[] = array('text' => TEXT_CONFIRM_DELETE);
              $contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-danger">' . IMAGE_DELETE . '</button> <a href="' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] . '&' : '') . (isset($_GET['ocid']) ? 'ocid=' . (int)$_GET['ocid'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
              break;
            default:
              if (is_object($ocInfo)) {
                $heading[] = array('text' => '<h4>[' . $ocInfo->comment_id . ']  ' . $ocInfo->comment_title . '</h4>');

                $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . '&ocid=' . $ocInfo->comment_id . '&action=new') . '" class="btn btn-primary">' . IMAGE_EDIT . '</a> <a href="' . zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'ocid=' . $ocInfo->comment_id . (isset($_GET['page']) ? '&page=' . (int)$_GET['page'] : '') . '&action=del') . '" class="btn btn-warning" role="button">' . IMAGE_DELETE . '</a>');
                $commentContent = $db->Execute("SELECT comment_content
                                                FROM " . TABLE_ORDER_COMMENTS_CONTENT . "
                                                WHERE comment_id = " . (int)$ocInfo->comment_id . "
                                                AND language_id = " . (int)$_SESSION['languages_id']);

                $contents[] = array('text' => COMMENT_CONTENT . '&nbsp;::&nbsp; ' . $commentContent->fields['comment_content']);
                $contents[] = array('text' => DATE_CREATED . '&nbsp;::&nbsp; ' . zen_date_short($ocInfo->date_added));
                $contents[] = array('text' => DATE_MODIFIED . '&nbsp;::&nbsp; ' . zen_date_short($ocInfo->last_modified));
              }
              break;
          }
          ?>
          <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 configurationColumnRight">
              <?php
              if ((zen_not_null($heading)) && (zen_not_null($contents))) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              ?>
          </div>
        </div>

        <div class="row">
          <table class="table">
            <tr>
              <td><?php echo $oc_split->display_count($oc_query_numrows, $maxDisplaySearchResults, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUPONS); ?></td>
              <td class="text-right"><?php echo $oc_split->display_links($oc_query_numrows, $maxDisplaySearchResults, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], (isset($_GET['status']) ? '&status=' . $_GET['status'] : '')); ?></td>
            </tr>

            <tr>
              <td class="text-right" colspan="2"><a name="commentInsert" href="<?php echo zen_href_link(FILENAME_ORDER_COMMENT_BOILERPLATE, 'page=' . $_GET['page'] . '&action=new'); ?>" class="btn btn-primary"><?php echo IMAGE_INSERT; ?></a></td>
            </tr>
          </table>
        </div>
        <?php
      }
      ?>
      <!-- body_text_eof //-->
      <!-- body_eof //-->

    </div>
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
