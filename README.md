# 🎭 Mock API with Superpowers

A flexible mock API service that echoes your requests with dynamic fake data generation. Perfect for frontend development, API testing, demos, and integration testing.

## 🚀 Quick Start

### 1. Get Your API Token

Request a token:
```bash
curl -X POST https://your-api.com/__token/request \
  -H "Content-Type: application/json" \
  -d '{"email": "your@email.com"}'
```

Check your email for a verification token and secret, then verify:
```bash
curl -X POST https://your-api.com/__token/verify \
  -H "Content-Type: application/json" \
  -d '{
    "email": "your@email.com",
    "token": "YOUR_TOKEN_HERE",
    "secret": "YOUR_SECRET_HERE"
  }'
```

### 2. Make Your First Request

```bash
curl -X POST https://your-api.com/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "?name",
    "email": "?email",
    "age": "?numberSmall"
  }'
```

**Response:**
```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "age": 7
}
```

---

## 📋 Token Management Endpoints

### Request Token
**POST** `/__token/request`

Request a new API token. One token per email.

**Request:**
```json
{
  "email": "your@email.com"
}
```

**Response (200):**
```json
{
  "message": "Verification code sent to email (check spam, abuse, the cloud, trash, under the bed)."
}
```

**Error (409):**
```json
{
  "error": "One token per email. Those are the rules!"
}
```

---

### Verify Token
**POST** `/__token/verify`

Activate your token using the credentials sent to your email.

**Request:**
```json
{
  "email": "your@email.com",
  "token": "uuid-token-from-email",
  "secret": "secret-phrase-from-email"
}
```

**Response (200):**
```json
{
  "message": "Api Key verified - welcome aboard."
}
```

---

### Recover Token
**POST** `/__token/recover`

Forgot your token? We'll send it again.

**Request:**
```json
{
  "email": "your@email.com"
}
```

**Response (200):**
```json
{
  "message": "Once lost can be found (check your email - we sent the token there)."
}
```

**Error (404):**
```json
{
  "error": "We seem unable to find any token at all for that email. Do not try again - we did not make a mistake here (you did, so check spelling)."
}
```

---

## 🎯 Main API Endpoint

### Echo with Fake Data
**ANY** `/{any path you want}`

Send any request to any path. The API will echo your request body back with fake data substitutions.

**Authentication:** Include your token in the `Authorization` header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

Or use the custom header:
```
X-API-Token: YOUR_TOKEN_HERE
```

---

## 🎲 Fake Data Generation

Replace any string value starting with `?` to generate fake data:

### Personal Information
| Placeholder | Example Output |
|------------|----------------|
| `?name` | "Jane Smith" |
| `?firstName` | "John" |
| `?lastName` | "Doe" |
| `?email` | "john@example.com" |
| `?username` | "john_doe_92" |
| `?phone` | "+1-555-123-4567" |

### Company
| Placeholder | Example Output |
|------------|----------------|
| `?company` | "Acme Corp" |
| `?jobTitle` | "Software Engineer" |

### Address
| Placeholder | Example Output |
|------------|----------------|
| `?address` | "742 Evergreen Terrace, Springfield" |
| `?street` | "123 Main Street" |
| `?city` | "New York" |
| `?state` | "California" |
| `?zip` | "90210" |
| `?country` | "United States" |

### Numbers
| Placeholder | Example Output |
|------------|----------------|
| `?number` | 4721 |
| `?numberSmall` | 7 |
| `?numberLarge` | 842531 |
| `?decimal` | 342.87 |
| `?price` | 49.99 |
| `?id` | 12345 |

### Text
| Placeholder | Example Output |
|------------|----------------|
| `?word` | "example" |
| `?sentence` | "This is a sample sentence." |
| `?paragraph` | "Long paragraph text..." |
| `?text` | "200 characters of text..." |
| `?lorem` | "Lorem ipsum dolor sit amet." |
| `?loremShort` | "lorem ipsum dolor" |
| `?loremLong` | "Multiple paragraphs..." |

### Internet
| Placeholder | Example Output |
|------------|----------------|
| `?url` | "https://example.com" |
| `?domain` | "example.com" |
| `?ip` | "192.168.1.1" |
| `?slug` | "sample-slug-text" |

### Date & Time
| Placeholder | Example Output |
|------------|----------------|
| `?date` | "2024-03-15" |
| `?dateTime` | "2024-03-15 14:30:00" |
| `?stupidDateTime` | "03/15/2024 14:30:00" ⚠️ |
| `?time` | "14:30:00" |
| `?timestamp` | 1710514200 |

⚠️ *Yes, we included the American date format. Yes, we named it that. No, we're not sorry.*

### Other
| Placeholder | Example Output |
|------------|----------------|
| `?uuid` | "a3bb189e-8bf9-3888-9912-ace4e6543002" |
| `?boolean` | true |
| `?color` | "#3498db" |
| `?colorName` | "Blue" |
| `?creditCard` | "4532-1234-5678-9010" |
| `?image` | "https://via.placeholder.com/640x480" |
| `?avatar` | "https://via.placeholder.com/200x200" |

---

## ⚙️ Special Instructions

Control the API's behavior using the `__instructions` object:

### Example Request:
```json
{
  "user": {
    "name": "?name",
    "email": "?email"
  },
  "__instructions": {
    "delay": 2000,
    "status": 201,
    "headers": {
      "X-Custom-Header": "CustomValue"
    },
    "body": {
      "success": true,
      "message": "Custom response body"
    }
  }
}
```

### Available Instructions:

#### `delay` (integer)
Add artificial delay in milliseconds (max 5000ms).
```json
"__instructions": {
  "delay": 1500
}
```

#### `status` (integer)
Override response HTTP status code.
```json
"__instructions": {
  "status": 404
}
```

#### `headers` (object)
Add custom response headers.
```json
"__instructions": {
  "headers": {
    "X-Custom": "Value",
    "X-Request-ID": "12345"
  }
}
```

#### `body` (any)
Override the entire response body (fake data won't be applied to instructions.body, but will be applied if you use `?faker` inside it).
```json
"__instructions": {
  "body": {
    "message": "Completely custom response",
    "randomName": "?name"
  }
}
```

---

## 📊 Rate Limiting

- **Rate limit:** 3 requests per window
- **Window size:** Based on your `delay` instruction (default 1000ms)
- **Throttling:** Per API token (not per IP)

If you exceed the rate limit:
```json
{
  "error": "Too many requests — slow down, speed racer!"
}
```

---

## 💡 Use Cases

### 1. Frontend Development
```javascript
// Test your UI with realistic data
fetch('https://your-api.com/api/users', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    users: [
      { name: "?name", email: "?email", avatar: "?avatar" },
      { name: "?name", email: "?email", avatar: "?avatar" },
      { name: "?name", email: "?email", avatar: "?avatar" }
    ]
  })
})
```

### 2. API Testing
```bash
# Test error handling
curl -X POST https://your-api.com/test \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "__instructions": { "status": 500 },
    "error": "Something went wrong"
  }'
```

### 3. Load Testing
```bash
# Test with artificial delays
curl -X POST https://your-api.com/slow-endpoint \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "data": "test",
    "__instructions": { "delay": 3000 }
  }'
```

### 4. Integration Testing
```json
{
  "order": {
    "id": "?uuid",
    "customer": "?name",
    "email": "?email",
    "total": "?price",
    "items": [
      { "name": "?word", "price": "?price" },
      { "name": "?word", "price": "?price" }
    ]
  },
  "__instructions": {
    "status": 201,
    "headers": { "X-Order-ID": "?uuid" }
  }
}
```

---

## 🚫 Limitations

- **Max request size:** 100KB
- **Max delay:** 5000ms (5 seconds)
- **Tokens deleted:** After 30 days of inactivity
- **One token per email:** No exceptions!

---

## ❓ FAQ

**Q: What happens if I send an empty request body?**  
A: You get a 204 No Content response.

**Q: Can I use any HTTP method?**  
A: Yes! GET, POST, PUT, PATCH, DELETE, OPTIONS - anything goes.

**Q: Can I test my actual API paths?**  
A: Absolutely! That's why we use `/__token` for management endpoints. Your `/users`, `/api/products`, etc. paths won't conflict.

**Q: Do I need to verify my token immediately?**  
A: No, but unverified tokens can't make API requests. Verify when you're ready to use it.

**Q: Can I change my email?**  
A: Nope! One token per email, forever (or 30 days of inactivity).

---

## 🎉 Example: Complete Workflow

```bash
# 1. Request token
curl -X POST https://your-api.com/__token/request \
  -H "Content-Type: application/json" \
  -d '{"email": "dev@example.com"}'

# 2. Check email, then verify
curl -X POST https://your-api.com/__token/verify \
  -H "Content-Type: application/json" \
  -d '{
    "email": "dev@example.com",
    "token": "received-token",
    "secret": "received-secret"
  }'

# 3. Start making requests!
curl -X POST https://your-api.com/api/users/profile \
  -H "Authorization: Bearer received-token" \
  -H "Content-Type: application/json" \
  -d '{
    "user": {
      "id": "?uuid",
      "name": "?name",
      "email": "?email",
      "phone": "?phone",
      "address": {
        "street": "?street",
        "city": "?city",
        "country": "?country"
      },
      "createdAt": "?dateTime"
    },
    "__instructions": {
      "delay": 500,
      "status": 200
    }
  }'
```

---

## 🐛 Error Responses

| Status | Message | Meaning |
|--------|---------|---------|
| 401 | "No API token provided..." | Missing Authorization header |
| 401 | "Invalid API token..." | Token doesn't exist |
| 403 | "API token not verified yet..." | Token exists but not verified |
| 413 | "Nope! That's WAY too much..." | Request body over 100KB |
| 429 | "Too many requests..." | Rate limit exceeded |

---

**Happy mocking! 🎭**
