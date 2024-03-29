<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$containerBuilder = new ContainerBuilder();
$containerBuilder->register('FiniteAutomatonService', \Services\FiniteAutomatonService::class);
$containerBuilder->register('GrammarMapper', \Services\GrammarMapper::class)
    ->addArgument($containerBuilder->get("FiniteAutomatonService"));
$containerBuilder->register('InputFileService', \Services\InputFileService::class);
$containerBuilder->register('PrintService', \Services\PrintService::class);
$containerBuilder->register('Configuration', \Configuration\Configuration::class);
$containerBuilder->register('GrammarFactory', \Services\GrammarFactory::class)
    ->addArgument($containerBuilder->get("InputFileService"))
    ->addArgument($containerBuilder->get("GrammarMapper"))
    ->addArgument($containerBuilder->get("FiniteAutomatonService"));
$containerBuilder->register('LexicalAnalyserService', \Services\LexicalAnalyserService::class)
    ->addArgument($containerBuilder->get("InputFileService"));
$containerBuilder->register('ParserTableMapper', \Services\ParserTableMapper::class)
    ->addArgument($containerBuilder->get("InputFileService"));;
$containerBuilder->register('SyntacticalAnalyserService', \Services\SyntacticalAnalyserService::class);
$containerBuilder->register('SemanticAnalyserService', \Services\SemanticAnalyserService::class);
$containerBuilder->register('IntermediateCodeGeneratorService', \Services\IntermediateCodeGeneratorService::class);
$containerBuilder->register('CodeOptimizerService', \Services\CodeOptimizerService::class);

