# HSE Management System - API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication

Most endpoints require authentication. Include the authentication token in the request header:

```
Authorization: Bearer {your-token}
```

## Endpoints

### Incidents

#### List Incidents
```
GET /incidents
```

**Query Parameters:**
- `status` - Filter by status (reported, open, investigating, closed)
- `severity` - Filter by severity (low, medium, high, critical)
- `date_from` - Filter incidents from date (YYYY-MM-DD)
- `date_to` - Filter incidents to date (YYYY-MM-DD)
- `page` - Page number for pagination

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "reference_number": "INC-20251202-0001",
      "title": "Incident Title",
      "description": "Incident description",
      "severity": "high",
      "status": "open",
      "location": "Location",
      "incident_date": "2025-12-01T10:00:00Z",
      "created_at": "2025-12-02T08:00:00Z"
    }
  ],
  "current_page": 1,
  "per_page": 10,
  "total": 50
}
```

#### Create Incident
```
POST /incidents
```

**Request Body:**
```json
{
  "title": "Incident Title",
  "description": "Detailed description",
  "severity": "high",
  "location": "Location",
  "date_occurred": "2025-12-01",
  "department_id": 1,
  "assigned_to": 2,
  "images": ["path/to/image1.jpg", "path/to/image2.jpg"]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "reference_number": "INC-20251202-0001",
    "title": "Incident Title",
    "status": "open"
  }
}
```

#### Get Incident
```
GET /incidents/{id}
```

**Response:**
```json
{
  "id": 1,
  "reference_number": "INC-20251202-0001",
  "title": "Incident Title",
  "description": "Description",
  "severity": "high",
  "status": "open",
  "location": "Location",
  "incident_date": "2025-12-01T10:00:00Z",
  "reporter": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "assigned_to": {
    "id": 2,
    "name": "Jane Smith"
  },
  "department": {
    "id": 1,
    "name": "Safety Department"
  },
  "images": ["path/to/image1.jpg"],
  "created_at": "2025-12-02T08:00:00Z"
}
```

#### Update Incident
```
PUT /incidents/{id}
```

**Request Body:** (Same as Create, all fields optional)

#### Delete Incident
```
DELETE /incidents/{id}
```

#### Assign Incident
```
POST /incidents/{id}/assign
```

**Request Body:**
```json
{
  "assigned_to": 2
}
```

#### Close Incident
```
POST /incidents/{id}/close
```

**Request Body:**
```json
{
  "resolution_notes": "Incident resolved successfully"
}
```

---

### Toolbox Talks

#### List Toolbox Talks
```
GET /toolbox-talks
```

**Query Parameters:**
- `status` - Filter by status
- `department` - Filter by department ID
- `date_from` - Filter from date
- `date_to` - Filter to date

#### Create Toolbox Talk
```
POST /toolbox-talks
```

**Request Body:**
```json
{
  "title": "Safety Talk Title",
  "description": "Description",
  "department_id": 1,
  "supervisor_id": 2,
  "topic_id": 3,
  "scheduled_date": "2025-12-15",
  "start_time": "09:00",
  "duration_minutes": 15,
  "location": "Main Hall",
  "talk_type": "safety",
  "biometric_required": true,
  "is_recurring": false
}
```

#### Start Toolbox Talk
```
POST /toolbox-talks/{id}/start
```

#### Complete Toolbox Talk
```
POST /toolbox-talks/{id}/complete
```

---

### Safety Communications

#### List Communications
```
GET /safety-communications
```

#### Create Communication
```
POST /safety-communications
```

**Request Body:**
```json
{
  "title": "Safety Alert",
  "content": "Important safety information",
  "channels": ["email", "sms", "digital_signage"],
  "target_audience": ["all_employees"],
  "priority": "high",
  "acknowledgment_required": true,
  "acknowledgment_deadline": "2025-12-10"
}
```

#### Send Communication
```
POST /safety-communications/{id}/send
```

---

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "severity": ["The selected severity is invalid."]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "message": "This action is unauthorized."
}
```

### Not Found (404)
```json
{
  "message": "Resource not found."
}
```

### Server Error (500)
```json
{
  "message": "Server Error",
  "error": "Error details"
}
```

---

## Rate Limiting

API requests are rate-limited to 60 requests per minute per user.

---

## Pagination

List endpoints support pagination. Use `page` query parameter:

```
GET /incidents?page=2
```

Response includes pagination metadata:
```json
{
  "data": [...],
  "current_page": 2,
  "per_page": 10,
  "total": 100,
  "last_page": 10,
  "from": 11,
  "to": 20
}
```

---

## Filtering and Sorting

Most list endpoints support filtering and sorting:

- Filter: `?status=open&severity=high`
- Sort: `?sort=created_at&order=desc`

---

## Date Formats

All dates should be in ISO 8601 format:
- Date: `YYYY-MM-DD`
- DateTime: `YYYY-MM-DDTHH:mm:ssZ`

---

## File Uploads

For file uploads (images), use `multipart/form-data`:

```
POST /incidents
Content-Type: multipart/form-data

title: "Incident Title"
images[]: [file1.jpg]
images[]: [file2.jpg]
```

Maximum file size: 5MB per file
Maximum files: 5 per request
Allowed types: jpeg, jpg, png, gif

