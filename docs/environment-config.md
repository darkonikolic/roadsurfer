# ⚙️ Environment Configuration Guide

## 📋 Overview

This guide covers all environment configurations for the project, including development, testing, and production environments. Each environment has specific settings optimized for its purpose.

## 🏗️ Environment Types

### **Development Environment** (`dev`)
**Purpose**: Local development and debugging

**Configuration**:
- **Database**: `roadster` (PostgreSQL)
- **Environment**: `dev`
- **Debug**: Enabled
- **Cache**: Redis (development mode)
- **Logs**: Verbose
- **Error Reporting**: Full
- **Profiler**: Enabled

**Key Settings**:
```yaml
# .env.local (development)
APP_ENV=dev
APP_DEBUG=true
DATABASE_URL=postgresql://postgres:password@postgres:5432/roadster
REDIS_URL=redis://redis:6379
LOG_LEVEL=debug
```

### **Test Environment** (`test`)
**Purpose**: Automated testing and CI/CD

**Configuration**:
- **Database**: `roadster_test` (PostgreSQL)
- **Environment**: `test`
- **Debug**: Disabled
- **Cache**: Redis (test mode)
- **Logs**: Minimal
- **Error Reporting**: Disabled
- **Profiler**: Disabled

**Key Settings**:
```yaml
# .env.test
APP_ENV=test
APP_DEBUG=false
DATABASE_URL=postgresql://postgres:password@postgres:5432/roadster_test
REDIS_URL=redis://redis:6379
LOG_LEVEL=error
```

### **Production Environment** (`prod`)
**Purpose**: Live application deployment

**Configuration**:
- **Database**: `roadster_prod` (PostgreSQL)
- **Environment**: `prod`
- **Debug**: Disabled
- **Cache**: Redis (production mode)
- **Logs**: Error only
- **Error Reporting**: Disabled
- **Profiler**: Disabled

**Key Settings**:
```yaml
# .env.prod
APP_ENV=prod
APP_DEBUG=false
DATABASE_URL=postgresql://user:password@host:5432/roadster_prod
REDIS_URL=redis://host:6379
LOG_LEVEL=error
```

## 🗄️ Database Configuration

### **Development Database**
```yaml
# Database settings for development
DATABASE_URL=postgresql://postgres:password@postgres:5432/roadster
DATABASE_HOST=postgres
DATABASE_PORT=5432
DATABASE_NAME=roadster
DATABASE_USER=postgres
DATABASE_PASSWORD=password
```

**Features**:
- **Persistent Storage**: Data persists between container restarts
- **Debug Mode**: Full query logging enabled
- **Schema Updates**: Automatic schema updates
- **Migration Tracking**: Doctrine migrations enabled

### **Test Database**
```yaml
# Database settings for testing
DATABASE_URL=postgresql://postgres:password@postgres:5432/roadster_test
DATABASE_HOST=postgres
DATABASE_PORT=5432
DATABASE_NAME=roadster_test
DATABASE_USER=postgres
DATABASE_PASSWORD=password
```

**Features**:
- **Isolated Environment**: Separate from development database
- **Test Data**: Safe test data management
- **Transaction Rollback**: Automatic cleanup after tests
- **Migration Tracking**: Doctrine migrations enabled

### **Production Database**
```yaml
# Database settings for production
DATABASE_URL=postgresql://user:password@host:5432/roadster_prod
DATABASE_HOST=host
DATABASE_PORT=5432
DATABASE_NAME=roadster_prod
DATABASE_USER=user
DATABASE_PASSWORD=password
```

**Features**:
- **High Availability**: Clustered database setup
- **Backup Strategy**: Automated backups
- **Connection Pooling**: Optimized connection management
- **Security**: Encrypted connections

## 🔄 Cache Configuration

### **Redis Settings**
```yaml
# Redis configuration
REDIS_URL=redis://redis:6379
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_DB=0
```

**Cache Types**:
- **Application Cache**: Symfony application cache
- **Session Storage**: User session data
- **Query Cache**: Database query results
- **Page Cache**: Full page caching

### **Cache Configuration by Environment**

#### **Development Cache**
```yaml
# Development cache settings
cache:
  app: cache.adapter.redis
  system: cache.adapter.system
  directory: '%kernel.cache_dir%/pools'
  default_doctrine_orm_result_cache_lifetime: 0
  default_doctrine_orm_result_cache_id: ~
```

#### **Test Cache**
```yaml
# Test cache settings
cache:
  app: cache.adapter.array
  system: cache.adapter.system
  directory: '%kernel.cache_dir%/pools'
  default_doctrine_orm_result_cache_lifetime: 0
  default_doctrine_orm_result_cache_id: ~
```

#### **Production Cache**
```yaml
# Production cache settings
cache:
  app: cache.adapter.redis
  system: cache.adapter.system
  directory: '%kernel.cache_dir%/pools'
  default_doctrine_orm_result_cache_lifetime: 3600
  default_doctrine_orm_result_cache_id: ~
```

## 📝 Logging Configuration

### **Development Logging**
```yaml
# Development logging
monolog:
  channels: ['deprecation']
  handlers:
    main:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: debug
      channels: ["!event"]
    nested:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: debug
      channels: ["!event"]
    console:
      type: console
      process_psr_3_messages: false
      channels: ["!event", "!doctrine"]
    deprecation:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.deprecations.log"
      channels: ["deprecation"]
```

### **Test Logging**
```yaml
# Test logging
monolog:
  handlers:
    main:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: error
      channels: ["!event"]
    nested:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: error
      channels: ["!event"]
    console:
      type: console
      process_psr_3_messages: false
      channels: ["!event", "!doctrine"]
```

### **Production Logging**
```yaml
# Production logging
monolog:
  handlers:
    main:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: error
      channels: ["!event"]
    nested:
      type: stream
      path: "%kernel.logs_dir%/%kernel.environment%.log"
      level: error
      channels: ["!event"]
    console:
      type: console
      process_psr_3_messages: false
      channels: ["!event", "!doctrine"]
```

## 🔧 PHP Configuration

### **Development PHP Settings**
```ini
; docker/php.ini (development)
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 100M
post_max_size = 100M
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
log_errors = On
error_log = /var/log/php_errors.log
opcache.enable = 1
opcache.enable_cli = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
```

### **Test PHP Settings**
```ini
; docker/php.ini (test)
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M
display_errors = Off
display_startup_errors = Off
error_reporting = E_ERROR | E_WARNING | E_PARSE
log_errors = On
error_log = /var/log/php_errors.log
opcache.enable = 1
opcache.enable_cli = 1
opcache.memory_consumption = 64
opcache.interned_strings_buffer = 4
opcache.max_accelerated_files = 2000
opcache.revalidate_freq = 0
opcache.fast_shutdown = 1
```

### **Production PHP Settings**
```ini
; docker/php.ini (production)
memory_limit = 256M
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M
display_errors = Off
display_startup_errors = Off
error_reporting = E_ERROR | E_WARNING | E_PARSE
log_errors = On
error_log = /var/log/php_errors.log
opcache.enable = 1
opcache.enable_cli = 0
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 0
opcache.fast_shutdown = 1
```

## 🌐 Web Server Configuration

### **Nginx Configuration**
```nginx
# docker/nginx.conf
server {
    listen 8080;
    server_name localhost;
    root /var/www/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass php:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/project_error.log;
    access_log /var/log/nginx/project_access.log;
}
```

### **Environment-Specific Nginx Settings**

#### **Development**
```nginx
# Development settings
error_log /var/log/nginx/project_error.log debug;
access_log /var/log/nginx/project_access.log;
```

#### **Test**
```nginx
# Test settings
error_log /var/log/nginx/project_error.log warn;
access_log /var/log/nginx/project_access.log;
```

#### **Production**
```nginx
# Production settings
error_log /var/log/nginx/project_error.log error;
access_log /var/log/nginx/project_access.log;
```

## 🔒 Security Configuration

### **Development Security**
```yaml
# Development security settings
security:
  providers:
    app_user_provider:
      entity:
        class: App\Entity\User
        property: email
  firewalls:
    dev:
      pattern: ^/(_(profiler|wdt)|css|images|js)/
      security: false
    main:
      lazy: true
      provider: app_user_provider
      form_login:
        login_path: app_login
        check_path: app_login
      logout:
        path: app_logout
```

### **Test Security**
```yaml
# Test security settings
security:
  providers:
    app_user_provider:
      entity:
        class: App\Entity\User
        property: email
  firewalls:
    dev:
      pattern: ^/(_(profiler|wdt)|css|images|js)/
      security: false
    main:
      lazy: true
      provider: app_user_provider
      form_login:
        login_path: app_login
        check_path: app_login
      logout:
        path: app_logout
```

### **Production Security**
```yaml
# Production security settings
security:
  providers:
    app_user_provider:
      entity:
        class: App\Entity\User
        property: email
  firewalls:
    main:
      lazy: true
      provider: app_user_provider
      form_login:
        login_path: app_login
        check_path: app_login
      logout:
        path: app_logout
```

## 📊 Monitoring Configuration

### **Health Check Endpoints**
```yaml
# Health check configuration
health_checks:
  database:
    enabled: true
    timeout: 5
  redis:
    enabled: true
    timeout: 3
  application:
    enabled: true
    timeout: 10
```

### **Metrics Collection**
```yaml
# Metrics configuration
metrics:
  enabled: true
  collection_interval: 60
  storage: redis
  retention_days: 30
```

## 🔄 Environment Variables

### **Required Environment Variables**
```bash
# Core application variables
APP_ENV=dev|test|prod
APP_DEBUG=true|false
APP_SECRET=your-secret-key

# Database variables
DATABASE_URL=postgresql://user:password@host:port/database
DATABASE_HOST=host
DATABASE_PORT=5432
DATABASE_NAME=database
DATABASE_USER=user
DATABASE_PASSWORD=password

# Cache variables
REDIS_URL=redis://host:port
REDIS_HOST=host
REDIS_PORT=6379
REDIS_DB=0

# Logging variables
LOG_LEVEL=debug|info|warning|error
LOG_CHANNEL=stack

# Security variables
JWT_SECRET=your-jwt-secret
JWT_PASSPHRASE=your-jwt-passphrase
```

### **Optional Environment Variables**
```bash
# Optional variables
MAILER_DSN=smtp://user:pass@smtp.example.com:25
MESSENGER_TRANSPORT_DSN=doctrine://default
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

## 🚀 Environment Setup Commands

### **Development Setup**
```bash
# Setup development environment
cp .env .env.local
make up
make install
make db-recreate-dev
make db-migrate-dev
```

### **Test Setup**
```bash
# Setup test environment
cp .env.test .env.local
make db-recreate-test
make db-migrate-test
APP_ENV=test make test
```

### **Production Setup**
```bash
# Setup production environment
cp .env.prod .env.local
make up
make install
make db-migrate-all
```

## 📚 Additional Resources

### **Environment Management**
- [Symfony Environment Configuration](https://symfony.com/doc/current/configuration.html)
- [Docker Environment Variables](https://docs.docker.com/compose/environment-variables/)
- [PHP Configuration](https://www.php.net/manual/en/configuration.php)

### **Database Configuration**
- [Doctrine Configuration](https://symfony.com/doc/current/doctrine.html)
- [PostgreSQL Configuration](https://www.postgresql.org/docs/current/runtime-config.html)
- [Redis Configuration](https://redis.io/topics/config)

### **Security Configuration**
- [Symfony Security](https://symfony.com/doc/current/security.html)
- [Nginx Security](https://nginx.org/en/docs/http/ngx_http_core_module.html)
- [PHP Security](https://www.php.net/manual/en/security.php) 