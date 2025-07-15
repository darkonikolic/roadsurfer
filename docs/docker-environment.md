# 🐳 Docker Development Environment

## 📋 Overview

The project uses Docker with Docker Compose to provide a complete development environment with all necessary services.

## 🏗️ Services Architecture

### **Core Services**

#### **Nginx** (Port: 8080)
- **Purpose**: Web server with PHP-FPM integration
- **Configuration**: `docker/nginx.conf`
- **Features**: 
  - PHP-FPM proxy
  - Static file serving
  - Gzip compression
  - Security headers

#### **PHP-FPM** (Port: 9000)
- **Purpose**: PHP 8.3 application server
- **Configuration**: `docker/php.ini`
- **Features**:
  - PHP 8.3+ with latest features
  - Composer package management
  - Symfony CLI tools
  - Development extensions enabled

#### **PostgreSQL** (Port: 5432)
- **Purpose**: Primary database server
- **Databases**:
  - `roadster` - Development database
  - `roadster_test` - Test database
  - `roadster_prod` - Production database (when configured)
- **Features**:
  - Persistent data storage
  - Optimized for Symfony/Doctrine
  - Connection pooling

#### **Redis** (Port: 6379)
- **Purpose**: Cache and session storage
- **Features**:
  - Session storage
  - Application caching
  - Queue processing (when configured)
  - Real-time features support

## 🚀 Environment Setup

### **Prerequisites**
```bash
# Ensure Docker and Docker Compose are installed
docker --version
docker-compose --version
```

### **Initial Setup**
```bash
# Clone the repository
git clone <repository-url>
cd roadster

# Start the development environment
make up

# Install dependencies
make install
```

### **Environment Configuration**

#### **Development Environment**
- **Database**: `roadster` (PostgreSQL)
- **Environment**: `dev`
- **Debug**: Enabled
- **Cache**: Redis
- **Logs**: Verbose

#### **Test Environment**  
- **Database**: `roadster_test` (PostgreSQL)
- **Environment**: `test`
- **Debug**: Disabled
- **Cache**: Redis
- **Logs**: Minimal

#### **Production Environment**
- **Database**: `roadster_prod` (PostgreSQL)
- **Environment**: `prod`
- **Debug**: Disabled
- **Cache**: Redis
- **Logs**: Error only

## 🔧 Container Management

### **Essential Commands**
```bash
# Start all services
make up

# Stop all services
make down

# View logs
make logs

# Rebuild containers
make rebuild

# Open PHP shell in container
make shell
```

### **Database Management**
```bash
# Recreate development database
make db-recreate-dev

# Recreate test database
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

### **Development Workflow**
```bash
# Start environment
make up

# Setup databases
make db-recreate-all

# Run tests
make test

# View logs
make logs

# Stop environment
make down
```

## 🚨 Troubleshooting

### **Container Issues**
```bash
# Check container status
docker-compose -f docker/docker-compose.yml ps

# Rebuild containers
make rebuild

# View detailed logs
make logs

# Check Docker and Docker Compose versions
docker --version
docker-compose --version
```

### **Database Connection Issues**
```bash
# Recreate databases
make db-recreate-all

# Check database status
docker-compose -f docker/docker-compose.yml exec postgres psql -U postgres -d roadster

# Verify environment variables
docker-compose -f docker/docker-compose.yml exec php env | grep DATABASE
```

### **PHP/Application Issues**
```bash
# Check PHP configuration
docker-compose -f docker/docker-compose.yml exec php php -i

# Verify Composer dependencies
docker-compose -f docker/docker-compose.yml exec php composer install

# Check Symfony configuration
docker-compose -f docker/docker-compose.yml exec php bin/console debug:config
```

### **Network Issues**
```bash
# Check port availability
netstat -an | grep 8080
netstat -an | grep 5432
netstat -an | grep 6379

# Restart Docker network
docker network prune
docker-compose -f docker/docker-compose.yml down
docker-compose -f docker/docker-compose.yml up -d
```

## 📊 Performance Optimization

### **Development Optimizations**
- **Volume Mounting**: Source code mounted for live development
- **Hot Reload**: PHP-FPM configured for development
- **Debug Mode**: Full error reporting enabled
- **Cache Disabled**: Symfony cache disabled for development

### **Production Considerations**
- **Multi-stage Builds**: Optimized Docker images
- **Health Checks**: Container health monitoring
- **Resource Limits**: Memory and CPU constraints
- **Security**: Non-root user execution

## 🔒 Security

### **Development Security**
- **Local Only**: Services bound to localhost
- **Debug Mode**: Full error reporting (development only)
- **Test Data**: Safe test data in containers

### **Production Security**
- **Network Isolation**: Services in isolated networks
- **Secrets Management**: Environment variables for sensitive data
- **SSL/TLS**: HTTPS termination at load balancer
- **Access Control**: Database and Redis access restrictions

## 📚 Additional Resources

### **Docker Documentation**
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [Multi-stage Builds](https://docs.docker.com/develop/dev-best-practices/multistage-build/)

### **Symfony Docker Integration**
- [Symfony Docker Documentation](https://github.com/dunglas/symfony-docker)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.php)
- [Nginx Configuration](https://nginx.org/en/docs/)

### **Database Optimization**
- [PostgreSQL Performance Tuning](https://www.postgresql.org/docs/current/runtime-config-resource.html)
- [Redis Configuration](https://redis.io/topics/config)
- [Doctrine Database Configuration](https://symfony.com/doc/current/doctrine.html) 