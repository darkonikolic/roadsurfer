# 🚀 Roadster - Clean Architecture Symfony Project

## 📋 Overview

This is a modern Symfony 7.0+ application built with Clean Architecture principles, featuring BDD (Behavior Driven Development) testing approach, PHP 8.3+ syntax, and comprehensive code quality tools.

## 🏗️ Project Structure

For detailed project structure information, see **[Project Structure Documentation](docs/project-structure.md)**.

The project follows Clean Architecture principles with the following key directories:

- **`docker/`** - 🐳 Docker development environment
- **`prompts/`** - 🤖 AI development prompts organized by status
- **`queries/`** - 🔄 Query files for prompt regeneration
- **`roadsurfer-com/`** - 📦 Symfony application with Clean Architecture layers
- **`docs/`** - 📚 Project documentation
- **`Makefile`** - Build automation

## 🎯 Current Status

The project implements Clean Architecture with the following completed features:

### ✅ **Completed:**
- **Infrastructure Layer**: Docker environment, PostgreSQL, Redis, health checks
- **Application Layer**: HealthService for health monitoring, JSON processing services
- **Presentation Layer**: HealthController with `/health` endpoint, REST API endpoints
- **Shared Layer**: HealthStatusDTO and ServiceHealthDTO, comprehensive DTOs
- **Testing**: 12 tests passing with BDD approach organized by layers
- **Code Quality**: PHPStan, Psalm, PHP CS Fixer, PHPMD configured
- **API Documentation**: Nelmio API documentation at `/api/doc`
- **Console Commands**: JSON processing command with custom path support

### ⚠️ **In Progress:**
- **Domain Layer**: Structure ready but no domain entities implemented
- **Advanced Features**: REST APIs, domain logic, CQRS, Event Sourcing pending

## 🚀 Quick Start

For detailed setup instructions and all available commands, see the **[Quick Start Guide](docs/quickstart.md)**.

### ⚡ Essential Commands
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

## 🐳 Docker Development Environment

For detailed Docker environment information, see **[Docker Environment Guide](docs/docker-environment.md)**.

The project uses Docker with the following services:
- **Nginx** (8080) - Web server with PHP-FPM
- **PHP-FPM** (9000) - PHP 8.3 application server
- **PostgreSQL** (5432) - Database server
- **Redis** (6379) - Cache server

## 🌐 API Access

- **API Documentation**: [http://localhost:8080/api/doc](http://localhost:8080/api/doc) - Interactive API documentation
- **API JSON Schema**: [http://localhost:8080/api/doc.json](http://localhost:8080/api/doc.json) - API schema in JSON format
- **Health Check**: [http://localhost:8080/health](http://localhost:8080/health) - Application health status

## 🏛️ Clean Architecture Layers

For detailed Clean Architecture information, see **[Clean Architecture Guide](docs/clean-architecture.md)**.

## 🧪 Testing Strategy

For comprehensive testing information, see **[Testing Strategy Guide](docs/testing-strategy.md)**.

### **BDD Approach with Layer Organization:**
- **`tests/Infrastructure/`** - Database, logging, external services tests
- **`tests/Presentation/`** - HTTP endpoints, controller tests

### **Current Test Coverage:**
- ✅ **12 tests passing** with 40 assertions
- ✅ **Health endpoint tests** - Complete functionality
- ✅ **Infrastructure tests** - Database and logging
- ✅ **Application tests** - JSON processing and management services
- ✅ **Presentation tests** - REST API endpoints
- ⚠️ **Domain tests** - Pending domain entities

## 🔧 Code Quality Tools

### **Comprehensive Quality Pipeline:**
- **PHPStan** - Static analysis (Level 6)
- **Psalm** - Type checking and error detection
- **PHP CS Fixer** - Code formatting
- **PHPMD** - Mess detection
- **PHPCPD** - Copy-paste detection
- **PHPMetrics** - Code metrics

## 📚 Additional Resources

### 📖 Documentation
- [Quick Start Guide](docs/quickstart.md) - Complete setup and command reference
- [Clean Architecture Guide](docs/clean-architecture.md) - Detailed Clean Architecture implementation
- [Docker Environment Guide](docs/docker-environment.md) - Complete Docker setup and configuration
- [Testing Strategy Guide](docs/testing-strategy.md) - Comprehensive testing approach
- [Development Commands Reference](docs/development-commands.md) - All available development commands
- [Troubleshooting Guide](docs/troubleshooting.md) - Common issues and solutions
- [Environment Configuration Guide](docs/environment-config.md) - Environment setup and configuration
- [Code Quality Standards](docs/code_quality.md) - Quality tools and standards
- [Cache Strategy](docs/cache_strategy.md) - Redis caching implementation and strategy
- [API Documentation](docs/api_documentation.md) - Nelmio API documentation and testing
- [Symfony 7.0 Documentation](https://symfony.com/doc/7.0/)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)

### 🛠️ Tools
- **Symfony CLI**: Available in PHP container
- **Composer**: Package management
- **PHPUnit**: Testing framework with BDD approach
- **Docker**: Complete development environment

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Follow Clean Architecture principles
4. Add tests for new functionality
5. Ensure code quality checks pass
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

**Happy Coding! 🎉**
