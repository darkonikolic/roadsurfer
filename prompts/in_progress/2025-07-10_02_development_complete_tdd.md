# Development - Fruits and Vegetables Service Implementation

⚠️ **IMPORTANT**: This prompt must strictly follow all execution rules defined in [`rules.md`](./rules.md).  
No assumptions, no hallucinations, no duplication of prompt 2 logic, and strict adherence to KISS/DRY/YAGNI/SOLID principles is required.

## 🎯 Goal from README.md
Build a service that:
- Processes `request.json` and creates separate collections for Fruits and Vegetables
- Each collection has `add()`, `remove()`, `list()` methods
- Units stored as grams
- Store collections in database
- Provide API endpoints to query and add items
- Optional: unit conversion (kg/g), search functionality

## 📊 **EXECUTION SUMMARY TABLE**

| Phase | Description | Priority | Estimated Time |
|-------|-------------|----------|----------------|
| 1 | Symfony Upgrade & DTOs | [#core] | 1 hour |
| 2 | JSON Processing Services | [#core] | 1.5 hours |
| 2.5 | Console Command for JSON Processing | [#core] | 0.5 hours |
| 3 | Database Entities & Persistence | [#core] | 1 hour |
| 4 | Caching Infrastructure | [#optional] | 0.5 hours |
| 5 | Application Services | [#core] | 1 hour |
| 6 | API Endpoints | [#core] | 1 hour |
| 7 | API Documentation | [#bonus] | 0.5 hours |
| 8 | Integration & E2E Testing | [#bonus] | 1 hour |

**MVP Requirements:** Phases 1, 2, 2.5, 3, 5, 6 (5 hours total) - **5/6 COMPLETED**
**Optional Enhancements:** Phases 4, 7, 8 (2 hours additional) - **2/3 COMPLETED**

## 💡 **MAJOR FOCUS: Development Principles & Constraints**
* **KISS, DRY, YAGNI, SOLID principles** - Keep it simple, avoid duplication, don't over-engineer
* **Clean domain model** - No unnecessary code duplication or complexity
* **Input validation** - Think carefully about how to handle and validate all inputs
* **Good practices** - No logic in controllers, proper information hiding
* **Timebox: 3-4 hours** - Focus on essential features, avoid over-engineering
* **Comprehensive testing** - All code must be tested thoroughly
* **Simple persistence** - No bonus points for complex persistence methods, keep it straightforward

## ✅ What's Already Done
- [x] Basic Symfony clean architecture structure
- [x] Health endpoint (`GET /health`)
- [x] Basic tests for health, logging, database
- [x] Docker environment and configuration
- [x] Code quality tools (PHPStan, Psalm, PHPMD)
- [x] Clean architecture layers (Application, Domain, Infrastructure, Presentation, Shared)
- [x] BDD-style testing approach with organized test structure
- [x] 12 tests passing with 40 assertions
- [x] Symfony 7.0 LTS upgrade completed successfully
- [x] All DTOs created and tested (Product, ProductList, Fruit, FruitList, Vegetable, VegetableList)
- [x] JSON processing services implemented with 100% test coverage
- [x] Unit conversion service (kg to g) implemented
- [x] Product splitting service implemented
- [ ] Console command for processing request.json with custom path support
- [x] Database entities and repositories created
- [x] Redis caching infrastructure implemented
- [x] Application services (FruitManagementService, VegetableManagementService) implemented
- [x] REST API endpoints with validation implemented
- [x] Nelmio API documentation accessible at `/api/doc`
- [ ] Comprehensive test coverage for all components
- [ ] Integration and end-to-end testing completed

## 🚀 Development Roadmap

### Phase 1: Symfony Upgrade and DTOs (TDD First) [#core] ✅ **COMPLETED**
**Goal:** Establish foundation with upgraded Symfony and data transfer objects for type safety
**Estimated time:** 1 hour

- [x] [#core] Upgrade Symfony from 6.0 to latest LTS (7.0) while preserving all functionality
- [x] [#core] Create `ProductDTO` with properties (id, name, type, quantity, unit)
- [x] [#core] Create `ProductListDTO` to hold array of ProductDTOs
- [x] [#core] Create `FruitDTO` extending ProductDTO with fruit-specific validation
- [x] [#core] Create `FruitListDTO` to hold array of FruitDTOs
- [x] [#core] Create `VegetableDTO` extending ProductDTO with vegetable-specific validation
- [x] [#core] Create `VegetableListDTO` to hold array of VegetableDTOs
- [x] [#core] Write unit tests for all DTOs with validation

### Phase 2: JSON Processing Services (TDD First) [#core] ✅ **COMPLETED**
**Goal:** Process incoming JSON data and convert units from kilograms to grams
**Estimated time:** 1.5 hours

- [x] [#core] Create `JsonToProductListService` that takes JSON string and produces valid `ProductListDTO`
- [x] [#core] Create `UnitConversionService` that converts all product units from kilograms to grams
- [x] [#core] Create `ProductSplitterService` that splits `ProductListDTO` into `FruitListDTO` and `VegetableListDTO` based on type
- [x] [#core] Ensure `ProductListDTO` is not stored in database (only used for processing)
- [x] [#core] Write comprehensive unit tests for all services with 100% test coverage
- [x] [#core] Validate JSON payload structure and ensure every item in array is valid before processing

### Phase 2.5: Console Command for JSON Processing (TDD First) [#core] ❌ **NOT COMPLETED**
**Goal:** Create a Symfony console command that reads request.json file and processes the data
**Estimated time:** 0.5 hours

- [ ] [#core] Create `ProcessRequestJsonCommand` that reads JSON file from default path (`request.json`)
- [ ] [#core] Allow custom file path as optional argument to override default path
- [ ] [#core] Integrate with existing JSON processing services (`JsonToProductListService`, `UnitConversionService`, `ProductSplitterService`)
- [ ] [#core] Process the JSON data and display summary of fruits and vegetables found
- [ ] [#core] Add option to save processed data to database (fruits and vegetables)
- [ ] [#core] Write comprehensive tests for the command with mocked services
- [ ] [#core] Add proper error handling for file not found, invalid JSON, etc.
- [ ] [#core] Add command to Makefile for easy execution

### Phase 3: Database Entities and Persistence (TDD First) [#core] ✅ **COMPLETED**
**Goal:** Create database schema and persistence layer for fruits and vegetables
**Estimated time:** 1 hour

- [x] [#core] Create `Fruit` entity with properties (id, name, quantity in grams, created_at, updated_at)
- [x] [#core] Create `Vegetable` entity with properties (id, name, quantity in grams, created_at, updated_at)
- [x] [#core] Create database migrations for separate fruits and vegetables tables
- [x] [#core] Create `FruitRepository` with search methods
- [x] [#core] Create `VegetableRepository` with search methods
- [ ] [#core] Write integration tests for entities and repositories

### Phase 4: Caching Infrastructure (TDD First) [#optional] ✅ **COMPLETED**
**Goal:** Implement Redis caching for improved performance on search operations
**Estimated time:** 0.5 hours

- [x] [#optional] Configure Redis for caching search results
- [x] [#optional] Create `FruitCacheService` for caching fruit search results
- [x] [#optional] Create `VegetableCacheService` for caching vegetable search results
- [x] [#optional] Implement cache invalidation on insert/update operations
- [ ] [#optional] Write tests for caching functionality

### Phase 5: Application Services (TDD First) [#core] ✅ **COMPLETED**
**Goal:** Implement business logic services for managing fruits and vegetables
**Estimated time:** 1 hour

- [x] [#core] Create `FruitManagementService` for adding/removing fruits with cache invalidation
- [x] [#core] Create `VegetableManagementService` for adding/removing vegetables with cache invalidation
- [x] [#core] Create `FruitSearchService` for searching fruits with Redis caching
- [x] [#core] Create `VegetableSearchService` for searching vegetables with Redis caching
- [x] [#core] Write service tests with mocked repositories and cache

### Phase 6: API Endpoints (TDD First) [#core] ✅ **COMPLETED**
**Goal:** Create REST API endpoints for fruits and vegetables with validation
**Estimated time:** 1 hour

- [x] [#core] Create `FruitController` with endpoints:
  - `GET /api/fruits` (list with search parameters)
  - `POST /api/fruits` (add new fruit with validation)
  - `DELETE /api/fruits/{id}` (remove fruit by ID)
- [x] [#core] Create `VegetableController` with endpoints:
  - `GET /api/vegetables` (list with search parameters)
  - `POST /api/vegetables` (add new vegetable with validation)
  - `DELETE /api/vegetables/{id}` (remove vegetable by ID)
- [x] [#core] Create request/response DTOs for API endpoints
- [x] [#core] Add comprehensive input validation for all endpoints
- [ ] [#core] Write controller tests with mocked services
- [x] [#core] Add Nelmio API documentation annotations for all endpoints

### Phase 7: API Documentation (TDD First) [#bonus] ✅ **COMPLETED**
**Goal:** Provide interactive API documentation for testing and integration
**Estimated time:** 0.5 hours

- [x] [#bonus] Install and configure Nelmio API documentation bundle
- [x] [#bonus] Create API documentation for all endpoints at `/api/doc`
- [x] [#bonus] Ensure all endpoints are testable via Nelmio documentation
- [x] [#bonus] Add example requests and responses for each endpoint
- [ ] [#bonus] Write tests for API documentation accessibility

### Phase 8: Integration & End-to-End Testing [#bonus] ❌ **NOT COMPLETED**
**Goal:** Comprehensive testing of complete workflow and performance validation
**Estimated time:** 1 hour

- [ ] [#bonus] Create integration tests for GET endpoints without filters
- [ ] [#bonus] Create integration tests for GET endpoints with ID filter
- [ ] [#bonus] Create integration tests for GET endpoints with name filter
- [ ] [#bonus] Create integration tests for GET endpoints with combined filters
- [ ] [#bonus] Create integration tests for POST endpoints with valid data
- [ ] [#bonus] Create integration tests for POST endpoints with invalid data
- [ ] [#bonus] Create integration tests for DELETE endpoints with valid ID
- [ ] [#bonus] Create integration tests for DELETE endpoints with invalid ID
- [ ] [#bonus] Create integration tests for Vegetable endpoints (same as fruits)
- [ ] [#bonus] Create integration tests for JSON processing workflow
- [ ] [#bonus] Create integration tests for cache functionality
- [ ] [#bonus] Test database operations directly via repository
- [ ] [#bonus] Validate HTTP status codes and JSON responses
- [ ] [#bonus] Test error handling and edge cases
- [ ] [#bonus] Performance testing for large datasets

## 🧪 TDD Approach for Each Feature
1. **Write Test First** (Red) - Define expected behavior
2. **Make Test Pass** (Green) - Minimal implementation
3. **Refactor** - Improve code quality
4. **Document** - Update API documentation

## 📋 Implementation Checklist
- [x] Upgrade Symfony to latest LTS (7.0) with full compatibility
- [x] Create all required DTOs (Product, ProductList, Fruit, FruitList, Vegetable, VegetableList)
- [x] Implement JSON processing service with 100% test coverage
- [x] Implement unit conversion service (kg to g)
- [x] Implement product splitting service
- [ ] Create console command for processing request.json with custom path support
- [x] Create separate database tables for fruits and vegetables
- [x] Implement Redis caching for search results with invalidation
- [x] Create REST API endpoints with validation
- [x] Add Nelmio API documentation for all endpoints
- [ ] Write integration tests for all endpoints (GET/POST/DELETE with filters)
- [ ] Write integration tests for JSON processing workflow
- [ ] Write integration tests for cache functionality
- [x] Preserve Makefile usage and BDD testing approach

## ⚡ **DEVELOPMENT CONSTRAINTS & FOCUS**
- [ ] **KISS Principle**: Keep implementation simple, avoid over-engineering
- [ ] **DRY Principle**: No code duplication, reuse common patterns
- [ ] **YAGNI Principle**: Only implement what's needed, no premature optimization
- [ ] **SOLID Principles**: Proper separation of concerns, single responsibility
- [ ] **Clean Domain Model**: Focus on business logic, avoid unnecessary complexity
- [ ] **Input Validation**: Comprehensive validation for all JSON inputs and API endpoints
- [ ] **Controller Logic**: Keep controllers thin, move business logic to services
- [ ] **Timebox Adherence**: Complete implementation within 3-4 hours
- [ ] **Simple Persistence**: Straightforward database operations, no complex patterns
- [ ] **Comprehensive Testing**: 100% test coverage for critical services

## 🎯 Success Criteria
- [x] Symfony 7.0 LTS upgrade completed successfully
- [x] Service can process `request.json` and create separate collections
- [x] All units converted from kg to g automatically
- [x] Data stored in separate database tables
- [x] API endpoints work with search and validation
- [x] Redis caching implemented with proper invalidation
- [ ] Integration tests pass for all endpoints (GET/POST/DELETE with filters)
- [ ] Integration tests pass for JSON processing workflow
- [ ] Integration tests pass for cache functionality
- [x] Nelmio API documentation accessible at `/api/doc`
- [x] Code follows clean architecture principles
- [x] Makefile and BDD approach preserved
- [ ] Console command for processing request.json with custom path support

## 📝 Notes
- **KISS & YAGNI**: Focus on essential features only, avoid over-engineering
- **DRY**: Reuse common patterns across DTOs and services
- **SOLID**: Keep controllers thin, services focused, proper separation of concerns
- **Clean Domain Model**: Focus on business logic without unnecessary complexity
- **Input Validation**: Comprehensive validation for JSON processing and API endpoints
- **Simple Persistence**: Straightforward database operations, no complex patterns
- **Timebox**: Complete implementation within 3-4 hours, prioritize essential features
- **Integration Testing**: Test real database operations via repository, validate HTTP responses
- **Backward Compatibility**: Maintain functionality during Symfony upgrade
- [ ] Test Strategy: Focus on integration tests that cover complete workflow from HTTP to database 