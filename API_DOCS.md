# API Documentation - Islamic Education Platform

## Overview

Base URL: `/api/`

All endpoints return JSON responses. Authentication is session-based.

---

## Authentication

### Login

```
POST /api/auth.php
```

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| email | string | ✓ | User email |
| password | string | ✓ | User password |

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "full_name": "أحمد",
    "email": "ahmed@example.com",
    "role": "student"
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

---

### Register

```
POST /api/auth.php
```

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| name | string | ✓ | Full name |
| email | string | ✓ | Email address |
| password | string | ✓ | Password (min 8 chars) |
| role | string | ✓ | student / teacher |

**Response:**
```json
{
  "success": true,
  "user_id": 5
}
```

---

## Courses

### Create Course (Teachers Only)

```
POST /api/courses.php
```

**Headers:** Session with `role=teacher` required

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| action | string | ✓ | "create" |
| title | string | ✓ | Course title |
| description | string | ✓ | Course description |
| price | number | - | Price (0 = free) |
| category | string | ✓ | Category slug |
| image | file | - | Thumbnail image |
| csrf_token | string | ✓ | CSRF token |

**Success:** Redirects to `/pages/teacher/index.php?success=course_created`

---

## Progress

### Update Lesson Progress

```
POST /api/progress.php
Content-Type: application/json
```

**Body:**
```json
{
  "lesson_id": 5,
  "course_id": 2,
  "status": "completed"
}
```

**Response:**
```json
{
  "success": true
}
```

---

## Notifications

### Get Notifications Count

```
GET /api/notifications.php
```

**Requires:** Active session

**Response:**
```json
{
  "count": 3,
  "important": true,
  "latest": {
    "title": "New Course Approved",
    "message": "Your course has been approved"
  }
}
```

---

## Lessons

### List Lessons

```
GET /api/lessons/index.php?course_id={id}
```

### Get Single Lesson

```
GET /api/lessons/single.php?id={lesson_id}
```

---

## Messages

### List Messages

```
GET /api/messages/index.php
```

### Send Message

```
POST /api/messages/single.php
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 401 | Unauthorized |
| 403 | Forbidden |
| 500 | Server Error |

---

## Notes

- All POST requests should include CSRF token
- Session-based authentication (cookie)
- API responses are UTF-8 encoded JSON
