<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_block_calculator_uninstall() {
    global $DB;

    $DB->delete_records('calculator_history');
    
    return true;
}
