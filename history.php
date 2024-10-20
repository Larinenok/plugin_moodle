<?php
declare(strict_types=1);

require_once('../../config.php');
require_once(__DIR__ . '/quadratic_equation_solver.php');
require_once(__DIR__ . '/calculator_modules.php');

global $DB, $OUTPUT, $PAGE;

$PAGE->set_url(new moodle_url('/blocks/calculator/history.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('History of Calculations');

$calculatormodule = optional_param('calculatormodule', 'quadraticequationsolver', PARAM_TEXT);
$calculatormodules = get_calculator_modules();

$history = $calculatormodules[$calculatormodule]->read_db();

$data = [
    'calculatorcontent' => $calculatormodules[$calculatormodule]->get_history_form($history),
];

if (is_null($history)) {
    $data['nohistory'] = get_string('nohistory', 'block_calculator');
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('block_calculator/history', $data);;
echo $OUTPUT->footer();
