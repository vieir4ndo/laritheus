<?php

namespace Services;

use Helpers\CommandLineHelper;

class SemanticAnalyserService
{
    public function execute(&$symbol_table){

        $errors = false;

        foreach ($symbol_table as $item){
            if ($item["type"] == "var" and !$item["declarada"]){
                $errors = true;
                CommandLineHelper::print_magenta_message("Semantic error: Var '{$item["rotulo"]}' on line {$item["linha"]} was not declared");
            }
        }

        if ($errors){
            exit(0);
        }

        CommandLineHelper::print_green_message("Semantic Analysis Completed With No Errors");
    }
}