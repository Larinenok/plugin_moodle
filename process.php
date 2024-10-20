<?php
declare(strict_types=1);

require_once('../../config.php');
require_once(__DIR__ . '/quadratic_equation_solver.php');
require_once(__DIR__ . '/calculator_modules.php');

global $DB, $OUTPUT, $PAGE;

$PAGE->set_url(new moodle_url('/blocks/calculator/process.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Result of Calculations');

$request = $_REQUEST;
$calculatormodule = optional_param('calculatormodule', 'quadraticequationsolver', PARAM_TEXT);
$calculatormodules = get_calculator_modules();

$calculatormodules[$calculatormodule]->process_request($request);
$calculatormodules[$calculatormodule]->calculate();
$calculatormodules[$calculatormodule]->write_db();

$data = [
    'calculatorcontent' => $calculatormodules[$calculatormodule]->get_process_form(),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_calculator/process', $data);
echo $OUTPUT->footer();
