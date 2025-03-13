<?php
/*
 * @package Order Comment Boilerplate
 * @copyright Copyright 2008-2016 Zen4All
 * @copyright Portions 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: order_comment_boilerplate.php 2.0 2025-03-13
 */
$predefinedCommentsQuery = "SELECT comment_id, comment_title, comment_content
                            FROM " . TABLE_ORDER_COMMENTS_CONTENT . "
                            WHERE language_id = " . (int) $_SESSION['languages_id'] . "
                            ORDER BY comment_id ASC";
$predefinedComments = $db->Execute($predefinedCommentsQuery);
$predefinedCommentsArray = [];
$predefinedCommentsArray[0] = [
    'id' => NULL,
    'text' => TEXT_SELECT_COMMENT,
    'content' => NULL
];
foreach ($predefinedComments as $predefinedComment) {
    $predefinedCommentsArray[] = [
        'id' => $predefinedComment['comment_id'],
        'text' => $predefinedComment['comment_title'],
        'content' => addslashes($predefinedComment['comment_content'])
    ];
}
?>
<script>
    let commentHtml = '';
    const commentsArray = new Array(
<?php
$i = 0;
$len = count($predefinedCommentsArray);
foreach ($predefinedCommentsArray as $value) {
    if ($i == $len - 1) {
        echo "{value : '" . $value['id'] . "', comment : `" . $value['content'] . "`}";
    } else {
        echo "{value : '" . $value['id'] . "', comment : `" . $value['content'] . "`},";
    }
    $i++;
}
?>
    );
    $(document).ready(function () {

        commentHtml += '<div class="form-group">\n';
        commentHtml += '  <?php echo zen_draw_label(ENTRY_PREDEFINED_COMMENTS, 'predefined_comments', 'class="col-sm-3 control-label"'); ?>\n';
        commentHtml += '  <div class="col-sm-9">\n';
        commentHtml += '    <select name="predefined_comments" id="predefined_comments" class="form-control" readonly>\n';
<?php
$j = 0;
foreach ($predefinedCommentsArray as $value) {
    echo 'commentHtml += \'      <option value="' . $value['id'] . '" ' . ($j == 0 ? ' selected' : '') . '>' . $value['text'] . '</option>\';';
    $j++;
}
?>
        commentHtml += '    </select>\n'
        commentHtml += '  </div>\n';
        commentHtml += '</div>\n';

        $('form[name="statusUpdateForm"]').prepend(commentHtml);

        $('#predefined_comments').change(function () {
            var val = $(":selected", this).index();
            $('textarea[name="comments"]').val(commentsArray[val].comment);
        });
    });
</script>