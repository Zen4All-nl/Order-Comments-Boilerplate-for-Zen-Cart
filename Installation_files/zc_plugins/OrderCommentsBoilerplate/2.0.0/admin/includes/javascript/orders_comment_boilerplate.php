<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$predefinedCommentsQuery = "SELECT comment_id, comment_title, comment_content
                                      FROM " . TABLE_ORDER_COMMENTS_CONTENT . "
                                      WHERE language_id = " . (int)$_SESSION['languages_id'] . "
                                      ORDER BY comment_id ASC";
$predefinedComments = $db->Execute($predefinedCommentsQuery);
$predefinedCommentsArray = [];
$predefinedCommentsArray[0] = [
  'id' => NULL,
  'text' => TEXT_SELECT_COMMENT
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

    $('form[name="statusUpdate"]').prepend(commentHtml);

    $('#predefined_comments').change(function () {
      var val = $(":selected", this).index();
      $('textarea[name="comments"]').val(commentsArray[val].comment);
    });
  });
</script>