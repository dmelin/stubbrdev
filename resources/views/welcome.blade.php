<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stubbr.dev - Mock API for Frontend Development</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-code: #1e1e2e;
            --text: #1a1a2e;
            --text-dim: #64748b;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --border: #e5e7eb;
            --success: #10b981;
            --error: #ef4444;
        }

        html.dark {
            --bg: #0f0f14;
            --bg-secondary: #1a1a24;
            --bg-code: #0a0a0f;
            --text: #e5e7eb;
            --text-dim: #94a3b8;
            --accent: #818cf8;
            --accent-hover: #6366f1;
            --border: #2d2d3a;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Header */
        header {
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            background: var(--bg);
            z-index: 100;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
            text-decoration: none;
        }

        .theme-toggle {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 0.5rem;
            cursor: pointer;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }

        .theme-toggle:hover {
            border-color: var(--accent);
        }

        .theme-toggle svg {
            width: 20px;
            height: 20px;
        }

        /* Hero */
        .hero {
            padding: 4rem 0 3rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .hero p {
            font-size: 1.125rem;
            color: var(--text-dim);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Steps */
        .step {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .step-number {
            background: var(--accent);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .step-title {
            font-size: 1.125rem;
            font-weight: 600;
        }

        /* Token Form */
        .token-form {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .token-form input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            background: var(--bg);
            color: var(--text);
        }

        .token-form input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--accent);
        }

        .token-result {
            display: none;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
        }

        .token-result.visible {
            display: flex;
        }

        .token-result.success {
            border-color: var(--success);
        }

        .token-result.error {
            border-color: var(--error);
        }

        .token-result .icon {
            flex-shrink: 0;
        }

        .token-result .token-value {
            flex: 1;
            font-family: monospace;
            font-size: 0.875rem;
            word-break: break-all;
        }

        .token-result .copy-btn {
            background: none;
            border: none;
            color: var(--text-dim);
            cursor: pointer;
            padding: 0.25rem;
        }

        .token-result .copy-btn:hover {
            color: var(--accent);
        }

        .token-hint {
            font-size: 0.875rem;
            color: var(--text-dim);
            margin-top: 0.5rem;
        }

        /* Request Builder */
        .request-builder {
            margin-top: 1rem;
        }

        .preset-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-size: 1rem;
            background: var(--bg);
            color: var(--text);
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .preset-select:focus {
            outline: none;
            border-color: var(--accent);
        }

        .code-panels {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 640px) {
            .code-panels {
                grid-template-columns: 1fr;
            }
        }

        .code-panel {
            background: var(--bg-code);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .code-panel-header {
            padding: 0.5rem 1rem;
            background: rgba(0, 0, 0, 0.2);
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .code-panel-header .method-path {
            font-family: monospace;
            text-transform: none;
            color: #10b981;
        }

        .code-panel textarea,
        .code-panel pre {
            width: 100%;
            min-height: 200px;
            padding: 1rem;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.8125rem;
            line-height: 1.6;
            color: #e5e7eb;
            background: transparent;
            border: none;
            resize: vertical;
        }

        .code-panel textarea {
            outline: none;
        }

        .code-panel pre {
            margin: 0;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .response-placeholder {
            color: #64748b;
            font-style: italic;
        }

        .send-btn-wrapper {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .response-status {
            font-size: 0.875rem;
            color: var(--text-dim);
        }

        .response-status.success {
            color: var(--success);
        }

        .response-status.error {
            color: var(--error);
        }

        /* Reference Section */
        .reference {
            margin: 3rem 0;
        }

        .reference h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .accordion {
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .accordion-item {
            border-bottom: 1px solid var(--border);
        }

        .accordion-item:last-child {
            border-bottom: none;
        }

        .accordion-header {
            width: 100%;
            padding: 1rem 1.25rem;
            background: var(--bg-secondary);
            border: none;
            text-align: left;
            font-size: 1rem;
            font-weight: 500;
            color: var(--text);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .accordion-header:hover {
            background: var(--bg);
        }

        .accordion-header .chevron {
            transition: transform 0.2s ease;
        }

        .accordion-item.open .accordion-header .chevron {
            transform: rotate(180deg);
        }

        .accordion-content {
            display: none;
            padding: 1.25rem;
            background: var(--bg);
        }

        .accordion-item.open .accordion-content {
            display: block;
        }

        .placeholder-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .placeholder-table th,
        .placeholder-table td {
            padding: 0.5rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .placeholder-table th {
            font-weight: 600;
            color: var(--text-dim);
        }

        .placeholder-table code {
            background: var(--bg-secondary);
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.8125rem;
        }

        .example-code {
            background: var(--bg-code);
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
        }

        .example-code pre {
            margin: 0;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.8125rem;
            line-height: 1.6;
            color: #e5e7eb;
        }

        /* Documentation Section */
        .docs {
            margin: 4rem 0 3rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 1.5rem;
        }

        .docs h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 2rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--border);
        }

        .doc-section {
            margin-bottom: 2.5rem;
        }

        .doc-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text);
        }

        .doc-section h4 {
            font-size: 1rem;
            font-weight: 600;
            margin: 1.5rem 0 0.5rem;
            color: var(--text-dim);
        }

        .doc-section p {
            margin-bottom: 0.75rem;
            color: var(--text);
            line-height: 1.7;
        }

        .doc-section ul {
            margin: 0.5rem 0 1rem 1.5rem;
            color: var(--text);
        }

        .doc-section li {
            margin-bottom: 0.375rem;
            line-height: 1.6;
        }

        .doc-section code {
            background: var(--bg-secondary);
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.8125rem;
            font-family: 'SF Mono', 'Fira Code', monospace;
        }

        .doc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin: 0.75rem 0;
        }

        .doc-table th,
        .doc-table td {
            padding: 0.5rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .doc-table th {
            font-weight: 600;
            color: var(--text-dim);
            background: var(--bg-secondary);
        }

        .doc-table td:first-child {
            white-space: nowrap;
        }

        .doc-table td:last-child {
            color: var(--text-dim);
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.8125rem;
        }

        .doc-table code {
            background: var(--bg-secondary);
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.8125rem;
        }

        /* Footer */
        footer {
            padding: 2rem 0;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--text-dim);
            font-size: 0.875rem;
        }

        footer a {
            color: var(--accent);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Utility */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a href="/" class="logo">stubbr.dev</a>
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path>
                </svg>
                <svg class="moon-icon" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <h1>Stubbr.dev</h1>
            <p>Mock API for frontend development. Write production-ready API calls from day one.</p>
        </section>

        <!-- Step 1: Get Token -->
        <section class="step">
            <div class="step-header">
                <div class="step-number">1</div>
                <h2 class="step-title">Get your API token</h2>
            </div>
            <form class="token-form" id="tokenForm">
                <input type="email" id="emailInput" placeholder="your@email.com" required>
                <button type="submit" class="btn btn-primary" id="tokenBtn">Get Token</button>
            </form>
            <div class="token-result" id="tokenResult">
                <span class="icon" id="tokenIcon"></span>
                <span class="token-value" id="tokenValue"></span>
                <button class="copy-btn" id="copyBtn" title="Copy token">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                </button>
            </div>
            <p class="token-hint">One token per email. Already have one? Enter your email to recover it.</p>
        </section>

        <!-- Step 2: Try a Request -->
        <section class="step">
            <div class="step-header">
                <div class="step-number">2</div>
                <h2 class="step-title">Try a request</h2>
            </div>
            <div class="request-builder">
                <select class="preset-select" id="presetSelect">
                    <option value="users">User list (with __repeat)</option>
                    <option value="uuid">User list (with UUID7 IDs)</option>
                    <option value="single">Single user (nested data)</option>
                    <option value="error">Error response (status 500)</option>
                    <option value="slow">Slow response (2s delay)</option>
                </select>
                <div class="code-panels">
                    <div class="code-panel">
                        <div class="code-panel-header">
                            <span>Request</span>
                            <span class="method-path" id="methodPath">POST /api/users</span>
                        </div>
                        <textarea id="requestBody" spellcheck="false"></textarea>
                    </div>
                    <div class="code-panel">
                        <div class="code-panel-header">
                            <span>Response</span>
                            <span class="response-status" id="responseStatus"></span>
                        </div>
                        <pre id="responseBody"><span class="response-placeholder">Response will appear here...</span></pre>
                    </div>
                </div>
                <div class="send-btn-wrapper">
                    <button class="btn btn-primary" id="sendBtn" disabled>Send Request</button>
                    <span class="response-status" id="sendStatus"></span>
                </div>
            </div>
        </section>

        <!-- Quick Reference -->
        <section class="reference">
            <h2>Quick Reference</h2>
            <div class="accordion">
                <div class="accordion-item">
                    <button class="accordion-header">
                        Placeholders
                        <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                    <div class="accordion-content">
                        <table class="placeholder-table">
                            <tr><th>Type</th><th>Placeholders</th></tr>
                            <tr><td>Personal</td><td><code>?name</code> <code>?firstName</code> <code>?lastName</code> <code>?email</code> <code>?username</code> <code>?phone</code></td></tr>
                            <tr><td>Company</td><td><code>?company</code> <code>?jobTitle</code></td></tr>
                            <tr><td>Address</td><td><code>?address</code> <code>?street</code> <code>?city</code> <code>?state</code> <code>?zip</code> <code>?country</code></td></tr>
                            <tr><td>Numbers</td><td><code>?number</code> <code>?numberSmall</code> <code>?numberLarge</code> <code>?decimal</code> <code>?price</code> <code>?id</code></td></tr>
                            <tr><td>IDs</td><td><code>?uuid</code> <code>?counter</code> <code>?counterUuid</code></td></tr>
                            <tr><td>Text</td><td><code>?word</code> <code>?sentence</code> <code>?paragraph</code> <code>?lorem</code></td></tr>
                            <tr><td>Internet</td><td><code>?url</code> <code>?domain</code> <code>?ip</code> <code>?slug</code></td></tr>
                            <tr><td>Date/Time</td><td><code>?date</code> <code>?dateTime</code> <code>?time</code> <code>?timestamp</code></td></tr>
                            <tr><td>Other</td><td><code>?boolean</code> <code>?color</code> <code>?image</code> <code>?avatar</code></td></tr>
                        </table>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Array Repeating (__repeat)
                        <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                    <div class="accordion-content">
                        <p style="margin-bottom: 1rem; color: var(--text-dim);">Use <code>__repeat</code> to generate arrays. The key is automatically pluralized. Add <code>__uuid: true</code> for UUID7 IDs.</p>
                        <div class="example-code">
<pre>{
  "user": {
    "__repeat": 3,
    "__uuid": true,
    "id": "?id",
    "name": "?name"
  }
}

// Returns:
{
  "users": [
    { "id": "01932c5d-9e1f-7bc3-...", "name": "John Doe" },
    { "id": "01932c5d-9e20-7a12-...", "name": "Jane Smith" },
    { "id": "01932c5d-9e21-7d34-...", "name": "Bob Wilson" }
  ]
}</pre>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Instructions (__instructions)
                        <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9l6 6 6-6"></path>
                        </svg>
                    </button>
                    <div class="accordion-content">
                        <table class="placeholder-table">
                            <tr><th>Option</th><th>Description</th></tr>
                            <tr><td><code>delay</code></td><td>Add delay in ms (max 5000)</td></tr>
                            <tr><td><code>status</code></td><td>Override HTTP status code</td></tr>
                            <tr><td><code>headers</code></td><td>Add custom response headers</td></tr>
                            <tr><td><code>body</code></td><td>Override entire response body</td></tr>
                        </table>
                        <div class="example-code" style="margin-top: 1rem;">
<pre>{
  "__instructions": {
    "delay": 1000,
    "status": 201,
    "body": {
      "success": true,
      "user": { "name": "?name" }
    }
  }
}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Full Documentation -->
        <section class="docs">
            <h2>Documentation</h2>

            <article class="doc-section">
                <h3>How It Works</h3>
                <p>Stubbr lets you write production-ready API calls from day one. Your request has two parts:</p>
                <ul>
                    <li><strong>Payload</strong> — The real data your backend will receive in production</li>
                    <li><strong>__instructions</strong> — Defines what Stubbr returns during development (ignored by your real backend)</li>
                </ul>
                <p>When you switch to production, just change the API host. Your backend receives the payload and ignores <code>__instructions</code>.</p>
            </article>

            <article class="doc-section">
                <h3>Authentication</h3>
                <p>Include your token in every request using either method:</p>
                <div class="example-code">
<pre>Authorization: Bearer YOUR_TOKEN

// or

X-API-Token: YOUR_TOKEN</pre>
                </div>
                <p><strong>Token endpoints:</strong></p>
                <ul>
                    <li><code>GET /api/__token/request?email=you@example.com</code> — Get a new token</li>
                    <li><code>GET /api/__token/recover?email=you@example.com</code> — Recover existing token</li>
                </ul>
                <p>One token per email. Tokens are deleted after 30 days of inactivity.</p>
            </article>

            <article class="doc-section">
                <h3>The __instructions Object</h3>
                <p>Control the response behavior:</p>
                <table class="doc-table">
                    <tr><th>Option</th><th>Type</th><th>Description</th></tr>
                    <tr><td><code>body</code></td><td>any</td><td>The response body to return. Supports <code>?</code> placeholders and <code>__repeat</code>.</td></tr>
                    <tr><td><code>status</code></td><td>number</td><td>HTTP status code (default: 200)</td></tr>
                    <tr><td><code>delay</code></td><td>number</td><td>Delay in milliseconds before responding (max: 5000)</td></tr>
                    <tr><td><code>headers</code></td><td>object</td><td>Custom response headers</td></tr>
                    <tr><td><code>max_pages</code></td><td>number</td><td>Enable pagination metadata in response</td></tr>
                    <tr><td><code>no_cache</code></td><td>boolean</td><td>Skip caching this response</td></tr>
                </table>
                <div class="example-code">
<pre>{
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
}</pre>
                </div>
            </article>

            <article class="doc-section">
                <h3>Generating Arrays with __repeat</h3>
                <p>Use <code>__repeat</code> inside <code>__instructions.body</code> to generate arrays:</p>
                <div class="example-code">
<pre>{
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

// Returns:
{
  "users": [
    { "id": 0, "name": "John Doe" },
    { "id": 1, "name": "Jane Smith" },
    ...
  ]
}</pre>
                </div>
                <p><strong>Notes:</strong></p>
                <ul>
                    <li>The key is automatically pluralized (<code>user</code> → <code>users</code>)</li>
                    <li>Use <code>__as</code> to override the output key name</li>
                    <li>Maximum 20 items per array</li>
                    <li>Maximum 2 levels of nesting</li>
                    <li><code>?counter</code> increments globally across the entire response</li>
                </ul>
            </article>

            <article class="doc-section">
                <h3>UUID Mode with __uuid</h3>
                <p>Add <code>"__uuid": true</code> to a <code>__repeat</code> block to make <code>?id</code> and <code>?uuid</code> return UUID7 values:</p>
                <div class="example-code">
<pre>{
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

// Returns:
{
  "users": [
    { "id": "01932c5d-9e1f-7bc3-9e84-4f5a3b2c1d0e", "name": "John Doe" },
    { "id": "01932c5d-9e20-7a12-8b45-2d6e7f8a9b0c", "name": "Jane Smith" },
    { "id": "01932c5d-9e21-7d34-9c56-3e7f8a9b0c1d", "name": "Bob Wilson" }
  ]
}</pre>
                </div>
                <p>UUID7 is time-ordered, making it ideal for database primary keys. The flag applies to the entire block and nested blocks.</p>
            </article>

            <article class="doc-section">
                <h3>All Placeholders</h3>
                <p>Use these inside <code>__repeat</code> blocks to generate fake data:</p>

                <h4>Personal</h4>
                <table class="doc-table">
                    <tr><td><code>?name</code></td><td>Full name</td><td>"Jane Smith"</td></tr>
                    <tr><td><code>?firstName</code></td><td>First name</td><td>"John"</td></tr>
                    <tr><td><code>?lastName</code></td><td>Last name</td><td>"Doe"</td></tr>
                    <tr><td><code>?email</code></td><td>Email address</td><td>"john@example.com"</td></tr>
                    <tr><td><code>?username</code></td><td>Username</td><td>"john_doe_92"</td></tr>
                    <tr><td><code>?phone</code></td><td>Phone number</td><td>"+1-555-123-4567"</td></tr>
                </table>

                <h4>Company</h4>
                <table class="doc-table">
                    <tr><td><code>?company</code></td><td>Company name</td><td>"Acme Corp"</td></tr>
                    <tr><td><code>?jobTitle</code></td><td>Job title</td><td>"Software Engineer"</td></tr>
                </table>

                <h4>Address</h4>
                <table class="doc-table">
                    <tr><td><code>?address</code></td><td>Full address</td><td>"742 Evergreen Terrace, Springfield"</td></tr>
                    <tr><td><code>?street</code></td><td>Street address</td><td>"123 Main Street"</td></tr>
                    <tr><td><code>?city</code></td><td>City</td><td>"New York"</td></tr>
                    <tr><td><code>?state</code></td><td>State</td><td>"California"</td></tr>
                    <tr><td><code>?zip</code></td><td>Postal code</td><td>"90210"</td></tr>
                    <tr><td><code>?country</code></td><td>Country</td><td>"United States"</td></tr>
                </table>

                <h4>Numbers</h4>
                <table class="doc-table">
                    <tr><td><code>?number</code></td><td>Number 1-10000</td><td>4721</td></tr>
                    <tr><td><code>?numberSmall</code></td><td>Number 1-10</td><td>7</td></tr>
                    <tr><td><code>?numberLarge</code></td><td>Number 10000-1000000</td><td>842531</td></tr>
                    <tr><td><code>?decimal</code></td><td>Decimal number</td><td>342.87</td></tr>
                    <tr><td><code>?price</code></td><td>Price value</td><td>49.99</td></tr>
                    <tr><td><code>?id</code></td><td>ID number</td><td>12345</td></tr>
                </table>

                <h4>Identifiers</h4>
                <table class="doc-table">
                    <tr><td><code>?uuid</code></td><td>Random UUID</td><td>"a3bb189e-8bf9-3888-9912-ace4e6543002"</td></tr>
                    <tr><td><code>?counter</code></td><td>Incrementing number</td><td>0, 1, 2, 3...</td></tr>
                    <tr><td><code>?counterUuid</code></td><td>Incrementing UUID</td><td>"00000000-0000-0000-0000-000000000001"</td></tr>
                </table>

                <h4>Text</h4>
                <table class="doc-table">
                    <tr><td><code>?word</code></td><td>Single word</td><td>"example"</td></tr>
                    <tr><td><code>?sentence</code></td><td>One sentence</td><td>"This is a sample sentence."</td></tr>
                    <tr><td><code>?paragraph</code></td><td>One paragraph</td><td>"Lorem ipsum..."</td></tr>
                    <tr><td><code>?text</code></td><td>200 characters</td><td>"Lorem ipsum dolor..."</td></tr>
                    <tr><td><code>?lorem</code></td><td>Lorem sentence</td><td>"Lorem ipsum dolor sit amet."</td></tr>
                    <tr><td><code>?loremShort</code></td><td>3 words</td><td>"lorem ipsum dolor"</td></tr>
                    <tr><td><code>?loremLong</code></td><td>Multiple paragraphs</td><td>"Lorem ipsum..."</td></tr>
                </table>

                <h4>Internet</h4>
                <table class="doc-table">
                    <tr><td><code>?url</code></td><td>URL</td><td>"https://example.com/path"</td></tr>
                    <tr><td><code>?domain</code></td><td>Domain name</td><td>"example.com"</td></tr>
                    <tr><td><code>?ip</code></td><td>IP address</td><td>"192.168.1.1"</td></tr>
                    <tr><td><code>?slug</code></td><td>URL slug</td><td>"sample-slug-text"</td></tr>
                </table>

                <h4>Date & Time</h4>
                <table class="doc-table">
                    <tr><td><code>?date</code></td><td>Date (ISO)</td><td>"2024-03-15"</td></tr>
                    <tr><td><code>?dateTime</code></td><td>DateTime</td><td>"2024-03-15 14:30:00"</td></tr>
                    <tr><td><code>?stupidDateTime</code></td><td>US format</td><td>"03/15/2024 14:30:00"</td></tr>
                    <tr><td><code>?time</code></td><td>Time</td><td>"14:30:00"</td></tr>
                    <tr><td><code>?timestamp</code></td><td>Unix timestamp</td><td>1710514200</td></tr>
                </table>

                <h4>Other</h4>
                <table class="doc-table">
                    <tr><td><code>?boolean</code></td><td>true/false</td><td>true</td></tr>
                    <tr><td><code>?color</code></td><td>Hex color</td><td>"#3498db"</td></tr>
                    <tr><td><code>?colorName</code></td><td>Color name</td><td>"Blue"</td></tr>
                    <tr><td><code>?creditCard</code></td><td>Card number</td><td>"4532-1234-5678-9010"</td></tr>
                    <tr><td><code>?image</code></td><td>Image URL (640x480)</td><td>"https://via.placeholder.com/640x480"</td></tr>
                    <tr><td><code>?avatar</code></td><td>Avatar URL (200x200)</td><td>"https://via.placeholder.com/200x200"</td></tr>
                </table>
            </article>

            <article class="doc-section">
                <h3>Response Caching</h3>
                <p>Identical requests return cached responses. The cache key is based on:</p>
                <ul>
                    <li>Your API token</li>
                    <li>HTTP method</li>
                    <li>Request path</li>
                    <li>Query parameters</li>
                    <li>Request body</li>
                </ul>
                <p><strong>This means generated fake data (names, emails, UUIDs, etc.) stays consistent for identical requests.</strong> This is useful for testing - you get the same response every time without randomness breaking your tests.</p>
                <p>To get fresh data, either:</p>
                <ul>
                    <li>Use <code>"no_cache": true</code> in <code>__instructions</code></li>
                    <li>Change something in the request (different body, query param, etc.)</li>
                    <li>Clear your cache</li>
                </ul>
                <p>Cached responses include the header <code>__from_cache: true</code>.</p>
                <p><strong>Clear your cache:</strong></p>
                <div class="example-code">
<pre>POST /api/__cache/clear
Authorization: Bearer YOUR_TOKEN</pre>
                </div>
            </article>

            <article class="doc-section">
                <h3>Rate Limiting</h3>
                <ul>
                    <li><strong>API requests:</strong> 10 requests per second (per token)</li>
                    <li><strong>Token requests:</strong> 1 request per 10 seconds (per IP)</li>
                </ul>
                <p>If you add a <code>delay</code> in <code>__instructions</code>, the rate limit window adjusts accordingly.</p>
            </article>

            <article class="doc-section">
                <h3>Limits</h3>
                <table class="doc-table">
                    <tr><td>Max request body</td><td>100 KB</td></tr>
                    <tr><td>Max delay</td><td>5000 ms</td></tr>
                    <tr><td>Max items per __repeat</td><td>20</td></tr>
                    <tr><td>Max nesting depth</td><td>2 levels</td></tr>
                    <tr><td>Token inactivity timeout</td><td>30 days</td></tr>
                </table>
            </article>

            <article class="doc-section">
                <h3>Error Responses</h3>
                <table class="doc-table">
                    <tr><th>Status</th><th>Message</th></tr>
                    <tr><td>400</td><td>Invalid JSON</td></tr>
                    <tr><td>401</td><td>No API token provided / Invalid token</td></tr>
                    <tr><td>403</td><td>Token not verified</td></tr>
                    <tr><td>413</td><td>Request body too large</td></tr>
                    <tr><td>429</td><td>Rate limit exceeded</td></tr>
                </table>
            </article>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>
                <a href="https://github.com/Yorsyboy/stubbrdev">GitHub</a> &middot;
                Built by Daniel Melin
            </p>
        </div>
    </footer>

    <script>
        // Presets - payload is what your real backend receives,
        // __instructions.body defines the mock response
        const presets = {
            users: {
                method: 'POST',
                path: '/api/users',
                body: {
                    // Real payload your backend will receive
                    filters: { active: true },
                    page: 1,
                    // Mock response definition
                    __instructions: {
                        body: {
                            user: {
                                __repeat: 5,
                                id: '?counter',
                                name: '?name',
                                email: '?email'
                            }
                        }
                    }
                }
            },
            uuid: {
                method: 'POST',
                path: '/api/users',
                body: {
                    // Real payload
                    filters: { active: true },
                    // Mock response with UUID7 IDs
                    __instructions: {
                        body: {
                            user: {
                                __repeat: 5,
                                __uuid: true,
                                id: '?id',
                                name: '?name',
                                email: '?email'
                            }
                        }
                    }
                }
            },
            single: {
                method: 'POST',
                path: '/api/user/1',
                body: {
                    // Real payload
                    user_id: 1,
                    include: ['address', 'profile'],
                    // Mock response
                    __instructions: {
                        body: {
                            user: {
                                __repeat: 1,
                                id: 1,
                                name: '?name',
                                email: '?email',
                                address: {
                                    street: '?street',
                                    city: '?city',
                                    country: '?country'
                                }
                            }
                        }
                    }
                }
            },
            error: {
                method: 'POST',
                path: '/api/checkout',
                body: {
                    // Real payload
                    cart_id: 'cart_abc123',
                    payment_method: 'card',
                    // Mock error response
                    __instructions: {
                        status: 500,
                        body: {
                            error: 'payment_failed',
                            message: 'Card declined'
                        }
                    }
                }
            },
            slow: {
                method: 'POST',
                path: '/api/search',
                body: {
                    // Real payload
                    query: 'typescript',
                    limit: 10,
                    // Mock slow response
                    __instructions: {
                        delay: 2000,
                        body: {
                            result: {
                                __repeat: 3,
                                id: '?counter',
                                title: '?sentence',
                                url: '?url'
                            }
                        }
                    }
                }
            }
        };

        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = themeToggle.querySelector('.sun-icon');
        const moonIcon = themeToggle.querySelector('.moon-icon');

        function setTheme(dark) {
            document.documentElement.classList.toggle('dark', dark);
            sunIcon.style.display = dark ? 'none' : 'block';
            moonIcon.style.display = dark ? 'block' : 'none';
            localStorage.setItem('theme', dark ? 'dark' : 'light');
        }

        // Check saved preference or system preference
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        setTheme(savedTheme === 'dark' || (!savedTheme && prefersDark));

        themeToggle.addEventListener('click', () => {
            setTheme(!document.documentElement.classList.contains('dark'));
        });

        // Token management
        let currentToken = localStorage.getItem('stubbr_token') || '';

        const tokenForm = document.getElementById('tokenForm');
        const emailInput = document.getElementById('emailInput');
        const tokenBtn = document.getElementById('tokenBtn');
        const tokenResult = document.getElementById('tokenResult');
        const tokenIcon = document.getElementById('tokenIcon');
        const tokenValue = document.getElementById('tokenValue');
        const copyBtn = document.getElementById('copyBtn');
        const sendBtn = document.getElementById('sendBtn');

        // Show existing token if available
        if (currentToken) {
            showToken(currentToken, true);
            sendBtn.disabled = false;
        }

        tokenForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = emailInput.value.trim();
            if (!email) return;

            tokenBtn.disabled = true;
            tokenBtn.innerHTML = '<span class="spinner"></span>';

            try {
                // Try to request new token first
                let response = await fetch(`/api/__token/request?email=${encodeURIComponent(email)}`);

                let data = await response.json();

                // If already exists, try recovery
                if (response.status === 409) {
                    response = await fetch(`/api/__token/recover?email=${encodeURIComponent(email)}`);
                    data = await response.json();
                }

                if (data.token) {
                    currentToken = data.token;
                    localStorage.setItem('stubbr_token', currentToken);
                    showToken(currentToken, true);
                    sendBtn.disabled = false;
                } else {
                    showToken(data.error || 'Failed to get token', false);
                }
            } catch (err) {
                showToken('Network error. Please try again.', false);
            }

            tokenBtn.disabled = false;
            tokenBtn.textContent = 'Get Token';
        });

        function showToken(value, success) {
            tokenResult.classList.add('visible');
            tokenResult.classList.toggle('success', success);
            tokenResult.classList.toggle('error', !success);
            tokenIcon.textContent = success ? '✓' : '✗';
            tokenValue.textContent = value;
            copyBtn.style.display = success ? 'block' : 'none';
        }

        copyBtn.addEventListener('click', () => {
            navigator.clipboard.writeText(currentToken);
            copyBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg>';
            setTimeout(() => {
                copyBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>';
            }, 1500);
        });

        // Request builder
        const presetSelect = document.getElementById('presetSelect');
        const methodPath = document.getElementById('methodPath');
        const requestBody = document.getElementById('requestBody');
        const responseBody = document.getElementById('responseBody');
        const responseStatus = document.getElementById('responseStatus');
        const sendStatus = document.getElementById('sendStatus');

        function loadPreset(key) {
            const preset = presets[key];
            methodPath.textContent = `${preset.method} ${preset.path}`;
            requestBody.value = JSON.stringify(preset.body, null, 2);
            responseBody.innerHTML = '<span class="response-placeholder">Response will appear here...</span>';
            responseStatus.textContent = '';
            sendStatus.textContent = '';
        }

        loadPreset('users');

        presetSelect.addEventListener('change', (e) => {
            loadPreset(e.target.value);
        });

        sendBtn.addEventListener('click', async () => {
            if (!currentToken) {
                sendStatus.textContent = 'Get a token first';
                sendStatus.className = 'response-status error';
                return;
            }

            const preset = presets[presetSelect.value];
            let body;
            try {
                body = JSON.parse(requestBody.value);
            } catch (err) {
                sendStatus.textContent = 'Invalid JSON';
                sendStatus.className = 'response-status error';
                return;
            }

            sendBtn.disabled = true;
            sendBtn.innerHTML = '<span class="spinner"></span> Sending...';
            sendStatus.textContent = '';
            responseBody.innerHTML = '<span class="response-placeholder">Loading...</span>';

            const startTime = Date.now();

            try {
                const response = await fetch(preset.path, {
                    method: preset.method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${currentToken}`
                    },
                    body: JSON.stringify(body)
                });

                const elapsed = Date.now() - startTime;
                const data = await response.json();

                responseBody.textContent = JSON.stringify(data, null, 2);
                responseStatus.textContent = response.status;
                responseStatus.className = `response-status ${response.ok ? 'success' : 'error'}`;
                sendStatus.textContent = `${elapsed}ms`;
                sendStatus.className = 'response-status';
            } catch (err) {
                responseBody.textContent = err.message;
                responseStatus.textContent = 'Error';
                responseStatus.className = 'response-status error';
            }

            sendBtn.disabled = false;
            sendBtn.textContent = 'Send Request';
        });

        // Accordion
        document.querySelectorAll('.accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                header.parentElement.classList.toggle('open');
            });
        });
    </script>
</body>
</html>
