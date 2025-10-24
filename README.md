# 🎭 StubbrDev - Mock API with Superpowers

A flexible mock API service that echoes your requests with dynamic fake data generation. Perfect for frontend development, API testing, demos, and integration testing.

**Live at:** [stubbr.dev](https://stubbr.dev)

---

## 🚀 Quick Start

### 1. Get Your API Token

Request a token (one per email):
```bash
curl -X POST https://stubbr.dev/__token/request \
  -H "Content-Type: application/json" \
  -d '{"email": "your@email.com"}'
```

**Response:**
```json
{
  "message": "Token created successfully!",
  "token": "a3bb189e-8bf9-3888-9912-ace4e6543002"
}
```

**Note:** Currently, tokens are provided immediately. In production, you'll receive your token via email and need to verify it before use.

### 2. Make Your First Request

```bash
curl -X POST /api/api/users \
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
  "message": "Token created successfully!",
  "token": "a3bb189e-8bf9-3888-9912-ace4e6543002"
}
```

**Error (409):**
```json
{
  "error": "One token per email. Those are the rules!"
}
```

**Rate Limit:** 1 request per 10 seconds per IP address.

---

### Recover Token
**POST** `/__token/recover`

Forgot your token? Retrieve it again.

**Request:**
```json
{
  "email": "your@email.com"
}
```

**Response (200):**
```json
{
  "message": "Token recovered successfully!",
  "token": "a3bb189e-8bf9-3888-9912-ace4e6543002"
}
```

**Error (404):**
```json
{
  "error": "No token found for this email. Check your spelling or request a new one."
}
```

**Rate Limit:** 1 request per 10 seconds per IP address.

---

### Verify Token (Future)
**POST** `/__token/verify`

*Note: This endpoint is currently inactive but will be required in production when tokens are sent via email.*

**Request:**
```json
{
  "email": "your@email.com",
  "token": "your-token-from-email",
  "secret": "your-secret-phrase-from-email"
}
```

---

## 🎯 Main API Endpoint

### Echo with Fake Data
**ANY** `/{any-path-you-want}`

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

### 👤 Personal Information
| Placeholder | Example Output |
|------------|----------------|
| `?name` | "Jane Smith" |
| `?firstName` | "John" |
| `?lastName` | "Doe" |
| `?email` | "john@example.com" |
| `?username` | "john_doe_92" |
| `?phone` | "+1-555-123-4567" |

### 🏢 Company
| Placeholder | Example Output |
|------------|----------------|
| `?company` | "Acme Corp" |
| `?jobTitle` | "Software Engineer" |

### 📍 Address
| Placeholder | Example Output |
|------------|----------------|
| `?address` | "742 Evergreen Terrace, Springfield" |
| `?street` | "123 Main Street" |
| `?city` | "New York" |
| `?state` | "California" |
| `?zip` | "90210" |
| `?country` | "United States" |

### 🔢 Numbers & IDs
| Placeholder | Example Output |
|------------|----------------|
| `?number` | 4721 |
| `?numberSmall` | 7 |
| `?numberLarge` | 842531 |
| `?decimal` | 342.87 |
| `?price` | 49.99 |
| `?id` | 12345 |
| `?counter` | 0, 1, 2... (increments) |
| `?counterUuid` | 00000000-0000-0000-0000-000000000000 |

### 📝 Text
| Placeholder | Example Output |
|------------|----------------|
| `?word` | "example" |
| `?sentence` | "This is a sample sentence." |
| `?paragraph` | "Long paragraph text..." |
| `?text` | "200 characters of text..." |
| `?lorem` | "Lorem ipsum dolor sit amet." |
| `?loremShort` | "lorem ipsum dolor" |
| `?loremLong` | "Multiple paragraphs..." |

### 🌐 Internet
| Placeholder | Example Output |
|------------|----------------|
| `?url` | "https://example.com" |
| `?domain` | "example.com" |
| `?ip` | "192.168.1.1" |
| `?slug` | "sample-slug-text" |

### 📅 Date & Time
| Placeholder | Example Output |
|------------|----------------|
| `?date` | "2024-03-15" |
| `?dateTime` | "2024-03-15 14:30:00" |
| `?stupidDateTime` | "03/15/2024 14:30:00" ⚠️ |
| `?time` | "14:30:00" |
| `?timestamp` | 1710514200 |

⚠️ *Yes, we included the American date format. Yes, we named it that. No, we're not sorry.*

### 🎨 Other
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

## 🔁 Array Repeating with `__repeat`

Generate multiple items easily using the `__repeat` pattern:

### Basic Example
```json
{
  "user": {
    "__repeat": 3,
    "name": "?name",
    "email": "?email"
  }
}
```

**Response:**
```json
{
  "users": [
    { "name": "John Doe", "email": "john@example.com" },
    { "name": "Jane Smith", "email": "jane@example.com" },
    { "name": "Bob Johnson", "email": "bob@example.com" }
  ]
}
```

### Custom Array Name with `__as`
```json
{
  "product": {
    "__repeat": 2,
    "__as": "items",
    "name": "?word",
    "price": "?price"
  }
}
```

**Response:**
```json
{
  "items": [
    { "name": "example", "price": 49.99 },
    { "name": "sample", "price": 29.99 }
  ]
}
```

### Using Counters
```json
{
  "user": {
    "__repeat": 3,
    "id": "?counter",
    "uuid": "?counterUuid",
    "name": "?name"
  }
}
```

**Response:**
```json
{
  "users": [
    { "id": 0, "uuid": "00000000-0000-0000-0000-000000000000", "name": "John Doe" },
    { "id": 1, "uuid": "00000000-0000-0000-0000-000000000001", "name": "Jane Smith" },
    { "id": 2, "uuid": "00000000-0000-0000-0000-000000000002", "name": "Bob Johnson" }
  ]
}
```

### Nested Repeating
```json
{
  "department": {
    "__repeat": 2,
    "name": "?word",
    "employee": {
      "__repeat": 3,
      "id": "?counter",
      "name": "?name"
    }
  }
}
```

**Response:**
```json
{
  "departments": [
    {
      "name": "Engineering",
      "employees": [
        { "id": 0, "name": "John Doe" },
        { "id": 1, "name": "Jane Smith" },
        { "id": 2, "name": "Bob Johnson" }
      ]
    },
    {
      "name": "Marketing",
      "employees": [
        { "id": 3, "name": "Alice Cooper" },
        { "id": 4, "name": "Charlie Brown" },
        { "id": 5, "name": "Diana Prince" }
      ]
    }
  ]
}
```

**Notes:**
- The singular key is automatically pluralized (e.g., `user` → `users`)
- Use `__as` to override the array name
- Maximum 100 repeats per array
- Counters increment globally across the entire response
- You can nest repeats for complex data structures

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
Add artificial delay in milliseconds (max 5000ms). Also affects rate limiting window.
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
Override the entire response body. You can still use `?faker` placeholders inside.
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

- **Rate limit:** 10 requests per window
- **Window size:** Based on your `delay` instruction (default 1000ms, max 5000ms)
- **Throttling:** Per API token (not per IP)

If you exceed the rate limit:
```json
{
  "error": "Too many requests — slow down, speed racer!"
}
```

**Token request rate limit:** 1 request per 10 seconds per IP address.

---

## 💡 Use Cases

### 1. Frontend Development
```javascript
// Test your UI with realistic data
fetch('/api/api/users', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    user: {
      __repeat: 10,
      name: "?name",
      email: "?email",
      avatar: "?avatar"
    }
  })
})
```

### 2. API Testing
```bash
# Test error handling
curl -X POST /api/api/errors \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "__instructions": { "status": 500 },
    "error": "Internal server error"
  }'
```

### 3. Load Testing
```bash
# Test with artificial delays
curl -X POST /api/api/slow \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "data": "test",
    "__instructions": { "delay": 3000 }
  }'
```

### 4. Complex Mock Data
```json
{
  "store": {
    "__repeat": 2,
    "name": "?company",
    "address": {
      "street": "?street",
      "city": "?city"
    },
    "product": {
      "__repeat": 5,
      "id": "?counter",
      "name": "?word",
      "price": "?price",
      "inStock": "?boolean"
    }
  },
  "__instructions": {
    "status": 200,
    "headers": {
      "X-Total-Stores": "2"
    }
  }
}
```

---

## 🚫 Limitations

- **Max request size:** 100KB
- **Max delay:** 5000ms (5 seconds)
- **Max repeats:** 100 items per array
- **Tokens deleted:** After 30 days of inactivity
- **One token per email:** No exceptions!

---

## ❓ FAQ

**Q: What happens if I send an empty request body?**  
A: You get a 204 No Content response.

**Q: Can I use any HTTP method?**  
A: Yes! GET, POST, PUT, PATCH, DELETE, OPTIONS - anything goes.

**Q: Do I need to verify my token?**  
A: Not currently! Your token is active immediately. In production, you'll need to verify via email.

**Q: Can I change my email?**  
A: Nope! One token per email, forever (or 30 days of inactivity).

**Q: What if I lose my token?**  
A: Use the `/__token/recover` endpoint with your email to get it back.

**Q: How does the counter work with multiple arrays?**  
A: Counters increment globally across your entire request, maintaining sequence across all repeated items.

**Q: Can I nest `__repeat` inside other `__repeat` blocks?**  
A: Yes! You can nest repeats as deeply as you need for complex data structures.

---

## 🎉 Example: Complete Workflow

```bash
# 1. Request token
curl -X POST /api/__token/request \
  -H "Content-Type: application/json" \
  -d '{"email": "dev@example.com"}'

# Response: { "message": "...", "token": "your-uuid-token" }

# 2. Start making requests immediately!
curl -X POST /api/api/dashboard \
  -H "Authorization: Bearer your-uuid-token" \
  -H "Content-Type: application/json" \
  -d '{
    "user": {
      "id": "?uuid",
      "name": "?name",
      "email": "?email",
      "address": {
        "street": "?street",
        "city": "?city",
        "country": "?country"
      }
    },
    "order": {
      "__repeat": 5,
      "orderId": "?counter",
      "total": "?price",
      "date": "?dateTime",
      "item": {
        "__repeat": 3,
        "sku": "?counterUuid",
        "name": "?word",
        "price": "?price"
      }
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
| 401 | "No API token provided. Did you forget it at home?" | Missing Authorization header |
| 401 | "Invalid API token. This is not the token we're looking for." | Token doesn't exist |
| 403 | "API token not verified yet. Check your email and verify first!" | Token disabled |
| 413 | "Nope! That's WAY too much for me to handle!!" | Request body over 100KB |
| 429 | "Too many requests — slow down, speed racer!" | API rate limit exceeded |
| 429 | "Whoa there! You can only request a token once every 10 seconds." | Token request throttled |

---

## 🛠️ Technical Details

- **Framework:** Laravel 11
- **Fake Data:** Faker PHP library
- **Rate Limiting:** Adaptive throttling based on request delay
- **Privacy:** Emails are hashed using HMAC-SHA256
- **Current Environment:** Google Cloud Run with SQLite
- **Production Plan:** Proper database + email verification system

---

## 🚀 Roadmap

- ✅ Instant token generation
- ✅ Fake data generation with 40+ placeholders
- ✅ Array repeating with `__repeat`
- ✅ Adaptive rate limiting
- 🔄 Email-based token delivery (coming in production)
- 🔄 Token verification requirement (coming in production)
- 🔄 Persistent database migration (coming in production)

---

**Happy mocking! 🎭**

Built with ❤️ by Daniel Melin | [stubbr.dev](https://stubbr.dev)