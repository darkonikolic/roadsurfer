# Cache Strategy Documentation

## Overview

This document describes the caching strategy implemented for the Fruits and Vegetables Service using Redis for improved performance on search operations.

## Architecture

### Cache Layer Location
- **Path**: `src/Infrastructure/External/Cache/`
- **Services**: `FruitCacheService`, `VegetableCacheService`
- **Tests**: `tests/Infrastructure/External/Cache/`

### Technology Stack
- **Cache Engine**: Redis
- **Serialization**: JSON
- **TTL Strategy**: Different TTLs for different cache types
- **Invalidation**: Pattern-based bulk deletion

## Cache Services

### FruitCacheService

Handles caching for fruit-related search operations.

#### Methods
- `getFruits(): ?array` - Retrieve all fruits from cache
- `setFruits(array $fruits): bool` - Store all fruits in cache
- `getFruitsByName(string $searchTerm): ?array` - Retrieve fruits by name search
- `setFruitsByName(string $searchTerm, array $fruits): bool` - Store search results
- `invalidateCache(): bool` - Clear all fruit cache entries

#### Cache Keys
```
fruits:all                    // All fruits list
fruits:search:{md5_hash}      // Search results by term
```

### VegetableCacheService

Handles caching for vegetable-related search operations.

#### Methods
- `getVegetables(): ?array` - Retrieve all vegetables from cache
- `setVegetables(array $vegetables): bool` - Store all vegetables in cache
- `getVegetablesByName(string $searchTerm): ?array` - Retrieve vegetables by name search
- `setVegetablesByName(string $searchTerm, array $vegetables): bool` - Store search results
- `invalidateCache(): bool` - Clear all vegetable cache entries

#### Cache Keys
```
vegetables:all                // All vegetables list
vegetables:search:{md5_hash}  // Search results by term
```

## TTL Strategy

### Time-to-Live Configuration
```php
private const CACHE_TTL_ALL = 3600;     // 1 hour for complete lists
private const CACHE_TTL_SEARCH = 1800;  // 30 minutes for search results
```

### Rationale
- **Complete Lists (1 hour)**: Less frequent updates, longer cache duration
- **Search Results (30 minutes)**: More dynamic, shorter cache duration
- **Balance**: Performance vs. data freshness

## Cache Invalidation Strategy

### Pattern-Based Invalidation
```php
// Fruit cache invalidation
$pattern = 'fruits:*';
$keys = $this->redis->keys($pattern);
foreach ($keys as $key) {
    $this->redis->del($key);
}
```

### Invalidation Triggers
- **Database Insert**: New fruit/vegetable added
- **Database Update**: Existing fruit/vegetable modified
- **Database Delete**: Fruit/vegetable removed
- **Manual Invalidation**: Admin-triggered cache clear

### Invalidation Patterns
```
fruits:*        // All fruit-related cache entries
vegetables:*    // All vegetable-related cache entries
```

## Data Serialization

### JSON Format
```json
[
    {
        "id": 1,
        "name": "Apple",
        "quantity": 20000.0,
        "created_at": "2025-01-10T10:00:00+00:00",
        "updated_at": "2025-01-10T10:00:00+00:00"
    }
]
```

### Serialization Process
1. **Entity to Array**: Convert Doctrine entities to arrays
2. **DateTime Formatting**: ISO 8601 format for timestamps
3. **JSON Encoding**: UTF-8 encoded JSON strings
4. **Redis Storage**: Binary-safe storage

## Error Handling

### Redis Connection Errors
```php
try {
    $result = $this->redis->get($cacheKey);
} catch (\RedisException $e) {
    // Log error and return null (cache miss)
    throw $e;
}
```

### Cache Miss Handling
```php
if ($cachedData === false) {
    return null; // Cache miss, fetch from database
}
```

## Performance Considerations

### Cache Hit Benefits
- **Reduced Database Load**: Avoid repeated queries
- **Faster Response Times**: In-memory data access
- **Scalability**: Redis can handle high concurrent access

### Cache Miss Strategy
- **Graceful Degradation**: Fallback to database
- **Background Population**: Cache populated after miss
- **No Blocking**: Application continues without cache

### Memory Management
- **TTL Expiration**: Automatic cleanup of old entries
- **Pattern-Based Cleanup**: Bulk deletion for invalidation
- **Memory Monitoring**: Redis memory usage tracking

## Testing Strategy

### Unit Tests
- **Mock Redis**: Isolated testing without Redis dependency
- **Cache Hit/Miss Scenarios**: Test all cache states
- **Error Handling**: Redis connection failures
- **Invalidation Testing**: Pattern-based deletion

### Integration Tests
- **Real Redis**: End-to-end cache operations
- **Database Integration**: Cache-database consistency
- **Performance Testing**: Response time measurements

## Configuration

### Redis Connection
```yaml
# config/services.yaml
services:
    App\Infrastructure\External\Cache\FruitCacheService:
        arguments:
            $redis: '@redis'
    
    App\Infrastructure\External\Cache\VegetableCacheService:
        arguments:
            $redis: '@redis'
```

### Environment Variables
```env
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_DB=0
```

## Monitoring and Metrics

### Key Metrics
- **Cache Hit Ratio**: Percentage of cache hits
- **Response Times**: API response time improvements
- **Memory Usage**: Redis memory consumption
- **Error Rates**: Cache operation failures

### Monitoring Tools
- **Redis INFO**: Built-in Redis monitoring
- **Application Logs**: Cache operation logging
- **Performance Profiling**: Cache impact measurement

## Best Practices

### Cache Key Design
- **Consistent Naming**: `{entity}:{operation}:{identifier}`
- **Hash Collision Prevention**: MD5 for search terms
- **Namespace Separation**: Different prefixes for different entities

### TTL Management
- **Business Logic Alignment**: TTL based on data volatility
- **Memory Optimization**: Balance between performance and memory
- **Update Frequency**: Align with data change patterns

### Error Resilience
- **Graceful Degradation**: Application works without cache
- **Error Logging**: Comprehensive error tracking
- **Retry Mechanisms**: Automatic retry for transient failures

## Future Enhancements

### Planned Improvements
- **Cache Warming**: Pre-populate cache on startup
- **Compression**: Reduce memory usage for large datasets
- **Distributed Caching**: Multi-node Redis cluster
- **Cache Analytics**: Detailed cache performance metrics

### Scalability Considerations
- **Redis Cluster**: Horizontal scaling
- **Cache Sharding**: Distribute load across nodes
- **Read Replicas**: Separate read/write operations
- **Memory Optimization**: Efficient data structures 