<?php
declare(strict_types=1);

require_once('../../config.php');
require_once(__DIR__ . '/../moodleblock.class.php');
require_once(__DIR__ . '/block_calculator.php');

global $DB, $OUTPUT, $PAGE;

$PAGE->set_url(new moodle_url('/blocks/calculator/process.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Result of Calculations');

$a = optional_param('a', 0, PARAM_FLOAT);
$b = optional_param('b', 0, PARAM_FLOAT);
$c = optional_param('c', 0, PARAM_FLOAT);

$equation = "{$a}x²";

if ($b < 0) {
    $equation .= '-' . abs($b) . 'x';
} else {
    $equation .= '+' . abs($b) . 'x';
}

if ($c < 0) {
    $equation .= '-' . abs($c);
} else {
    $equation .= '+' . abs($c);
}

list($discriminant, $x1, $x2) = block_calculator::calculate($a, $b, $c);
block_calculator::write_db($a, $b, $c, $discriminant, $x1, $x2);

if (is_null($discriminant)) {
    $result = get_string('a_zero', 'block_calculator');
} elseif ($discriminant < 0) {
    $result = get_string('no_solution', 'block_calculator');
} elseif ($discriminant == 0) {
    $result = 'x = ' . round($x1, 2);
} else {
    $result = 'x1 = ' . round($x1, 2) . ', x2 = ' . round($x2, 2);
}

$data = [
    'name_calculation' => get_string('name_calculation', 'block_calculator'),
    'name_equation' => get_string('name_equation', 'block_calculator'),
    'equation' => $equation,
    'name_discriminant' => get_string('name_discriminant', 'block_calculator'),
    'discriminant' => $discriminant,
    'name_result' => get_string('name_result', 'block_calculator'),
    'result' => $result,
];
$form = $OUTPUT->render_from_template('block_calculator/result_of_calculation', $data);

echo $OUTPUT->header();
echo $form;
echo $OUTPUT->footer();
