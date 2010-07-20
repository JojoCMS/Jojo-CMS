<?php

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$action = $_POST['name'];
$id = $_POST['value'];

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
}
$comment = Jojo_Plugin_Jojo_comment::getItemsById($id);
$html = Jojo_Plugin_Jojo_comment::getItemHtml($comment);
$res = json_encode($html);
echo $res;

exit;
