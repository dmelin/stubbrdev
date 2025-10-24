import React, { useState } from 'react';
import { Copy, Check, Key, Code, Zap, Shield, Clock } from 'lucide-react';

export default function StubbrLanding() {
  const [email, setEmail] = useState('');
  const [token, setToken] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [copied, setCopied] = useState(false);

  const requestToken = async () => {
    if (!email || !email.includes('@')) {
      setError('Please enter a valid email address');
      return;
    }

    setLoading(true);
    setError('');
    setToken('');

    try {
      const response = await fetch('https://stubbr.dev/__token/request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email })
      });

      const data = await response.json();

      if (response.ok) {
        setToken(data.token);
      } else {
        setError(data.error || 'Something went wrong');
      }
    } catch (err) {
      setError('Failed to connect. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const copyToken = () => {
    navigator.clipboard.writeText(token);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter') {
      requestToken();
    }
  };

  return (
    <div className="min-h-screen bg-gray-100 text-gray-800">
      <header className="border-b border-gray-300 bg-white/50 backdrop-blur-sm sticky top-0 z-50">
        <div className="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center">
              <Code className="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 className="text-xl font-semibold text-gray-900">StubbrDev</h1>
              <p className="text-xs text-gray-600">Mock API with Superpowers</p>
            </div>
          </div>
          <a href="https://stubbr.dev" className="text-sm text-gray-600 hover:text-gray-900">
            stubbr.dev
          </a>
        </div>
      </header>

      <section className="py-16 px-6 bg-gradient-to-b from-white to-gray-50">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-4xl font-bold text-gray-900 mb-4">
            Mock APIs Made Simple
          </h2>
          <p className="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
            A flexible mock API service that echoes your requests with dynamic fake data generation. 
            Perfect for frontend development, testing, and rapid prototyping.
          </p>

          <div className="grid md:grid-cols-4 gap-4 mb-12">
            <div className="bg-white rounded-lg p-4 border border-gray-200">
              <Zap className="w-8 h-8 text-gray-700 mx-auto mb-2" />
              <p className="text-sm font-medium">Instant Setup</p>
            </div>
            <div className="bg-white rounded-lg p-4 border border-gray-200">
              <Shield className="w-8 h-8 text-gray-700 mx-auto mb-2" />
              <p className="text-sm font-medium">Secure Tokens</p>
            </div>
            <div className="bg-white rounded-lg p-4 border border-gray-200">
              <Code className="w-8 h-8 text-gray-700 mx-auto mb-2" />
              <p className="text-sm font-medium">40+ Placeholders</p>
            </div>
            <div className="bg-white rounded-lg p-4 border border-gray-200">
              <Clock className="w-8 h-8 text-gray-700 mx-auto mb-2" />
              <p className="text-sm font-medium">Rate Limited</p>
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 max-w-md mx-auto">
            <div className="flex items-center justify-center gap-2 mb-4">
              <Key className="w-5 h-5 text-gray-700" />
              <h3 className="text-lg font-semibold text-gray-900">Get Your API Token</h3>
            </div>
            
            {!token ? (
              <div className="space-y-4">
                <div>
                  <input
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    onKeyPress={handleKeyPress}
                    placeholder="your@email.com"
                    className="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 text-gray-900"
                  />
                </div>
                {error && (
                  <div className="text-sm text-red-600 bg-red-50 px-4 py-2 rounded-lg">
                    {error}
                  </div>
                )}
                <button
                  onClick={requestToken}
                  disabled={loading}
                  className="w-full bg-gray-800 text-white py-3 rounded-lg font-medium hover:bg-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  {loading ? 'Requesting...' : 'Get Token'}
                </button>
                <p className="text-xs text-gray-500">One token per email • Free forever</p>
              </div>
            ) : (
              <div className="space-y-4">
                <div className="bg-gray-50 rounded-lg p-4 border border-gray-200">
                  <p className="text-xs text-gray-600 mb-2">Your API Token</p>
                  <div className="flex items-center gap-2">
                    <code className="flex-1 text-sm font-mono text-gray-900 break-all">
                      {token}
                    </code>
                    <button
                      onClick={copyToken}
                      className="p-2 hover:bg-gray-200 rounded-lg transition-colors"
                      title="Copy token"
                    >
                      {copied ? (
                        <Check className="w-4 h-4 text-green-600" />
                      ) : (
                        <Copy className="w-4 h-4 text-gray-600" />
                      )}
                    </button>
                  </div>
                </div>
                <div className="text-sm text-gray-600 bg-green-50 px-4 py-3 rounded-lg border border-green-200">
                  ✓ Token ready! Start making requests immediately.
                </div>
                <button
                  onClick={() => { setToken(''); setEmail(''); }}
                  className="text-sm text-gray-600 hover:text-gray-900"
                >
                  Request another token
                </button>
              </div>
            )}
          </div>
        </div>
      </section>

      <section className="py-16 px-6 bg-white border-t border-gray-200">
        <div className="max-w-4xl mx-auto">
          <h3 className="text-2xl font-bold text-gray-900 mb-6">Quick Start</h3>
          <div className="bg-gray-50 rounded-xl p-6 border border-gray-200 overflow-x-auto">
            <pre className="text-sm text-gray-800">
{`curl -X POST https://stubbr.dev/api/users \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Content-Type: application/json" \\
  -d '{
    "name": "?name",
    "email": "?email",
    "age": "?numberSmall"
  }'`}
            </pre>
          </div>
          <div className="mt-4 bg-gray-50 rounded-xl p-6 border border-gray-200">
            <p className="text-xs text-gray-600 mb-2">Response:</p>
            <pre className="text-sm text-gray-800">
{`{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "age": 7
}`}
            </pre>
          </div>
        </div>
      </section>

      <section className="py-16 px-6 bg-gray-50">
        <div className="max-w-4xl mx-auto">
          <h3 className="text-2xl font-bold text-gray-900 mb-6">Fake Data Placeholders</h3>
          <div className="grid md:grid-cols-2 gap-6">
            {[
              { title: 'Personal', items: ['?name', '?firstName', '?lastName', '?email', '?username', '?phone'] },
              { title: 'Numbers', items: ['?number', '?numberSmall', '?numberLarge', '?decimal', '?price', '?id'] },
              { title: 'Text', items: ['?word', '?sentence', '?paragraph', '?lorem', '?loremShort', '?loremLong'] },
              { title: 'Date & Time', items: ['?date', '?dateTime', '?time', '?timestamp', '?stupidDateTime'] },
              { title: 'Address', items: ['?address', '?street', '?city', '?state', '?zip', '?country'] },
              { title: 'Other', items: ['?uuid', '?boolean', '?color', '?url', '?domain', '?image'] }
            ].map((category, idx) => (
              <div key={idx} className="bg-white rounded-lg p-6 border border-gray-200">
                <h4 className="font-semibold text-gray-900 mb-3">{category.title}</h4>
                <div className="flex flex-wrap gap-2">
                  {category.items.map((item, i) => (
                    <code key={i} className="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                      {item}
                    </code>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16 px-6 bg-white">
        <div className="max-w-4xl mx-auto">
          <h3 className="text-2xl font-bold text-gray-900 mb-6">Array Repeating</h3>
          <p className="text-gray-600 mb-6">
            Generate multiple items using{' '}
            <code className="bg-gray-100 px-2 py-1 rounded text-sm">__repeat</code>
          </p>
          <div className="grid md:grid-cols-2 gap-6">
            <div className="bg-gray-50 rounded-xl p-6 border border-gray-200">
              <p className="text-xs text-gray-600 mb-2">Request:</p>
              <pre className="text-sm text-gray-800 overflow-x-auto">
{`{
  "user": {
    "__repeat": 3,
    "id": "?counter",
    "name": "?name",
    "email": "?email"
  }
}`}
              </pre>
            </div>
            <div className="bg-gray-50 rounded-xl p-6 border border-gray-200">
              <p className="text-xs text-gray-600 mb-2">Response:</p>
              <pre className="text-sm text-gray-800 overflow-x-auto">
{`{
  "users": [
    {
      "id": 0,
      "name": "John Doe",
      "email": "john@example.com"
    }
  ]
}`}
              </pre>
            </div>
          </div>
        </div>
      </section>

      <section className="py-16 px-6 bg-gray-50">
        <div className="max-w-4xl mx-auto">
          <h3 className="text-2xl font-bold text-gray-900 mb-6">Special Instructions</h3>
          <div className="bg-white rounded-xl p-6 border border-gray-200">
            <p className="text-gray-600 mb-4">
              Control response behavior with{' '}
              <code className="bg-gray-100 px-2 py-1 rounded text-sm">__instructions</code>
            </p>
            <pre className="text-sm text-gray-800 overflow-x-auto">
{`{
  "user": { "name": "?name" },
  "__instructions": {
    "delay": 2000,
    "status": 201,
    "headers": {
      "X-Custom": "Value"
    },
    "body": {
      "success": true
    }
  }
}`}
            </pre>
          </div>
        </div>
      </section>

      <section className="py-16 px-6 bg-white">
        <div className="max-w-4xl mx-auto">
          <h3 className="text-2xl font-bold text-gray-900 mb-6">Rate Limiting</h3>
          <div className="grid md:grid-cols-3 gap-6">
            <div className="bg-gray-50 rounded-lg p-6 border border-gray-200 text-center">
              <p className="text-3xl font-bold text-gray-900 mb-2">10</p>
              <p className="text-sm text-gray-600">Requests per window</p>
            </div>
            <div className="bg-gray-50 rounded-lg p-6 border border-gray-200 text-center">
              <p className="text-3xl font-bold text-gray-900 mb-2">100KB</p>
              <p className="text-sm text-gray-600">Max request size</p>
            </div>
            <div className="bg-gray-50 rounded-lg p-6 border border-gray-200 text-center">
              <p className="text-3xl font-bold text-gray-900 mb-2">5s</p>
              <p className="text-sm text-gray-600">Max delay</p>
            </div>
          </div>
        </div>
      </section>

      <footer className="py-12 px-6 bg-gray-800 text-gray-300">
        <div className="max-w-4xl mx-auto text-center">
          <p className="mb-2">Built with ❤️ by Daniel Melin</p>
          <a href="https://stubbr.dev" className="text-gray-400 hover:text-white">
            stubbr.dev
          </a>
        </div>
      </footer>
    </div>
  );
}
