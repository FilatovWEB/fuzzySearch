<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/*
 * tested on PHP Version 7.4.0RC2
 */

function correctWord($term)
{
    // получаем словарь
    include_once 'fuzzy.php';
    $voc = [];
    $fh = fopen("pldf-win.txt", "r");
    while(!feof($fh)) {
        $voc[] = fgets($fh);
    }
    fclose($fh);

    $fuzzy = new Class_Fuzzy_String_Search($voc);
    return $fuzzy->correctWord($term);
}

function correctKeyboardLayout($term)
{
    include_once("langCorrect/ReflectionTypeHint.php");
    include_once("langCorrect/LangCorrect.php");
    include_once("langCorrect/UTF8.php");

    $corrector = new Text_LangCorrect();
    return $corrector->parse($term, $corrector::KEYBOARD_LAYOUT);
}

var_dump(correctWord(['мошина', 'карова', 'варона']));
echo correctKeyboardLayout("PHP - zpsr ghjuhfvvbhjdfybz, laravel - ahtqvdjhr");



