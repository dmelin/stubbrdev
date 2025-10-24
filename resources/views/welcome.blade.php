<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StubbrDev API - Mock API with Superpowers</title>
    <meta name="description" content="A flexible mock API service that echoes your requests with dynamic fake data generation. Perfect for frontend development, API testing, and demos.">
    <meta name="author" content="Daniel Melin">

    <!-- React & ReactDOM from CDN -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

    <!-- Babel Standalone for JSX -->
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #0f1419;
            --bg-secondary: #1a1f2e;
            --bg-card: rgba(26, 31, 46, 0.5);
            --text-primary: #f5f5f7;
            --text-secondary: rgba(245, 245, 247, 0.7);
            --primary: #a855f7;
            --accent: #06b6d4;
            --border: rgba(168, 85, 247, 0.2);
            --shadow-glow: 0 0 40px rgba(168, 85, 247, 0.3);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
            background: linear-gradient(180deg, #0f1419, #1a1f2e);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 25%;
            left: 25%;
            width: 400px;
            height: 400px;
            background: var(--primary);
            opacity: 0.15;
            border-radius: 50%;
            filter: blur(100px);
            animation: pulse 4s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: 25%;
            right: 25%;
            width: 400px;
            height: 400px;
            background: var(--accent);
            opacity: 0.15;
            border-radius: 50%;
            filter: blur(100px);
            animation: pulse 4s ease-in-out infinite 2s;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .hero-content {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 9999px;
            background: rgba(168, 85, 247, 0.1);
            border: 1px solid var(--border);
            margin-bottom: 32px;
            font-size: 14px;
        }

        h1 {
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 24px;
        }

        .subtitle {
            font-size: clamp(1.125rem, 3vw, 1.5rem);
            color: var(--text-secondary);
            max-width: 800px;
            margin: 0 auto 48px;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 64px;
        }

        .btn {
            padding: 12px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
        }

        .btn-primary:hover {
            box-shadow: var(--shadow-glow);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: rgba(168, 85, 247, 0.1);
        }

        /* Feature Badges */
        .feature-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
        }

        .feature-badge {
            padding: 12px 24px;
            border-radius: 12px;
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .feature-badge:hover {
            border-color: var(--primary);
        }

        /* Section */
        section {
            padding: 96px 24px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 64px;
        }

        .section-title {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
        }

        .section-desc {
            font-size: 1.25rem;
            color: var(--text-secondary);
        }

        /* Code Block */
        .code-block {
            position: relative;
            margin: 24px 0;
        }

        .code-block pre {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            overflow-x: auto;
            font-size: 14px;
            font-family: 'Monaco', 'Courier New', monospace;
        }

        .copy-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 8px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .copy-btn:hover {
            background: var(--bg-primary);
        }

        /* Steps */
        .steps {
            display: flex;
            flex-direction: column;
            gap: 48px;
        }

        .step {
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        .step-icon {
            flex-shrink: 0;
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-glow);
        }

        .step-content {
            flex: 1;
        }

        .step-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .step-number {
            color: rgba(168, 85, 247, 0.5);
        }

        .step-desc {
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        /* Cards */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 32px;
        }

        .card {
            padding: 24px;
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 32px rgba(168, 85, 247, 0.15);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .card-item {
            margin-bottom: 12px;
        }

        .card-label {
            font-size: 12px;
            font-family: 'Monaco', monospace;
            color: var(--accent);
            display: block;
            margin-bottom: 4px;
        }

        .card-value {
            font-size: 14px;
            font-family: 'Monaco', monospace;
            padding: 8px;
            background: rgba(6, 182, 212, 0.1);
            border-radius: 6px;
        }

        /* Footer */
        footer {
            padding: 48px 24px;
            border-top: 1px solid var(--border);
            background: var(--bg-card);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 32px;
            margin-bottom: 32px;
        }

        .footer-section h3 {
            margin-bottom: 16px;
            font-size: 1.125rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section li {
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .footer-bottom {
            padding-top: 32px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-bottom p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .step {
                flex-direction: column;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
    </style>
</head>
<body>
<div id="root"></div>

<script type="text/babel">
    const { useState } = React;

    // Code Block Component
    const CodeBlock = ({ code }) => {
        const [copied, setCopied] = useState(false);

        const copyToClipboard = () => {
            navigator.clipboard.writeText(code);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        };

        return (
            <div className="code-block">
                <button className="copy-btn" onClick={copyToClipboard}>
                    {copied ? '✓ Copied' : 'Copy'}
                </button>
                <pre><code>{code}</code></pre>
            </div>
        );
    };

    // Hero Component
    const Hero = () => (
        <section className="hero">
            <div className="hero-content">
                <div className="badge animate-fade-in">
                    <span>⚡</span>
                    <span>Mock API with Superpowers</span>
                </div>

                <h1 className="animate-fade-in">StubbrDev API</h1>

                <p className="subtitle animate-fade-in">
                    Echo your requests with dynamic fake data generation.
                    <br />
                    Perfect for frontend development, API testing, and demos.
                </p>

                <div className="btn-group animate-fade-in">
                    <a href="#quick-start" className="btn btn-primary">
                        Get Started →
                    </a>
                    <a href="#documentation" className="btn btn-secondary">
                        📖 View Docs
                    </a>
                </div>

                <div className="feature-badges animate-fade-in">
                    <div className="feature-badge">Any HTTP Method</div>
                    <div className="feature-badge">100+ Fake Data Types</div>
                    <div className="feature-badge">Custom Delays</div>
                    <div className="feature-badge">Rate Limiting</div>
                </div>
            </div>
        </section>
    );

    // Quick Start Component
    const QuickStart = () => {
        const steps = [
            {
                icon: '📧',
                title: 'Request Your Token',
                desc: 'Get started by requesting an API token with your email',
                code: `curl -X POST https://your-api.com/__token/request \\
  -H "Content-Type: application/json" \\
  -d '{"email": "your@email.com"}'`
            },
            {
                icon: '✅',
                title: 'Verify Your Token',
                desc: 'Check your email and verify with the provided credentials',
                code: `curl -X POST https://your-api.com/__token/verify \\
  -H "Content-Type: application/json" \\
  -d '{
    "email": "your@email.com",
    "token": "YOUR_TOKEN_HERE",
    "secret": "YOUR_SECRET_HERE"
  }'`
            },
            {
                icon: '🚀',
                title: 'Make Your First Request',
                desc: 'Start using the API with dynamic fake data generation',
                code: `curl -X POST https://your-api.com/users \\
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \\
  -H "Content-Type: application/json" \\
  -d '{
    "name": "?name",
    "email": "?email",
    "age": "?numberSmall"
  }'`
            }
        ];

        return (
            <section id="quick-start">
                <div className="container">
                    <div className="section-header">
                        <h2 className="section-title">Quick Start</h2>
                        <p className="section-desc">Get up and running in 3 simple steps</p>
                    </div>

                    <div className="steps">
                        {steps.map((step, index) => (
                            <div key={index} className="step animate-fade-in">
                                <div className="step-icon">
                                    <span style=@{{fontSize: '32px'}}>{step.icon}</span>
                                </div>
                                <div className="step-content">
                                    <h3 className="step-title">
                                        <span className="step-number">0{index + 1}</span>
                                        {step.title}
                                    </h3>
                                    <p className="step-desc">{step.desc}</p>
                                    <CodeBlock code={step.code} />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        );
    };

    // Fake Data Showcase Component
    const FakeDataShowcase = () => {
        const categories = [
            {
                name: 'Personal',
                items: [
                    { placeholder: '?name', example: 'Jane Smith' },
                    { placeholder: '?email', example: 'jane@example.com' },
                    { placeholder: '?phone', example: '+1-555-123-4567' }
                ]
            },
            {
                name: 'Numbers',
                items: [
                    { placeholder: '?number', example: '4721' },
                    { placeholder: '?price', example: '49.99' },
                    { placeholder: '?decimal', example: '342.87' }
                ]
            },
            {
                name: 'Date & Time',
                items: [
                    { placeholder: '?date', example: '2024-03-15' },
                    { placeholder: '?time', example: '14:30:00' },
                    { placeholder: '?timestamp', example: '1710514200' }
                ]
            },
            {
                name: 'Internet',
                items: [
                    { placeholder: '?url', example: 'https://example.com' },
                    { placeholder: '?domain', example: 'example.com' },
                    { placeholder: '?ip', example: '192.168.1.1' }
                ]
            }
        ];

        return (
            <section style=@{{background: 'var(--bg-card)'}}>
                <div className="container">
                    <div className="section-header">
                        <div className="badge">
                            <span>✨</span>
                            <span>100+ Fake Data Types</span>
                        </div>
                        <h2 className="section-title">Dynamic Data Generation</h2>
                        <p className="section-desc">
                            Replace any string with a ? prefix to generate realistic fake data
                        </p>
                    </div>

                    <div className="card-grid">
                        {categories.map(category => (
                            <div key={category.name} className="card animate-fade-in">
                                <h3 className="card-title">{category.name}</h3>
                                {category.items.map(item => (
                                    <div key={item.placeholder} className="card-item">
                                        <code className="card-label">{item.placeholder}</code>
                                        <code className="card-value">{item.example}</code>
                                    </div>
                                ))}
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        );
    };

    // Footer Component
    const Footer = () => (
        <footer>
            <div className="container">
                <div className="footer-content">
                    <div className="footer-section">
                        <h3>StubbrDev API</h3>
                        <p style=@{{color: 'var(--text-secondary)', fontSize: '14px'}}>
                            Mock API with Superpowers.
                            <br />
                            Built for developers, by developers.
                        </p>
                    </div>

                    <div className="footer-section">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="#quick-start" style=@{{color: 'var(--text-secondary)', textDecoration: 'none'}}>Quick Start</a></li>
                            <li><a href="#documentation" style=@{{color: 'var(--text-secondary)', textDecoration: 'none'}}>Documentation</a></li>
                            <li><a href="#examples" style=@{{color: 'var(--text-secondary)', textDecoration: 'none'}}>Examples</a></li>
                        </ul>
                    </div>

                    <div className="footer-section">
                        <h3>Limitations</h3>
                        <ul>
                            <li>• Max request size: 100KB</li>
                            <li>• Max delay: 5000ms</li>
                            <li>• One token per email</li>
                            <li>• Tokens deleted after 30 days</li>
                        </ul>
                    </div>
                </div>

                <div className="footer-bottom">
                    <p>© 2024 StubbrDev API. Created by Daniel Melin</p>
                </div>
            </div>
        </footer>
    );

    // Main App Component
    const App = () => (
        <div>
            <Hero />
            <QuickStart />
            <FakeDataShowcase />
            <Footer />
        </div>
    );

    // Render the app
    const root = ReactDOM.createRoot(document.getElementById('root'));
    root.render(<App />);
</script>
</body>
</html>
