<?php
declare(strict_types=1);

require_once(__DIR__ . '/interfaces.php');

class quadratic_equation_solver_data implements data_handler {
    public const string NAME = 'quadraticequationsolver';

    // Переменные для вычислений
    public float $a = 0;
    public float $b = 0;
    public float $c = 0;
    public ?float $d = null;
    public ?float $x1 = null;
    public ?float $x2 = null;

    // Переменные для главной формы
    public string $formaction;
    public string $namebutton;
    public string $urlhistory;
    public string $namehistory;
    public string $placeholder;

    // Переменные для формы вычисления
    public string $azero;
    public string $nosolution;
    public string $namecalculation;
    public string $nameequation;
    public string $namediscriminant;
    public string $nameresult;

    // Переменные для формы истории
    public string $time;
    public string $titlehistory;

    public function __construct() {
        $this->formaction = (string)(new moodle_url('/blocks/calculator/process.php')) . '?calculatormodule=' . self::NAME;
        $this->namebutton = get_string('submitbutton', 'block_calculator');
        $this->urlhistory = (string)(new moodle_url('/blocks/calculator/history.php')) . '?calculatormodule=' . self::NAME;
        $this->namehistory = get_string('namehistory', 'block_calculator');
        $this->placeholder = get_string('placeholder', 'block_calculator');

        $this->azero = get_string('azero', 'block_calculator');
        $this->nosolution = get_string('nosolution', 'block_calculator');
        $this->namecalculation = get_string('namecalculation', 'block_calculator');
        $this->nameequation = get_string('nameequation', 'block_calculator');
        $this->namediscriminant = get_string('namediscriminant', 'block_calculator');
        $this->nameresult = get_string('nameresult', 'block_calculator');

        $this->time = get_string('time', 'block_calculator');
        $this->titlehistory = get_string('titlehistory', 'block_calculator');
    }
}

class quadratic_equation_solver_math implements solver {
    public static function calculate(data_handler $datahandler): array {
        if ($datahandler->a == 0) {
            return [null, null, null];
        }

        $datahandler->d = $datahandler->b * $datahandler->b - 4 * $datahandler->a * $datahandler->c;

        if ($datahandler->d == 0) {
            $datahandler->x1 = -$datahandler->b / (2 * $datahandler->a);
        } elseif ($datahandler->d > 0) {
            $datahandler->x1 = (-$datahandler->b + sqrt($datahandler->d)) / (2 * $datahandler->a);
            $datahandler->x2 = (-$datahandler->b - sqrt($datahandler->d)) / (2 * $datahandler->a);
        }

        return [$datahandler->d, $datahandler->x1, $datahandler->x2];
    }
}

class quadratic_equation_solver_db implements storage {
    public static function write_db(data_handler $datahandler) {
        global $USER, $DB;

        try {
            $record = new stdClass();
            $record->userid = $USER->id;
            $record->a = $datahandler->a;
            $record->b = $datahandler->b;
            $record->c = $datahandler->c;
            $record->d = $datahandler->d;
            $record->x1 = $datahandler->x1;
            $record->x2 = $datahandler->x2;
            $record->timecreated = time();
            $DB->insert_record('calculator_history', $record);
        } catch (Exception $e) {
            throw new Exception("Error writing to the database. {$e}");
        }
    }

    public static function read_db(): ?array {
        global $DB, $USER;

        try {
            $records = $DB->get_records('calculator_history', ['userid' => $USER->id]);
        } catch (Exception $e) {
            throw new Exception("Error reading from the database. {$e}");
        }

        if (!$records) {
            return null;
        }

        $history = [];

        foreach ($records as $record) {
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

        return $history;
    }
}

class quadratic_equation_solver_view implements form_handler {
    public static function process_request(array $request, data_handler $datahandler) {
        $datahandler->a = (float)$request['a'];
        $datahandler->b = (float)$request['b'];
        $datahandler->c = (float)$request['c'];
    }

    public static function get_main_form(data_handler $datahandler): string {
        global $OUTPUT;

        $data = [
            'formaction' => $datahandler->formaction,
            'namebutton' => $datahandler->namebutton,
            'urlhistory' => $datahandler->urlhistory,
            'namehistory' => $datahandler->namehistory,
            'placeholder' => $datahandler->placeholder
        ];

        return $OUTPUT->render_from_template('block_calculator/quadratic_equation_solver/main', $data);
    }

    public static function get_process_form(data_handler $datahandler): string {
        global $OUTPUT;

        $equation = $datahandler->a . 'x²';

        if ($datahandler->b < 0) {
            $equation .= '-' . abs($datahandler->b) . 'x';
        } else {
            $equation .= '+' . abs($datahandler->b) . 'x';
        }

        if ($datahandler->c < 0) {
            $equation .= '-' . abs($datahandler->c);
        } else {
            $equation .= '+' . abs($datahandler->c);
        }

        if (is_null($datahandler->d)) {
            $result = $datahandler->azero;
        } elseif ($datahandler->d < 0) {
            $result = $datahandler->nosolution;
        } elseif ($datahandler->d == 0) {
            $result = 'x = ' . round($datahandler->x1, 2);
        } else {
            $result = 'x1 = ' . round($datahandler->x1, 2) . ', x2 = ' . round($datahandler->x2, 2);
        }

        $data = [
            'namecalculation' => $datahandler->namecalculation,
            'nameequation' => $datahandler->nameequation,
            'equation' => $equation,
            'namediscriminant' => $datahandler->namediscriminant,
            'discriminant' => $datahandler->d,
            'nameresult' => $datahandler->nameresult,
            'result' => $result,
        ];

        return $OUTPUT->render_from_template('block_calculator/quadratic_equation_solver/process', $data);
    }

    public static function get_history_form(?array $history, data_handler $datahandler): ?string {
        global $OUTPUT;

        if (is_null($history)) {
            return null;
        }

        $data = [
            'history' => $history,
            'time' => $datahandler->time,
            'titlehistory' => $datahandler->titlehistory,
        ];

        return $OUTPUT->render_from_template('block_calculator/quadratic_equation_solver/history', $data);
    }
}
