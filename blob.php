<?php
$dbh = new PDO("mysql:host=localhost;dbname=registration", "root", "");
if(isset($_POST['btn'])){
  $name = $_FILES['myfile']['name'];
  $type = $_FILES['myfile']['type'];
  $data = file_get_contents($_FILES['myfile']['tmp_name']);
  $stnt = $dbh->prepare("insert into myblob values('', ?,?,?)");
  $stnt->bindParan(1,$name);
  $stnt->bindParan(2,$type);
  $stnt->bindParan(3,$data);
  $stnt->bindParan(1,$name);
  $stnt->execute();
}
?>