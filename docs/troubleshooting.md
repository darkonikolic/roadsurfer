# 🚨 Troubleshooting Guide

## 📋 Overview

This guide provides solutions for common issues encountered during development. Each section includes diagnostic steps and resolution procedures.

## 🐳 Docker Issues

### **Container Won't Start**
**Symptoms**: Containers fail to start or exit immediately

**Diagnostic Steps**:
```bash
# Check container status
docker-compose -f docker/docker-compose.yml ps

# View container logs
docker-compose -f docker/docker-compose.yml logs

# Check Docker daemon
docker info

# Check available disk space
df -h
```

**Solutions**:
```bash
# Rebuild containers
make rebuild

# Clean Docker cache
docker system prune -a

# Restart Docker daemon
sudo systemctl restart docker

# Check port conflicts
netstat -an | grep 8080
netstat -an | grep 5432
netstat -an | grep 6379
```

### **Port Conflicts**
**Symptoms**: "Address already in use" errors

**Diagnostic Steps**:
```bash
# Check what's using the ports
netstat -an | grep 8080
netstat -an | grep 5432
netstat -an | grep 6379

# Check Docker containers
docker ps -a
```

**Solutions**:
```bash
# Stop conflicting services
sudo systemctl stop apache2  # if using port 8080
sudo systemctl stop postgresql  # if using port 5432

# Change ports in docker-compose.yml
# Edit docker/docker-compose.yml and change port mappings

# Kill processes using ports
sudo lsof -ti:8080 | xargs kill -9
sudo lsof -ti:5432 | xargs kill -9
sudo lsof -ti:6379 | xargs kill -9
```

### **Permission Issues**
**Symptoms**: "Permission denied" errors

**Diagnostic Steps**:
```bash
# Check file permissions
ls -la

# Check Docker group membership
groups $USER

# Check Docker socket permissions
ls -la /var/run/docker.sock
```

**Solutions**:
```bash
# Fix file permissions
sudo chown -R $USER:$USER .

# Add user to docker group
sudo usermod -aG docker $USER

# Fix Docker socket permissions
sudo chmod 666 /var/run/docker.sock

# Logout and login again for group changes
```

## 🗄️ Database Issues

### **Database Connection Errors**
**Symptoms**: "Connection refused" or "Database not found" errors

**Diagnostic Steps**:
```bash
# Check if database container is running
docker-compose -f docker/docker-compose.yml ps postgres

# Check database logs
docker-compose -f docker/docker-compose.yml logs postgres

# Test database connection
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -c "\l"

# Check environment variables
docker-compose -f docker/docker-compose.yml exec php env | grep DATABASE
```

**Solutions**:
```bash
# Recreate databases
make db-recreate-all

# Check database configuration
docker-compose -f docker/docker-compose.yml exec php bin/console debug:config doctrine

# Verify database exists
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster -c "\dt"

# Check database migrations
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:migrations:status
```

### **Migration Issues**
**Symptoms**: Migration errors or schema inconsistencies

**Diagnostic Steps**:
```bash
# Check migration status
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:migrations:status

# Validate schema
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:schema:validate

# Check migration files
ls -la roadsurfer-com/migrations/
```

**Solutions**:
```bash
# Reset database and run migrations
make db-recreate-all
make db-migrate-all

# Generate new migration
docker-compose -f docker/docker-compose.yml exec php bin/console make:migration

# Update schema
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:schema:update --force

# Rollback migrations
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:migrations:migrate prev
```

### **Test Database Issues**
**Symptoms**: Tests failing due to database problems

**Diagnostic Steps**:
```bash
# Check test environment
APP_ENV=test docker-compose -f docker/docker-compose.yml exec php bin/console debug:config

# Check test database
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster_test -c "\dt"

# Run tests with verbose output
make test VERBOSE=1
```

**Solutions**:
```bash
# Recreate test database
make db-recreate-test

# Run migrations on test database
make db-migrate-test

# Run tests with test environment
APP_ENV=test make test

# Check test configuration
APP_ENV=test docker-compose -f docker/docker-compose.yml exec php bin/console debug:config doctrine
```

## 🧪 Testing Issues

### **Test Failures**
**Symptoms**: Tests failing unexpectedly

**Diagnostic Steps**:
```bash
# Run tests with verbose output
make test VERBOSE=1

# Run specific test
make test TEST=tests/Infrastructure/TestDatabaseConnectionTest.php

# Check test environment
APP_ENV=test docker-compose -f docker/docker-compose.yml exec php bin/console debug:config

# Check test database
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster_test -c "\dt"
```

**Solutions**:
```bash
# Recreate test database
make db-recreate-test

# Run tests with test environment
APP_ENV=test make test

# Clear test cache
APP_ENV=test docker-compose -f docker/docker-compose.yml exec php bin/console cache:clear

# Check test coverage
make test-coverage
```

### **Slow Test Execution**
**Symptoms**: Tests taking too long to run

**Diagnostic Steps**:
```bash
# Check test execution time
time make test

# Check database performance
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster_test -c "SELECT * FROM pg_stat_activity;"

# Check memory usage
docker stats
```

**Solutions**:
```bash
# Use database transactions for test isolation
# Add @group annotation to tests
# Mock external services
# Use in-memory SQLite for unit tests

# Optimize database queries
docker-compose -f docker/docker-compose.yml exec php bin/console doctrine:query:sql "EXPLAIN ANALYZE SELECT * FROM table_name;"
```

### **Flaky Tests**
**Symptoms**: Tests pass sometimes and fail other times

**Diagnostic Steps**:
```bash
# Run tests multiple times
for i in {1..5}; do make test; done

# Check for shared state
grep -r "static" tests/

# Check for time-dependent tests
grep -r "sleep\|time" tests/
```

**Solutions**:
```bash
# Ensure test isolation
# Use unique test data for each test
# Avoid time-dependent assertions
# Clean up test data after each test

# Use database transactions
# Mock external services
# Use test fixtures
```

## 🔧 Application Issues

### **PHP Errors**
**Symptoms**: PHP errors or warnings

**Diagnostic Steps**:
```bash
# Check PHP configuration
docker-compose -f docker/docker-compose.yml exec php php -i

# Check PHP error logs
docker-compose -f docker/docker-compose.yml exec php tail -f var/log/dev.log

# Check Symfony configuration
docker-compose -f docker/docker-compose.yml exec php bin/console debug:config
```

**Solutions**:
```bash
# Clear cache
docker-compose -f docker/docker-compose.yml exec php bin/console cache:clear

# Check Composer dependencies
docker-compose -f docker/docker-compose.yml exec php composer install

# Update PHP configuration
# Edit docker/php.ini

# Check for syntax errors
docker-compose -f docker/docker-compose.yml exec php php -l src/
```

### **Symfony Issues**
**Symptoms**: Symfony framework errors

**Diagnostic Steps**:
```bash
# Check Symfony environment
docker-compose -f docker/docker-compose.yml exec php bin/console debug:config

# Check routes
docker-compose -f docker/docker-compose.yml exec php bin/console debug:router

# Check services
docker-compose -f docker/docker-compose.yml exec php bin/console debug:container
```

**Solutions**:
```bash
# Clear cache
docker-compose -f docker/docker-compose.yml exec php bin/console cache:clear

# Warm up cache
docker-compose -f docker/docker-compose.yml exec php bin/console cache:warmup

# Check for missing services
docker-compose -f docker/docker-compose.yml exec php bin/console debug:container --env-vars

# Update Symfony
docker-compose -f docker/docker-compose.yml exec php composer update symfony/*
```

### **Composer Issues**
**Symptoms**: Dependency or autoloading errors

**Diagnostic Steps**:
```bash
# Check Composer status
docker-compose -f docker/docker-compose.yml exec php composer diagnose

# Check autoloader
docker-compose -f docker/docker-compose.yml exec php composer dump-autoload

# Check dependencies
docker-compose -f docker/docker-compose.yml exec php composer show
```

**Solutions**:
```bash
# Install dependencies
docker-compose -f docker/docker-compose.yml exec php composer install

# Update dependencies
docker-compose -f docker/docker-compose.yml exec php composer update

# Clear Composer cache
docker-compose -f docker/docker-compose.yml exec php composer clear-cache

# Regenerate autoloader
docker-compose -f docker/docker-compose.yml exec php composer dump-autoload -o
```

## 🏗️ Architecture Issues

### **Circular Dependencies**
**Symptoms**: "Circular dependency" errors

**Diagnostic Steps**:
```bash
# Check for circular dependencies
docker-compose -f docker/docker-compose.yml exec php bin/console debug:container --env-vars

# Use PHPStan to detect issues
make phpstan

# Check service definitions
grep -r "services:" config/
```

**Solutions**:
```bash
# Ensure dependencies point inward only
# Use dependency injection
# Break circular dependencies with interfaces
# Use lazy loading where appropriate

# Check service configuration
# Edit config/services.yaml
```

### **Fat Controllers**
**Symptoms**: Controllers with too much business logic

**Diagnostic Steps**:
```bash
# Check controller complexity
find src/Presentation/Controller -name "*.php" -exec wc -l {} \;

# Use PHPMD to detect issues
make phpmd
```

**Solutions**:
```bash
# Move business logic to Application layer
# Use Application services
# Keep controllers thin
# Follow Single Responsibility Principle
```

### **Tight Coupling**
**Symptoms**: Hard to test or modify code

**Diagnostic Steps**:
```bash
# Check for direct dependencies
grep -r "new " src/

# Use PHPStan to detect issues
make phpstan
```

**Solutions**:
```bash
# Use dependency injection
# Prefer interfaces over concrete classes
# Use constructor injection
# Follow Dependency Inversion Principle
```

## 🤖 Prompt Management Issues

### **Missing Query Files**
**Symptoms**: Prompts without corresponding query files

**Diagnostic Steps**:
```bash
# Check for missing query files
ls prompts/in_progress/
ls prompts/backlog/
ls queries/
```

**Solutions**:
```bash
# Create missing query files
# Ensure all prompts have corresponding queries
# Follow naming conventions
# Update documentation
```

### **Query Regeneration Issues**
**Symptoms**: Queries not regenerating prompts correctly

**Diagnostic Steps**:
```bash
# Check query file structure
cat queries/query2.md

# Verify prompt structure
cat prompts/in_progress/2025-07-10_02_development_complete_tdd.md
```

**Solutions**:
```bash
# Update query files with complete specifications
# Ensure all technical details are preserved
# Follow enterprise standards
# Test query regeneration
```

### **Rules Compliance Issues**
**Symptoms**: Prompts not following established rules

**Diagnostic Steps**:
```bash
# Check rules compliance
cat prompts/rules.md

# Verify prompt structure
grep -r "## " prompts/
```

**Solutions**:
```bash
# Update prompts to follow rules
# Ensure enterprise standards
# Add missing sections
# Validate prompt quality
```

## 📊 Performance Issues

### **Slow Application**
**Symptoms**: Application responding slowly

**Diagnostic Steps**:
```bash
# Check memory usage
docker-compose -f docker/docker-compose.yml exec php php -r "echo memory_get_usage(true);"

# Check database performance
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster -c "SELECT * FROM pg_stat_activity;"

# Check Redis performance
docker-compose -f docker/docker-compose.yml exec redis redis-cli INFO
```

**Solutions**:
```bash
# Optimize database queries
# Use caching
# Optimize PHP configuration
# Use OPcache
# Monitor resource usage
```

### **High Memory Usage**
**Symptoms**: Out of memory errors

**Diagnostic Steps**:
```bash
# Check PHP memory limit
docker-compose -f docker/docker-compose.yml exec php php -i | grep memory_limit

# Check container memory usage
docker stats

# Check application memory usage
docker-compose -f docker/docker-compose.yml exec php php -r "echo memory_get_peak_usage(true);"
```

**Solutions**:
```bash
# Increase PHP memory limit
# Optimize code
# Use lazy loading
# Implement pagination
# Monitor memory usage
```

## 🔒 Security Issues

### **Permission Problems**
**Symptoms**: File permission errors

**Diagnostic Steps**:
```bash
# Check file permissions
ls -la

# Check container user
docker-compose -f docker/docker-compose.yml exec php whoami

# Check volume permissions
docker volume ls
```

**Solutions**:
```bash
# Fix file permissions
sudo chown -R $USER:$USER .

# Fix volume permissions
docker volume prune
docker-compose -f docker/docker-compose.yml down -v
docker-compose -f docker/docker-compose.yml up -d
```

### **Environment Variable Issues**
**Symptoms**: Configuration not loading correctly

**Diagnostic Steps**:
```bash
# Check environment variables
docker-compose -f docker/docker-compose.yml exec php env

# Check .env files
ls -la .env*

# Check Symfony environment
docker-compose -f docker/docker-compose.yml exec php bin/console debug:config
```

**Solutions**:
```bash
# Create .env.local file
# Set correct environment variables
# Check .env file syntax
# Restart containers
```

## 📚 Additional Resources

### **Docker Troubleshooting**
- [Docker Troubleshooting Guide](https://docs.docker.com/config/daemon/)
- [Docker Compose Troubleshooting](https://docs.docker.com/compose/troubleshooting/)

### **Symfony Debugging**
- [Symfony Debugging](https://symfony.com/doc/current/debug.html)
- [Symfony Profiler](https://symfony.com/doc/current/profiler.html)

### **Database Troubleshooting**
- [PostgreSQL Troubleshooting](https://www.postgresql.org/docs/current/runtime-config.html)
- [Doctrine Debugging](https://symfony.com/doc/current/doctrine.html#debugging-queries)

### **Testing Troubleshooting**
- [PHPUnit Troubleshooting](https://phpunit.de/documentation.html)
- [Test Isolation Best Practices](https://phpunit.de/documentation.html#test-isolation) 