<?php

declare(strict_types=1);

namespace Sdn\RectorCustomRules\Rules;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use Rector\ValueObject\MethodName;
use Sdn\ItScopeService\Helpers\StringHelper;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use Rector\Php\ReservedKeywordAnalyzer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ConvertProbArgVarNamesToCamelCaseRector extends AbstractRector
{
    /** @var array<string> */
    private array $properties = [];

    public function __construct(
        private readonly ReservedKeywordAnalyzer $reservedKeywordAnalyzer,
    ) {
    }

    public function getNodeTypes(): array
    {
        return [Class_::class, Variable::class, PropertyFetch::class];
    }

    /**
     * @param Node $node
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof ClassLike) {
            // Get properties declared in the constructor
            $constructor = $node->getMethod(MethodName::CONSTRUCT);
            if ($constructor) {
                foreach ($constructor->getParams() as $param) {
                    if (
                        $param->flags !== 0 &&
                        $param->var instanceof Variable &&
                        is_string($param->var->name)
                    ) {
                        $this->properties[] = $param->var->name;
                    }
                }
            }

            return null;
        }

        if ($node instanceof Variable) {
            return $this->processVariable($node);
        }

        if ($node instanceof PropertyFetch) {
            return $this->processPropertyFetch($node);
        }

        return null;
    }

    private function processVariable(?Variable $node): ?Node
    {
        if ($node === null) {
            return null;
        }

        $currentName = $node->name;

        if ($currentName instanceof Expr) {
            return null;
        }

        if ($this->reservedKeywordAnalyzer->isNativeVariable($currentName)) {
            return null;
        }

        $newName = StringHelper::stringToCamelCase($currentName);

        if ($newName === '' || $currentName === $newName) {
            return null;
        }

        $node->name = $newName;

        return $node;
    }

    private function processPropertyFetch(PropertyFetch $node): ?Node
    {
        $propertyName = $node->name;

        if (!$propertyName instanceof Identifier) {
            return null;
        }

        $newName = StringHelper::stringToCamelCase($propertyName->name);

        if ($newName === '' || $propertyName->name === $newName) {
            return null;
        }

        $propertyName->name = $newName;

        return $node;
    }

    /**
     * @throws PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Converts the names of variables and object properties to camelCase',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                    public function __construct(
                        private string $FIRST_PROPERTY,
                        private string $SECOND_Property,
                        private string $third_PROPERTY,
                        private string $fourth_property,
                    ) {
                    }

                    public function toCamelCasePublicMethod(string $FIRST_PARAMETER, string $SECOND_Parameter, string $third_PARAMETER, string $fourth_parameter): void
                    {
                        $this->FIRST_PROPERTY = $FIRST_PARAMETER;
                        $this->SECOND_Property = $SECOND_Parameter;
                        $this->third_PROPERTY = $third_PARAMETER;
                        $this->fourth_property = $fourth_parameter;
                    }

                    public function toCamelCasePrivatMethod(string $FIRST_PARAMETER, string $SECOND_Parameter, string $third_PARAMETER, string $fourth_parameter): void
                    {
                        $this->FIRST_PROPERTY = $FIRST_PARAMETER;
                        $this->SECOND_Property = $SECOND_Parameter;
                        $this->third_PROPERTY = $third_PARAMETER;
                        $this->fourth_property = $fourth_parameter;
                    }
                CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
                    public function __construct(
                        private string $firstProperty,
                        private string $secondProperty,
                        private string $thirdProperty,
                        private string $fourthProperty,
                    ) {
                    }

                    public function toCamelCasePublicMethod(string $firstParameter, string $secondParameter, string $thirdParameter, string $fourthParameter): void
                    {
                        $this->firstProperty = $firstParameter;
                        $this->secondProperty = $secondParameter;
                        $this->thirdProperty = $thirdParameter;
                        $this->fourthProperty = $fourthParameter;
                    }

                    public function toCamelCasePrivatMethod(string $firstParameter, string $secondParameter, string $thirdParameter, string $fourthParameter): void
                    {
                        $this->firstProperty = $firstParameter;
                        $this->secondProperty = $secondParameter;
                        $this->thirdProperty = $thirdParameter;
                        $this->fourthProperty = $fourthParameter;
                    }
                CODE_SAMPLE
                ),
            ]
        );
    }
}
