# 🏛️ Clean Architecture Guide

## 📋 Overview

This project follows Clean Architecture principles as defined by Robert C. Martin, ensuring separation of concerns, testability, and maintainability. The architecture is organized into distinct layers with clear dependency rules.

## 🏗️ Architecture Layers

### **Domain Layer** (`src/Domain/`)
**Purpose**: Contains business logic and rules - the heart of the application

**Contains**:
- **Entities**: Core business objects with identity
- **Domain Services**: Business logic that doesn't belong to a single entity
- **Value Objects**: Immutable objects representing concepts
- **Repository Interfaces**: Contracts for data access
- **Domain Events**: Business events that occurred

**Example**: `HealthChecker` contains business logic for health assessment

**Dependencies**: None (innermost layer)

### **Application Layer** (`src/Application/`)
**Purpose**: Orchestrates use cases and coordinates between layers

**Contains**:
- **Command/Query Handlers**: Handle application commands and queries
- **Application Services**: Coordinate domain objects and infrastructure
- **Use Cases**: Business workflows and processes
- **DTOs**: Data transfer objects for layer communication

**Example**: `HealthService` coordinates health checks from different sources

**Dependencies**: Domain Layer only

### **Infrastructure Layer** (`src/Infrastructure/`)
**Purpose**: Handles external concerns (database, external APIs, etc.)

**Contains**:
- **Database Implementations**: Repository implementations
- **External Service Adapters**: API clients and integrations
- **Framework Configurations**: Symfony, Doctrine configurations
- **External Libraries**: Third-party service integrations

**Example**: `DatabaseHealthService` implements database-specific health checks

**Dependencies**: Application and Domain layers

### **Presentation Layer** (`src/Presentation/`)
**Purpose**: Handles HTTP requests and responses

**Contains**:
- **Controllers**: HTTP request handlers
- **Request/Response Objects**: HTTP data structures
- **Templates**: View rendering (Twig)
- **API Endpoints**: REST/GraphQL endpoints

**Example**: `HealthController` handles HTTP requests to health endpoints

**Dependencies**: Application layer

### **Shared Layer** (`src/Shared/`)
**Purpose**: Common utilities and DTOs used across layers

**Contains**:
- **DTOs**: Data transfer objects
- **Exceptions**: Shared exception classes
- **Utilities**: Common helper functions
- **Constants**: Shared constants and enums

**Example**: `HealthStatusDTO` used for data transfer between layers

**Dependencies**: Can be used by any layer

## 🔄 Dependency Rules

### **Dependency Direction**
```
Presentation → Application → Domain
Infrastructure → Application → Domain
Shared → Any Layer
```

**Key Principles**:
- **Inward Dependencies Only**: Dependencies point toward the center (Domain)
- **No Outward Dependencies**: Outer layers cannot depend on inner layers
- **Interface Segregation**: Use interfaces for layer communication
- **Dependency Inversion**: Depend on abstractions, not concretions

### **Layer Communication**
```
HTTP Request → Controller → Application Service → Domain Service → Repository → Database
```

## 🛠️ Development Principles

### **Single Responsibility Principle**
- Each class has one reason to change
- Classes should be focused and cohesive
- Avoid god objects and fat controllers

### **Dependency Injection**
- Use constructor injection for dependencies
- Avoid service locator pattern
- Prefer interfaces over concrete implementations

### **Immutable DTOs**
- Use readonly classes for data transfer
- Prevent accidental state mutations
- Ensure thread safety

### **Type Safety**
- Leverage PHP 8.3+ strict typing features
- Use typed properties and return types
- Avoid mixed types and dynamic properties

## 📁 Directory Structure

```
src/
├── Application/
│   └── Service/
│       └── HealthService.php
├── Domain/
│   ├── Entity/
│   ├── Service/
│   ├── Repository/
│   └── Event/
├── Infrastructure/
│   ├── External/
│   │   └── Health/
│   │       ├── DatabaseHealthService.php
│   │       └── RedisHealthService.php
│   └── Persistence/
│       ├── Entity/
│       └── Repository/
├── Presentation/
│   ├── Controller/
│   │   ├── DefaultController.php
│   │   └── HealthController.php
│   └── Template/
│       ├── base.html.twig
│       └── index.html.twig
└── Shared/
    └── DTO/
        ├── HealthStatusDTO.php
        └── ServiceHealthDTO.php
```

## 🧪 Testing Strategy by Layer

### **Domain Layer Testing**
- **Unit Tests**: Test business logic in isolation
- **Focus**: Entities, Domain services, Value objects
- **Tools**: PHPUnit with mocks for external dependencies

### **Application Layer Testing**
- **Integration Tests**: Test use cases and workflows
- **Focus**: Command/Query handlers, Application services
- **Tools**: PHPUnit with real domain objects, mocked infrastructure

### **Infrastructure Layer Testing**
- **Integration Tests**: Test external integrations
- **Focus**: Database operations, API clients
- **Tools**: PHPUnit with real database connections

### **Presentation Layer Testing**
- **Feature Tests**: Test HTTP endpoints
- **Focus**: Controllers, Request/Response handling
- **Tools**: PHPUnit WebTestCase, HTTP client

## 🚨 Common Architecture Issues

### **Circular Dependencies**
**Problem**: Layers depending on each other
**Solution**: Ensure dependencies point inward only
**Example**: Infrastructure → Application → Domain (correct)

### **Fat Controllers**
**Problem**: Business logic in controllers
**Solution**: Move logic to Application/Domain layers
**Example**: Controller should only handle HTTP concerns

### **Tight Coupling**
**Problem**: Direct dependencies on concrete classes
**Solution**: Use dependency injection and interfaces
**Example**: Inject `HealthServiceInterface` instead of `HealthService`

### **Anemic Domain Model**
**Problem**: Entities with no behavior
**Solution**: Add business logic to domain entities
**Example**: `User` entity should contain user-related business rules

## 📊 Code Quality Metrics

### **Architecture Compliance**
- **Dependency Direction**: All dependencies point inward
- **Layer Isolation**: No cross-layer dependencies
- **Interface Usage**: >80% of dependencies use interfaces

### **Code Organization**
- **Single Responsibility**: Each class has one purpose
- **Cohesion**: Related functionality grouped together
- **Coupling**: Minimal dependencies between components

## 🔧 Modern PHP 8.3+ Features

### **Constructor Property Promotion**
```php
class HealthService
{
    public function __construct(
        private readonly HealthRepositoryInterface $repository,
        private readonly LoggerInterface $logger
    ) {}
}
```

### **Readonly Classes**
```php
readonly class HealthStatusDTO
{
    public function __construct(
        public string $status,
        public array $services
    ) {}
}
```

### **Named Arguments**
```php
$healthStatus = new HealthStatusDTO(
    status: 'healthy',
    services: ['database' => 'up', 'redis' => 'up']
);
```

### **Match Expressions**
```php
$status = match($healthCheck->getStatus()) {
    'healthy' => '✅',
    'degraded' => '⚠️',
    'unhealthy' => '❌',
    default => '❓'
};
```

## 📚 Additional Resources

### **Clean Architecture**
- [Clean Architecture by Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Hexagonal Architecture (Ports & Adapters)](https://alistair.cockburn.us/hexagonal-architecture/)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)

### **PHP 8.3+ Features**
- [PHP 8.3 Release Notes](https://www.php.net/releases/8.3/en.php)
- [Constructor Property Promotion](https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion)
- [Readonly Classes](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.readonly)

### **Testing Strategies**
- [Test-Driven Development](https://en.wikipedia.org/wiki/Test-driven_development)
- [Behavior-Driven Development](https://en.wikipedia.org/wiki/Behavior-driven_development)
- [PHPUnit Best Practices](https://phpunit.de/documentation.html) 