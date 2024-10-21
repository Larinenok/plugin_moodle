<?php

require_once(__DIR__ . '/interfaces.php');

class calculator_wrapper {
    private data_handler $datahandler;
    private solver $solver;
    private storage $storage;
    private form_handler $formhandler;

    public function __construct(
        data_handler $datahandler,
        solver $solver,
        storage $storage,
        form_handler $formhandler,
    ) {
        $this->datahandler = $datahandler;
        $this->solver = $solver;
        $this->storage = $storage;
        $this->formhandler = $formhandler;
    }

    public function get_main_form(): string {
        return $this->formhandler::get_main_form($this->datahandler);
    }

    public function get_process_form(array $request): string {
        $this->formhandler::process_request($request, $this->datahandler);
        $this->solver::calculate($this->datahandler);
        $this->storage::write_db($this->datahandler);

        return $this->formhandler::get_process_form($this->datahandler);
    }

    public function get_history_form(): ?string {
        $history = $this->storage::read_db();

        return $this->formhandler::get_history_form($history, $this->datahandler);
    }
}
