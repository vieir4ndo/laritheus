<?php

namespace Services;

use Entities\ParserTable;
use Enums\TransitionAction;
use Helpers\CommandLineHelper;

class SyntacticalAnalyserService
{

    public function __construct()
    {
    }

    public function execute(array &$symbol_table, array &$tape, ParserTable $parser_table, $production_number_dictionary)
    {
        $production_names = [];

        foreach ($production_number_dictionary as $item) {
            if (!in_array($item["production_name"], $production_names)) {
                $production_names[] = $item["production_name"];
            }
        }

        $i = 0;

        $symbol_stack = new \Ds\Stack();
        $state_stack = new \Ds\Stack();

        $state_stack->push(0);

        $done = false;

        while (true) {
            /*system('clear');
            print_r($symbol_stack);
            print_r($state_stack);*/

            $current_state = $parser_table->get_state_by_id($state_stack->peek());

            if (!$done && !$symbol_stack->isEmpty() && in_array($symbol_stack->peek(), $production_names)) {
                $done = true;
                $transition = $current_state->get_transition_by_token($symbol_stack->peek());
            } else {
                $done = false;
                $transition = $current_state->get_transition_by_token($tape[$i]["token_type"]);
            }

            if ($transition == null) {
                $token_to_show = ($tape[$i - 1]["token_type"] == "id") ? $tape[$i - 1]["token_value"] : $tape[$i - 1]["token_type"];
                CommandLineHelper::print_magenta_message("Syntactical error: Error near '{$token_to_show}' on line {$tape[$i-1]["line"]}");
                exit(0);
            }

            if (!$symbol_stack->isEmpty() && $symbol_stack->peek() == "declaracao"){
                for ($j = 0; $j < count($symbol_table); $j++){
                    if ($symbol_table[$j]["rotulo"] == $tape[$i - 2]["token_value"]){
                        $symbol_table[$j]["declarada"] = true;
                        $symbol_table[$j]["declarada_em"] = $tape[$i - 2]["line"];
                    }
                }
            }

            if (!$symbol_stack->isEmpty() && $symbol_stack->peek() == "atribuicao") {
                for ($j = 0; $j < count($symbol_table); $j++){
                    if ($symbol_table[$j]["rotulo"] == $tape[$i - 4]["token_value"]){
                        $symbol_table[$j]["valor"] = $tape[$i - 2]["token_value"];
                    }
                }
            }

            if ($transition->get_action() == TransitionAction::Shift) {
                $i++;
                $state_stack->push($transition->get_next_state());
                $symbol_stack->push($transition->get_token());
            } else if ($transition->get_action() == TransitionAction::Reduce) {
                foreach ($production_number_dictionary[$transition->get_next_state()]["items_to_reduce"] as $_) {
                    $state_stack->pop();
                    $symbol_stack->pop();
                }
                $symbol_stack->push($production_number_dictionary[$transition->get_next_state()]["production_name"]);
            } else if ($transition->get_action() == TransitionAction::Accept) {
                CommandLineHelper::print_green_message("Syntactical Analysis Completed With No Errors");
                break;
            } else {
                $state_stack->push($transition->get_next_state());
            }
        }
    }
}