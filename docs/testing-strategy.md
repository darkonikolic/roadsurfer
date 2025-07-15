# 🧪 Testing Strategy Guide

## 📋 Overview

This project follows a comprehensive testing strategy based on Behavior-Driven Development (BDD) principles, organized by Clean Architecture layers. The testing approach ensures high code quality, maintainability, and confidence in the codebase.

## 🏗️ Testing Pyramid

### **Unit Tests** (Base - 70% of tests)
- **Purpose**: Test individual components in isolation
- **Scope**: Domain logic, Application services, Shared utilities
- **Tools**: PHPUnit with mocks and stubs
- **Speed**: Fast execution (< 1 second per test)
- **Coverage Goal**: >90%

### **Integration Tests** (Middle - 20% of tests)
- **Purpose**: Test component interactions and external integrations
- **Scope**: Infrastructure implementations, Controller behavior
- **Tools**: PHPUnit with real database connections
- **Speed**: Medium execution (1-5 seconds per test)
- **Coverage Goal**: >80%

### **Feature Tests** (Top - 10% of tests)
- **Purpose**: Test complete user workflows (BDD approach)
- **Scope**: End-to-end functionality, API endpoints
- **Tools**: PHPUnit WebTestCase, HTTP client
- **Speed**: Slow execution (5-30 seconds per test)
- **Coverage Goal**: Critical paths covered

## 📁 Test Organization by Layer

### **Domain Layer Tests** (`tests/Domain/`)
**Purpose**: Test business logic and domain rules

**Test Types**:
- **Unit Tests**: Entity behavior, Value object validation
- **Domain Service Tests**: Business logic workflows
- **Repository Interface Tests**: Contract compliance

**Example Tests**:
```php
class HealthCheckerTest extends TestCase
{
    public function test_should_return_healthy_when_all_services_up(): void
    {
        // Arrange
        $healthChecker = new HealthChecker();
        $services = ['database' => 'up', 'redis' => 'up'];
        
        // Act
        $result = $healthChecker->checkHealth($services);
        
        // Assert
        $this->assertEquals('healthy', $result->getStatus());
    }
}
```

### **Application Layer Tests** (`tests/Application/`)
**Purpose**: Test use cases and application workflows

**Test Types**:
- **Integration Tests**: Service coordination
- **Command/Query Tests**: Handler behavior
- **Workflow Tests**: End-to-end use cases

**Example Tests**:
```php
class HealthServiceTest extends TestCase
{
    public function test_should_coordinate_health_checks(): void
    {
        // Arrange
        $databaseService = $this->createMock(DatabaseHealthService::class);
        $redisService = $this->createMock(RedisHealthService::class);
        $healthService = new HealthService($databaseService, $redisService);
        
        // Act & Assert
        $result = $healthService->getSystemHealth();
        $this->assertInstanceOf(HealthStatusDTO::class, $result);
    }
}
```

### **Infrastructure Layer Tests** (`tests/Infrastructure/`)
**Purpose**: Test external integrations and data persistence

**Test Types**:
- **Integration Tests**: Database operations, API clients
- **External Service Tests**: Third-party integrations
- **Configuration Tests**: Framework setup

**Example Tests**:
```php
class DatabaseHealthServiceTest extends TestCase
{
    public function test_should_check_database_connection(): void
    {
        // Arrange
        $connection = $this->createMock(Connection::class);
        $service = new DatabaseHealthService($connection);
        
        // Act
        $result = $service->checkHealth();
        
        // Assert
        $this->assertInstanceOf(ServiceHealthDTO::class, $result);
    }
}
```

### **Presentation Layer Tests** (`tests/Presentation/`)
**Purpose**: Test HTTP endpoints and user interactions

**Test Types**:
- **Feature Tests**: Complete HTTP workflows
- **Controller Tests**: Request/Response handling
- **API Tests**: REST endpoint behavior

**Example Tests**:
```php
class HealthControllerTest extends WebTestCase
{
    public function test_health_endpoint_returns_200(): void
    {
        // Arrange
        $client = static::createClient();
        
        // Act
        $client->request('GET', '/health');
        
        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
```

## 🧪 BDD Testing Approach

### **Behavior-Driven Development**
- **Given-When-Then**: Clear test structure
- **User Stories**: Focus on user behavior
- **Acceptance Criteria**: Business requirements as tests

### **Gherkin Syntax** (for Behat tests)
```gherkin
Feature: Health Check
  As a system administrator
  I want to check system health
  So that I can ensure the system is running properly

  Scenario: Check system health
    Given the system is running
    When I request the health status
    Then I should receive a healthy status
    And all services should be up
```

### **Test Naming Conventions**
```php
// Unit Tests
public function test_should_return_healthy_when_all_services_up(): void
public function test_should_return_degraded_when_some_services_down(): void
public function test_should_throw_exception_when_invalid_service(): void

// Integration Tests
public function test_health_service_should_coordinate_multiple_checks(): void
public function test_database_connection_should_be_established(): void

// Feature Tests
public function test_health_endpoint_should_return_json_response(): void
public function test_health_endpoint_should_include_all_services(): void
```

## 📊 Coverage Goals

### **Overall Coverage Targets**
- **Unit Tests**: >90% coverage
- **Integration Tests**: >80% coverage
- **Feature Tests**: Critical paths covered
- **Overall**: >80% code coverage

### **Coverage by Layer**
- **Domain Layer**: >95% (business logic critical)
- **Application Layer**: >85% (orchestration logic)
- **Infrastructure Layer**: >75% (external dependencies)
- **Presentation Layer**: >70% (HTTP handling)

### **Coverage Reporting**
```bash
# Generate coverage report
make test-coverage

# View coverage in browser
open coverage/index.html
```

## 🛠️ Testing Tools

### **PHPUnit Configuration**
```xml
<!-- phpunit.xml.dist -->
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### **Test Database Setup**
```bash
# Recreate test database
make db-recreate-test

# Run migrations on test database
make db-migrate-test

# Run tests with test environment
APP_ENV=test make test
```

### **Mocking and Stubbing**
```php
// Mock external dependencies
$databaseService = $this->createMock(DatabaseHealthService::class);
$databaseService->method('checkHealth')
    ->willReturn(new ServiceHealthDTO('up', 'healthy'));

// Stub method calls
$logger = $this->createMock(LoggerInterface::class);
$logger->expects($this->once())
    ->method('info')
    ->with('Health check completed');
```

## 🚨 Common Testing Issues

### **Test Failures**
```bash
# Recreate test database
make db-recreate-test

# Run tests with verbose output
make test VERBOSE=1

# Check test environment
APP_ENV=test php bin/console debug:config
```

### **Database Connection Issues**
- Ensure test database exists: `make db-recreate-test`
- Check environment variables: `APP_ENV=test`
- Verify database accessibility in test environment

### **Slow Test Execution**
- Use database transactions for test isolation
- Mock external services and APIs
- Use in-memory SQLite for unit tests
- Parallel test execution where possible

### **Flaky Tests**
- Ensure test isolation (no shared state)
- Use unique test data for each test
- Avoid time-dependent assertions
- Clean up test data after each test

## 📈 Test Metrics and Quality

### **Test Quality Metrics**
- **Test Coverage**: Percentage of code covered by tests
- **Test Execution Time**: Total time to run all tests
- **Test Reliability**: Percentage of tests that pass consistently
- **Test Maintainability**: Ease of updating tests when code changes

### **Continuous Integration**
```yaml
# .github/workflows/tests.yml
- name: Run Tests
  run: |
    make db-recreate-test
    make test
    make test-coverage
```

### **Test Data Management**
- **Fixtures**: Predefined test data sets
- **Factories**: Dynamic test data generation
- **Fakers**: Realistic test data generation
- **Seeds**: Database seeding for tests

## 📚 Additional Resources

### **BDD Testing**
- [Behavior-Driven Development](https://en.wikipedia.org/wiki/Behavior-driven_development)
- [Behat Documentation](https://docs.behat.org/)
- [Gherkin Syntax](https://cucumber.io/docs/gherkin/reference/)

### **PHPUnit**
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [PHPUnit Best Practices](https://phpunit.de/documentation.html)
- [Test Doubles](https://phpunit.de/documentation.html#test-doubles)

### **Testing Patterns**
- [Test-Driven Development](https://en.wikipedia.org/wiki/Test-driven_development)
- [Arrange-Act-Assert](https://en.wikipedia.org/wiki/Arrange-Act-Assert)
- [Given-When-Then](https://en.wikipedia.org/wiki/Given-When-Then)

### **Coverage Tools**
- [Xdebug](https://xdebug.org/)
- [PHPUnit Coverage](https://phpunit.de/documentation.html#code-coverage-analysis)
- [Coverage Reports](https://phpunit.de/documentation.html#code-coverage-analysis) 