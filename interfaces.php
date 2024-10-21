<?php
declare(strict_types=1);

interface solver {
    function calculate(): array;
}

interface storage {
    function write_db();
    function read_db(): ?array;
}

interface form_handler {
    function process_request(array $request);
    function get_main_form(): string;
    function get_process_form(): string;
    function get_history_form(?array $history): string;
}

interface calculator_module extends solver, storage, form_handler {}
