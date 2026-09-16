<?php
$xml = file_get_contents('PENGERJAAN/extracted/word/document.xml');
$text = strip_tags(str_replace(['<w:p', '</w:p>'], ["\n<w:p", "\n</w:p>"], $xml));
file_put_contents('PENGERJAAN/extracted/text.txt', trim($text));
