<?php

declare(strict_types=1);

namespace Sdn\ItScopeService\Rector\CustomRules;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Sdn\ItScopeService\Helpers\StringHelper;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ConvertMethodsNameToCamelCaseRector extends AbstractRector
{
    /** @var array<string> */
    private array $methods = [];

    public function getNodeTypes(): array
    {
        return [Class_::class, ClassMethod::class, StaticCall::class, MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        return match (true) {
            $node instanceof ClassMethod => $this->refactorMethodDeclaration($node),
            $node instanceof MethodCall => $this->refactorMethodCall($node),
            $node instanceof StaticCall => $this->refactorStaticCall($node),
            $node instanceof ClassLike => $this->handleClassLike($node),
            default => null,
        };
    }

    private function handleClassLike(ClassLike $node): ?Node
    {
        foreach ($node->getMethods() as $method) {
            if (!$method->isFinal() || !$method->isMagic()) {
                $this->methods[] = $method->name->toString();
            }
        }

        return null;
    }

    private function refactorMethodDeclaration(ClassMethod $node): ?Node
    {
        if ($node->isFinal() || $node->isMagic()) {
            return null;
        }

        $oldName = $node->name->toString();
        $newName = StringHelper::stringToCamelCase($oldName);

        if ($oldName === $newName) {
            return null; // Skip if the name is already in camelCase
        }

        $node->name = new Identifier($newName);

        return $node;
    }

    private function refactorMethodCall(MethodCall $node): ?Node
    {
        if (!($node->var instanceof Variable) || $node->var->name !== 'this') {
            return null;
        }

        $methodCallName = $this->getName($node->name);

        if ($methodCallName === null) {
            return null;
        }

        if (!in_array($methodCallName, $this->methods, true)) {
            return null;
        }

        $newMethodCallName = StringHelper::stringToCamelCase($methodCallName);

        if ($methodCallName === $newMethodCallName) {
            return null; // Skip if the name is already in camelCase
        }

        $node->name = new  Identifier($newMethodCallName);

        return $node;
    }

    private function refactorStaticCall(StaticCall $node): ?Node
    {
        if (!($node->class instanceof Name) || $node->class->toString() !== 'self') {
            return null;
        }

        $methodCallName = $this->getName($node->name);

        if (!in_array($methodCallName, $this->methods, true)) {
            return null;
        }

        $newMethodCallName = StringHelper::stringToCamelCase($methodCallName);

        if ($methodCallName === $newMethodCallName) {
            return null; // Skip if the name is already in camelCase
        }

        $node->name = new Identifier($newMethodCallName);

        return $node;
    }

    /**
     * @throws PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Converts the names of methods to camel case',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                    public function TEST_PUBLIC_FUNCTION(string $firstParameter, string $secondParameter): string
                    {
                        return $this->test_public_function_three($firstParameter, $secondParameter);
                    }

                    public function TEST_public_FUNCTION_two(string $firstParameter, string $secondParameter): string
                    {
                        return $this->test_public_function_three($firstParameter, $secondParameter);
                    }

                    public function test_public_function_three(string $firstParameter, string $secondParameter): string
                    {
                        return $firstParameter . '-' . $secondParameter;
                    }

                    private function TEST_PRIVATE_FUNCTION(string $firstParameter, string $secondParameter): string
                    {
                        return $this->test_public_function_three($firstParameter, $secondParameter);
                    }

                    private function TEST_private_FUNCTION_two(string $firstParameter, string $secondParameter): string
                    {
                        return $this->test_private_function_three($firstParameter, $secondParameter);
                    }

                    private function test_private_function_three(string $firstParameter, string $secondParameter): string
                    {
                        return $firstParameter . '-' . $secondParameter;
                    }
                CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
                    public function testPublicFunction(string $firstParameter, string $secondParameter): string
                    {
                        return $this->testPublicFunctionThree($firstParameter, $secondParameter);
                    }

                    public function testPublicFunctionTwo(string $firstParameter, string $secondParameter): string
                    {
                        return $this->testPublicFunctionThree($firstParameter, $secondParameter);
                    }

                    public function testPublicFunctionThree(string $firstParameter, string $secondParameter): string
                    {
                        return $firstParameter . '-' . $secondParameter;
                    }

                    private function testPrivateFunction(string $firstParameter, string $secondParameter): string
                    {
                        return $this->testPublicFunctionThree($firstParameter, $secondParameter);
                    }

                    private function testPrivateFunctionTwo(string $firstParameter, string $secondParameter): string
                    {
                        return $this->testPrivateFunctionThree($firstParameter, $secondParameter);
                    }

                    private function testPrivateFunctionThree(string $firstParameter, string $secondParameter): string
                    {
                        return $firstParameter . '-' . $secondParameter;
                    }
                CODE_SAMPLE
                ),
            ]);
    }
}
