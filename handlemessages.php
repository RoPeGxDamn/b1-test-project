<?php
// Обработка сообщений из поискового параметра ?status=
if (!empty($_GET['status'])) {
    switch ($_GET['status']) {
        case 'succ':
            $statusType = 'alert-success';
            $statusMsg = 'Data imported successfully';
            break;

        case 'err':
            $statusType = 'alert-danger';
            $statusMsg = 'An error occurred. Please try again';
            break;

        case 'invalid_file':
            $statusType = 'alert-danger';
            $statusMsg = 'Please upload a valid CSV file';
            break;

        default:
            $statusType = '';
            $statusMsg = '';
    }
}
