<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import CodeWell from '../components/ui/CodeWell.vue';
import CopyButton from '../components/ui/CopyButton.vue';
import RecipeCard from '../components/ui/RecipeCard.vue';
import { RECIPES, highlightCode } from '../lib/builder';
import { GENERATOR_GROUPS } from '../lib/generators';
import { navigate } from '../lib/router';
import { ensureToken } from '../lib/token';
import { addEndpoint } from '../lib/workspace';

const NAV = [
    { id: 'quickstart', label: 'Quickstart' },
    { id: 'tokens', label: 'Tokens' },
    {
        id: 'instructions',
        label: 'Instructions',
        children: [
            { id: 'instr-status', label: 'status' },
            { id: 'instr-delay', label: 'delay' },
            { id: 'instr-max_pages', label: 'max_pages' },
            { id: 'instr-no_cache', label: 'no_cache' },
            { id: 'instr-flaky', label: 'flaky' },
        ],
    },
    { id: 'body', label: 'Body & __repeat' },
    { id: 'generators', label: 'Generators' },
    { id: 'caching', label: 'Caching' },
    { id: 'limits', label: 'Limits & errors' },
    { id: 'recipes', label: 'Recipes' },
];

const INSTRUCTION_CARDS = [
    { id: 'instr-body', name: 'body', type: 'any', copy: 'The response you want back. Supports ? generators and __repeat. Leave it out and Stubbr echoes your request.' },
    { id: 'instr-status', name: 'status', type: 'int', copy: 'Any HTTP code. Use 429 and 503 to test retry logic, 401 to test re-auth.' },
    { id: 'instr-delay', name: 'delay', type: 'ms', copy: 'Up to 5 000 ms. The quickest way to find a missing spinner.' },
    { id: 'instr-headers', name: 'headers', type: 'object', copy: 'Extra response headers. Security and connection headers are ignored.' },
    { id: 'instr-max_pages', name: 'max_pages', type: 'int', copy: 'Adds a meta block with page, per_page, total_pages. Page with ?page=N.' },
    { id: 'instr-no_cache', name: 'no_cache', type: 'bool', copy: 'Fresh fake values on every single call instead of the cached reply.' },
    { id: 'instr-flaky', name: 'flaky', type: 'true | object', copy: 'Fail some calls. true is a coin flip with a 500, 502, 503, 504 or 429. { "every": 3, "codes": [502, 410] } fails every third call with one of your codes. Failures reply { error, code } with a __flaky: failed header, and flaky requests are never cached.' },
];

const LIMITS = [
    ['100 KB', 'max request body'],
    ['5 000 ms', 'max delay'],
    ['20', 'items per __repeat'],
    ['2', 'levels of nesting'],
    ['10 / s', 'requests per token'],
    ['30 days', 'until an idle token is removed'],
];

const ERRORS = [
    ['400', 'Invalid JSON'],
    ['401', 'No API token, or an unknown one'],
    ['403', 'Token not verified'],
    ['413', 'Request body over 100 KB'],
    ['429', 'Rate limit exceeded'],
];

const host = window.location.origin.replace(/^https?:\/\//, '');

const SNIPPETS = {
    token: `curl "https://${host}/api/__token/request?email=you@example.com"`,
    first: `curl -X POST https://${host}/api/users \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Content-Type: application/json" \\
  -d '{
    "filters": { "active": true },
    "__instructions": {
      "body": {
        "user": { "__repeat": 3, "id": "?counter", "name": "?name", "email": "?email" }
      }
    }
  }'`,
    swap: `const API_HOST = import.meta.env.DEV
  ? 'https://${host}'
  : 'https://api.yourcompany.com';

await fetch(\`\${API_HOST}/api/users\`, {
  method: 'POST',
  headers: { Authorization: \`Bearer \${token}\`, 'Content-Type': 'application/json' },
  body: JSON.stringify({
    filters: { active: true },          // your real payload
    __instructions: { body: { user: { __repeat: 10, name: '?name' } } }, // ignored by your backend
  }),
});`,
    instructions: `"__instructions": {
  "delay": 1500,
  "flaky": { "every": 3, "codes": [502, 503] },
  "headers": { "X-Request-Id": "abc-123" },
  "body": { "order_id": "?uuid", "status": "paid" }
}
// calls 1 and 2 return the body, call 3 fails with 502 or 503, all take 1.5 s`,
    repeat: `"body": {
  "user": {
    "__repeat": 5,
    "__uuid": true,
    "__as": "members",
    "id": "?id",
    "name": "?name"
  }
}

// → { "members": [ { "id": "01932c5d-…", "name": "Jane Smith" }, … ] }`,
    clear: `curl -X POST https://${host}/api/__cache/clear \\
  -H "Authorization: Bearer YOUR_TOKEN"`,
};

const activeId = ref('quickstart');
let observer = null;

onMounted(() => {
    const sections = Array.from(document.querySelectorAll('.docs-section[id]'));
    observer = new IntersectionObserver((entries) => {
        const visible = entries
            .filter((entry) => entry.isIntersecting)
            .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
        if (visible.length) activeId.value = visible[0].target.id;
    }, { rootMargin: '-10% 0px -70% 0px', threshold: 0 });
    sections.forEach((section) => observer.observe(section));
    if (window.location.hash) {
        const el = document.getElementById(window.location.hash.slice(1));
        el?.scrollIntoView({ block: 'start' });
    }
});

onBeforeUnmount(() => observer?.disconnect());

const isActive = (item) => activeId.value === item.id || item.children?.some((child) => child.id === activeId.value);

const pickRecipe = (recipe) => {
    addEndpoint(recipe.build());
    ensureToken().catch(() => {});
    navigate('/builder');
};
</script>

<template>
    <div class="docs" data-theme="light">
        <aside class="docs-side">
            <RouterLink to="/" class="docs-brand" aria-label="Stubbr home">
                <span class="docs-brand-mark" aria-hidden="true">{&gt;}</span>
                <span class="docs-brand-name">Docs</span>
            </RouterLink>
            <nav class="docs-nav" aria-label="Documentation">
                <template v-for="item in NAV" :key="item.id">
                    <a :href="`#${item.id}`" class="docs-nav-item" :class="{ active: isActive(item) && (!item.children || activeId === item.id) }">{{ item.label }}</a>
                    <a
                        v-for="child in item.children || []"
                        :key="child.id"
                        :href="`#${child.id}`"
                        class="docs-nav-item docs-nav-sub"
                        :class="{ active: activeId === child.id }"
                    >{{ child.label }}</a>
                </template>
            </nav>
            <RouterLink to="/builder" class="btn btn-primary btn-sm docs-open">Open builder</RouterLink>
        </aside>

        <main class="docs-main">
            <section id="quickstart" class="docs-section">
                <span class="tag tag-solid">GUIDE</span>
                <h2>Quickstart</h2>
                <p class="docs-lede">
                    Stubbr is a mock API you call like the real one. Every request carries your real payload plus a dev-only
                    <span class="inline-code">__instructions</span> object that tells Stubbr what to send back. When the backend
                    arrives you change the host; the payload stays.
                </p>
                <ol class="docs-steps">
                    <li>
                        <h4>Get a token</h4>
                        <p>One per email, no password. Asking again returns the same token.</p>
                        <CodeWell>
                            <template #header><span>TERMINAL</span><CopyButton class="docs-copy" :text="SNIPPETS.token" label="copy" mono /></template>
                            <pre v-html="highlightCode(SNIPPETS.token)"></pre>
                        </CodeWell>
                    </li>
                    <li>
                        <h4>Send a request</h4>
                        <p>Any path under <span class="inline-code">/api/</span> works. The body shape is yours to invent.</p>
                        <CodeWell>
                            <template #header><span>TERMINAL</span><CopyButton class="docs-copy" :text="SNIPPETS.first" label="copy" mono /></template>
                            <pre v-html="highlightCode(SNIPPETS.first)"></pre>
                        </CodeWell>
                    </li>
                    <li>
                        <h4>Swap the host when the backend lands</h4>
                        <p>Your real backend receives the payload and ignores <span class="inline-code">__instructions</span>. Nothing to rip out.</p>
                        <CodeWell>
                            <template #header><span>JAVASCRIPT</span><CopyButton class="docs-copy" :text="SNIPPETS.swap" label="copy" mono /></template>
                            <pre v-html="highlightCode(SNIPPETS.swap)"></pre>
                        </CodeWell>
                    </li>
                </ol>
            </section>

            <section id="tokens" class="docs-section">
                <span class="tag tag-solid">REFERENCE</span>
                <h2>Tokens</h2>
                <p class="docs-lede">Send the token with every request, either way works.</p>
                <div class="docs-grid">
                    <div class="docs-card"><div class="docs-card-name">Authorization <span>header</span></div><p><span class="inline-code">Authorization: Bearer YOUR_TOKEN</span></p></div>
                    <div class="docs-card"><div class="docs-card-name">X-API-Token <span>header</span></div><p><span class="inline-code">X-API-Token: YOUR_TOKEN</span></p></div>
                    <div class="docs-card"><div class="docs-card-name">GET /api/__token/request <span>?email=</span></div><p>Creates a token for that email. Returns 409 if one already exists.</p></div>
                    <div class="docs-card"><div class="docs-card-name">GET /api/__token/recover <span>?email=</span></div><p>Returns the existing token for that email.</p></div>
                </div>
                <p class="docs-note">Token endpoints accept one request every 10 seconds per IP. Tokens that sit idle for 30 days are deleted.</p>
            </section>

            <section id="instructions" class="docs-section">
                <span class="tag tag-solid">REFERENCE</span>
                <h2>Instructions</h2>
                <p class="docs-lede">
                    Send an <span class="inline-code">__instructions</span> object inside any request body. Stubbr reads it to
                    decide how the response behaves. Your real backend will simply ignore the key.
                </p>
                <p class="docs-note">
                    Any method works, GET included: Stubbr reads the body regardless. Browsers refuse to send a body with GET or HEAD,
                    which is why the builder on this site performs GET stubs as POST behind the scenes; the reply is identical.
                </p>
                <div class="docs-grid">
                    <div v-for="card in INSTRUCTION_CARDS" :id="card.id" :key="card.id" class="docs-card">
                        <div class="docs-card-name">{{ card.name }} <span>{{ card.type }}</span></div>
                        <p>{{ card.copy }}</p>
                    </div>
                </div>
                <CodeWell class="docs-example">
                    <template #header><span>EXAMPLE</span><CopyButton class="docs-copy" :text="SNIPPETS.instructions" label="copy" mono /></template>
                    <pre v-html="highlightCode(SNIPPETS.instructions)"></pre>
                </CodeWell>
            </section>

            <section id="body" class="docs-section">
                <span class="tag tag-solid">REFERENCE</span>
                <h2>Body &amp; __repeat</h2>
                <p class="docs-lede">
                    Put <span class="inline-code">__repeat</span> on any object inside <span class="inline-code">body</span> to turn it into an array of that shape.
                </p>
                <div class="docs-grid">
                    <div class="docs-card"><div class="docs-card-name">__repeat <span>int</span></div><p>Number of items, up to 20. The key is pluralised: <span class="inline-code">user</span> becomes <span class="inline-code">users</span>.</p></div>
                    <div class="docs-card"><div class="docs-card-name">__as <span>string</span></div><p>Use a different output key instead of the pluralised one.</p></div>
                    <div class="docs-card"><div class="docs-card-name">__uuid <span>bool</span></div><p>Makes <span class="inline-code">?id</span> and <span class="inline-code">?uuid</span> return time-ordered UUID7 values in this block and below.</p></div>
                    <div class="docs-card"><div class="docs-card-name">nesting <span>2 levels</span></div><p>Repeats inside repeats are fine, two deep. <span class="inline-code">?counter</span> keeps counting across the whole response.</p></div>
                </div>
                <CodeWell class="docs-example">
                    <template #header><span>EXAMPLE</span><CopyButton class="docs-copy" :text="SNIPPETS.repeat" label="copy" mono /></template>
                    <pre v-html="highlightCode(SNIPPETS.repeat)"></pre>
                </CodeWell>
            </section>

            <section id="generators" class="docs-section">
                <span class="tag tag-solid">REFERENCE</span>
                <h2>Generators</h2>
                <p class="docs-lede">Any string value starting with <span class="inline-code">?</span> is replaced with fake data. Unknown names are returned as they are.</p>
                <div class="docs-grid docs-grid-gen">
                    <div v-for="group in GENERATOR_GROUPS" :key="group.id" class="docs-card">
                        <div class="docs-card-name">{{ group.label }}</div>
                        <ul class="gen-list">
                            <li v-for="[name, meaning, example] in group.items" :key="name">
                                <code class="gen-name">{{ name }}</code>
                                <span class="gen-meaning">{{ meaning }}</span>
                                <code class="gen-example">{{ example }}</code>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="caching" class="docs-section">
                <span class="tag tag-solid">REFERENCE</span>
                <h2>Caching</h2>
                <p class="docs-lede">
                    Identical requests get identical replies. The cache key is your token, the method, the path, the query string and the body, so generated
                    names and IDs stay stable for a given request. Cached replies carry a <span class="inline-code">__from_cache: true</span> header.
                </p>
                <div class="docs-grid">
                    <div class="docs-card"><div class="docs-card-name">no_cache <span>per request</span></div><p>Set <span class="inline-code">"no_cache": true</span> in <span class="inline-code">__instructions</span> to skip the cache for that call.</p></div>
                    <div class="docs-card"><div class="docs-card-name">change anything <span>new key</span></div><p>A different body, query parameter or path is a different cache entry.</p></div>
                </div>
                <CodeWell class="docs-example">
                    <template #header><span>CLEAR EVERYTHING FOR YOUR TOKEN</span><CopyButton class="docs-copy" :text="SNIPPETS.clear" label="copy" mono /></template>
                    <pre v-html="highlightCode(SNIPPETS.clear)"></pre>
                </CodeWell>
            </section>

            <section id="limits" class="docs-section">
                <span class="tag tag-solid">REFERENCE</span>
                <h2>Limits &amp; errors</h2>
                <p class="docs-lede">Generous for a hobby project, tight enough to stay up. If you add a delay, the rate-limit window grows with it.</p>
                <div class="docs-stats">
                    <div v-for="[value, label] in LIMITS" :key="label" class="docs-stat">
                        <div class="docs-stat-value">{{ value }}</div>
                        <div class="docs-stat-label">{{ label }}</div>
                    </div>
                </div>
                <ul class="docs-errors">
                    <li v-for="[code, meaning] in ERRORS" :key="code">
                        <span class="status-badge" :data-tone="Number(code) >= 400 ? 'warn' : 'ok'">{{ code }}</span>
                        <span>{{ meaning }}</span>
                    </li>
                </ul>
            </section>

            <section id="recipes" class="docs-section">
                <span class="tag tag-solid">RECIPES</span>
                <h2>Start from a recipe</h2>
                <p class="docs-lede">Each one drops a working endpoint into your builder. Edit anything afterwards.</p>
                <div class="docs-recipes">
                    <RecipeCard v-for="recipe in RECIPES" :key="recipe.id" :recipe="recipe" @pick="pickRecipe" />
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped>
.docs {
    min-height: 100dvh;
    display: grid;
    grid-template-columns: 214px minmax(0, 1fr);
    background: var(--bg);
    color: var(--text);
}

.docs-side {
    position: sticky;
    top: 0;
    align-self: start;
    height: 100dvh;
    overflow: auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 22px 18px;
    background: var(--surface-2);
    border-right: 1px solid var(--border);
}

.docs-brand {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    border-radius: var(--r-sm);
}

.docs-brand-mark {
    width: 26px;
    height: 26px;
    border-radius: 8px;
    background: #191621;
    color: #ffe14d;
    display: grid;
    place-items: center;
    font: 700 11px var(--font-mono);
}

.docs-brand-name {
    font: 700 15px var(--font-display);
}

.docs-nav {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.docs-nav-item {
    display: block;
    padding: 8px 10px;
    border-radius: var(--r-sm);
    font: 500 13px var(--font-ui);
    color: var(--text-2);
    min-height: 36px;
}

.docs-nav-item:hover {
    background: var(--surface-3);
    color: var(--text);
}

.docs-nav-item.active {
    background: #191621;
    color: #ffe14d;
    font-weight: 700;
}

.docs-nav-sub {
    padding-left: 22px;
    color: var(--text-3);
    font-family: var(--font-mono);
    font-size: 12px;
}

.docs-open {
    margin-top: auto;
    align-self: flex-start;
}

.docs-main {
    padding: 36px 44px 80px;
    min-width: 0;
    max-width: 920px;
}

.docs-section {
    padding-bottom: 56px;
    scroll-margin-top: 24px;
}

.docs-section + .docs-section {
    padding-top: 40px;
    border-top: 1px solid var(--border);
}

.docs-section h2 {
    margin-top: 14px;
    font-size: clamp(30px, 3.5vw, 40px);
    line-height: 1.1;
}

.docs-lede {
    margin-top: 14px;
    max-width: 60ch;
    font-size: 16px;
    line-height: 1.65;
    color: var(--text-2);
}

.docs-note {
    margin-top: 16px;
    font-size: 13.5px;
    line-height: 1.55;
    color: var(--text-2);
}

.docs-steps {
    display: flex;
    flex-direction: column;
    gap: 22px;
    margin-top: 26px;
    counter-reset: step;
}

.docs-steps li {
    counter-increment: step;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.docs-steps h4 {
    font-size: 17px;
}

.docs-steps h4::before {
    content: counter(step);
    display: inline-grid;
    place-items: center;
    width: 24px;
    height: 24px;
    margin-right: 10px;
    border-radius: 50%;
    background: var(--accent);
    color: var(--on-accent);
    font: 700 12px var(--font-mono);
    vertical-align: 2px;
}

.docs-steps p {
    font-size: 14px;
    line-height: 1.55;
    color: var(--text-2);
}

.docs-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-top: 26px;
}

.docs-grid-gen {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.docs-card {
    padding: 18px;
    border-radius: var(--r-lg);
    background: var(--surface);
    border: 1px solid var(--border);
    scroll-margin-top: 24px;
    min-width: 0;
}

.docs-card-name {
    font: 600 13.5px var(--font-mono);
    color: var(--text);
    word-break: break-word;
}

.docs-card-name span {
    color: var(--text-3);
    font-weight: 500;
}

.docs-card p {
    margin-top: 8px;
    font: 400 13.5px/1.55 var(--font-ui);
    color: var(--text-2);
}

.docs-example {
    margin-top: 22px;
}

.docs-copy {
    margin-left: auto;
    color: #ffe14d;
    font-size: 10.5px;
    min-height: 24px;
    padding: 2px 8px;
}

.docs-copy:hover {
    background: #241f33;
    color: #ffe14d;
}

.docs :deep(.code-well-header) {
    padding: 12px 16px 0;
}

.gen-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 10px;
}

.gen-list li {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr);
    gap: 2px 10px;
    font-size: 12.5px;
    line-height: 1.45;
}

.gen-name {
    font: 600 12px var(--font-mono);
    color: var(--ok-text);
}

.gen-meaning {
    color: var(--text-2);
}

.gen-example {
    grid-column: 1 / -1;
    font: 400 11px var(--font-mono);
    color: var(--text-3);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.docs-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-top: 26px;
}

.docs-stat {
    padding: 16px 18px;
    border-radius: var(--r-lg);
    background: var(--surface);
    border: 1px solid var(--border);
}

.docs-stat-value {
    font: 700 24px var(--font-display);
    letter-spacing: -0.02em;
}

.docs-stat-label {
    margin-top: 4px;
    font: 500 12.5px var(--font-ui);
    color: var(--text-2);
}

.docs-errors {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 22px;
}

.docs-errors li {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: var(--text-2);
}

.docs-recipes {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
    margin-top: 26px;
}

@media (max-width: 960px) {
    .docs-grid,
    .docs-grid-gen,
    .docs-recipes {
        grid-template-columns: 1fr;
    }

    .docs-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 760px) {
    .docs {
        grid-template-columns: 1fr;
    }

    .docs-side {
        position: static;
        height: auto;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        border-right: 0;
        border-bottom: 1px solid var(--border);
    }

    .docs-nav {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 4px;
        width: 100%;
    }

    .docs-nav-sub {
        display: none;
    }

    .docs-open {
        margin-top: 0;
        margin-left: auto;
    }

    .docs-main {
        padding: 28px 18px 64px;
    }
}
</style>
