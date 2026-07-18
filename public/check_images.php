<?php
foreach (glob('images/*') as $f) {
    $size = @getimagesize($f);
    if ($size) {
        echo basename($f) . ': ' . $size[0] . 'x' . $size[1] . ' (' . $size['mime'] . ")\n";
    } else {
        echo basename($f) . ": not an image\n";
    }
}
