# Cache Strategy Documentation

## Overview

This project implements a comprehensive caching strategy using Symfony's cache component with filesystem adapter for development and testing environments.

## Cache Configuration

### Environment-Specific TTL Settings

| Environment | Result Cache TTL | Query Cache TTL | Purpose |
|-------------|------------------|-----------------|---------|
| **Development** | 1 second | 1 second | Fast development iteration |
| **Production** | 3600 seconds (1 hour) | 1800 seconds (30 min) | Performance optimization |
| **Testing** | 1 second | 1 second | Fast test execution |

### Environment Variables

```bash
# Development
DOCTRINE_RESULT_CACHE_TTL=1
DOCTRINE_QUERY_CACHE_TTL=1

# Testing
DOCTRINE_RESULT_CACHE_TTL_TEST=1
DOCTRINE_QUERY_CACHE_TTL_TEST=1

# Production (when deployed)
DOCTRINE_RESULT_CACHE_TTL=3600
DOCTRINE_QUERY_CACHE_TTL=1800
```

## Cache Pools

### Doctrine Cache Pools

1. **`doctrine.result_cache_pool`**
   - **Purpose**: Caches query results
   - **Adapter**: Filesystem
   - **Usage**: Repository methods with expensive queries
   - **TTL**: Environment-specific

2. **`doctrine.query_cache_pool`**
   - **Purpose**: Caches SQL queries
   - **Adapter**: Filesystem
   - **Usage**: Automatic by Doctrine ORM
   - **TTL**: Environment-specific

### System Cache Pools

- `cache.validator` - Validation rules cache
- `cache.annotations` - Doctrine annotations cache
- `cache.serializer` - Serialization cache
- `cache.property_info` - Reflection cache
- `cache.messenger.restart_workers_signal` - Messenger signals

## Usage Examples

### Repository Caching

```php
class FruitRepository extends ServiceEntityRepository
{
    public function findAllWithCache(): array
    {
        return $this->resultCache->get('fruits_all', function() {
            return $this->createQueryBuilder('f')
                ->orderBy('f.name', 'ASC')
                ->getQuery()
                ->getResult();
        });
    }

    public function findExpensiveFruitsWithCustomTTL(int $ttlSeconds = 60): array
    {
        return $this->resultCache->get('expensive_fruits', function() {
            return $this->createQueryBuilder('f')
                ->where('f.quantity > :minQuantity')
                ->setParameter('minQuantity', 200.0)
                ->getQuery()
                ->getResult();
        }, $ttlSeconds);
    }
}
```

### Cache Management

```php
// Clear specific cache
$this->resultCache->delete('fruits_all');

// Clear all caches
$this->resultCache->clear();
```

## Redis Management

### Redis Commander Web GUI

- **URL**: http://localhost:8082
- **Username**: admin
- **Password**: admin123
- **Features**:
  - View all Redis keys
  - Monitor cache usage
  - Manual cache management
  - Real-time statistics

### Access from Homepage

The Redis Commander is accessible via the homepage link: **🔴 Redis Commander**

## Testing Strategy

### Test Environment TTL

- **Result Cache TTL**: 1 second
- **Query Cache TTL**: 1 second
- **Purpose**: Ensure tests run quickly and don't interfere with each other

### Development Environment TTL

- **Result Cache TTL**: 1 second
- **Query Cache TTL**: 1 second
- **Purpose**: Fast development iteration and debugging

### Test Isolation

```php
class AbstractIntegrationTestCase extends KernelTestCase
{
    protected function clearCache(): void
    {
        // Clear Doctrine result cache
        $resultCache = $this->container->get('doctrine.result_cache_pool');
        $resultCache->clear();
        
        // Clear Doctrine query cache
        $queryCache = $this->container->get('doctrine.query_cache_pool');
        $queryCache->clear();
    }
}
```

## Best Practices

### 1. Cache Key Naming

```php
// Good
$cacheKey = 'fruits_by_name_' . md5($name);
$cacheKey = 'fruits_quantity_range_' . $minQuantity . '_' . $maxQuantity;

// Bad
$cacheKey = 'cache_key_1';
$cacheKey = 'data';
```

### 2. TTL Selection

```php
// Short TTL for frequently changing data
$ttl = 60; // 1 minute

// Medium TTL for semi-static data
$ttl = 3600; // 1 hour

// Long TTL for static data
$ttl = 86400; // 24 hours
```

### 3. Cache Invalidation

```php
// Clear related caches when data changes
public function clearFruitCaches(): void
{
    $this->resultCache->delete('fruits_all');
    $this->resultCache->delete('fruits_by_name_' . md5('Apple'));
}
```

## Monitoring

### Cache Performance Metrics

- **Hit Rate**: Monitor cache hit/miss ratios
- **Memory Usage**: Track cache size
- **TTL Effectiveness**: Analyze cache expiration patterns

### Redis Commander Features

- **Real-time monitoring**
- **Key inspection**
- **Memory usage analysis**
- **Performance metrics**

## Future Enhancements

### Redis Integration

When Redis compatibility issues are resolved:

```yaml
framework:
    cache:
        app: cache.adapter.redis
        default_redis_provider: 'redis://%env(REDIS_HOST)%:%env(REDIS_PORT)%/%env(REDIS_DB)%'
```

### Advanced Caching

- **Cache warming strategies**
- **Distributed caching**
- **Cache compression**
- **Cache tagging for invalidation** 