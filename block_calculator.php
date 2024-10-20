<?php
declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/calculator_modules.php');

class block_calculator extends block_base {
    private string $module;
    private array $calculatormodules;

    public function init() {
        $this->title = get_string('pluginname', 'block_calculator');
        $this->module = 'quadraticequationsolver';
        $this->calculatormodules = get_calculator_modules();
    }

    public function get_content() {
        global $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        if (!isset($this->calculatormodules[$this->module])) {
            throw new Exception("Module '{$this->module}' not found.");
        }

        $data = [
            'calculatorcontent' => $this->calculatormodules[$this->module]->get_main_form(),
        ];
        $form = $OUTPUT->render_from_template('block_calculator/block_calculator', $data);

        $this->content = new stdClass;
        $this->content->text = $form;

        return $this->content;
    }
}
