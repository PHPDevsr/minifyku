<?php

declare(strict_types=1);

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2025 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\FunctionLike\SimplifyUselessVariableRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\Ternary\TernaryEmptyArrayArrayDimFetchToCoalesceRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector;
use Rector\EarlyReturn\Rector\If_\ChangeIfElseValueAssignToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\EarlyReturn\Rector\Return_\PreparedValueToEarlyReturnRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Strict\Rector\If_\BooleanInIfConditionRuleFixerRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Closure\ClosureReturnTypeRector;
use Rector\TypeDeclaration\Rector\Empty_\EmptyOnNullableObjectToInstanceOfRector;
use Rector\TypeDeclaration\Rector\Function_\AddFunctionVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPhpSets(php83: true)
    ->withPreparedSets(deadCode: true, codeQuality: true, codingStyle: true, instanceOf: true, strictBooleans: true, phpunitCodeQuality: true, typeDeclarations: true, earlyReturn: true)
    ->withAutoloadPaths([
        __DIR__ . '/vendor/autoload.php',
    ])
    ->withComposerBased(phpunit: true)
    ->withParallel(200, 16, 16)
    ->withCache(
        // Github action cache or local
        is_dir('/tmp') ? '/tmp/rector' : null,
        FileCacheStorage::class,
    )
    // paths to refactor; solid alternative to CLI arguments
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // do you need to include constants, class aliases or custom autoloader? files listed will be executed
    ->withBootstrapFiles([
        realpath(getcwd()) . '/vendor/codeigniter4/framework/system/Test/bootstrap.php',
    ])
    ->withPHPStanConfigs([
        __DIR__ . '/phpstan.neon.dist',
        __DIR__ . '/vendor/codeigniter/phpstan-codeigniter/extension.neon',
        __DIR__ . '/vendor/phpstan/phpstan-strict-rules/rules.neon',
    ])
    // is there a file you need to skip?
    ->withSkip([
        // No need
        DeclareStrictTypesRector::class,
        EncapsedStringsToSprintfRector::class,
        NewlineAfterStatementRector::class,
        NewlineBeforeNewAssignSetRector::class,
        ChangeOrIfContinueToMultiContinueRector::class,
    ])
    // auto import fully qualified class names
    ->withImportNames(removeUnusedImports: true)
    ->withRules([
        DeclareStrictTypesRector::class,
        SimplifyUselessVariableRector::class,
        RemoveAlwaysElseRector::class,
        ChangeNestedForeachIfsToEarlyContinueRector::class,
        ChangeIfElseValueAssignToEarlyReturnRector::class,
        PreparedValueToEarlyReturnRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,
        TernaryEmptyArrayArrayDimFetchToCoalesceRector::class,
        EmptyOnNullableObjectToInstanceOfRector::class,
        DisallowedEmptyRuleFixerRector::class,
        PrivatizeFinalClassPropertyRector::class,
        BooleanInIfConditionRuleFixerRector::class,
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        AddFunctionVoidReturnTypeWhereNoReturnRector::class,
        AddMethodCallBasedStrictParamTypeRector::class,
        TypedPropertyFromAssignsRector::class,
        ClosureReturnTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        AddArrowFunctionReturnTypeRector::class,
    ])
    ->withConfiguredRule(StringClassNameToClassConstantRector::class, [
        // keep '\\' prefix string on string '\Foo\Bar'
        StringClassNameToClassConstantRector::SHOULD_KEEP_PRE_SLASH => true,
    ]);
