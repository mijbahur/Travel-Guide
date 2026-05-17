<?php
function handleImageUpload($fileInput)
{
    if (empty($_FILES[$fileInput]['name'])) {
        return null;
    }

    $name = $_FILES[$fileInput]['name'];
    $src = $_FILES[$fileInput]['tmp_name'];
    $des = __DIR__ . '/../asset/uploads/posts/' . $name;

    if (move_uploaded_file($src, $des)) {
        return 'asset/uploads/posts/' . $name;
    } else {
        return false;
    }
}
?>