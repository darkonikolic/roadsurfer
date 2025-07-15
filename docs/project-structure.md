# 📁 Project Structure

## 🏗️ Overview

This document describes the complete project structure for the Roadster Symfony application with Clean Architecture principles.

## 📂 Directory Structure

```
roadster/
├── docker/                    # 🐳 Docker development environment
│   ├── docker-compose.yml     # Multi-service setup
│   ├── Dockerfile             # PHP-FPM development image
│   ├── php.ini               # PHP configuration
│   └── nginx.conf            # Nginx web server config
├── prompts/                   # 🤖 AI Development Prompts
│   ├── in_progress/          # Active development prompts
│   │   └── 2025-07-10_02_development_complete_tdd.md
│   ├── backlog/              # Planned development prompts
│   │   ├── 2025-07-10_05_further_development_advanced.md
│   │   └── 2025-07-10_06_deployment_infrastructure_strategy.md
│   ├── done/                 # Completed prompts
│   │   └── 2025-07-10_01_infrastructure_base_testing.md
│   └── rules.md              # AI execution rules
├── queries/                   # 🔄 Query files for prompt regeneration
│   ├── 2025-07-10_01_infrastructure_base_testing.md
│   ├── 2025-07-10_02_development_complete_tdd.md
│   ├── 2025-07-10_05_further_development_advanced.md
│   ├── 2025-07-10_06_deployment_infrastructure_strategy.md
│   └── rules.md              # Query for rules.md generation
├── roadsurfer-com/           # 📦 Symfony Application
│   ├── src/                 # Clean Architecture Source Code
│   │   ├── Application/     # Application Layer (Use Cases)
│   │   │   ├── Command/     # Command handlers
│   │   │   ├── Query/       # Query handlers
│   │   │   └── Service/     # Application services
│   │   ├── Domain/          # Domain Layer (Business Logic)
│   │   │   ├── Entity/      # Domain entities
│   │   │   ├── Repository/  # Repository interfaces
│   │   │   ├── Service/     # Domain services
│   │   │   ├── ValueObject/ # Value objects
│   │   │   └── Event/       # Domain events
│   │   ├── Infrastructure/  # Infrastructure Layer (External Concerns)
│   │   │   ├── Persistence/ # Database implementations
│   │   │   │   ├── Repository/ # Repository implementations
│   │   │   │   ├── Entity/  # Database entities
│   │   │   │   └── Migration/ # Database migrations
│   │   │   ├── External/    # External services
│   │   │   │   ├── Redis/   # Redis implementations
│   │   │   │   └── Health/  # Health check implementations
│   │   │   └── Configuration/ # Infrastructure config
│   │   ├── Shared/          # Shared Kernel
│   │   │   ├── Exception/   # Custom exceptions
│   │   │   ├── DTO/         # Data Transfer Objects
│   │   │   └── Service/     # Shared services
│   │   └── Presentation/    # Presentation Layer (HTTP)
│   │       ├── Controller/  # HTTP controllers
│   │       ├── Request/     # Form requests
│   │       ├── Response/    # Response objects
│   │       └── Template/    # View templates
│   ├── tests/               # Test files organized by layers
│   │   ├── Infrastructure/  # Infrastructure tests
│   │   │   ├── LoggingTest.php
│   │   │   └── TestDatabaseConnectionTest.php
│   │   └── Presentation/    # Presentation tests
│   │       └── HealthCheckTest.php
│   ├── config/              # Configuration files
│   ├── public/              # Web root
│   └── composer.json        # PHP dependencies
├── docs/                    # 📚 Documentation
│   ├── quickstart.md        # Quick start guide
│   ├── code_quality.md      # Code quality standards
│   └── project-structure.md # This file
└── Makefile                 # Build automation
```

## 🏛️ Clean Architecture Layers

For detailed Clean Architecture information, see **[Clean Architecture Guide](clean-architecture.md)**.

### **Application Layer** (`src/Application/`)
- **Purpose**: Orchestrates use cases and coordinates between layers
- **Contains**: Command/Query handlers, Application services
- **Example**: `HealthService` coordinates health checks from different sources

### **Domain Layer** (`src/Domain/`)
- **Purpose**: Contains business logic and rules
- **Contains**: Entities, Domain services, Value objects, Repository interfaces
- **Status**: Structure ready, entities pending

### **Infrastructure Layer** (`src/Infrastructure/`)
- **Purpose**: Handles external concerns (database, external APIs, etc.)
- **Contains**: Database implementations, External service adapters
- **Example**: `DatabaseHealthService` implements database-specific health checks

### **Presentation Layer** (`src/Presentation/`)
- **Purpose**: Handles HTTP requests and responses
- **Contains**: Controllers, Request/Response objects, Templates
- **Example**: `HealthController` handles HTTP requests to health endpoints

### **Shared Layer** (`src/Shared/`)
- **Purpose**: Common utilities and DTOs used across layers
- **Contains**: DTOs, Exceptions, Shared services
- **Example**: `HealthStatusDTO` used for data transfer between layers

## 🤖 AI Development System

### **Prompts Directory** (`prompts/`)
- **Purpose**: Contains AI development prompts organized by status
- **Structure**:
  - `in_progress/` - Active development prompts
  - `backlog/` - Planned development prompts
  - `done/` - Completed prompts
  - `rules.md` - AI execution rules

### **Queries Directory** (`queries/`)
- **Purpose**: Contains query files that can regenerate all prompts without loss of structure or data
- **Naming Convention**: Query files have identical names to their corresponding prompts
- **Usage**: Each query contains complete specifications to regenerate the original prompt

### **Available Query Files**:
- `2025-07-10_01_infrastructure_base_testing.md` - Infrastructure setup and testing
- `2025-07-10_02_development_complete_tdd.md` - Fruits/Vegetables service with TDD
- `2025-07-10_05_further_development_advanced.md` - Enterprise infrastructure roadmap
- `2025-07-10_06_deployment_infrastructure_strategy.md` - Production CI/CD architecture
- `rules.md` - AI execution rules with best practices

## 🐳 Docker Development Environment

For detailed Docker environment information, see **[Docker Environment Guide](docker-environment.md)**.

### **Services**:
- **Nginx** (8080) - Web server with PHP-FPM
- **PHP-FPM** (9000) - PHP 8.3 application server
- **PostgreSQL** (5432) - Database server
- **Redis** (6379) - Cache server

### **Configuration Files**:
- `docker-compose.yml` - Multi-service setup
- `Dockerfile` - PHP-FPM development image
- `php.ini` - PHP configuration
- `nginx.conf` - Nginx web server config

## 🧪 Testing Structure

For comprehensive testing information, see **[Testing Strategy Guide](testing-strategy.md)**.

### **Test Organization by Layers**:
- **`tests/Infrastructure/`** - Database, logging, external services tests
- **`tests/Presentation/`** - HTTP endpoints, controller tests
- **`tests/Application/`** - Use case and service tests (pending)
- **`tests/Domain/`** - Domain logic tests (pending)

### **Current Test Coverage**:
- ✅ **12 tests passing** with 40 assertions
- ✅ **Health endpoint tests** - Complete functionality
- ✅ **Infrastructure tests** - Database and logging
- ⚠️ **Domain tests** - Pending domain entities
- ⚠️ **Application tests** - Pending use cases

## 🔧 Development Tools

### **Build Automation**:
- `Makefile` - Centralized build automation
- `composer.json` - PHP dependencies
- `composer.lock` - Locked dependency versions

### **Code Quality Tools**:
- **PHPStan** - Static analysis (Level 6)
- **Psalm** - Type checking and error detection
- **PHP CS Fixer** - Code formatting
- **PHPMD** - Mess detection
- **PHPCPD** - Copy-paste detection
- **PHPMetrics** - Code metrics

## 📚 Documentation

### **Documentation Files**:
- `docs/quickstart.md` - Quick start guide
- `docs/code_quality.md` - Code quality standards
- `docs/project-structure.md` - This file
- `docs/clean-architecture.md` - Clean Architecture guide
- `docs/docker-environment.md` - Docker environment guide
- `docs/testing-strategy.md` - Testing strategy guide
- `docs/development-commands.md` - Development commands reference
- `docs/troubleshooting.md` - Troubleshooting guide
- `docs/environment-config.md` - Environment configuration guide

### **Additional Resources**:
- `README.md` - Main project overview
- `prompts/README.md` - Prompts organization guide

## 🎯 Key Principles

### **Clean Architecture**:
- **Dependency Direction**: Dependencies point inward (Domain → Application → Infrastructure)
- **Single Responsibility**: Each class has one reason to change
- **Dependency Injection**: Use constructor injection for dependencies
- **Immutable DTOs**: Use readonly classes for data transfer
- **Type Safety**: Leverage PHP 8.3+ strict typing features

### **AI Development System**:
- **Zero Data Loss**: Complete regeneration without missing details
- **Consistency**: All prompts follow same structure and rules
- **Maintainability**: Easy to update and modify prompts
- **Enterprise Ready**: All queries include enterprise best practices 