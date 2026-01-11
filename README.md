# Stubbr.dev - Mock API for Frontend Development

A mock API service that lets you build frontend applications with realistic API calls from day one - without writing mock data code.

**Live at:** [stubbr.dev](https://stubbr.dev)

---

## How It Works

Stubbr lets you write production-ready API calls from day one. Your request has two parts:

- **Payload** — The real data your backend will receive in production
- **__instructions** — Defines what Stubbr returns during development (ignored by your real backend)

When you switch to production, just change the API host. Your backend receives the payload and ignores `__instructions`.

```javascript
// This works in BOTH development and production
const API_HOST = process.env.NODE_ENV === 'development'
  ? 'https://stubbr.dev'
  : 'https://api.yourcompany.com';

const response = await fetch(`${API_HOST}/api/users`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    filters: { active: true },           // Your real payload
    page: 1,
    __instructions: {                     // Stubbr-specific (ignored by real backend)
      body: {
        user: {
          __repeat: 10,
          id: "?counter",
          name: "?name",
          email: "?email"
        }
      }
    }
  })
});
```

Your real backend receives `filters` and `page`, ignores `__instructions`, and returns real data. Zero refactoring required.

---

## Quick Start

### 1. Get Your API Token

```bash
curl "https://stubbr.dev/api/__token/request?email=your@email.com"
```

**Response:**
```json
{
  "message": "Token created successfully!",
  "token": "a3bb189e-8bf9-3888-9912-ace4e6543002"
}
```

### 2. Make Your First Request

```bash
curl -X POST https://stubbr.dev/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "filters": { "active": true },
    "__instructions": {
      "body": {
        "user": {
          "__repeat": 3,
          "id": "?counter",
          "name": "?name",
          "email": "?email"
        }
      }
    }
  }'
```

**Response:**
```json
{
  "users": [
    { "id": 0, "name": "John Doe", "email": "john@example.com" },
    { "id": 1, "name": "Jane Smith", "email": "jane@example.com" },
    { "id": 2, "name": "Bob Wilson", "email": "bob@example.com" }
  ]
}
```

---

## Authentication

Include your token in every request using either method:

```
Authorization: Bearer YOUR_TOKEN
```

or

```
X-API-Token: YOUR_TOKEN
```

### Token Endpoints

| Endpoint | Description |
|----------|-------------|
| `GET /api/__token/request?email=you@example.com` | Get a new token |
| `GET /api/__token/recover?email=you@example.com` | Recover existing token |

One token per email. Tokens are deleted after 30 days of inactivity.

**Rate Limit:** 1 request per 10 seconds per IP address.

---

## The __instructions Object

Control the response behavior:

| Option | Type | Description |
|--------|------|-------------|
| `body` | any | The response body to return. Supports `?` placeholders and `__repeat`. |
| `status` | number | HTTP status code (default: 200) |
| `delay` | number | Delay in milliseconds before responding (max: 5000) |
| `headers` | object | Custom response headers |
| `max_pages` | number | Enable pagination metadata in response |
| `no_cache` | boolean | Skip caching this response |

```json
{
  "order_id": 123,
  "__instructions": {
    "status": 201,
    "delay": 500,
    "headers": {
      "X-Request-Id": "abc-123"
    },
    "body": {
      "success": true,
      "order": {
        "__repeat": 1,
        "id": 123,
        "total": "?price",
        "created_at": "?dateTime"
      }
    }
  }
}
```

---

## Generating Arrays with __repeat

Use `__repeat` inside `__instructions.body` to generate arrays:

```json
{
  "__instructions": {
    "body": {
      "user": {
        "__repeat": 5,
        "id": "?counter",
        "name": "?name"
      }
    }
  }
}
```

**Response:**
```json
{
  "users": [
    { "id": 0, "name": "John Doe" },
    { "id": 1, "name": "Jane Smith" },
    ...
  ]
}
```

**Notes:**
- The key is automatically pluralized (`user` → `users`)
- Use `__as` to override the output key name
- Maximum 20 items per array
- Maximum 2 levels of nesting
- `?counter` increments globally across the entire response

---

## UUID Mode with __uuid

Add `"__uuid": true` to a `__repeat` block to make `?id` and `?uuid` return UUID7 values:

```json
{
  "__instructions": {
    "body": {
      "user": {
        "__repeat": 3,
        "__uuid": true,
        "id": "?id",
        "name": "?name"
      }
    }
  }
}
```

**Response:**
```json
{
  "users": [
    { "id": "01932c5d-9e1f-7bc3-9e84-4f5a3b2c1d0e", "name": "John Doe" },
    { "id": "01932c5d-9e20-7a12-8b45-2d6e7f8a9b0c", "name": "Jane Smith" },
    { "id": "01932c5d-9e21-7d34-9c56-3e7f8a9b0c1d", "name": "Bob Wilson" }
  ]
}
```

UUID7 is time-ordered, making it ideal for database primary keys. The flag applies to the entire block and nested blocks.

---

## All Placeholders

Use these inside `__repeat` blocks to generate fake data:

### Personal
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?name` | Full name | "Jane Smith" |
| `?firstName` | First name | "John" |
| `?lastName` | Last name | "Doe" |
| `?email` | Email address | "john@example.com" |
| `?username` | Username | "john_doe_92" |
| `?phone` | Phone number | "+1-555-123-4567" |

### Company
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?company` | Company name | "Acme Corp" |
| `?jobTitle` | Job title | "Software Engineer" |

### Address
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?address` | Full address | "742 Evergreen Terrace, Springfield" |
| `?street` | Street address | "123 Main Street" |
| `?city` | City | "New York" |
| `?state` | State | "California" |
| `?zip` | Postal code | "90210" |
| `?country` | Country | "United States" |

### Numbers
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?number` | Number 1-10000 | 4721 |
| `?numberSmall` | Number 1-10 | 7 |
| `?numberLarge` | Number 10000-1000000 | 842531 |
| `?decimal` | Decimal number | 342.87 |
| `?price` | Price value | 49.99 |
| `?id` | ID number | 12345 |

### Identifiers
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?uuid` | Random UUID | "a3bb189e-8bf9-3888-9912-ace4e6543002" |
| `?counter` | Incrementing number | 0, 1, 2, 3... |
| `?counterUuid` | Incrementing UUID | "00000000-0000-0000-0000-000000000001" |

### Text
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?word` | Single word | "example" |
| `?sentence` | One sentence | "This is a sample sentence." |
| `?paragraph` | One paragraph | "Lorem ipsum..." |
| `?text` | 200 characters | "Lorem ipsum dolor..." |
| `?lorem` | Lorem sentence | "Lorem ipsum dolor sit amet." |
| `?loremShort` | 3 words | "lorem ipsum dolor" |
| `?loremLong` | Multiple paragraphs | "Lorem ipsum..." |

### Internet
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?url` | URL | "https://example.com/path" |
| `?domain` | Domain name | "example.com" |
| `?ip` | IP address | "192.168.1.1" |
| `?slug` | URL slug | "sample-slug-text" |

### Date & Time
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?date` | Date (ISO) | "2024-03-15" |
| `?dateTime` | DateTime | "2024-03-15 14:30:00" |
| `?stupidDateTime` | US format | "03/15/2024 14:30:00" |
| `?time` | Time | "14:30:00" |
| `?timestamp` | Unix timestamp | 1710514200 |

### Other
| Placeholder | Description | Example |
|-------------|-------------|---------|
| `?boolean` | true/false | true |
| `?color` | Hex color | "#3498db" |
| `?colorName` | Color name | "Blue" |
| `?creditCard` | Card number | "4532-1234-5678-9010" |
| `?image` | Image URL (640x480) | "https://via.placeholder.com/640x480" |
| `?avatar` | Avatar URL (200x200) | "https://via.placeholder.com/200x200" |

---

## Response Caching

Identical requests return cached responses. The cache key is based on:

- Your API token
- HTTP method
- Request path
- Query parameters
- Request body

**This means generated fake data (names, emails, UUIDs, etc.) stays consistent for identical requests.** This is useful for testing - you get the same response every time without randomness breaking your tests.

To get fresh data, either:
- Use `"no_cache": true` in `__instructions`
- Change something in the request (different body, query param, etc.)
- Clear your cache

Cached responses include the header `__from_cache: true`.

### Clear Your Cache

```bash
curl -X POST https://stubbr.dev/api/__cache/clear \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Rate Limiting

- **API requests:** 10 requests per second (per token)
- **Token requests:** 1 request per 10 seconds (per IP)

If you add a `delay` in `__instructions`, the rate limit window adjusts accordingly.

---

## Limits

| Limit | Value |
|-------|-------|
| Max request body | 100 KB |
| Max delay | 5000 ms |
| Max items per __repeat | 20 |
| Max nesting depth | 2 levels |
| Token inactivity timeout | 30 days |

---

## Error Responses

| Status | Message |
|--------|---------|
| 400 | Invalid JSON |
| 401 | No API token provided / Invalid token |
| 403 | Token not verified |
| 413 | Request body too large |
| 429 | Rate limit exceeded |

---

## Technical Details

- **Framework:** Laravel 12
- **Fake Data:** FakerPHP library
- **UUIDs:** Ramsey UUID (UUID7)
- **Rate Limiting:** Adaptive throttling based on request delay
- **Privacy:** Emails hashed with HMAC-SHA256
- **Deployment:** Google Cloud Run

---

## Roadmap

- [x] Instant token generation
- [x] 40+ fake data placeholders
- [x] Array repeating with `__repeat`
- [x] UUID7 mode with `__uuid`
- [x] Adaptive rate limiting
- [x] Response caching
- [ ] Email-based token delivery
- [ ] Token verification requirement
- [ ] Dashboard for usage analytics

---

**Happy mocking!**

Built with care by Daniel Melin | [stubbr.dev](https://stubbr.dev) | [GitHub](https://github.com/Yorsyboy/stubbrdev)
