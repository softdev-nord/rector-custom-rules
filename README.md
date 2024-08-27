# Rector Custom Rules Collection
This package contains a collection of custom rules that can be used for all PHP projects.

## Installation
```bash
composer require --dev sdn/rector-custom-rules
```

## Usage
Add the following to your rector config:

```php
...
use Sdn\RectorCustomRules\Rules\ConvertProbArgVarNamesToCamelCaseRector
use Sdn\RectorCustomRules\Rules\ConvertMethodsNameToCamelCaseRector
use 
...

$rectorConfig
    ->rule(
        ConvertProbArgVarNamesToCamelCaseRector::class,
        ConvertMethodsNameToCamelCaseRector::class
   );
```

## Rector rules

### ConvertProbArgVarNamesToCamelCaseRector

Converts the names of object properties, arguments and variables to camel case.
```diff
class ProbArgVarNamesToCamelCase
 {
     public function __construct(
-        private string $FIRST_PROPERTY,
-        private string $SECOND_Property,
-        private string $third_PROPERTY,
-        private string $fourth_property,
+        private string $firstProperty,
+        private string $secondProperty,
+        private string $thirdProperty,
+        private string $fourthProperty,
     ) {
     }

-    public function toCamelCasePublicMethod(string $FIRST_PARAMETER, string $SECOND_Parameter, string $third_PARAMETER, string $fourth_parameter): void
+    public function toCamelCasePublicMethod(string $firstParameter, string $secondParameter, string $thirdParameter, string $fourthParameter): void
     {
-        $this->FIRST_PROPERTY = $FIRST_PARAMETER;
-        $this->SECOND_Property = $SECOND_Parameter;
-        $this->third_PROPERTY = $third_PARAMETER;
-        $this->fourth_property = $fourth_parameter;
+        $this->firstProperty = $firstParameter;
+        $this->secondProperty = $secondParameter;
+        $this->thirdProperty = $thirdParameter;
+        $this->fourthProperty = $fourthParameter;
     }

-    private function toCamelCasePrivateMethod(string $FIRST_PARAMETER, string $SECOND_Parameter, string $third_PARAMETER, string $fourth_parameter): void
+    private function toCamelCasePrivateMethod(string $firstParameter, string $secondParameter, string $thirdParameter, string $fourthParameter): void
     {
-        $this->FIRST_PROPERTY = $FIRST_PARAMETER;
-        $this->SECOND_Property = $SECOND_Parameter;
-        $this->third_PROPERTY = $third_PARAMETER;
-        $this->fourth_property = $fourth_parameter;
+        $this->firstProperty = $firstParameter;
+        $this->secondProperty = $secondParameter;
+        $this->thirdProperty = $thirdParameter;
+        $this->fourthProperty = $fourthParameter;
     }
 }

```

### ConvertMethodsNameToCamelCaseRector

Converts the names of methods to camel case.

```diff
class MethodsNameToCamelCase
 {
-    public function TEST_PUBLIC_FUNCTION(string $firstParameter, string $secondParameter): string
+    public function testPublicFunction(string $firstParameter, string $secondParameter): string
     {
-        return $this->test_public_function_three($firstParameter, $secondParameter);
+        return $this->testPublicFunctionThree($firstParameter, $secondParameter);
     }

-    public function TEST_public_FUNCTION_two(string $firstParameter, string $secondParameter): string
+    public function testPublicFunctionTwo(string $firstParameter, string $secondParameter): string
     {
-        return $this->test_public_function_three($firstParameter, $secondParameter);
+        return $this->testPublicFunctionThree($firstParameter, $secondParameter);
     }

-    public function test_public_function_three(string $firstParameter, string $secondParameter): string
+    public function testPublicFunctionThree(string $firstParameter, string $secondParameter): string
     {
         return $firstParameter . '-' . $secondParameter;
     }

-    private function TEST_PRIVATE_FUNCTION(string $firstParameter, string $secondParameter): string
+    private function testPrivateFunction(string $firstParameter, string $secondParameter): string
     {
-        return $this->test_public_function_three($firstParameter, $secondParameter);
+        return $this->testPublicFunctionThree($firstParameter, $secondParameter);
     }

-    private function TEST_private_FUNCTION_two(string $firstParameter, string $secondParameter): string
+    private function testPrivateFunctionTwo(string $firstParameter, string $secondParameter): string
     {
-        return $this->test_private_function_three($firstParameter, $secondParameter);
+        return $this->testPrivateFunctionThree($firstParameter, $secondParameter);
     }

-    private function test_private_function_three(string $firstParameter, string $secondParameter): string
+    private function testPrivateFunctionThree(string $firstParameter, string $secondParameter): string
     {
         return $firstParameter . '-' . $secondParameter;
     }

```