<?php
declare(strict_types=1);

require_once('../../config.php');

global $DB, $OUTPUT, $PAGE;

$PAGE->set_url(new moodle_url('/blocks/calculator/history.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('History of Calculations');

echo $OUTPUT->header();

$history_records = $DB->get_records('calculator_history', ['userid' => $USER->id]);

if ($history_records) {
    $history = [];

    foreach ($history_records as $record) {
        $history[] = [
            'a' => $record->a,
            'b' => $record->b,
            'c' => $record->c,
            'd' => $record->d,
            'x1' => $record->x1,
            'x2' => $record->x2,
            'timecreated' => date('Y-m-d H:i:s', (int)$record->timecreated)
        ];
    }

    $data = [
        'history' => $history,
        'time' => get_string('time', 'block_calculator'),
        'title_history' => get_string('title_history', 'block_calculator')
    ];
    $form = $OUTPUT->render_from_template('block_calculator/history', $data);
} else {
    $data = [
        'no_history' => get_string('no_history', 'block_calculator'),
        'time' => get_string('time', 'block_calculator'),
        'title_history' => get_string('title_history', 'block_calculator')
    ];
    $form = $OUTPUT->render_from_template('block_calculator/history', $data);
}

echo $form;
echo $OUTPUT->footer();
