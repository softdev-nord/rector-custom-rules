<?php

declare(strict_types=1);

namespace Sdn\RectorCustomRules\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class MethodParamsAndArgsToNewLinesRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, MethodCall::class];
    }

    /**
     * @param Node $node
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        return match (true) {
            $node instanceof ClassMethod => $this->refactorMethodDeclaration($node),
            $node instanceof MethodCall => $this->refactorMethodCall($node),
            default => null,
        };
    }

    private function refactorMethodDeclaration(ClassMethod $node): Node
    {
        if (count($node->params) > 3) {
            $this->reformatNodesToNewLines($node->params);
        }

        return $node;
    }

    private function refactorMethodCall(MethodCall $node): Node
    {
        if (count($node->args) > 3) {
            $this->reformatNodesToNewLines($node->args);
        }

        return $node;
    }

    /**
     * Reformat the nodes (parameters or arguments) to ensure each is on a new line and add a tab.
     *
     * @param Node[] $nodes
     */
    private function reformatNodesToNewLines(array $nodes): void
    {
        foreach ($nodes as $node) {
            $node->setAttribute('origNode', $node);
            $node->setAttribute('comments', [new Doc("\n")]);
        }
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Put each parameter of a method declaration or method call on a separate line if there are more than 3 parameters.
            In order for the indentation to be correct, php-cs fix must be executed after the rector.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    public function testPublicFunction(string $firstParameter, string $secondParameter, string $thirdParameter, string $fourthParameter): string
                    {
                        return $this->testPublicFunctionTwo($firstParameter, $secondParameter, $thirdParameter, $fourthParameter);
                    }

                    public function testPublicFunctionTwo(string $firstParameter, string $secondParameter, string $thirdParameter, string $fourthParameter): string
                    {
                        return $firstParameter . '-' . $secondParameter . '-' . $thirdParameter . '-' . $fourthParameter;
                    }
                CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
                    public function testPublicFunction(
                        string $firstParameter,
                        string $secondParameter,
                        string $thirdParameter,
                        string $fourthParameter): string 
                    {
                        return $this->testPublicFunctionTwo(
                            $firstParameter,
                            $secondParameter,
                            $thirdParameter,
                            $fourthParameter
                        );
                    }

                    public function testPublicFunctionTwo(
                        string $firstParameter,
                        string $secondParameter,
                        string $thirdParameter,
                        string $fourthParameter): string
                    {
                        return $firstParameter . '-' . $secondParameter . '-' . $thirdParameter . '-' . $fourthParameter;
                    }
                CODE_SAMPLE
            ),
        ]);
    }
}
