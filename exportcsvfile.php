<?php
require_once 'dbconfig.php';

// Будущее название экспортируемого файла
$filename =  $_POST['table'] . "_" . date('Y-m-d') . ".csv";
$delimiter = ";";
// Получение столбцов таблицы
$fields = array_keys($db->query("SELECT * FROM " . $_POST['table'])->fetch_assoc());
// Открытие потока чтения
$f = fopen('php://memory', 'w');

// Запись первой строки столбцов
fputcsv($f, $fields, $delimiter);


$result = $db->query("SELECT * FROM " . $_POST['table']);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lineData = array_values($row);
        fputcsv($f, $lineData, $delimiter);
    }
}

// Индикатор позиции чтения файла
fseek($f, 0);
// Установка headers для скачивания файла
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '";');
// Записывает результат в буфер вывода
fpassthru($f);
exit();
