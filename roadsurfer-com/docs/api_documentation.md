# API Documentation

## Overview

This document provides comprehensive API documentation for the Fruits and Vegetables service, including all endpoints, request/response formats, and cURL examples for testing.

## Base URL

```
http://localhost:8080
```

## Authentication

Currently, no authentication is required for API endpoints.

## Endpoints

### Health Check

#### GET /health

Returns the health status of the application.

**cURL Example:**
```bash
curl -X GET "http://localhost:8080/health" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2025-01-10T12:00:00+00:00",
  "services": {
    "database": "healthy",
    "redis": "healthy"
  }
}
```

### File Content

#### GET /api/file-content

Returns the content of the request.json file with processed fruits and vegetables.

**cURL Example:**
```bash
curl -X GET "http://localhost:8080/api/file-content" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "fruits": [
    {
      "id": 2,
      "name": "Apples",
      "type": "fruit",
      "quantity": 20.0,
      "unit": "kg"
    }
  ],
  "vegetables": [
    {
      "id": 1,
      "name": "Carrot",
      "type": "vegetable",
      "quantity": 10922.0,
      "unit": "g"
    }
  ]
}
```

### Fruits API

#### GET /api/fruits

Retrieve a list of all fruits with optional search and unit conversion.

**Query Parameters:**
- `search` (optional): Search term for fruit names
- `unit` (optional): Unit for quantity (g or kg), default: g

**cURL Examples:**

**Get all fruits:**
```bash
curl -X GET "http://localhost:8080/api/fruits" \
  -H "Accept: application/json"
```

**Get fruits with search:**
```bash
curl -X GET "http://localhost:8080/api/fruits?search=Apple" \
  -H "Accept: application/json"
```

**Get fruits with unit conversion:**
```bash
curl -X GET "http://localhost:8080/api/fruits?unit=kg" \
  -H "Accept: application/json"
```

**Get fruits with search and unit:**
```bash
curl -X GET "http://localhost:8080/api/fruits?search=Apple&unit=kg" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Fruits retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Apple",
      "quantity": 500.0,
      "unit": "g"
    }
  ]
}
```

#### POST /api/fruits

Add a new fruit to the collection.

**Request Body:**
```json
{
  "name": "Apple",
  "quantity": 1.5,
  "unit": "kg"
}
```

**cURL Example:**
```bash
curl -X POST "http://localhost:8080/api/fruits" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Apple",
    "quantity": 1.5,
    "unit": "kg"
  }'
```

**Response (Success - 201):**
```json
{
  "success": true,
  "message": "Fruit added successfully",
  "data": {
    "id": 1,
    "name": "Apple",
    "quantity": 1500.0,
    "unit": "g"
  }
}
```

**Response (Validation Error - 400):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": [
    "Name cannot be empty",
    "Quantity must be greater than 0"
  ]
}
```

#### DELETE /api/fruits/{id}

Remove a fruit from the collection by ID.

**cURL Example:**
```bash
curl -X DELETE "http://localhost:8080/api/fruits/1" \
  -H "Accept: application/json"
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Fruit removed successfully"
}
```

**Response (Not Found - 404):**
```json
{
  "success": false,
  "message": "Fruit not found"
}
```

### Vegetables API

#### GET /api/vegetables

Retrieve a list of all vegetables with optional search and unit conversion.

**Query Parameters:**
- `search` (optional): Search term for vegetable names
- `unit` (optional): Unit for quantity (g or kg), default: g

**cURL Examples:**

**Get all vegetables:**
```bash
curl -X GET "http://localhost:8080/api/vegetables" \
  -H "Accept: application/json"
```

**Get vegetables with search:**
```bash
curl -X GET "http://localhost:8080/api/vegetables?search=Carrot" \
  -H "Accept: application/json"
```

**Get vegetables with unit conversion:**
```bash
curl -X GET "http://localhost:8080/api/vegetables?unit=kg" \
  -H "Accept: application/json"
```

**Get vegetables with search and unit:**
```bash
curl -X GET "http://localhost:8080/api/vegetables?search=Carrot&unit=kg" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "message": "Vegetables retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Carrot",
      "quantity": 300.0,
      "unit": "g"
    }
  ]
}
```

#### POST /api/vegetables

Add a new vegetable to the collection.

**Request Body:**
```json
{
  "name": "Carrot",
  "quantity": 0.5,
  "unit": "kg"
}
```

**cURL Example:**
```bash
curl -X POST "http://localhost:8080/api/vegetables" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Carrot",
    "quantity": 0.5,
    "unit": "kg"
  }'
```

**Response (Success - 201):**
```json
{
  "success": true,
  "message": "Vegetable added successfully",
  "data": {
    "id": 1,
    "name": "Carrot",
    "quantity": 500.0,
    "unit": "g"
  }
}
```

**Response (Validation Error - 400):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": [
    "Name cannot be empty",
    "Quantity must be greater than 0"
  ]
}
```

#### DELETE /api/vegetables/{id}

Remove a vegetable from the collection by ID.

**cURL Example:**
```bash
curl -X DELETE "http://localhost:8080/api/vegetables/1" \
  -H "Accept: application/json"
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Vegetable removed successfully"
}
```

**Response (Not Found - 404):**
```json
{
  "success": false,
  "message": "Vegetable not found"
}
```

## Testing with cURL

### Complete Test Suite

Here's a complete set of cURL commands to test all endpoints:

```bash
#!/bin/bash

# Base URL
BASE_URL="http://localhost:8080"

echo "=== Testing Health Check ==="
curl -X GET "$BASE_URL/health" -H "Accept: application/json"

echo -e "\n\n=== Testing File Content ==="
curl -X GET "$BASE_URL/api/file-content" -H "Accept: application/json"

echo -e "\n\n=== Testing Fruits API ==="

# Get all fruits
echo "GET /api/fruits"
curl -X GET "$BASE_URL/api/fruits" -H "Accept: application/json"

# Get fruits with search
echo -e "\nGET /api/fruits?search=Apple"
curl -X GET "$BASE_URL/api/fruits?search=Apple" -H "Accept: application/json"

# Get fruits with unit conversion
echo -e "\nGET /api/fruits?unit=kg"
curl -X GET "$BASE_URL/api/fruits?unit=kg" -H "Accept: application/json"

# Add a new fruit
echo -e "\nPOST /api/fruits"
curl -X POST "$BASE_URL/api/fruits" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Apple",
    "quantity": 2.0,
    "unit": "kg"
  }'

# Delete a fruit (replace {id} with actual ID)
echo -e "\nDELETE /api/fruits/1"
curl -X DELETE "$BASE_URL/api/fruits/1" -H "Accept: application/json"

echo -e "\n\n=== Testing Vegetables API ==="

# Get all vegetables
echo "GET /api/vegetables"
curl -X GET "$BASE_URL/api/vegetables" -H "Accept: application/json"

# Get vegetables with search
echo -e "\nGET /api/vegetables?search=Carrot"
curl -X GET "$BASE_URL/api/vegetables?search=Carrot" -H "Accept: application/json"

# Get vegetables with unit conversion
echo -e "\nGET /api/vegetables?unit=kg"
curl -X GET "$BASE_URL/api/vegetables?unit=kg" -H "Accept: application/json"

# Add a new vegetable
echo -e "\nPOST /api/vegetables"
curl -X POST "$BASE_URL/api/vegetables" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Carrot",
    "quantity": 1.0,
    "unit": "kg"
  }'

# Delete a vegetable (replace {id} with actual ID)
echo -e "\nDELETE /api/vegetables/1"
curl -X DELETE "$BASE_URL/api/vegetables/1" -H "Accept: application/json"
```

### Quick Test Commands

**Test Health:**
```bash
curl -X GET "http://localhost:8080/health"
```

**Test File Content:**
```bash
curl -X GET "http://localhost:8080/api/file-content"
```

**Test Fruits (GET):**
```bash
curl -X GET "http://localhost:8080/api/fruits"
```

**Test Fruits (POST):**
```bash
curl -X POST "http://localhost:8080/api/fruits" \
  -H "Content-Type: application/json" \
  -d '{"name": "Apple", "quantity": 1.5, "unit": "kg"}'
```

**Test Vegetables (GET):**
```bash
curl -X GET "http://localhost:8080/api/vegetables"
```

**Test Vegetables (POST):**
```bash
curl -X POST "http://localhost:8080/api/vegetables" \
  -H "Content-Type: application/json" \
  -d '{"name": "Carrot", "quantity": 0.5, "unit": "kg"}'
```

## Error Handling

All endpoints return consistent error responses:

### Validation Errors (400)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": ["Error message 1", "Error message 2"]
}
```

### Not Found Errors (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Internal Server Errors (500)
```json
{
  "success": false,
  "message": "Internal server error",
  "errors": ["Error details"]
}
```

## Unit Conversion

The API automatically converts units:
- **Input**: kg or g
- **Storage**: Always stored in grams
- **Output**: Can be returned in kg or g based on `unit` parameter

### Examples:
- Input: `{"quantity": 1.5, "unit": "kg"}` → Stored as `1500g`
- Input: `{"quantity": 500, "unit": "g"}` → Stored as `500g`
- GET with `?unit=kg`: Returns quantities in kg
- GET with `?unit=g`: Returns quantities in g (default)

## Testing Tips

1. **Start with health check** to ensure server is running
2. **Test GET endpoints first** to see existing data
3. **Use POST to add data** before testing DELETE
4. **Check response status codes** and error messages
5. **Verify unit conversion** by testing different units
6. **Test search functionality** with various search terms

## Interactive Documentation

For interactive testing, visit:
- **Swagger UI**: `http://localhost:8080/api/doc`
- **Health Check**: `http://localhost:8080/health` 