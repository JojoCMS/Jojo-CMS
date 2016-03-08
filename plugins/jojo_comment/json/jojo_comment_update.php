<?php

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$action = $_POST['name'];
$id = isset($_POST['value']) ? $_POST['value'] : 0;
$content = isset($_POST['content']) ? $_POST['content'] : '';
$comment = Jojo_Plugin_Jojo_comment::getItemsById($id);
$userid = $comment ? $comment['userid'] : 0;

$page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
if (!$page->perms->hasPerm($_USERGROUPS, 'view') && !($action=='update' && $_USERID && $_USERID==$userid)) {
  echo  json_encode("You do not have permission to use this function");
  exit();
}

if ($action=='delete') {
  Jojo::deleteQuery("DELETE FROM {comment} WHERE commentid = ? LIMIT 1", array($id));
  exit;
} elseif ($action=='follow') {
  Jojo::updateQuery("UPDATE {comment} SET nofollow = 0 WHERE commentid = ? LIMIT 1", array($id));
} elseif ($action=='nofollow') {
  Jojo::updateQuery("UPDATE {comment} SET nofollow = 1 WHERE commentid = ? LIMIT 1", array($id));
} elseif ($action=='anchor') {
  Jojo::updateQuery("UPDATE {comment} SET useanchortext='1', nofollow='0' WHERE commentid = ? LIMIT 1", array($id));
} elseif ($action=='noanchor') {
   Jojo::updateQuery("UPDATE {comment} SET useanchortext='0' WHERE commentid = ? LIMIT 1", array($id));
} elseif ($action=='update') {
    $bb = new bbconverter;
    $bb->truncateurl = 30;
    $bb->nofollow = true;
    $bb->setBBCode($content);
    $htmlbody = $bb->convert('bbcode2html');
    Jojo::updateQuery("UPDATE {comment} SET bbbody = ?, body = ? WHERE commentid = ? LIMIT 1", array($content, $htmlbody, $id));
}

$comment = Jojo_Plugin_Jojo_comment::getItemsById($id);
$html = trim(Jojo_Plugin_Jojo_comment::getItemHtml($comment));
$res = json_encode($html);
echo $res;

exit;
