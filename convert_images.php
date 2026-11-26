<?php
 = public/front/img/bnnr-bg.png;
 = public/front/img/bnnr-bg.webp;
if (file_exists()) {
     = imagecreatefrompng();
    imagepalettetotruecolor();
    imagealphablending(, true);
    imagesavealpha(, true);
    imagewebp(, , 82);
    imagedestroy();
    echo bnnr-bg.webp
