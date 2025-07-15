# 🔧 Development Commands Reference

## 📋 Overview

This document provides a comprehensive reference for all development commands available in the project. All commands are executed from the project root directory.

## 🚀 Essential Commands

### **Environment Management**
```bash
# Start development environment
make up

# Stop development environment
make down

# Restart environment
make restart

# View container logs
make logs

# Rebuild containers
make rebuild
```

### **Database Management**
```bash
# Recreate development database (drops and recreates)
make db-recreate-dev

# Recreate test database (drops and recreates)
make db-recreate-test

# Recreate both databases
make db-recreate-all

# Run migrations on dev database
make db-migrate-dev

# Run migrations on test database
make db-migrate-test

# Run migrations on both databases
make db-migrate-all
```

### **Testing**
```bash
# Run all tests
make test

# Run specific test file
make test TEST=tests/Infrastructure/TestDatabaseConnectionTest.php

# Run tests with coverage report
make test-coverage

# Run tests with verbose output
make test VERBOSE=1
```

## 🛠️ Development Workflow Commands

### **Initial Setup**
```bash
# Clone repository and setup
git clone <repository-url>
cd roadster

# Start environment
make up

# Install dependencies
make install

# Setup databases
make db-recreate-all

# Run initial tests
make test
```

### **Daily Development**
```bash
# Start environment
make up

# Run tests before development
make test

# Make code changes...

# Run tests after changes
make test

# Stop environment
make down
```

### **Code Quality**
```bash
# Run all quality checks
make quality

# Run specific quality tools
make phpstan
make psalm
make cs-fix
make phpmd
```

## 📊 Database Commands

### **Development Database**
```bash
# Recreate development database
make db-recreate-dev

# Run migrations on development database
make db-migrate-dev

# Check database status
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster -c "\dt"

# Access database shell
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster
```

### **Test Database**
```bash
# Recreate test database
make db-recreate-test

# Run migrations on test database
make db-migrate-test

# Check test database status
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster_test -c "\dt"

# Access test database shell
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster_test
```

### **Both Databases**
```bash
# Recreate both databases
make db-recreate-all

# Run migrations on both databases
make db-migrate-all

# Check both databases
make db-status
```

## 🧪 Testing Commands

### **Test Execution**
```bash
# Run all tests
make test

# Run specific test suite
make test TEST=tests/Unit/
make test TEST=tests/Integration/
make test TEST=tests/Feature/

# Run specific test file
make test TEST=tests/Infrastructure/TestDatabaseConnectionTest.php

# Run specific test method
make test TEST=tests/Infrastructure/TestDatabaseConnectionTest.php --filter test_database_connection
```

### **Test Coverage**
```bash
# Run tests with coverage report
make test-coverage

# Generate coverage report only
make coverage

# View coverage in browser
open coverage/index.html
```

### **Test Environment**
```bash
# Run tests with test environment
APP_ENV=test make test

# Run tests with verbose output
make test VERBOSE=1

# Run tests with debug output
make test DEBUG=1
```

## 🔍 Debugging Commands

### **Container Debugging**
```bash
# Access PHP container shell
make shell

# Access database container shell
docker-compose -f docker/docker-compose.yml exec postgres bash

# Access nginx container shell
docker-compose -f docker/docker-compose.yml exec nginx bash

# View container logs
make logs

# View specific container logs
docker-compose -f docker/docker-compose.yml logs php
docker-compose -f docker/docker-compose.yml logs postgres
docker-compose -f docker/docker-compose.yml logs nginx
```

### **Application Debugging**
```bash
# Check PHP configuration
docker-compose -f docker/docker-compose.yml exec php php -i

# Check Symfony configuration
docker-compose -f docker/docker-compose.yml exec php bin/console debug:config

# Check environment variables
docker-compose -f docker/docker-compose.yml exec php env

# Check Composer dependencies
docker-compose -f docker/docker-compose.yml exec php composer show
```

### **Database Debugging**
```bash
# Check database connection
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:query:sql "SELECT 1"

# Check database schema
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:schema:validate

# Check database migrations
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:migrations:status
```

## 🧹 Maintenance Commands

### **Cleanup Commands**
```bash
# Remove all containers and volumes
make clean

# Remove only containers
docker-compose -f docker/docker-compose.yml down

# Remove containers and volumes
docker-compose -f docker/docker-compose.yml down -v

# Clean Docker cache
docker system prune -a
```

### **Cache Management**
```bash
# Clear Symfony cache
docker-compose -f docker/docker-compose.yml exec php bin/console cache:clear

# Warm up cache
docker-compose -f docker/docker-compose.yml exec php bin/console cache:warmup

# Clear Redis cache
docker-compose -f docker/docker-compose.yml exec redis redis-cli FLUSHALL
```

### **Log Management**
```bash
# View application logs
docker-compose -f docker/docker-compose.yml exec php tail -f var/log/dev.log

# View error logs
docker-compose -f docker/docker-compose.yml exec php tail -f var/log/dev.log | grep ERROR

# Clear log files
docker-compose -f docker/docker-compose.yml exec php rm -f var/log/*.log
```

## 📈 Monitoring Commands

### **Health Checks**
```bash
# Check application health
curl http://localhost:8080/health

# Check container health
docker-compose -f docker/docker-compose.yml ps

# Check resource usage
docker stats
```

### **Performance Monitoring**
```bash
# Check PHP memory usage
docker-compose -f docker/docker-compose.yml exec php php -r "echo memory_get_usage(true);"

# Check database performance
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster -c "SELECT * FROM pg_stat_activity;"

# Check Redis performance
docker-compose -f docker/docker-compose.yml exec redis redis-cli INFO
```

## 🔧 Advanced Commands

### **Development Tools**
```bash
# Run PHP CS Fixer
make cs-fix

# Run PHPStan analysis
make phpstan

# Run Psalm analysis
make psalm

# Run PHPMD analysis
make phpmd

# Run all quality tools
make quality
```

### **Symfony Commands**
```bash
# List all Symfony commands
docker-compose -f docker/docker-compose.yml exec php bin/console list

# Generate entity
docker-compose -f docker/docker-compose.yml exec php bin/console make:entity

# Generate controller
docker-compose -f docker/docker-compose.yml exec php bin/console make:controller

# Generate migration
docker-compose -f docker/docker-compose.yml exec php bin/console make:migration
```

### **Composer Commands**
```bash
# Install dependencies
docker-compose -f docker/docker-compose.yml exec php composer install

# Update dependencies
docker-compose -f docker/docker-compose.yml exec php composer update

# Add new package
docker-compose -f docker/docker-compose.yml exec php composer require package-name

# Remove package
docker-compose -f docker/docker-compose.yml exec php composer remove package-name
```

## 🚨 Troubleshooting Commands

### **Common Issues**
```bash
# Port conflicts
netstat -an | grep 8080
netstat -an | grep 5432
netstat -an | grep 6379

# Permission issues
sudo chown -R $USER:$USER .

# Docker issues
docker system prune -a
docker volume prune
docker network prune
```

### **Reset Environment**
```bash
# Complete reset
make clean
make up
make install
make db-recreate-all
make test
```

## 📚 Command Reference

### **Make Commands Summary**
| Command | Description |
|---------|-------------|
| `make up` | Start development environment |
| `make down` | Stop development environment |
| `make restart` | Restart environment |
| `make logs` | View container logs |
| `make rebuild` | Rebuild containers |
| `make shell` | Access PHP container shell |
| `make install` | Install dependencies |
| `make test` | Run all tests |
| `make test-coverage` | Run tests with coverage |
| `make quality` | Run all quality checks |
| `make clean` | Remove all containers and volumes |
| `make db-recreate-dev` | Recreate development database |
| `make db-recreate-test` | Recreate test database |
| `make db-recreate-all` | Recreate both databases |
| `make db-migrate-dev` | Run migrations on dev database |
| `make db-migrate-test` | Run migrations on test database |
| `make db-migrate-all` | Run migrations on both databases |

### **Docker Commands Summary**
| Command | Description |
|---------|-------------|
| `docker-compose -f docker/docker-compose.yml up -d` | Start services |
| `docker-compose -f docker/docker-compose.yml down` | Stop services |
| `docker-compose -f docker/docker-compose.yml ps` | List containers |
| `docker-compose -f docker/docker-compose.yml logs` | View logs |
| `docker-compose -f docker/docker-compose.yml exec php bash` | Access PHP container |
| `docker-compose -f docker/docker-compose.yml exec postgres bash` | Access database container |

## 📚 Additional Resources

### **Make Documentation**
- [Make Documentation](https://www.gnu.org/software/make/)
- [Makefile Best Practices](https://makefiletutorial.com/)

### **Docker Documentation**
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)

### **Symfony Commands**
- [Symfony Console Documentation](https://symfony.com/doc/current/console.html)
- [Symfony Make Bundle](https://symfony.com/doc/current/bundles/SymfonyMakerBundle/index.html) 