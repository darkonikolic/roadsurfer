# 🚀 Roadster Development Makefile
# All commands go through Docker for consistency
#
# 📋 IMPORTANT: This Makefile provides a development environment wrapper around
# the original Fruits and Vegetables project. The original project can be run
# directly with Docker commands as shown in roadsurfer-com/README.md, but this
# Makefile provides additional benefits:
#
# ✅ ADVANTAGES OF MAKE COMMANDS:
# - Consistent environment across team members
# - Simplified commands (make up vs docker-compose up -d)
# - Built-in error handling and user feedback
# - Colorized output for better UX
# - Centralized configuration management
# - Additional development tools (quality checks, monitoring, etc.)
#
# 🔄 ORIGINAL PROJECT COMMANDS (roadsurfer-com/README.md):
# docker run -it -w/app -v$(pwd):/app tturkowski/fruits-and-vegetables bin/phpunit
# docker run -it -w/app -v$(pwd):/app -p8080:8080 tturkowski/fruits-and-vegetables php -S 0.0.0.0:8080 -t /app/public
#
# 🚀 EQUIVALENT MAKE COMMANDS:
# make test          # Runs PHPUnit tests
# make up           # Starts development server with Nginx + PHP-FPM
# make shell        # Enters container for direct development

# Variables
DOCKER_COMPOSE = docker-compose -f docker/docker-compose.yml
PHP_CONTAINER = $(DOCKER_COMPOSE) exec php
COMPOSER = $(PHP_CONTAINER) composer
SYMFONY = $(PHP_CONTAINER) php bin/console
PHPUNIT = $(PHP_CONTAINER) php bin/phpunit

# Colors for output
GREEN = \033[0;32m
YELLOW = \033[1;33m
RED = \033[0;31m
NC = \033[0m # No Color

# Default target
.DEFAULT_GOAL := help

# Help target
.PHONY: help
help: ## 📖 Show this help
	@echo "$(GREEN)🚀 Roadster Development Commands$(NC)"
	@echo ""
	@echo "$(YELLOW)Development:$(NC)"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo ""
	@echo "$(YELLOW)Performance:$(NC)"
	@echo "$(GREEN)  optimize$(NC) - Optimize application performance"
	@echo ""
	@echo "$(YELLOW)Testing Commands:$(NC)"
	@echo "$(GREEN)  test-coverage$(NC) - Run tests with coverage report"
	@echo "$(GREEN)  rebuild-with-coverage$(NC) - Rebuild with PCOV for coverage"
	@echo ""
	@echo "$(YELLOW)Usage:$(NC) make [command]"
	@echo "$(YELLOW)Example:$(NC) make up"
	@echo ""
	@echo "$(YELLOW)💡 Note:$(NC) This is a development environment wrapper."
	@echo "$(YELLOW)📖 Original project:$(NC) See roadsurfer-com/README.md for direct Docker commands"

# 🐳 Docker Commands
.PHONY: up
up: ## 🚀 Start development environment
	@echo "$(GREEN)🚀 Starting development environment...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✅ Development environment is running!$(NC)"
	@echo "$(YELLOW)🌐 Web: http://localhost:8080$(NC)"
	@echo "$(YELLOW)🗄️  PHPMyAdmin: http://localhost:8081$(NC)"

.PHONY: down
down: ## 🛑 Stop development environment
	@echo "$(YELLOW)🛑 Stopping development environment...$(NC)"
	$(DOCKER_COMPOSE) down
	@echo "$(GREEN)✅ Development environment is stopped!$(NC)"

.PHONY: restart
restart: ## 🔄 Restart development environment
	@echo "$(YELLOW)🔄 Restarting development environment...$(NC)"
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✅ Development environment is restarted!$(NC)"

.PHONY: logs
logs: ## 📋 Show logs
	$(DOCKER_COMPOSE) logs -f

.PHONY: logs-php
logs-php: ## 📋 Show PHP logs
	$(DOCKER_COMPOSE) logs -f php

.PHONY: logs-nginx
logs-nginx: ## 📋 Show Nginx logs
	$(DOCKER_COMPOSE) logs -f nginx

.PHONY: logs-mysql
logs-mysql: ## 📋 Show MySQL logs
	$(DOCKER_COMPOSE) logs -f mysql

.PHONY: shell
shell: ## 🐚 Enter PHP container
	$(PHP_CONTAINER) bash

.PHONY: status
status: ## 📊 Show service status
	$(DOCKER_COMPOSE) ps

# 📦 Composer Commands
.PHONY: install
install: ## 📦 Install PHP dependencies
	@echo "$(GREEN)📦 Installing PHP dependencies...$(NC)"
	$(COMPOSER) install
	@echo "$(GREEN)✅ Dependencies are installed!$(NC)"

.PHONY: update
update: ## 📦 Update PHP dependencies
	@echo "$(YELLOW)📦 Updating PHP dependencies...$(NC)"
	$(COMPOSER) update
	@echo "$(GREEN)✅ Dependencies are updated!$(NC)"

.PHONY: require
require: ## 📦 Add new package (usage: make require PACKAGE=package-name)
	@if [ -z "$(PACKAGE)" ]; then \
		echo "$(RED)❌ Please enter PACKAGE=package-name$(NC)"; \
		echo "$(YELLOW)Example: make require PACKAGE=symfony/validator$(NC)"; \
		exit 1; \
	fi
	@echo "$(GREEN)📦 Adding package: $(PACKAGE)...$(NC)"
	$(COMPOSER) require $(PACKAGE)
	@echo "$(GREEN)✅ Package is added!$(NC)"

.PHONY: remove
remove: ## 📦 Remove package (usage: make remove PACKAGE=package-name)
	@if [ -z "$(PACKAGE)" ]; then \
		echo "$(RED)❌ Please enter PACKAGE=package-name$(NC)"; \
		echo "$(YELLOW)Example: make remove PACKAGE=symfony/validator$(NC)"; \
		exit 1; \
	fi
	@echo "$(YELLOW)📦 Removing package: $(PACKAGE)...$(NC)"
	$(COMPOSER) remove $(PACKAGE)
	@echo "$(GREEN)✅ Package is removed!$(NC)"

# 🔍 Code Quality Commands
.PHONY: quality-pipeline
quality-pipeline: ## 🔍 Complete quality pipeline (format + all checks + tests)
	@echo "$(GREEN)🔍 Code Quality Pipeline Started$(NC)"
	$(COMPOSER) run-script quality-pipeline
	@echo "$(GREEN)🎉 All quality checks passed!$(NC)"

.PHONY: quality
quality: ## 🔍 Run all quality checks
	@echo "$(GREEN)🔍 Running all quality checks...$(NC)"
	$(MAKE) format
	$(MAKE) psalm
	$(MAKE) phpstan
	$(MAKE) phpmd
	$(MAKE) phpcpd
	$(MAKE) test
	@echo "$(GREEN)✅ Quality checks completed!$(NC)"

.PHONY: quality-check
quality-check: ## 🔍 Run basic quality checks (no formatting)
	@echo "$(GREEN)🔍 Running basic quality checks...$(NC)"
	$(COMPOSER) run-script quality-optimize
	@echo "$(GREEN)✅ Quality checks completed!$(NC)"

.PHONY: format
format: ## 🔧 Auto-format code (PHP CS Fixer)
	@echo "$(GREEN)🔧 Formatting code...$(NC)"
	$(COMPOSER) run-script format
	@echo "$(GREEN)✅ Code is formatted!$(NC)"

.PHONY: format-check
format-check: ## 🔧 Check code formatting without fixing
	@echo "$(GREEN)🔧 Checking code formatting...$(NC)"
	$(PHP_CONTAINER) vendor/bin/php-cs-fixer fix --dry-run --diff
	@echo "$(GREEN)✅ Format check completed!$(NC)"

.PHONY: optimize
optimize: ## 🚀 Optimize application performance
	@echo "$(GREEN)🚀 Optimizing application performance...$(NC)"
	$(COMPOSER) run-script optimize
	@echo "$(GREEN)✅ Performance optimization completed!$(NC)"

.PHONY: psalm
psalm: ## 🔍 Type safety check (Psalm)
	@echo "$(GREEN)🔍 Running Psalm type safety check...$(NC)"
	$(PHP_CONTAINER) vendor/bin/psalm
	@echo "$(GREEN)✅ Psalm check completed!$(NC)"

.PHONY: phpstan
phpstan: ## 🔍 Static analysis (PHPStan)
	@echo "$(GREEN)🔍 Running PHPStan static analysis...$(NC)"
	$(PHP_CONTAINER) vendor/bin/phpstan analyse
	@echo "$(GREEN)✅ PHPStan analysis completed!$(NC)"

.PHONY: phpmd
phpmd: ## 🔍 Code smells check (PHPMD)
	@echo "$(GREEN)🔍 Running PHPMD code smells check...$(NC)"
	$(PHP_CONTAINER) vendor/bin/phpmd src text phpmd.xml
	@echo "$(GREEN)✅ PHPMD check completed!$(NC)"

.PHONY: phpcpd
phpcpd: ## 🔍 Duplicate code detection (PHPCPD)
	@echo "$(GREEN)🔍 Running PHPCPD duplicate detection...$(NC)"
	$(PHP_CONTAINER) vendor/bin/phpcpd src
	@echo "$(GREEN)✅ PHPCPD check completed!$(NC)"

.PHONY: metrics
metrics: ## 📊 Generate complexity metrics (PHP Metrics)
	@echo "$(GREEN)📊 Generating complexity metrics...$(NC)"
	$(PHP_CONTAINER) vendor/bin/phpmetrics --config=phpmetrics.json src
	@echo "$(GREEN)✅ Metrics generated!$(NC)"

# 🧪 Testing Commands
.PHONY: test
test: ## 🧪 Run all tests
	@echo "$(GREEN)🧪 Running tests...$(NC)"
	$(PHPUNIT)
	@echo "$(GREEN)✅ Tests are completed!$(NC)"

.PHONY: test-unit
test-unit: ## 🧪 Run unit tests only
	@echo "$(GREEN)🧪 Running unit tests...$(NC)"
	$(PHPUNIT) --testsuite="Unit Tests"
	@echo "$(GREEN)✅ Unit tests are completed!$(NC)"

.PHONY: test-integration
test-integration: ## 🧪 Run integration tests only
	@echo "$(GREEN)🧪 Running integration tests...$(NC)"
	$(PHPUNIT) --testsuite="Integration Tests"
	@echo "$(GREEN)✅ Integration tests are completed!$(NC)"

.PHONY: test-coverage
test-coverage: ## 🧪 Run tests with coverage report
	@echo "$(GREEN)🧪 Running tests with coverage...$(NC)"
	$(PHPUNIT) --coverage-html coverage/
	@echo "$(GREEN)✅ Coverage report is generated in coverage/ directory!$(NC)"
	@echo "$(YELLOW)💡 Open coverage/index.html to view the report$(NC)"

.PHONY: test-file
test-file: ## 🧪 Run specific test file (usage: make test-file FILE=path/to/test.php)
	@if [ -z "$(FILE)" ]; then \
		echo "$(RED)❌ Please enter FILE=path/to/test.php$(NC)"; \
		echo "$(YELLOW)Example: make test-file FILE=tests/App/Service/StorageServiceTest.php$(NC)"; \
		exit 1; \
	fi
	@echo "$(GREEN)🧪 Running test: $(FILE)...$(NC)"
	$(PHPUNIT) $(FILE)
	@echo "$(GREEN)✅ Test is completed!$(NC)"

# 🔧 Symfony Commands
.PHONY: cache-clear
cache-clear: ## 🧹 Clear Symfony cache
	@echo "$(GREEN)🧹 Clearing Symfony cache...$(NC)"
	$(SYMFONY) cache:clear
	@echo "$(GREEN)✅ Cache is cleared!$(NC)"

.PHONY: cache-warmup
cache-warmup: ## 🔥 Warm up Symfony cache
	@echo "$(GREEN)🔥 Warming up Symfony cache...$(NC)"
	$(SYMFONY) cache:warmup
	@echo "$(GREEN)✅ Cache is warmed up!$(NC)"

.PHONY: debug-router
debug-router: ## 🛣️ Show all routes
	@echo "$(GREEN)🛣️ Showing all routes...$(NC)"
	$(SYMFONY) debug:router

.PHONY: debug-container
debug-container: ## 📦 Show all services
	@echo "$(GREEN)📦 Showing all services...$(NC)"
	$(SYMFONY) debug:container

.PHONY: debug-config
debug-config: ## ⚙️ Show configuration
	@echo "$(GREEN)⚙️ Showing configuration...$(NC)"
	$(SYMFONY) debug:config

# 🗄️ Database Commands
.PHONY: db-create
db-create: ## 🗄️ Create database
	@echo "$(GREEN)🗄️ Creating database...$(NC)"
	$(SYMFONY) doctrine:database:create --if-not-exists
	@echo "$(GREEN)✅ Database is created!$(NC)"

.PHONY: db-drop
db-drop: ## 🗄️ Drop database
	@echo "$(YELLOW)🗄️ Dropping database...$(NC)"
	$(SYMFONY) doctrine:database:drop --force --if-exists
	@echo "$(GREEN)✅ Database is dropped!$(NC)"

.PHONY: db-migrate
db-migrate: ## 🗄️ Run migrations
	@echo "$(GREEN)🗄️ Running migrations...$(NC)"
	$(SYMFONY) doctrine:migrations:migrate --no-interaction
	@echo "$(GREEN)✅ Migrations are run!$(NC)"

.PHONY: db-fixtures
db-fixtures: ## 🗄️ Load test data
	@echo "$(GREEN)🗄️ Loading test data...$(NC)"
	$(SYMFONY) doctrine:fixtures:load --no-interaction
	@echo "$(GREEN)✅ Test data is loaded!$(NC)"

## Database Management
db-recreate-dev: ## Recreate development database and run migrations
	@echo "Recreating development database..."
	docker-compose -f docker/docker-compose.yml exec php php bin/console doctrine:database:drop --force --env=dev || true
	docker-compose -f docker/docker-compose.yml exec php php bin/console doctrine:database:create --env=dev
	docker-compose -f docker/docker-compose.yml exec php php bin/console doctrine:migrations:migrate --no-interaction --env=dev
	@echo "Development database recreated successfully!"

db-recreate-test: ## Recreate test database and run migrations
	@echo "Recreating test database..."
	docker-compose -f docker/docker-compose.yml exec -e APP_ENV=test php php bin/console doctrine:database:drop --force --env=test || true
	docker-compose -f docker/docker-compose.yml exec -e APP_ENV=test php php bin/console doctrine:database:create --env=test
	docker-compose -f docker/docker-compose.yml exec -e APP_ENV=test php php bin/console doctrine:migrations:migrate --no-interaction --env=test
	@echo "Test database recreated successfully!"

db-recreate-all: db-recreate-dev db-recreate-test ## Recreate both development and test databases

db-migrate-dev: ## Run migrations on development database
	@echo "Running migrations on development database..."
	docker-compose -f docker/docker-compose.yml exec php php bin/console doctrine:migrations:migrate --no-interaction --env=dev
	@echo "Development migrations completed!"

db-migrate-test: ## Run migrations on test database
	@echo "Running migrations on test database..."
	docker-compose -f docker/docker-compose.yml exec -e APP_ENV=test php php bin/console doctrine:migrations:migrate --no-interaction --env=test
	@echo "Test migrations completed!"

db-migrate-all: db-migrate-dev db-migrate-test ## Run migrations on both databases

# 🔍 Debug Commands
.PHONY: xdebug
xdebug: ## 🔍 Enable Xdebug
	@echo "$(GREEN)🔍 Enabling Xdebug...$(NC)"
	$(PHP_CONTAINER) sh -c "pecl install xdebug && docker-php-ext-enable xdebug"
	@echo "$(GREEN)✅ Xdebug is enabled!$(NC)"

.PHONY: phpinfo
phpinfo: ## ℹ️ Show PHP info
	@echo "$(GREEN)ℹ️ Showing PHP info...$(NC)"
	$(PHP_CONTAINER) php -i

.PHONY: php-version
php-version: ## ℹ️ Show PHP version
	@echo "$(GREEN)ℹ️ PHP version:$(NC)"
	$(PHP_CONTAINER) php -v

# 🧹 Maintenance Commands
.PHONY: clean
clean: ## 🧹 Clean everything (cache, logs, temp files)
	@echo "$(YELLOW)🧹 Cleaning all files...$(NC)"
	$(SYMFONY) cache:clear
	$(PHP_CONTAINER) find . -name "*.log" -delete
	$(PHP_CONTAINER) find . -name "*.tmp" -delete
	@echo "$(GREEN)✅ Cleaning is completed!$(NC)"

.PHONY: reset
reset: ## 🔄 Reset environment (down, clean, up, install)
	@echo "$(YELLOW)🔄 Resetting environment...$(NC)"
	$(MAKE) down
	$(MAKE) clean
	$(MAKE) up
	$(MAKE) install
	@echo "$(GREEN)✅ Environment is reset!$(NC)"

.PHONY: rebuild
rebuild: ## 🔨 Rebuild Docker images
	@echo "$(YELLOW)🔨 Rebuilding Docker images...$(NC)"
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) build --no-cache
	@echo "$(GREEN)✅ Docker images are rebuilt!$(NC)"


.PHONY: rebuild-with-coverage
rebuild-with-coverage: ## 🔨 Rebuild with PCOV for coverage
	@echo "$(YELLOW)🔨 Rebuilding with PCOV for coverage...$(NC)"
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) build --no-cache
	@echo "$(GREEN)✅ Docker images rebuilt with PCOV!$(NC)"
	@echo "$(YELLOW)💡 Now you can run: make test-coverage$(NC)"

# 📊 Monitoring Commands
.PHONY: stats
stats: ## 📊 Show Docker statistics
	@echo "$(GREEN)📊 Docker statistics:$(NC)"
	docker stats --no-stream

.PHONY: top
top: ## 📊 Show processes in PHP container
	@echo "$(GREEN)📊 Processes in PHP container:$(NC)"
	$(PHP_CONTAINER) top

.PHONY: memory
memory: ## 📊 Show memory usage
	@echo "$(GREEN)📊 Memory usage:$(NC)"
	$(PHP_CONTAINER) free -h

# 🚀 Quick Commands
.PHONY: dev
dev: ## 🚀 Quick development setup (up + install + cache-clear)
	@echo "$(GREEN)🚀 Quick development setup...$(NC)"
	$(MAKE) up
	$(MAKE) install
	$(MAKE) cache-clear
	@echo "$(GREEN)✅ Development setup is completed!$(NC)"

.PHONY: fresh
fresh: ## 🚀 Fresh start (reset + test)
	@echo "$(GREEN)🚀 Fresh start...$(NC)"
	$(MAKE) reset
	$(MAKE) test
	@echo "$(GREEN)✅ Fresh start is completed!$(NC)"

# 📝 Documentation
.PHONY: docs
docs: ## 📖 Open documentation in browser
	@echo "$(GREEN)📖 Opening documentation...$(NC)"
	@echo "$(YELLOW)🌐 Web: http://localhost:8080$(NC)"
	@echo "$(YELLOW)🗄️ PHPMyAdmin: http://localhost:8081$(NC)"
	@echo "$(YELLOW)📚 Symfony Docs: https://symfony.com/doc/6.0/$(NC)"

# 🆘 Emergency Commands
.PHONY: emergency-stop
emergency-stop: ## 🚨 Emergency stop everything
	@echo "$(RED)🚨 Emergency stop...$(NC)"
	docker stop $(docker ps -q)
	@echo "$(GREEN)✅ All containers are stopped!$(NC)"

.PHONY: emergency-clean
emergency-clean: ## 🚨 Emergency clean everything
	@echo "$(RED)🚨 Emergency clean...$(NC)"
	docker system prune -a --volumes -f
	@echo "$(GREEN)✅ All Docker resources are cleaned!$(NC)"

# 📋 Info Commands
.PHONY: info
info: ## ℹ️ Show environment information
	@echo "$(GREEN)ℹ️ Environment information:$(NC)"
	@echo "$(YELLOW)PHP version:$(NC)"
	$(PHP_CONTAINER) php -v
	@echo "$(YELLOW)Composer version:$(NC)"
	$(COMPOSER) --version
	@echo "$(YELLOW)Symfony version:$(NC)"
	$(SYMFONY) --version
	@echo "$(YELLOW)Docker services:$(NC)"
	$(DOCKER_COMPOSE) ps

.PHONY: ports
ports: ## 🌐 Show all ports
	@echo "$(GREEN)🌐 Active ports:$(NC)"
	@echo "$(YELLOW)Web Server:$(NC) http://localhost:8080"
	@echo "$(YELLOW)PHPMyAdmin:$(NC) http://localhost:8081"
	@echo "$(YELLOW)MySQL:$(NC) localhost:3306"
	@echo "$(YELLOW)Redis:$(NC) localhost:6379"
	@echo "$(YELLOW)PHP-FPM:$(NC) localhost:9000"

# 🎯 Project Specific Commands
.PHONY: fruits-test
fruits-test: ## 🍎 Test fruits and vegetables functionality
	@echo "$(GREEN)🍎 Testing fruits and vegetables functionality...$(NC)"
	$(PHPUNIT) --filter="FruitsAndVegetables"
	@echo "$(GREEN)✅ Fruits tests are completed!$(NC)"

.PHONY: api-test
api-test: ## 🌐 Test API endpoints
	@echo "$(GREEN)🌐 Testing API endpoints...$(NC)"
	$(PHPUNIT) --filter="Api"
	@echo "$(GREEN)✅ API tests are completed!$(NC)"

.PHONY: validate-json
validate-json: ## 📄 Validate request.json file
	@echo "$(GREEN)📄 Validating request.json file...$(NC)"
	$(PHP_CONTAINER) php -r "json_decode(file_get_contents('request.json')); echo json_last_error() === JSON_ERROR_NONE ? '✅ JSON is valid!' : '❌ JSON error: ' . json_last_error_msg();"

.PHONY: process-json
process-json: ## 📄 Process request.json file with custom path support
	@echo "$(GREEN)📄 Processing request.json file...$(NC)"
	@echo "$(YELLOW)Usage: make process-json [FILE=path/to/file.json]$(NC)"
	@if [ -z "$(FILE)" ]; then \
		echo "$(YELLOW)Processing default file: request.json$(NC)"; \
		$(SYMFONY) app:process-request-json; \
	else \
		echo "$(YELLOW)Processing custom file: $(FILE)$(NC)"; \
		$(SYMFONY) app:process-request-json --file=$(FILE); \
	fi
	@echo "$(GREEN)✅ JSON processing completed!$(NC)"

# 📝 Log Commands
.PHONY: tail-logs
tail-logs: ## 📋 Tail all logs
	@echo "$(GREEN)📋 Tailing all logs...$(NC)"
	$(DOCKER_COMPOSE) logs -f --tail=100

.PHONY: error-logs
error-logs: ## 📋 Show only error logs
	@echo "$(GREEN)📋 Error logs...$(NC)"
	$(DOCKER_COMPOSE) logs --tail=50 | grep -i error

.PHONY: access-logs
access-logs: ## 📋 Show access logs
	@echo "$(GREEN)📋 Access logs...$(NC)"
	$(DOCKER_COMPOSE) logs -f nginx | grep -E "(GET|POST|PUT|DELETE)"

# 🔧 Maintenance
.PHONY: backup
backup: ## 💾 Create database backup
	@echo "$(GREEN)💾 Creating database backup...$(NC)"
	$(DOCKER_COMPOSE) exec mysql mysqldump -u root -proot roadster > backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✅ Backup is created!$(NC)"

.PHONY: restore
restore: ## 💾 Restore database (usage: make restore FILE=backup.sql)
	@if [ -z "$(FILE)" ]; then \
		echo "$(RED)❌ Please enter FILE=backup.sql$(NC)"; \
		echo "$(YELLOW)Example: make restore FILE=backup_20231201_120000.sql$(NC)"; \
		exit 1; \
	fi
	@echo "$(YELLOW)💾 Restoring database from $(FILE)...$(NC)"
	$(DOCKER_COMPOSE) exec -T mysql mysql -u root -proot roadster < $(FILE)
	@echo "$(GREEN)✅ Database is restored!$(NC)"

# 🎉 Success Message
.PHONY: success
success: ## 🎉 Show success message
	@echo "$(GREEN)🎉 Everything is successfully configured!$(NC)"
	@echo "$(YELLOW)🚀 Start with: make up$(NC)"
	@echo "$(YELLOW)📖 Documentation: make help$(NC)"
	@echo "$(YELLOW)🌐 Web: http://localhost:8080$(NC)" 