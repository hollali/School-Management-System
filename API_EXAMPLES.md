# Student Management - API Examples

## Base URL
```
http://localhost:8000/api
```

## Authentication
All requests require a Bearer token in the Authorization header:
```
Authorization: Bearer {access_token}
```

## 1. List Students

**Request:**
```bash
GET /api/students
GET /api/students?page=1&per_page=15
GET /api/students?search=john
GET /api/students?class_id=1
GET /api/students?parent_id=1
GET /api/students?gender=Male
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "admission_number": "ADM-001",
      "date_of_birth": "2010-05-15",
      "gender": "Male",
      "phone": "+233244123456",
      "address": "123 Main Street",
      "parent_id": 1,
      "parent": {
        "id": 1,
        "user": {
          "id": 2,
          "name": "Jane Doe"
        }
      },
      "classes": [
        {
          "id": 1,
          "name": "Class 1A",
          "pivot": {
            "assigned_at": "2026-06-07T10:30:00.000Z",
            "status": "active"
          }
        }
      ],
      "created_at": "2026-06-07T10:00:00.000Z",
      "updated_at": "2026-06-07T10:00:00.000Z"
    }
  ],
  "pagination": {
    "total": 50,
    "per_page": 15,
    "current_page": 1,
    "last_page": 4
  }
}
```

## 2. Get Single Student

**Request:**
```bash
GET /api/students/1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "admission_number": "ADM-001",
    "date_of_birth": "2010-05-15",
    "gender": "Male",
    "phone": "+233244123456",
    "address": "123 Main Street",
    "parent": {
      "id": 1,
      "user": { "name": "Jane Doe" }
    },
    "classes": [
      {
        "id": 1,
        "name": "Class 1A",
        "pivot": {
          "status": "active",
          "assigned_at": "2026-06-07T10:30:00.000Z"
        }
      }
    ]
  }
}
```

## 3. Create Student

**Request:**
```bash
POST /api/students
Content-Type: application/json
Authorization: Bearer {token}

{
  "name": "Jane Smith",
  "email": "jane.smith@example.com",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123",
  "admission_number": "ADM-051",
  "date_of_birth": "2011-03-20",
  "gender": "Female",
  "phone": "+233244789012",
  "address": "456 Oak Avenue",
  "parent_id": 2,
  "class_id": 2
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Student created successfully",
  "data": {
    "id": 51,
    "user": {
      "id": 51,
      "name": "Jane Smith",
      "email": "jane.smith@example.com"
    },
    "admission_number": "ADM-051",
    "date_of_birth": "2011-03-20",
    "gender": "Female",
    "phone": "+233244789012",
    "address": "456 Oak Avenue",
    "parent_id": 2,
    "classes": [
      {
        "id": 2,
        "name": "Class 1B",
        "pivot": {
          "status": "active",
          "assigned_at": "2026-06-07T13:45:00.000Z"
        }
      }
    ]
  }
}
```

## 4. Update Student

**Request:**
```bash
PUT /api/students/1
Content-Type: application/json
Authorization: Bearer {token}

{
  "name": "John Doe Updated",
  "email": "john.updated@example.com",
  "phone": "+233244999999",
  "address": "789 New Street"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Student updated successfully",
  "data": {
    "id": 1,
    "user": {
      "id": 1,
      "name": "John Doe Updated",
      "email": "john.updated@example.com"
    },
    "phone": "+233244999999",
    "address": "789 New Street"
  }
}
```

## 5. Delete Student

**Request:**
```bash
DELETE /api/students/1
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Student deleted successfully"
}
```

## 6. Get Student's Classes

**Request:**
```bash
GET /api/students/1/classes
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Class 1A",
      "grade": 1,
      "section": "A",
      "teacher_id": 1,
      "subjects": [
        {
          "id": 1,
          "name": "Mathematics",
          "code": "MATH101"
        },
        {
          "id": 2,
          "name": "English",
          "code": "ENG101"
        }
      ]
    }
  ]
}
```

## 7. Get Student's Attendance

**Request:**
```bash
GET /api/students/1/attendance?page=1
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "date": "2026-06-07",
      "status": "present",
      "remarks": "On time",
      "created_at": "2026-06-07T08:30:00.000Z"
    },
    {
      "id": 2,
      "date": "2026-06-06",
      "status": "present",
      "remarks": "Late by 10 minutes",
      "created_at": "2026-06-06T08:45:00.000Z"
    }
  ],
  "pagination": {
    "total": 120,
    "per_page": 20,
    "current_page": 1
  }
}
```

## 8. Get Student's Grades

**Request:**
```bash
GET /api/students/1/grades
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "exam_id": 1,
      "exam": {
        "id": 1,
        "name": "Mid Term Exam",
        "date": "2026-05-15"
      },
      "subject_id": 1,
      "score": 85,
      "grade": "A",
      "remarks": "Excellent performance",
      "created_at": "2026-05-20T10:00:00.000Z"
    }
  ]
}
```

## 9. Assign Class to Student

**Request:**
```bash
POST /api/students/1/assign-class
Content-Type: application/json
Authorization: Bearer {token}

{
  "class_id": 2,
  "status": "active"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Class assigned successfully",
  "data": {
    "id": 1,
    "classes": [
      {
        "id": 2,
        "name": "Class 1B",
        "pivot": {
          "status": "active",
          "assigned_at": "2026-06-07T14:00:00.000Z"
        }
      }
    ]
  }
}
```

## 10. Bulk Import Students

**Request:**
```bash
POST /api/students/bulk-import
Content-Type: multipart/form-data
Authorization: Bearer {token}

[file] CSV file with headers:
name,email,password,admission_number,date_of_birth,gender,phone,address,parent_id
```

**CSV Example:**
```csv
name,email,password,admission_number,date_of_birth,gender,phone,address,parent_id
John Doe,john@example.com,Pass123!,ADM-101,2010-05-15,Male,+233244123456,123 Main St,1
Jane Doe,jane@example.com,Pass123!,ADM-102,2010-06-20,Female,+233244123457,456 Oak Ave,1
Bob Smith,bob@example.com,Pass123!,ADM-103,2011-01-10,Male,+233244123458,789 Pine St,2
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Imported 3 students",
  "imported": 3,
  "errors": []
}
```

## Error Responses

### 400 Bad Request - Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Student not found"
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Error creating student",
  "error": "Database connection error"
}
```

## cURL Examples

### List students
```bash
curl -X GET http://localhost:8000/api/students \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Create student
```bash
curl -X POST http://localhost:8000/api/students \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "SecurePass123",
    "password_confirmation": "SecurePass123",
    "class_id": 1
  }'
```

### Get specific student
```bash
curl -X GET http://localhost:8000/api/students/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update student
```bash
curl -X PUT http://localhost:8000/api/students/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"phone": "+233244999999"}'
```

### Delete student
```bash
curl -X DELETE http://localhost:8000/api/students/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```
