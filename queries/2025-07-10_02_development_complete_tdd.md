# QUERY TO GENERATE PROMPT 2
⚠️ The resulting prompt must strictly follow `rules.md`

## 🎯 OBJECTIVE
Generate a comprehensive development prompt for implementing a Fruits and Vegetables Service in Symfony with TDD approach.

## 📋 REQUIREMENTS

### **Core Structure:**
- Title: "Development - Fruits and Vegetables Service Implementation"
- Status: "IN_PROGRESS"
- Must include rules.md enforcement clause at the top
- Must be placed in `prompts/backlog/` directory

### **Goal Section:**
Build a service that:
- Processes `request.json` and creates separate collections for Fruits and Vegetables
- Each collection has `add()`, `remove()`, `list()` methods
- Units stored as grams
- Store collections in database
- Provide API endpoints to query and add items
- Optional: unit conversion (kg/g), search functionality

### **Execution Summary Table:**
Include a table with columns: Phase | Description | Priority | Estimated Time
- Phase 1: Symfony Upgrade & DTOs | [#core] | 1 hour
- Phase 2: JSON Processing Services | [#core] | 1.5 hours
- Phase 3: Database Entities & Persistence | [#core] | 1 hour
- Phase 4: Caching Infrastructure | [#optional] | 0.5 hours
- Phase 5: Application Services | [#core] | 1 hour
- Phase 6: API Endpoints | [#core] | 1 hour
- Phase 7: API Documentation | [#bonus] | 0.5 hours
- Phase 8: Integration & E2E Testing | [#bonus] | 1 hour

Include MVP Requirements: Phases 1, 2, 3, 5, 6 (4.5 hours total)
Include Optional Enhancements: Phases 4, 7, 8 (2 hours additional)

### **Development Principles & Constraints:**
- KISS, DRY, YAGNI, SOLID principles
- Clean domain model
- Input validation
- Good practices
- Timebox: 3-4 hours
- Comprehensive testing
- Simple persistence

### **What's Already Done Section:**
- Basic Symfony clean architecture structure
- Health endpoint (`GET /health`)
- Basic tests for health, logging, database
- Docker environment and configuration
- Code quality tools (PHPStan, Psalm, PHPMD)
- Clean architecture layers (Application, Domain, Infrastructure, Presentation, Shared)
- BDD-style testing approach with organized test structure
- 12 tests passing with 40 assertions

### **Development Roadmap with 8 Phases:**

#### **Phase 1: Symfony Upgrade and DTOs (TDD First) [#core]**
**Goal:** Establish foundation with upgraded Symfony and data transfer objects for type safety
**Estimated time:** 1 hour
- [#core] Upgrade Symfony from 6.0 to latest LTS (7.0) while preserving all functionality
- [#core] Create `ProductDTO` with properties (id, name, type, quantity, unit)
- [#core] Create `ProductListDTO` to hold array of ProductDTOs
- [#core] Create `FruitDTO` extending ProductDTO with fruit-specific validation
- [#core] Create `FruitListDTO` to hold array of FruitDTOs
- [#core] Create `VegetableDTO` extending ProductDTO with vegetable-specific validation
- [#core] Create `VegetableListDTO` to hold array of VegetableDTOs
- [#core] Write unit tests for all DTOs with validation

#### **Phase 2: JSON Processing Services (TDD First) [#core]**
**Goal:** Process incoming JSON data and convert units from kilograms to grams
**Estimated time:** 1.5 hours
- [#core] Create `JsonToProductListService` that takes JSON string and produces valid `ProductListDTO`
- [#core] Create `UnitConversionService` that converts all product units from kilograms to grams
- [#core] Create `ProductSplitterService` that splits `ProductListDTO` into `FruitListDTO` and `VegetableListDTO` based on type
- [#core] Ensure `ProductListDTO` is not stored in database (only used for processing)
- [#core] Write comprehensive unit tests for all services with 100% test coverage
- [#core] Validate JSON payload structure and ensure every item in array is valid before processing

#### **Phase 3: Database Entities and Persistence (TDD First) [#core]**
**Goal:** Create database schema and persistence layer for fruits and vegetables
**Estimated time:** 1 hour
- [#core] Create `Fruit` entity with properties (id, name, quantity in grams, created_at, updated_at)
- [#core] Create `Vegetable` entity with properties (id, name, quantity in grams, created_at, updated_at)
- [#core] Create database migrations for separate fruits and vegetables tables
- [#core] Create `FruitRepository` with search methods
- [#core] Create `VegetableRepository` with search methods
- [#core] Write integration tests for entities and repositories

#### **Phase 4: Caching Infrastructure (TDD First) [#optional]**
**Goal:** Implement Redis caching for improved performance on search operations
**Estimated time:** 0.5 hours
- [#optional] Configure Redis for caching search results
- [#optional] Create `FruitCacheService` for caching fruit search results
- [#optional] Create `VegetableCacheService` for caching vegetable search results
- [#optional] Implement cache invalidation on insert/update operations
- [#optional] Write tests for caching functionality

#### **Phase 5: Application Services (TDD First) [#core]**
**Goal:** Implement business logic services for managing fruits and vegetables
**Estimated time:** 1 hour
- [#core] Create `FruitManagementService` for adding/removing fruits with cache invalidation
- [#core] Create `VegetableManagementService` for adding/removing vegetables with cache invalidation
- [#core] Create `FruitSearchService` for searching fruits with Redis caching
- [#core] Create `VegetableSearchService` for searching vegetables with Redis caching
- [#core] Write service tests with mocked repositories and cache

#### **Phase 6: API Endpoints (TDD First) [#core]**
**Goal:** Create REST API endpoints for fruits and vegetables with validation
**Estimated time:** 1 hour
- [#core] Create `FruitController` with endpoints:
  - `GET /api/fruits` (list with search parameters)
  - `POST /api/fruits` (add new fruit with validation)
- [#core] Create `VegetableController` with endpoints:
  - `GET /api/vegetables` (list with search parameters)
  - `POST /api/vegetables` (add new vegetable with validation)
- [#core] Create request/response DTOs for API endpoints
- [#core] Add comprehensive input validation for all endpoints
- [#core] Write controller tests with mocked services

#### **Phase 7: API Documentation (TDD First) [#bonus]**
**Goal:** Provide interactive API documentation for testing and integration
**Estimated time:** 0.5 hours
- [#bonus] Install and configure Nelmio API documentation bundle
- [#bonus] Create API documentation for all endpoints at `/api/doc`
- [#bonus] Ensure all endpoints are testable via Nelmio documentation
- [#bonus] Add example requests and responses for each endpoint
- [#bonus] Write tests for API documentation accessibility

#### **Phase 8: Integration & End-to-End Testing [#bonus]**
**Goal:** Comprehensive testing of complete workflow and performance validation
**Estimated time:** 1 hour
- [#bonus] Create feature tests for complete JSON processing workflow
- [#bonus] Test `request.json` processing end-to-end with unit conversion
- [#bonus] Test API endpoints with real data and caching
- [#bonus] Test cache invalidation on database operations
- [#bonus] Performance testing for large datasets

### **TDD Approach Section:**
1. **Write Test First** (Red) - Define expected behavior
2. **Make Test Pass** (Green) - Minimal implementation
3. **Refactor** - Improve code quality
4. **Document** - Update API documentation

### **Implementation Checklist:**
- Upgrade Symfony to latest LTS (7.0) with full compatibility
- Create all required DTOs (Product, ProductList, Fruit, FruitList, Vegetable, VegetableList)
- Implement JSON processing service with 100% test coverage
- Implement unit conversion service (kg to g)
- Implement product splitting service
- Create separate database tables for fruits and vegetables
- Implement Redis caching for search results with invalidation
- Create REST API endpoints with validation
- Add Nelmio API documentation for all endpoints
- Write comprehensive tests (unit, integration, feature)
- Preserve Makefile usage and BDD testing approach

### **Development Constraints & Focus:**
- KISS Principle: Keep implementation simple, avoid over-engineering
- DRY Principle: No code duplication, reuse common patterns
- YAGNI Principle: Only implement what's needed, no premature optimization
- SOLID Principles: Proper separation of concerns, single responsibility
- Clean Domain Model: Focus on business logic, avoid unnecessary complexity
- Input Validation: Comprehensive validation for all JSON inputs and API endpoints
- Controller Logic: Keep controllers thin, move business logic to services
- Timebox Adherence: Complete implementation within 3-4 hours
- Simple Persistence: Straightforward database operations, no complex patterns
- Comprehensive Testing: 100% test coverage for critical services

### **Success Criteria:**
- Symfony 7.0 LTS upgrade completed successfully
- Service can process `request.json` and create separate collections
- All units converted from kg to g automatically
- Data stored in separate database tables
- API endpoints work with search and validation
- Redis caching implemented with proper invalidation
- All tests pass with 100% coverage for critical services
- Nelmio API documentation accessible at `/api/doc`
- Code follows clean architecture principles
- Makefile and BDD approach preserved

### **Notes Section:**
- KISS & YAGNI: Focus on essential features only, avoid over-engineering
- DRY: Reuse common patterns across DTOs and services
- SOLID: Keep controllers thin, services focused, proper separation of concerns
- Clean Domain Model: Focus on business logic without unnecessary complexity
- Input Validation: Comprehensive validation for JSON processing and API endpoints
- Simple Persistence: Straightforward database operations, no complex patterns
- Timebox: Complete implementation within 3-4 hours, prioritize essential features
- Testing: 100% test coverage for critical services, especially JSON processing
- Backward Compatibility: Maintain functionality during Symfony upgrade

## 🎯 OUTPUT REQUIREMENTS
- Generate the complete prompt file with all sections, phases, and technical details
- Maintain exact structure and formatting
- Include all priority indicators, time estimates, and goal descriptions
- Place the generated prompt in `prompts/backlog/` directory
- Ensure all technical specifications and class names are preserved exactly 