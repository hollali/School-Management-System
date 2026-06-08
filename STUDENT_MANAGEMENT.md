# Student Management Module - Implementation Guide

## Overview
This document details the **Student Management** module implementation for the School Management System, a final-year Laravel project.

## ✅ Completed Components

### 1. **Database Models & Relationships**
- ✅ `Student` Model with relationships to:
  - `User` (authentication account)
  - `ParentProfile` (parent/guardian)
  - `SchoolClass` (many-to-many with pivot: assigned_at, status)

### 2. **API Endpoints** (`/api/students`)
```
GET    /api/students                 - List all students (paginated, filterable)
GET    /api/students/{id}            - Get student details
POST   /api/students                 - Create new student
PUT    /api/students/{id}            - Update student
DELETE /api/students/{id}            - Delete student
GET    /api/students/{id}/classes    - Get student's classes
GET    /api/students/{id}/attendance - Get attendance history
GET    /api/students/{id}/grades     - Get grades/results
POST   /api/students/{id}/assign-class - Assign to class
POST   /api/students/bulk-import     - Import students from CSV
```

### 3. **Web Controllers**
- ✅ `StudentController` - Full CRUD with web views
- ✅ `StudentApiController` - RESTful API endpoints

### 4. **Request Validation**
- ✅ `StoreStudentRequest` - Create validation rules
- ✅ `UpdateStudentRequest` - Update validation rules

**Validation Rules:**
```
name: required, string, max 255
email: required, email, unique on create
password: required, min 8, confirmed
admission_number: nullable, unique
date_of_birth: nullable, date, before today
gender: Male|Female|Other
phone: nullable, max 32
address: nullable, max 500
parent_id: nullable, exists in parents
class_id: nullable, exists in classes
```

### 5. **Query Scopes** (Student Model)
```php
$query->searchByName('John')
$query->searchByEmail('john@example.com')
$query->searchByAdmissionNumber('ADM-001')
$query->byClass($classId)
$query->byParent($parentId)
$query->byGender('Male')
$query->active()
```

### 6. **Blade Views**
- ✅ `resources/views/students/index.blade.php` - Student listing with filters
- ✅ `resources/views/students/create.blade.php` - Create form
- ✅ `resources/views/students/edit.blade.php` - Edit form
- ✅ `resources/views/students/show.blade.php` - Student profile view

**Features:**
- Search by name, email, or admission number
- Filter by gender
- Pagination (15 per page)
- CRUD operations from UI
- Responsive Tailwind CSS design

### 7. **Database Factories & Seeders**
- ✅ `StudentFactory` - Generate test students
- ✅ `StudentSeeder` - Seed 50 sample students with classes and parents

**Usage:**
```bash
php artisan migrate --seed
php artisan db:seed --class=StudentSeeder
```

### 8. **Route Configuration**
- ✅ Web routes: `/students` (resource routing)
- ✅ API routes: `/api/students` (apiResource routing with middleware)

## 🔐 Security Features
- ✅ Authentication middleware on all routes
- ✅ Role-based access (Admin, Teacher only)
- ✅ Password hashing (bcrypt)
- ✅ CSRF protection on forms
- ✅ Unique email & admission number validation

## 📊 Features Implemented

### Core Features
1. **Student Registration** - Create user account + student profile
2. **Profile Management** - Update all student information
3. **Class Assignment** - Assign students to classes with status tracking
4. **Parent Linking** - Link students to parent/guardian
5. **Search & Filter** - Find students by multiple criteria
6. **Bulk Import** - Import students from CSV files
7. **Delete Students** - Remove student and user accounts

### Related Features
1. **View Classes** - See all classes assigned to a student
2. **View Attendance** - Access student's attendance records
3. **View Grades** - Check student's academic results
4. **Class Transfer** - Reassign student to different class

## 🔗 API Response Format

### Success Response
```json
{
  "success": true,
  "message": "Student created successfully",
  "data": {
    "id": 1,
    "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
    "admission_number": "ADM-001",
    "date_of_birth": "2010-05-15",
    "gender": "Male",
    "phone": "+233244123456",
    "address": "123 Main St",
    "parent": { "id": 1, "user": { "name": "Jane Doe" } },
    "classes": [{ "id": 1, "name": "Class 1A", "pivot": { "status": "active" } }]
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "error": "Detailed error info"
}
```

## 📁 File Structure
```
app/Http/
├── Controllers/
│   ├── StudentController.php          (Web routes)
│   └── StudentApiController.php       (API routes)
└── Requests/
    ├── StoreStudentRequest.php
    └── UpdateStudentRequest.php

app/Models/
└── Student.php                         (with scopes)

database/
├── factories/
│   └── StudentFactory.php
├── seeders/
│   └── StudentSeeder.php
└── migrations/
    └── *_create_student_class_tables.php

resources/views/students/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── show.blade.php

routes/
├── web.php                            (includes student routes)
└── api.php                            (includes API routes)
```

## 🚀 Getting Started

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Sample Data
```bash
php artisan db:seed --class=StudentSeeder
```

### 3. Access Student Management
- **Web**: Navigate to `/students`
- **API**: Send requests to `/api/students`

### 4. API Authentication
All API endpoints require Sanctum token:
```bash
Authorization: Bearer {token}
```

## 📝 Testing with API

### Create Student
```bash
POST /api/students
Content-Type: application/json
Authorization: Bearer {token}

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "admission_number": "ADM-001",
  "date_of_birth": "2010-05-15",
  "gender": "Male",
  "phone": "+233244123456",
  "address": "123 Main St",
  "parent_id": null,
  "class_id": 1
}
```

### List Students with Filters
```bash
GET /api/students?search=john&gender=Male&class_id=1&per_page=20
```

### Get Student Details
```bash
GET /api/students/1
```

### Update Student
```bash
PUT /api/students/1
Content-Type: application/json

{
  "name": "John Updated",
  "email": "john.updated@example.com",
  "phone": "+233244123457"
}
```

### Assign Class
```bash
POST /api/students/1/assign-class
Content-Type: application/json

{
  "class_id": 2,
  "status": "active"
}
```

### Bulk Import Students
```bash
POST /api/students/bulk-import
Content-Type: multipart/form-data

CSV file format:
name,email,password,admission_number,date_of_birth,gender,phone,address,parent_id

John Doe,john@example.com,pass123,ADM-001,2010-05-15,Male,+233244123456,123 Main St,1
Jane Doe,jane@example.com,pass123,ADM-002,2010-06-20,Female,+233244123457,456 Oak Ave,1
```

## ⚙️ Configuration

### Pagination
Default: 15 students per page (configurable via `per_page` parameter)

### Role Permissions
- **Admin** - Full access
- **Teacher** - Full access  
- **Parent** - View own child's profile (implement in Phase 3)
- **Student** - View own profile (implement in Phase 3)

## 🔄 Next Steps (Phase 2+)

### Phase 2: Attendance System
- Link students to attendance records
- Generate attendance reports

### Phase 3: Grades & Report Cards
- Record results for students
- Generate report cards
- Calculate GPA

### Phase 4: Parent Portal
- Parents view their child's:
  - Attendance
  - Grades
  - Behavior records
  - Homework assignments
- Parent notifications via messaging system

### Phase 5: Advanced Features
- Student transfer between schools
- Academic history/transcripts
- Student behavior tracking
- Fee payment tracking per student

## 📚 Resources

### Laravel Eloquent Relationships
- https://laravel.com/docs/eloquent-relationships
- https://laravel.com/docs/eloquent

### Spatie Permissions
- https://github.com/spatie/laravel-permission

### API Best Practices
- https://laravel.com/docs/sanctum
- https://laravel.com/docs/validation

## 🐛 Troubleshooting

### "SQLSTATE[HY000]: General error: 1 no such table"
**Solution**: Run migrations
```bash
php artisan migrate
```

### "Class not found" errors
**Solution**: Clear Laravel cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### "Unauthorized" on API endpoints
**Solution**: Ensure token is passed in Authorization header
```
Authorization: Bearer {your-token}
```

### Validation errors on create/update
**Solution**: Check error response message, ensure all required fields are valid

---

**Status**: ✅ Student Management (Phase 1) - COMPLETE  
**Next Phase**: Attendance System (Phase 2)  
**Last Updated**: 2026-06-07
