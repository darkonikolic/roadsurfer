# API Documentation with Nelmio

## Overview

This project uses **Nelmio API Documentation Bundle** to provide interactive API documentation for all endpoints. The documentation is accessible at `/api/doc` and allows developers to test endpoints directly from the browser.

## Features

### ✅ **Interactive API Documentation**
- **Swagger UI Interface**: Clean, modern interface at `/api/doc`
- **JSON API Specification**: Available at `/api/doc.json`
- **Real-time Testing**: Test endpoints directly from the documentation
- **Request/Response Examples**: Comprehensive examples for all endpoints

### ✅ **Complete Endpoint Coverage**
All API endpoints are documented with:
- **OpenAPI 3.0 Annotations**: Full specification compliance
- **Request Body Schemas**: Detailed input validation rules
- **Response Examples**: Success and error scenarios
- **Parameter Documentation**: Query parameters, path variables
- **HTTP Status Codes**: All possible response codes

## API Endpoints

### 🍎 **Fruits Management**

#### `GET /api/fruits`
- **Description**: List all fruits with optional search and unit conversion
- **Parameters**:
  - `search` (optional): Search term for fruit names
  - `unit` (optional): Unit for quantity (g or kg), default: g
- **Response**: Array of fruits with id, name, quantity, unit

#### `POST /api/fruits`
- **Description**: Add a new fruit to the collection
- **Request Body**:
  ```json
  {
    "name": "Apple",
    "quantity": 1.5,
    "unit": "kg"
  }
  ```
- **Validation**: Name required, quantity positive, unit must be kg or g
- **Response**: Created fruit with ID and converted quantity

#### `DELETE /api/fruits/{id}`
- **Description**: Remove a fruit by ID
- **Parameters**: `id` (path): Fruit ID
- **Response**: Success/error message

### 🥕 **Vegetables Management**

#### `GET /api/vegetables`
- **Description**: List all vegetables with optional search and unit conversion
- **Parameters**:
  - `search` (optional): Search term for vegetable names
  - `unit` (optional): Unit for quantity (g or kg), default: g
- **Response**: Array of vegetables with id, name, quantity, unit

#### `POST /api/vegetables`
- **Description**: Add a new vegetable to the collection
- **Request Body**:
  ```json
  {
    "name": "Carrot",
    "quantity": 0.5,
    "unit": "kg"
  }
  ```
- **Validation**: Name required, quantity positive, unit must be kg or g
- **Response**: Created vegetable with ID and converted quantity

#### `DELETE /api/vegetables/{id}`
- **Description**: Remove a vegetable by ID
- **Parameters**: `id` (path): Vegetable ID
- **Response**: Success/error message

## Testing via Nelmio Documentation

### 🔧 **How to Test Endpoints**

1. **Access Documentation**: Navigate to `http://localhost:8080/api/doc`
2. **Select Endpoint**: Click on any endpoint to expand details
3. **Fill Parameters**: Enter required parameters and request body
4. **Execute Request**: Click "Try it out" button
5. **View Response**: See real response with status code and data

### 📋 **Testing Scenarios**

#### **Valid Requests**
- Add fruits/vegetables with valid data
- List items with search parameters
- Remove items by valid ID
- Unit conversion (kg ↔ g)

#### **Error Handling**
- Invalid JSON data
- Missing required fields
- Negative quantities
- Invalid units
- Non-existent IDs
- Database errors

### 🧪 **Automated Testing**

All endpoints have comprehensive test coverage:
- **Controller Tests**: Integration tests for all endpoints
- **Service Tests**: Unit tests with mocked dependencies
- **Validation Tests**: Input validation scenarios
- **Error Handling**: Exception scenarios

## Configuration

### **Nelmio Bundle Setup**

```yaml
# config/packages/nelmio_api_doc.yaml
nelmio_api_doc:
    documentation:
        info:
            title: Fruits and Vegetables API
            description: API for managing fruits and vegetables collections
            version: 1.0.0
        components:
            securitySchemes:
                bearerAuth:
                    type: http
                    scheme: bearer
                    bearerFormat: JWT
        security:
            - bearerAuth: []
    areas:
        path_patterns:
            - ^/api(?!/doc$)
```

### **Routes Configuration**

```yaml
# config/routes/nelmio_api_doc.yaml
app.swagger_ui:
    path: /api/doc
    methods: GET
    defaults: { _controller: nelmio_api_doc.controller.swagger_ui }

app.swagger:
    path: /api/doc.json
    methods: GET
    defaults: { _controller: nelmio_api_doc.controller.swagger }
```

## Development Workflow

### **Adding New Endpoints**

1. **Create Controller**: Add new controller with OpenAPI annotations
2. **Add Tests**: Write comprehensive controller tests
3. **Update Documentation**: Ensure all annotations are complete
4. **Test via UI**: Verify endpoint works in Swagger UI

### **Annotation Examples**

```php
/**
 * @OA\Get(
 *     path="/api/fruits",
 *     summary="List all fruits",
 *     description="Retrieve a list of all fruits with optional search and unit conversion",
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search term for fruit names",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of fruits retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Fruits retrieved successfully"),
 *             @OA\Property(property="data", type="array", @OA\Items(...))
 *         )
 *     )
 * )
 */
```

## Benefits

### **For Developers**
- **Self-Documenting API**: Clear, interactive documentation
- **Easy Testing**: Test endpoints without external tools
- **Consistent Responses**: Standardized API response format
- **Validation Rules**: Clear input validation requirements

### **For Integration**
- **OpenAPI Compliance**: Standard specification for API tools
- **Code Generation**: Generate client libraries automatically
- **Testing Automation**: Automated API testing capabilities
- **Documentation**: Always up-to-date with code changes

## Troubleshooting

### **Common Issues**

1. **Documentation Not Loading**
   - Check if Nelmio bundle is installed
   - Verify routes are configured correctly
   - Clear cache: `php bin/console cache:clear`

2. **Annotations Not Showing**
   - Ensure OpenAPI annotations are properly formatted
   - Check for syntax errors in annotations
   - Verify controller is in correct namespace

3. **Testing Issues**
   - Check if database is accessible
   - Verify Redis connection for caching
   - Ensure all services are properly configured

### **Debug Commands**

```bash
# Check if documentation is accessible
curl http://localhost:8080/api/doc.json

# Clear cache
php bin/console cache:clear

# Check routes
php bin/console debug:router | grep api
```

## Future Enhancements

### **Planned Improvements**
- **Authentication**: Add JWT authentication documentation
- **Rate Limiting**: Document rate limiting policies
- **Webhooks**: Add webhook endpoint documentation
- **Bulk Operations**: Document bulk create/update endpoints
- **Advanced Filtering**: Add complex search/filter documentation

### **Integration Possibilities**
- **API Gateway**: Integration with API management platforms
- **Client Generation**: Auto-generate client libraries
- **Monitoring**: Integration with API monitoring tools
- **Analytics**: Track API usage and performance 