# Testing Strategy

## Overview

This document outlines the testing strategy for the RoadSurfer application, covering unit tests, integration tests, and end-to-end tests.

## Test Categories

### Unit Tests
- **Location**: `tests/Unit/`
- **Purpose**: Test individual components in isolation
- **Coverage**: Business logic, services, utilities
- **Mocking**: External dependencies are mocked

### Integration Tests
- **Location**: `tests/Integration/`
- **Purpose**: Test component interactions and database operations
- **Coverage**: Service interactions, repository operations, API endpoints
- **Database**: Uses test database with real entities

### End-to-End Tests
- **Location**: `tests/E2E/`
- **Purpose**: Test complete user workflows
- **Coverage**: Full application stack
- **External**: May include external service calls

## Test Structure

### Base Classes
- `AbstractIntegrationTestCase`: Base class for integration tests
- Provides database setup/teardown
- Includes container access for dependency injection

### Test Categories

#### Application Service Tests
- **ImportProductsServiceIntegrationTest**: Tests the complete import workflow
  - **Purpose**: Verify the entire data import process from JSON file to processed DTOs
  - **Coverage**: 
    - File loading and validation
    - JSON parsing to ProductListDTO
    - Unit conversion (kg/g to grams)
    - Product splitting into fruits and vegetables
  - **Test Cases**:
    - `testProcessFileContentWithRequestJson()`: Tests with request.json file
    - `testProcessFileContentWithCustomJson()`: Tests with custom JSON data
    - `testProcessFileContentWithNonExistentFile()`: Tests file validation
    - `testProcessFileContentWithInvalidJson()`: Tests JSON validation
  - **Expected Results**: Hardcoded entities based on request.json content
    - Fruits: 10 items (Apples, Pears, Melons, Berries, Bananas, Oranges, Avocado, Kiwi, Lettuce, Kumquat)
    - Vegetables: 10 items (Carrot, Beans, Beetroot, Broccoli, Tomatoes, Celery, Cabbage, Onion, Cucumber, Pepper)

#### Controller Tests
- **FruitControllerIntegrationTest**: Tests fruit API endpoints
- **VegetableControllerIntegrationTest**: Tests vegetable API endpoints
- **HealthControllerIntegrationTest**: Tests health check endpoints

#### Repository Tests
- **FruitRepositoryTest**: Tests fruit database operations
- **VegetableRepositoryTest**: Tests vegetable database operations

#### Cache Tests
- **FruitCacheServiceIntegrationTest**: Tests fruit caching
- **VegetableCacheServiceIntegrationTest**: Tests vegetable caching

## Test Data Management

### Database Setup
- Tests use separate test database
- Database is reset before each test
- Migrations are run automatically

### Test Data
- Uses realistic test data from `request.json`
- Custom test data for specific scenarios
- Temporary files for file-based tests

## Running Tests

### All Tests
```bash
docker-compose -f docker/docker-compose.yml exec php php bin/phpunit
```

### Specific Test Categories
```bash
# Unit tests only
docker-compose -f docker/docker-compose.yml exec php php bin/phpunit tests/Unit/

# Integration tests only
docker-compose -f docker/docker-compose.yml exec php php bin/phpunit tests/Integration/

# Specific test file
docker-compose -f docker/docker-compose.yml exec php php bin/phpunit tests/Integration/Application/Service/ImportProductsServiceIntegrationTest.php
```

### Coverage Reports
- HTML coverage reports generated in `coverage/`
- Clover XML reports for CI/CD integration
- Coverage thresholds enforced in CI

## Best Practices

### Test Naming
- Use descriptive test method names
- Follow pattern: `test[Scenario][ExpectedResult]`
- Example: `testProcessFileContentWithRequestJson()`

### Assertions
- Use specific assertions for better error messages
- Test both positive and negative scenarios
- Verify data integrity and business rules

### Documentation
- Document complex test scenarios
- Explain expected results and edge cases
- Include business context in test comments

### Performance
- Keep tests fast and focused
- Use appropriate test data sizes
- Clean up resources after tests

## Continuous Integration

### Automated Testing
- All tests run on every commit
- Coverage reports generated automatically
- Quality gates enforce minimum coverage

### Test Environment
- Isolated test environment
- Separate test database
- No external dependencies

## Future Enhancements

### Planned Improvements
- Add more edge case tests
- Implement performance benchmarks
- Add contract testing for external APIs
- Expand E2E test coverage

### Monitoring
- Track test execution times
- Monitor coverage trends
- Identify flaky tests