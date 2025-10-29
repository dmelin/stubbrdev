<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stubbr.dev - Ship Faster. Delete Nothing.</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #0a0a0f;
            --bg-light: #151520;
            --accent: #6366f1;
            --accent-dark: #4f46e5;
            --text: #e5e7eb;
            --text-dim: #9ca3af;
            --success: #10b981;
            --danger: #ef4444;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Animated gradient background */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, #0a0a0f 0%, #1a1a2e 100%);
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
        }

        .hero-content {
            text-align: center;
            padding: 4rem 0;
        }

        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 2rem;
            font-size: 0.875rem;
            color: var(--accent);
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero p {
            font-size: clamp(1.125rem, 3vw, 1.5rem);
            color: var(--text-dim);
            max-width: 800px;
            margin: 0 auto 3rem;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Code comparison section */
        .comparison {
            padding: 6rem 0;
            background: var(--bg-light);
        }

        .section-title {
            text-align: center;
            font-size: clamp(2rem, 5vw, 3rem);
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .section-subtitle {
            text-align: center;
            color: var(--text-dim);
            font-size: 1.25rem;
            margin-bottom: 4rem;
        }

        .comparison-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .code-block {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .code-block:hover {
            transform: translateY(-5px);
        }

        .code-block::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .code-block.bad::before {
            background: var(--danger);
        }

        .code-block.good::before {
            background: var(--success);
        }

        .code-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .code-header.bad {
            color: var(--danger);
        }

        .code-header.good {
            color: var(--success);
        }

        pre {
            background: rgba(0, 0, 0, 0.5);
            padding: 1.5rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            font-size: 0.875rem;
            line-height: 1.8;
        }

        code {
            font-family: 'Courier New', monospace;
            color: #e5e7eb;
        }

        .keyword { color: #c678dd; }
        .string { color: #98c379; }
        .comment { color: #5c6370; font-style: italic; }
        .function { color: #61afef; }

        /* Features section */
        .features {
            padding: 6rem 0;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--accent);
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--text-dim);
            line-height: 1.6;
        }

        /* Quick start section */
        .quick-start {
            padding: 6rem 0;
            background: var(--bg-light);
        }

        .terminal {
            background: #1a1a2e;
            border-radius: 1rem;
            overflow: hidden;
            margin-top: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .terminal-header {
            background: #0d0d15;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .terminal-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .dot-red { background: #ff5f56; }
        .dot-yellow { background: #ffbd2e; }
        .dot-green { background: #27c93f; }

        .terminal-body {
            padding: 2rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.8;
        }

        .terminal-prompt {
            color: var(--success);
        }

        .terminal-command {
            color: var(--text);
        }

        .terminal-output {
            color: var(--text-dim);
            margin-left: 0;
        }

        /* CTA section */
        .final-cta {
            padding: 6rem 0;
            text-align: center;
        }

        .final-cta h2 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            margin-bottom: 1rem;
        }

        .final-cta p {
            font-size: 1.25rem;
            color: var(--text-dim);
            margin-bottom: 2rem;
        }

        /* Footer */
        footer {
            padding: 3rem 0;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-dim);
        }

        footer a {
            color: var(--accent);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Animations on scroll */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .comparison-grid {
                grid-template-columns: 1fr;
            }
            
            .cta-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="badge">🎭 Zero refactoring. Zero mock code.</div>
                <h1>Ship Faster.<br>Delete Nothing.</h1>
                <p>Build your frontend with production-ready API calls from day one. When your backend is ready, just change the host. No refactoring. No "remove mock code" commits.</p>
                <div class="cta-buttons">
                    <a href="#quick-start" class="btn btn-primary">
                        Get Started Free
                        <span>→</span>
                    </a>
                    <a href="#comparison" class="btn btn-secondary">
                        See How It Works
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison Section -->
    <section id="comparison" class="comparison">
        <div class="container">
            <h2 class="section-title">The Migration Problem</h2>
            <p class="section-subtitle">Traditional mock APIs force you to maintain two codebases</p>
            
            <div class="comparison-grid">
                <div class="code-block bad">
                    <div class="code-header bad">
                        <span>❌</span>
                        <span>Traditional Approach</span>
                    </div>
                    <pre><code><span class="comment">// Development - mock data</span>
<span class="keyword">if</span> (DEV_MODE) {
  <span class="keyword">return</span> mockData
}

<span class="comment">// Production - real API</span>
<span class="keyword">const</span> response = <span class="keyword">await</span> <span class="function">fetch</span>(<span class="string">'/api/users'</span>, {
  <span class="keyword">body</span>: <span class="function">JSON.stringify</span>({ user_id })
})

<span class="comment">// Maintain two codepaths forever 😩</span></code></pre>
                </div>

                <div class="code-block good">
                    <div class="code-header good">
                        <span>✅</span>
                        <span>Stubbr.dev - Same Request, Real Response</span>
                    </div>
                    <pre><code><span class="comment">// Identical code in dev AND production!</span>
<span class="keyword">const</span> response = <span class="keyword">await</span> <span class="function">fetch</span>(`${API_HOST}/users`, {
  <span class="keyword">body</span>: <span class="function">JSON.stringify</span>({
    user_id: <span class="string">191</span>, <span class="comment">// ← Real payload</span>
    __instructions: { <span class="comment">// ← Ignored in prod</span>
      body: { users: { __repeat: 10 } }
    }
  })
})</code></pre>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">The Secret: Request Stays Clean</h2>
            <p class="section-subtitle">Real payload outside. Mock response inside __instructions.</p>
            
            <div style="max-width: 800px; margin: 0 auto 4rem; background: rgba(0,0,0,0.3); padding: 2rem; border-radius: 1rem; border: 1px solid rgba(99, 102, 241, 0.3);">
                <pre style="margin: 0;"><code>{
  <span class="string">"user_id"</span>: <span class="string">191</span>,              <span class="comment">// ← Production payload</span>
  <span class="string">"filters"</span>: { <span class="string">"active"</span>: <span class="keyword">true</span> }, <span class="comment">// ← Backend sees this</span>
  
  <span class="string">"__instructions"</span>: {        <span class="comment">// ← Gets ignored in prod</span>
    <span class="string">"body"</span>: {
      <span class="string">"users"</span>: {
        <span class="string">"__repeat"</span>: <span class="string">10</span>,
        <span class="string">"name"</span>: <span class="string">"?name"</span>,
        <span class="string">"email"</span>: <span class="string">"?email"</span>
      }
    }
  }
}</code></pre>
            </div>
            
            <h2 class="section-title" style="margin-top: 4rem;">Built for Modern Development</h2>
            <p class="section-subtitle">Everything you need to ship faster</p>
            
            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">🎯</div>
                    <h3>Clean Request Separation</h3>
                    <p>Real payload outside. Mock response in __instructions. Backend sees exactly what it'll get in production—no placeholder pollution.</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">🔄</div>
                    <h3>Instant Array Generation</h3>
                    <p>Use __repeat to generate 1 or 1000 items. Perfect for testing infinite scroll, pagination, and data grids.</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">⚡</div>
                    <h3>Test Real Scenarios</h3>
                    <p>Add delays, custom status codes, and headers with __instructions. Test loading states and error handling properly.</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">🎲</div>
                    <h3>40+ Fake Data Types</h3>
                    <p>Names, emails, addresses, prices, dates, UUIDs, and more. Realistic data makes better UI decisions.</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">🤝</div>
                    <h3>Backend Parallel Development</h3>
                    <p>Backend team sees exactly what shape you're sending. No "wait, what format?" conversations.</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">🚀</div>
                    <h3>One-Line Migration</h3>
                    <p>Change API_HOST and you're done. Backend ignores your placeholders. Zero refactoring needed.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Start Section -->
    <section id="quick-start" class="quick-start">
        <div class="container">
            <h2 class="section-title">Get Started in 60 Seconds</h2>
            <p class="section-subtitle">No installation. No configuration. Just make requests.</p>
            
            <div class="terminal fade-in">
                <div class="terminal-header">
                    <div class="terminal-dot dot-red"></div>
                    <div class="terminal-dot dot-yellow"></div>
                    <div class="terminal-dot dot-green"></div>
                </div>
                <div class="terminal-body">
                    <div><span class="terminal-prompt">$</span> <span class="terminal-command">curl -X POST https://stubbr.dev/__token/request \</span></div>
                    <div><span class="terminal-command">  -H "Content-Type: application/json" \</span></div>
                    <div><span class="terminal-command">  -d '{"email": "dev@example.com"}'</span></div>
                    <div class="terminal-output">{"token": "a3bb189e-8bf9-3888-9912-ace4e6543002"}</div>
                    
                    <div style="margin-top: 1.5rem;"><span class="terminal-prompt">$</span> <span class="terminal-command">curl https://stubbr.dev/api/users \</span></div>
                    <div><span class="terminal-command">  -H "Authorization: Bearer YOUR_TOKEN" \</span></div>
                    <div><span class="terminal-command">  -d '{</span></div>
                    <div><span class="terminal-command">    "user_id": 191,</span></div>
                    <div><span class="terminal-command">    "__instructions": {</span></div>
                    <div><span class="terminal-command">      "body": {"users": {"__repeat": 3, "name": "?name"}}</span></div>
                    <div><span class="terminal-command">    }</span></div>
                    <div><span class="terminal-command">  }'</span></div>
                    
                    <div class="terminal-output">{"users": [</div>
                    <div class="terminal-output">  {"name": "John Doe"},</div>
                    <div class="terminal-output">  {"name": "Jane Smith"},</div>
                    <div class="terminal-output">  {"name": "Bob Wilson"}</div>
                    <div class="terminal-output">]}</div>
                    
                    <div style="margin-top: 1.5rem; color: var(--success);">✓ Change host to prod → backend ignores __instructions</div>
                    <div style="color: var(--success);">✓ Your request payload (user_id) stays identical!</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="final-cta">
        <div class="container">
            <h2>Stop Writing Mock Code.<br>Start Shipping.</h2>
            <p>Join developers who ship features, not refactoring PRs.</p>
            <div class="cta-buttons">
                <a href="https://stubbr.dev/__token/request" class="btn btn-primary">
                    Get Your Free Token
                    <span>→</span>
                </a>
                <a href="https://github.com/dmelin/stubbrdev" class="btn btn-secondary">
                    Read the Docs
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>Built with ❤️ by Daniel Melin | <a href="https://stubbr.dev">stubbr.dev</a></p>
            <p style="margin-top: 0.5rem; font-size: 0.875rem;">Free forever. No credit card required.</p>
        </div>
    </footer>

    <script>
        // Fade in animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
