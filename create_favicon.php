<?php
$img = file_get_contents('public/images/TERNEDOR.png');
$base64 = base64_encode($img);
$svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 618 618" width="512" height="512">
  <image xlink:href="data:image/png;base64,' . $base64 . '" x="0" y="0" width="618" height="618"/>
</svg>';
file_put_contents('public/favicon.svg', $svg);
echo 'Favicon SVG creado con éxito';
?>
