# Quick Start Guide

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose installed
- Git installed

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/darkonikolic/roadsurfer
   ```

2. **Navigate to project directory:**
   ```bash
   cd roadsurfer
   ```

3. **Start the application:**
   ```bash
   make up
   ```

4. **Install dependencies and setup database:**
   ```bash
   make install
   ```

5. **⚠️ IMPORTANT: Recreate both databases to ensure proper environment setup:**
   ```bash
   make db-recreate-all
   ```
   This step is **required** for both development and test environments to work properly.

6. **Verify quality pipeline:**
   ```bash
   make quality-pipeline
   ```
   This runs all code quality checks (PHP CS Fixer, Psalm, PHPStan, PHPMD, PHPCPD, PHPUnit).

### Access Points

- **Main Application:** http://localhost:8080
- **API Documentation:** http://localhost:8080/api/doc
- **PHPMyAdmin:** http://localhost:8081
- **Redis Commander:** http://localhost:8082 (admin/admin123)
- **Health Check:** http://localhost:8080/health

### Development Commands

- **`make shell`** - Enter application shell
- **`make test`** - Run all tests
- **`make quality-pipeline`** - Run code quality checks
- **`make db-recreate-all`** - Recreate both development and test databases

### Quality Pipeline Components

The quality pipeline includes:
- **PHP CS Fixer** - Code formatting (safe configuration, no backslash addition)
- **Psalm** - Static analysis with type checking
- **PHPStan** - Static analysis for error detection
- **PHPMD** - Code smell detection
- **PHPCPD** - Duplicate code detection
- **PHPUnit** - Unit and integration tests

### Troubleshooting

If you encounter port conflicts, the application uses:
- **MySQL:** Port 3307 (instead of default 3306)
- **Redis:** Port 6380 (instead of default 6379)

**If quality pipeline fails:**
1. Ensure databases are recreated: `make db-recreate-all`
2. Check for missing imports in PHP files
3. Verify all tests pass: `make test`

---

## 🚀 Getting Started

### Prerequisites
- Docker and Docker Compose
- Make (optional, but recommended)

### 1. Initial Setup
```bash
# Clone the repository
git clone <repository-url>
cd roadster

# Start the development environment
make up

# Install dependencies
make install
```

### 2. Database Setup

For detailed database setup instructions, see **[Development Commands Reference](development-commands.md)**.

```bash
# Recreate development database (drops and recreates)
make db-recreate-dev

# Recreate test database (drops and recreates)
make db-recreate-test

# Recreate both databases
make db-recreate-all
```

### 3. Running Tests

For comprehensive testing information, see **[Testing Strategy Guide](testing-strategy.md)**.

```bash
# Run all tests
make test

# Run specific test
make test TEST=tests/Infrastructure/TestDatabaseConnectionTest.php
```

### 4. Development Commands

For complete command reference, see **[Development Commands Reference](development-commands.md)**.

#### **Essential Commands:**
```bash
# Start development environment
make up

# Run tests
make test

# Code quality checks
make quality

# Process request.json file
make process-json

# Show all available commands
make help
```

#### **API Access:**
- **API Documentation**: [http://localhost:8080/api/doc](http://localhost:8080/api/doc) - Interactive API documentation
- **API JSON Schema**: [http://localhost:8080/api/doc.json](http://localhost:8080/api/doc.json) - API schema in JSON format
- **Health Check**: [http://localhost:8080/health](http://localhost:8080/health) - Application health status

### 5. Environment Configuration

For detailed environment configuration, see **[Environment Configuration Guide](environment-config.md)**.

### 6. Troubleshooting

For comprehensive troubleshooting guide, see **[Troubleshooting Guide](troubleshooting.md)**.

## 📁 Project Structure

For detailed project structure information, see **[Project Structure Documentation](project-structure.md)**.

The project follows Clean Architecture principles with the following key directories:

- **`docker/`** - Docker development environment (see [Docker Environment Guide](docker-environment.md))
- **`prompts/`** - AI development prompts organized by status
- **`queries/`** - Query files for prompt regeneration
- **`roadsurfer-com/`** - Symfony application with Clean Architecture layers (see [Clean Architecture Guide](clean-architecture.md))
- **`docs/`** - Project documentation
- **`Makefile`** - Build automation

## 🔧 Development Workflow

### **Prompt Management with Queries**

The project includes a comprehensive prompt management system with query files for regeneration:

#### **Queries Directory** (`queries/`)
- **Purpose**: Contains query files that can regenerate all prompts without loss of structure or data
- **Naming Convention**: Query files have identical names to their corresponding prompts
- **Usage**: Each query contains complete specifications to regenerate the original prompt

#### **Available Query Files**:
- `2025-07-10_01_infrastructure_base_testing.md` - Infrastructure setup and testing
- `2025-07-10_02_development_complete_tdd.md` - Fruits/Vegetables service with TDD
- `2025-07-10_05_further_development_advanced.md` - Enterprise infrastructure roadmap
- `2025-07-10_06_deployment_infrastructure_strategy.md` - Production CI/CD architecture
- `rules.md` - AI execution rules with best practices

#### **Query Structure**:
Each query file contains:
- **Objective**: Clear purpose of the prompt
- **Requirements**: Complete specifications for regeneration
- **Output Requirements**: Format and placement instructions
- **Technical Details**: All implementation specifics preserved

#### **Benefits**:
- ✅ **Zero Data Loss**: Complete regeneration without missing details
- ✅ **Consistency**: All prompts follow same structure and rules
- ✅ **Maintainability**: Easy to update and modify prompts
- ✅ **Enterprise Ready**: All queries include enterprise best practices

### **Clean Architecture Development**

For detailed Clean Architecture guide, see **[Clean Architecture Guide](clean-architecture.md)**.

1. **Start environment**: `make up`
2. **Setup databases**: `make db-recreate-all`
3. **Run tests**: `make test`
4. **Develop following layers**:
   - **Domain**: Start with business logic in `src/Domain/`
   - **Application**: Add use cases in `src/Application/`
   - **Infrastructure**: Implement external concerns in `src/Infrastructure/`
   - **Presentation**: Add HTTP handling in `src/Presentation/`
   - **Shared**: Create DTOs and utilities in `src/Shared/`
5. **Test changes**: `make test`
6. **Stop environment**: `make down`

## 🏗️ Architecture Overview

For detailed architecture information, see **[Clean Architecture Guide](clean-architecture.md)**.

## 📊 Testing Strategy

For comprehensive testing information, see **[Testing Strategy Guide](testing-strategy.md)**.

## 🚨 Common Issues

For comprehensive troubleshooting guide, see **[Troubleshooting Guide](troubleshooting.md)**.

## 📚 Additional Resources

### **Project Documentation**
- [Clean Architecture Guide](clean-architecture.md) - Detailed Clean Architecture implementation
- [Docker Environment Guide](docker-environment.md) - Complete Docker setup and configuration
- [Testing Strategy Guide](testing-strategy.md) - Comprehensive testing approach
- [Development Commands Reference](development-commands.md) - All available development commands
- [Troubleshooting Guide](troubleshooting.md) - Common issues and solutions
- [Environment Configuration Guide](environment-config.md) - Environment setup and configuration
- [Cache Strategy](cache_strategy.md) - Redis caching implementation and strategy

### **Clean Architecture**
- [Clean Architecture by Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Hexagonal Architecture (Ports & Adapters)](https://alistair.cockburn.us/hexagonal-architecture/)

### **PHP 8.3+ Features**
- [PHP 8.3 Release Notes](https://www.php.net/releases/8.3/en.php)
- [Constructor Property Promotion](https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion)
- [Readonly Classes](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.readonly)

### **BDD Testing**
- [Behavior-Driven Development](https://en.wikipedia.org/wiki/Behavior-driven_development)
- [Behat Documentation](https://docs.behat.org/)
- [Gherkin Syntax](https://cucumber.io/docs/gherkin/reference/)

### **Prompt Engineering**
- [AI Prompt Engineering Best Practices](https://www.promptingguide.ai/)
- [Enterprise AI Implementation](https://www.gartner.com/en/topics/artificial-intelligence)
- [Technical Documentation Standards](https://developers.google.com/tech-writing) 