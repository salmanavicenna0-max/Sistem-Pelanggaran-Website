<?php
$zip = new ZipArchive;
if ($zip->open('PENGERJAAN/Pelanggaran Siswa SMAN 6 BANDUNG.docx') === TRUE) {
    echo strip_tags($zip->getFromName('word/document.xml'));
    $zip->close();
}
