<?php

require_once 'StringHelper.php';
require_once 'Grammar.php';
require_once 'Production.php';
require_once 'Rule.php';
require_once 'functions.php';
require_once __DIR__ . '/vendor/autoload.php';

use Garden\Cli\Cli;

try {
    error_reporting(E_ERROR | E_PARSE);

    $cli = new Cli();

    $cli->description('Implementa a conversão de GRs em AFDs')
        ->opt('grammar:g', 'Caminho para o arquivo com a GR.');

    $args = $cli->parse($argv, true);

    $ds = DIRECTORY_SEPARATOR;
    $grammar_path = $args->getOpt('gr', __DIR__ . $ds . 'grammar');

    $metadata = get_and_validate_grammar_file($grammar_path);

    $grammar_from_tokens = new Grammar();

    read_tokens_from_file($grammar_from_tokens, get_tokens_from_grammar_file($metadata));

    $grammar_from_file = new Grammar();

    read_grammar_from_file($grammar_from_file, get_grammar_from_grammar_file($metadata));

    $grammar = unify_grammars($grammar_from_tokens, $grammar_from_file);

    generate_deterministic_finite_automaton($grammar);

    // esse método só escreve no cmd
    print_nondeterministic_finite_automaton_in_cmd($grammar);

    // todo: esse método só coloca numa matrix
    $afnd = generate_nondeterministic_finite_automaton($grammar);

    // esse método só escreve no arquivo
    print_nondeterministic_finite_automaton_in_file($afnd);

    var_dump($grammar->get_unreachable_rules());
    var_dump($grammar->get_dead_rules());
} catch (Exception $e) {
}
