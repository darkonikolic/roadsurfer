# API Documentation

## 📋 **Overview**

This document describes the REST API endpoints for the Fruits and Vegetables application.

## 🔗 **Base URL**

- **Development**: `http://localhost:8080`
- **API Documentation**: `http://localhost:8080/api/doc`

## 📊 **Endpoints**

### **File Content API**

#### **GET /api/file_content**

Processes the `request.json` file and returns split fruits and vegetables data without importing to database.

**Request:**
```http
GET /api/file_content
```

**Response:**
```json
{
  "fruits": [
    {
      "id": 2,
      "name": "Apples",
      "type": "fruit",
      "quantity": 20000,
      "unit": "g"
    },
    {
      "id": 3,
      "name": "Pears",
      "type": "fruit",
      "quantity": 3500,
      "unit": "g"
    }
  ],
  "vegetables": [
    {
      "id": 1,
      "name": "Carrot",
      "type": "vegetable",
      "quantity": 10922,
      "unit": "g"
    },
    {
      "id": 5,
      "name": "Beans",
      "type": "vegetable",
      "quantity": 65000,
      "unit": "g"
    }
  ]
}
```

**Error Responses:**

**400 Bad Request:**
```json
{
  "error": "File not found: request.json"
}
```

**500 Internal Server Error:**
```json
{
  "error": "Internal server error: Processing failed"
}
```

### **Health Check API**

#### **GET /health**

Returns the application health status.

**Request:**
```http
GET /health
```

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2025-01-10T10:30:00Z",
  "services": {
    "database": "healthy",
    "redis": "healthy"
  }
}
```

## 🔧 **Testing**

### **Using curl**

```bash
# Test file content endpoint
curl -X GET http://localhost:8080/api/file_content

# Test health endpoint
curl -X GET http://localhost:8080/health
```

### **Using Postman**

1. Import the collection from `docs/postman_collection.json`
2. Set the base URL to `http://localhost:8080`
3. Run the requests

## 📚 **Interactive Documentation**

Visit [http://localhost:8080/api/doc](http://localhost:8080/api/doc) for interactive API documentation with:
- Request/response examples
- Try it out functionality
- Schema definitions
- Error responses

## 🔐 **Authentication**

Currently, the API endpoints are public and do not require authentication.

## 📈 **Rate Limiting**

No rate limiting is currently implemented.

## 🚨 **Error Handling**

All endpoints return appropriate HTTP status codes:
- `200` - Success
- `400` - Bad Request (validation errors)
- `404` - Not Found
- `500` - Internal Server Error

## 📝 **Data Formats**

### **Product Object**
```json
{
  "id": 1,
  "name": "Product Name",
  "type": "fruit|vegetable",
  "quantity": 100.0,
  "unit": "kg|g"
}
```

### **Error Object**
```json
{
  "error": "Error message description"
}
```

---

**Last Updated**: January 10, 2025  
**Version**: 1.0.0  
**Maintainer**: Development Team 