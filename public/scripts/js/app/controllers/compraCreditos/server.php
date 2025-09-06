<?php

$file = $_FILES["path"]["name"];
if (!is_dir("files/"))
    mkdir("files/", 0777);
if ($file && move_uploaded_file($_FILES["path"]["tmp_name"], "files/" . $file)) {
    echo $file;
}