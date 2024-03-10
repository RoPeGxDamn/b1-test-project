<?php
require_once 'dbconfig.php';

if (isset($_POST['importSubmit'])) {
    // Допустимые форматы файлов
    $csvMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );

    // Проверка на пустоту и формат файлов
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            // Открытие потока чтения файла
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

            // Пропуск первой строки
            fgetcsv($csvFile);
            // Чтение файла построчно
            while (($line = fgetcsv($csvFile, 1000, ";")) !== false) {
                $prevQuery = null;
                $queryInsert = null;
                if ($_POST['table'] == 'users') {
                    if (count($line) != 11) break;
                    $xml_id = $line[0];
                    $last_name = $line[1];
                    $name = $line[2];
                    $second_name = $line[3];
                    $department = $line[4];
                    $work_position = $line[5];
                    $email = $line[6];
                    $mobile_phone = $line[7];
                    $phone = $line[8];
                    $login = $line[9];
                    $password = $line[10];
                    $prevQuery = "SELECT XML_ID FROM users WHERE EMAIL = '" . $email . "'";
                    $queryInsert = "INSERT INTO users (
                        XML_ID,
                        LAST_NAME, 
                        NAME, 
                        SECOND_NAME, 
                        DEPARTMENT, 
                        WORK_POSITION, 
                        EMAIL, 
                        MOBILE_PHONE, 
                        PHONE, 
                        LOGIN, 
                        PASSWORD) 
                        VALUES ('" . $xml_id . "', 
                                '" . $last_name . "',
                                '" . $name . "',
                                '" . $second_name . "',
                                '" . $department . "',
                                '" . $work_position . "', 
                                '" . $email . "',
                                '" . $mobile_phone . "',
                                '" . $phone . "',
                                '" . $login . "',
                                '" . $password . "');";
                } else {
                    if (count($line) != 3) break;
                    $xml_id = $line[0];
                    $parent_xml_id = $line[1];
                    $name_department = $line[2];
                    $prevQuery = "SELECT XML_ID FROM departments WHERE XML_ID = '" . $xml_id . "'";
                    $queryInsert = "INSERT INTO departments (
                        XML_ID,
                        PARENT_XML_ID, 
                        NAME_DEPARTMENT) VALUES ('" . $xml_id . "', '" . $parent_xml_id . "', '" . $name_department . "')";
                }
                // Обращение к БД
                $prevResult = $db->query($prevQuery);
                if ($prevResult->num_rows != 1) {
                    $db->query($queryInsert);
                }
            }
        }
        // Конец потока чтения файла
        fclose($csvFile);
        // Установка поискового параметра
        $qstring = '?status=succ';
        // Запись в текстовый документ имена загруженных файлов без перезаписи
        file_put_contents('fileslist.txt', $_FILES['file']['name'] . PHP_EOL, FILE_APPEND);
    } else {
        $qstring = '?status=err';
    }
} else {
    $qstring = '?status=invalid_file';
}

// Переход к документу по дефолту
header("Location: index.php" . $qstring);
