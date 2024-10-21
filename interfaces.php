<?php
declare(strict_types=1);

interface data_handler {}

interface solver {
    static function calculate(data_handler $datahandler): array;
}

interface storage {
    static function write_db(data_handler $datahandler);
    static function read_db(): ?array;
}

interface form_handler {
    static function process_request(array $request, data_handler $datahandler);
    static function get_main_form(data_handler $datahandler): string;
    static function get_process_form(data_handler $datahandler): string;
    static function get_history_form(?array $history, data_handler $datahandler): ?string;
}
