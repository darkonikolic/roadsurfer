# 🔍 Code Quality Standards

## 📋 Overview

This document outlines the code quality tools and standards used in the Roadster project. Our goal is to maintain high code quality, consistency, and prevent common issues through automated analysis.

## 🚀 One Command to Rule Them All

### **Complete Quality Pipeline**
```bash
make quality-pipeline
```

This single command runs all code quality checks in the optimal order:

1. **Auto-format code** (PHP CS Fixer)
2. **Type safety check** (Psalm)
3. **Static analysis** (PHPStan)
4. **Code smells check** (PHPMD)
5. **Duplicate detection** (PHPCPD)
6. **Complexity metrics** (PHP Metrics)
7. **Run all tests** (PHPUnit)
8. **Generate report** (Combined report)

### **What it does:**
```bash
# 1. Format code first (fixes style issues)
make format

# 2. Run type safety checks
make psalm
make phpstan

# 3. Run code quality checks
make phpmd
make phpcpd

# 4. Generate complexity metrics
make metrics

# 5. Run all tests
make test

# 6. Generate combined report
make quality-report
```

### **Exit Codes:**
- **0** - All checks passed ✅
- **1** - Any check failed ❌
- **2** - Configuration error ⚠️

### **Output:**
```
🔍 Code Quality Pipeline Started
✅ Formatting code...
✅ Type safety check...
✅ Static analysis...
✅ Code smells check...
✅ Duplicate detection...
✅ Complexity metrics...
✅ Running tests...
✅ Generating report...

📊 Quality Report Generated: reports/quality-report.html
🎉 All quality checks passed!
```

## 🛠️ Code Quality Tools

| Tool | Purpose | Coverage | Auto-Fix | Configuration |
|------|---------|----------|----------|---------------|
| **Psalm** | Type safety, null safety, advanced type checking | Type errors, null issues | ❌ | `psalm.xml` |
| **PHPStan** | Static analysis, general bugs, undefined variables | Runtime errors, type issues | ❌ | `phpstan.neon` |
| **PHP CS Fixer** | Code formatting, PSR-2/PSR-12 compliance | Code style, formatting | ✅ | `.php-cs-fixer.php` |
| **PHPMD** | Code smells, complexity analysis | Code quality, maintainability | ❌ | `phpmd.xml` |
| **PHPCPD** | Duplicate code detection | Code duplication | ❌ | `phpcpd.xml` |
| **PHP Metrics** | Complexity metrics, maintainability index | Code complexity | ❌ | `phpmetrics.xml` |

## 🎯 Tool Coverage Matrix

| Quality Aspect | Psalm | PHPStan | PHP CS Fixer | PHPMD | PHPCPD | PHP Metrics |
|----------------|-------|---------|--------------|-------|--------|-------------|
| **Type Safety** | ✅ Advanced | ✅ Basic | ❌ | ❌ | ❌ | ❌ |
| **Code Style** | ❌ | ❌ | ✅ Auto-fix | ❌ | ❌ | ❌ |
| **Code Smells** | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| **Complexity** | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ Metrics |
| **Duplicates** | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| **Performance** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ Metrics |
| **Maintainability** | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ Metrics |

## 🚀 Usage Strategy

### **Development Workflow**

```bash
# 1. Auto-format code before committing
make format

# 2. Run quality checks
make quality-check

# 3. Run tests
make test

# 4. Full quality gate
make quality
```

### **Make Commands**

```bash
# Code formatting
make format          # Auto-format code (PHP CS Fixer)
make format-check    # Check formatting without fixing

# Quality analysis
make psalm           # Type safety check
make phpstan         # Static analysis
make phpmd           # Code smells check
make phpcpd          # Duplicate code check
make metrics         # Complexity metrics

# Combined checks
make quality-check   # Psalm + PHPStan + PHPMD
make quality         # All tools + tests

# Complete pipeline
make quality-pipeline # All checks in optimal order
```

## 📊 Quality Gates

### **Pre-commit Checks**
```bash
make format          # Ensure code is formatted
make quality-check   # Basic quality checks
```

### **CI/CD Pipeline**
```bash
make quality         # Full quality gate
make test           # All tests must pass
```

### **Release Quality Gate**
```bash
make quality         # All quality checks
make test           # All tests
make security-check # Security vulnerabilities
```

## ⚙️ Configuration Files

### **Psalm Configuration (`psalm.xml`)**
```xml
<?xml version="1.0"?>
<psalm
    errorLevel="4"
    resolveFromConfigFile="true"
    cacheDirectory="/var/www/var/psalm"
    findUnusedBaselineEntry="false"
    findUnusedCode="false"
    phpVersion="8.3"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns="https://getpsalm.org/schema/config"
    xsi:schemaLocation="https://getpsalm.org/schema/config vendor/vimeo/psalm/config.xsd"
>
    <projectFiles>
        <directory name="src"/>
        <ignoreFiles>
            <directory name="vendor"/>
            <directory name="tests"/>
        </ignoreFiles>
    </projectFiles>
    
    <issueHandlers>
        <UndefinedClass>
            <errorLevel type="suppress">
                <directory name="vendor"/>
            </errorLevel>
        </UndefinedClass>
    </issueHandlers>
</psalm>
```

### **PHP CS Fixer Configuration (`.php-cs-fixer.php`)**
```php
<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->exclude('vendor');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'not_operator_with_successor_space' => true,
        'trailing_comma_in_multiline' => true,
        'phpdoc_scalar' => true,
        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
        'single_trait_insert_per_statement' => true,
    ])
    ->setFinder($finder);
```

### **PHPMD Configuration (`phpmd.xml`)**
```xml
<?xml version="1.0"?>
<ruleset name="PHPMD rule set"
         xmlns="http://pmd.sf.net/ruleset/1.0.0"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://pmd.sf.net/ruleset/1.0.0
                             https://pmd.sourceforge.io/ruleset_xml_schema.xsd"
         xsi:noNamespaceSchemaLocation="
                             https://pmd.sourceforge.io/ruleset_xml_schema.xsd">
    <rule ref="rulesets/cleancode.xml"/>
    <rule ref="rulesets/codesize.xml"/>
    <rule ref="rulesets/controversial.xml"/>
    <rule ref="rulesets/design.xml"/>
    <rule ref="rulesets/naming.xml"/>
    <rule ref="rulesets/unusedcode.xml"/>
</ruleset>
```

## 📈 Quality Metrics

### **Target Metrics**
- **Cyclomatic Complexity**: < 10 per method
- **Maintainability Index**: > 65
- **Code Duplication**: < 5%
- **Type Coverage**: > 90%
- **Test Coverage**: > 80%

### **Monitoring**
```bash
# Generate metrics report
make metrics-report

# View complexity analysis
make complexity-analysis

# Check maintainability
make maintainability-check
```

## 🔧 Integration with IDE

### **VS Code Extensions**
- **PHP Intelephense** - PHP language support
- **PHP CS Fixer** - Auto-formatting
- **Psalm** - Type checking
- **PHPStan** - Static analysis

### **PHPStorm Integration**
- Enable **Psalm** plugin
- Enable **PHP CS Fixer** integration
- Enable **PHPStan** integration
- Enable **PHPMD** integration

## 🚨 Common Issues & Solutions

### **Type Safety Issues**
```php
// ❌ Bad - No type hints
function processItems($items) {
    return $items;
}

// ✅ Good - Type hints
function processItems(array $items): array {
    return $items;
}
```

### **Code Style Issues**
```php
// ❌ Bad - Inconsistent formatting
function test($a,$b){
return $a+$b;
}

// ✅ Good - PSR-2 compliant
function test(int $a, int $b): int
{
    return $a + $b;
}
```

### **Complexity Issues**
```php
// ❌ Bad - Too complex
function processData($data) {
    if ($data) {
        if (is_array($data)) {
            foreach ($data as $item) {
                if ($item['type'] === 'fruit') {
                    // 50+ lines of logic
                }
            }
        }
    }
}

// ✅ Good - Simplified
function processData(array $data): void
{
    foreach ($data as $item) {
        $this->processItem($item);
    }
}
```

## 📚 Best Practices

### **1. Type Safety**
- Always use type hints
- Use strict types (`declare(strict_types=1)`)
- Use Psalm annotations for complex types

### **2. Code Style**
- Follow PSR-2/PSR-12 standards
- Use consistent naming conventions
- Keep methods small and focused

### **3. Complexity Management**
- Keep cyclomatic complexity low
- Extract complex logic into separate methods
- Use early returns to reduce nesting

### **4. Error Handling**
- Use proper exception handling
- Validate input parameters
- Use meaningful error messages

## 🔄 Continuous Improvement

### **Regular Reviews**
- Weekly quality metrics review
- Monthly tool configuration updates
- Quarterly best practices review

### **Team Training**
- Code quality workshops
- Tool usage training
- Best practices documentation

---

*Last updated: 2024-01-XX*
*Maintained by: Development Team* 