<?php

namespace Services;

use Helpers\CommandLineHelper;

class IntermediateCodeGeneratorService
{
    public function execute(&$symbol_table){

        $code = ".data\n";
        $j = 0;

        //data
        for ($i = 0; $i < count($symbol_table); $i++){
            $item = $symbol_table[$i];
            if ($item["type"] == "var" and isset($item["valor"]) and !is_numeric(str_replace(",", "", $item["valor"]))){
                $code = "{$code}    {$item["rotulo"]}: .asciz {$item["valor"]}\n";
                $j++;
            }
        }

        $code = "{$code}.text\nmain:\n";

        //text
        for ($i = 0; $i < count($symbol_table); $i++){

            $item = $symbol_table[$i];

            if ($item["type"] == "var" and isset($item["valor"])){
                $val = (is_numeric(str_replace(",", "",$item["valor"]))) ? $item["valor"] : $item["rotulo"];
                $code = "{$code}    addi t{$j}, zero, {$val}\n";
                $j++;
            }
        }

        $fp = fopen("output_files/codes/o", 'w');

        fwrite($fp, $code);

        fclose($fp);

        CommandLineHelper::print_green_message("Intermediate Code Generated at file '/output_files/codes/output'");
    }
}