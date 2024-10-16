<?php

// Убедитесь, что файл подключен через Moodle API.
defined('MOODLE_INTERNAL') || die();

function xmldb_block_calculator_uninstall() {
    global $DB;

    // Удалить все данные из таблиц, связанных с плагином.
    $DB->delete_records('calculator_history');
    
    return true;
}
