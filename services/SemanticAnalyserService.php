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
                $lines = join( ", ", $item["linha"]);
                CommandLineHelper::print_magenta_message("Semantic error: Var '{$item["rotulo"]}' on lines {{$lines}} was not declared");
            }

            if($item["type"] == "var" and $item["declarada"]){
                sort($item["linha"]);
                $lines = join( ", ", $item["linha"]);
                if ($item["declarada_em"] > $item["linha"][0]){
                    $errors = true;
                    CommandLineHelper::print_magenta_message("Semantic error: Var '{$item["rotulo"]}' on line {{$lines}} was used before declaration");
                }
            }
        }

        if ($errors){
            exit(0);
        }

        CommandLineHelper::print_green_message("Semantic Analysis Completed With No Errors");
    }
}