<?php
declare(strict_types=1);

require_once(__DIR__ . '/quadratic_equation_solver.php');

function get_calculator_modules(): array {
    /**
     * @var calculator_module[] $calculatormodules
     */
    $calculatormodules = [
        'quadraticequationsolver' => new quadratic_equation_solver(),
        // другие модули
    ];

    return $calculatormodules;
}
