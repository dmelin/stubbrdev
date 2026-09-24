// Builder domain model: rows, instructions, payload assembly, snippets.

export const PLACEHOLDERS_BY_TYPE = {
    string: [
        '?name', '?firstName', '?lastName', '?email', '?username', '?phone',
        '?company', '?jobTitle', '?address', '?street', '?city', '?state', '?zip', '?country',
        '?word', '?sentence', '?paragraph', '?text', '?lorem', '?loremShort', '?loremLong',
        '?url', '?domain', '?ip', '?slug',
        '?date', '?dateTime', '?stupidDateTime', '?time',
        '?uuid', '?counterUuid',
        '?color', '?colorName', '?creditCard', '?image', '?avatar',
    ],
    number: ['?number', '?numberSmall', '?numberLarge', '?decimal', '?price', '?id', '?counter', '?timestamp'],
    boolean: ['?boolean'],
    object: [],
    array: [],
    null: [],
};

export const TYPE_OPTIONS = ['string', 'number', 'boolean', 'object', 'array', 'null'];
export const HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];
export const MAX_DEPTH = 2;
export const MAX_REPEAT = 20;
export const MAX_DELAY = 5000;

export const INSTRUCTIONS = [
    {
        key: 'status',
        type: 'number',
        defaultValue: '500',
        tone: 'warn',
        unit: 'code',
        hint: 'HTTP status to reply with. Try 429 or 503 to test retry logic.',
    },
    {
        key: 'delay',
        type: 'number',
        defaultValue: '2000',
        tone: 'info',
        unit: 'ms',
        hint: 'Wait this many milliseconds before replying, up to 5000.',
    },
    {
        key: 'max_pages',
        type: 'number',
        defaultValue: '3',
        tone: 'ok',
        unit: 'pages',
        hint: 'Adds pagination meta to the response. Page with ?page=N.',
    },
    {
        key: 'no_cache',
        type: 'boolean',
        defaultValue: true,
        tone: 'accent',
        unit: '',
        hint: 'Skip the response cache so every call gets fresh fake values.',
    },
    {
        key: 'flaky',
        type: 'flaky',
        defaultValue: () => ({ every: '', codes: '' }),
        tone: 'warn',
        unit: 'true | { every, codes }',
        hint: 'Fail some calls. Plain flaky is a coin flip with a 5xx/429 code; set every N and your own codes for a schedule. Never cached.',
    },
];

export const FLAKY_DEFAULT_CODES = [500, 502, 503, 504, 429];

const instructionDefault = (def) => (typeof def.defaultValue === 'function' ? def.defaultValue() : def.defaultValue);

const normalizeFlakyValue = (raw) => {
    if (raw === true || raw === null || raw === undefined || typeof raw !== 'object') {
        return { every: '', codes: '' };
    }
    return {
        every: raw.every === undefined || raw.every === null ? '' : String(raw.every),
        codes: Array.isArray(raw.codes) ? raw.codes.join(', ') : String(raw.codes ?? ''),
    };
};

export const parseFlakyCodes = (text) => String(text || '')
    .split(/[\s,]+/)
    .map((part) => Number(part))
    .filter((code) => Number.isInteger(code) && code >= 300 && code <= 599);

export const flakyLabel = (value) => {
    const every = Number(value?.every);
    return every >= 1 ? `every ${every}` : '50%';
};

export const uid = () => Math.random().toString(36).slice(2, 10);

export const placeholdersFor = (type) => PLACEHOLDERS_BY_TYPE[type] || [];
export const randomAllowed = (type) => placeholdersFor(type).length > 0;
export const defaultPlaceholder = (type) => placeholdersFor(type)[0] || '';

export const makeField = (overrides = {}) => ({
    id: uid(),
    kind: 'field',
    key: '',
    type: 'string',
    random: false,
    placeholder: '?name',
    value: '',
    ...overrides,
});

export const makeGroup = (overrides = {}) => ({
    id: uid(),
    kind: 'group',
    key: 'item',
    repeatEnabled: false,
    repeat: '3',
    rows: [],
    ...overrides,
});

export function setRowKind(row, kind) {
    if (row.kind === kind) return;
    row.kind = kind;
    if (kind === 'group') {
        delete row.type;
        delete row.random;
        delete row.placeholder;
        delete row.value;
        row.repeatEnabled = row.repeatEnabled ?? false;
        row.repeat = row.repeat ?? '3';
        row.rows = Array.isArray(row.rows) ? row.rows : [];
        return;
    }
    delete row.repeatEnabled;
    delete row.repeat;
    delete row.rows;
    row.type = row.type ?? 'string';
    row.random = row.random ?? false;
    row.placeholder = row.placeholder ?? defaultPlaceholder(row.type);
    row.value = row.value ?? '';
}

export function onRowTypeChange(row) {
    row.placeholder = defaultPlaceholder(row.type);
    if (!randomAllowed(row.type)) row.random = false;
    if (row.type === 'boolean' && row.value === '') row.value = 'true';
    if (row.type === 'null') row.value = '';
}

export const valuePlaceholderFor = (type) => {
    if (type === 'object') return '{ "x": 1 }';
    if (type === 'array') return '[1, 2]';
    if (type === 'number') return '42';
    return 'value';
};

export function makeInstructionState(overrides = {}) {
    const state = {};
    INSTRUCTIONS.forEach((def) => {
        const raw = overrides[def.key];
        let value = raw && raw.value !== undefined ? raw.value : instructionDefault(def);
        if (def.type === 'flaky') value = normalizeFlakyValue(value);
        state[def.key] = { enabled: Boolean(raw?.enabled), value };
    });
    return state;
}

export function makeEndpoint(overrides = {}) {
    return {
        id: uid(),
        name: '',
        method: 'POST',
        path: 'demo',
        payloadText: '',
        instructions: makeInstructionState(),
        rows: [],
        createdAt: Date.now(),
        ...overrides,
    };
}

export function normalizePath(raw) {
    let path = String(raw || '').trim().replace(/^\/+/, '');
    if (path.toLowerCase().startsWith('api/')) path = path.slice(4);
    return path;
}

export const endpointUrl = (endpoint) => `/api/${normalizePath(endpoint.path) || 'demo'}`;
export const endpointLabel = (endpoint) => `/${normalizePath(endpoint.path) || 'demo'}`;
export const methodSendsBody = (method) => !['GET', 'HEAD'].includes(String(method).toUpperCase());

export class BuilderError extends Error {
    constructor(scope, message) {
        super(message);
        this.name = 'BuilderError';
        this.scope = scope;
    }
}

function parseFieldValue(row, label) {
    if (row.random) return row.placeholder || defaultPlaceholder(row.type);
    if (row.type === 'string') return row.value;
    if (row.type === 'number') {
        const parsed = Number(row.value);
        if (row.value === '' || Number.isNaN(parsed)) {
            throw new BuilderError('rows', `${label} needs a valid number.`);
        }
        return parsed;
    }
    if (row.type === 'boolean') return row.value === 'true';
    if (row.type === 'null') return null;
    if (row.type === 'object' || row.type === 'array') {
        let parsed;
        try {
            parsed = JSON.parse(row.value || (row.type === 'object' ? '{}' : '[]'));
        } catch (_error) {
            throw new BuilderError('rows', `${label} must contain valid JSON.`);
        }
        if (row.type === 'object' && (typeof parsed !== 'object' || parsed === null || Array.isArray(parsed))) {
            throw new BuilderError('rows', `${label} must be a JSON object.`);
        }
        if (row.type === 'array' && !Array.isArray(parsed)) {
            throw new BuilderError('rows', `${label} must be a JSON array.`);
        }
        return parsed;
    }
    return row.value;
}

export function buildBody(rows, parentLabel = 'body', depth = 0) {
    const body = {};
    rows.forEach((row, index) => {
        const key = String(row.key || '').trim();
        if (!key) {
            throw new BuilderError('rows', `${parentLabel}: row ${index + 1} has no key.`);
        }
        const label = `${parentLabel}.${key}`;
        if (row.kind === 'group') {
            if (depth >= MAX_DEPTH) {
                throw new BuilderError('rows', `${label} is nested deeper than ${MAX_DEPTH} levels.`);
            }
            const group = buildBody(row.rows || [], label, depth + 1);
            if (row.repeatEnabled) {
                const repeat = Number(row.repeat);
                if (row.repeat === '' || Number.isNaN(repeat) || repeat < 0) {
                    throw new BuilderError('rows', `${label} repeat count must be 0 or more.`);
                }
                group.__repeat = repeat;
            }
            body[key] = group;
            return;
        }
        body[key] = parseFieldValue(row, label);
    });
    return body;
}

export function enabledInstructions(endpoint) {
    return INSTRUCTIONS.filter((def) => endpoint.instructions?.[def.key]?.enabled);
}

export function instructionValue(def, state) {
    if (def.type === 'boolean') return Boolean(state.value);
    if (def.type === 'flaky') {
        const every = Number(state.value?.every);
        const codes = parseFlakyCodes(state.value?.codes);
        if (state.value?.every !== '' && (!Number.isInteger(every) || every < 1)) {
            throw new BuilderError('instructions', '"flaky" every must be a whole number of 1 or more.');
        }
        if (!(every >= 1) && !codes.length) return true;
        const value = {};
        if (every >= 1) value.every = every;
        if (codes.length) value.codes = codes;
        return value;
    }
    const parsed = Number(state.value);
    if (state.value === '' || Number.isNaN(parsed)) {
        throw new BuilderError('instructions', `"${def.key}" must be a number.`);
    }
    return parsed;
}

export function buildPayload(endpoint) {
    let payloadFields = {};
    const source = String(endpoint.payloadText || '').trim();
    if (source) {
        // The editor takes the object's members; the well draws the braces.
        try {
            payloadFields = JSON.parse(`{${source}}`);
        } catch (_error) {
            throw new BuilderError('payload', 'Request payload is not valid JSON.');
        }
        if (typeof payloadFields !== 'object' || payloadFields === null || Array.isArray(payloadFields)) {
            throw new BuilderError('payload', 'Request payload must be a JSON object.');
        }
        if (Object.prototype.hasOwnProperty.call(payloadFields, '__instructions')) {
            throw new BuilderError('payload', 'Leave "__instructions" out of the payload. The builder adds it.');
        }
    }

    const instructions = {};
    if (endpoint.rows.length) {
        instructions.body = buildBody(endpoint.rows);
    }
    enabledInstructions(endpoint).forEach((def) => {
        instructions[def.key] = instructionValue(def, endpoint.instructions[def.key]);
    });

    return { ...payloadFields, __instructions: instructions };
}

export function safePayload(endpoint) {
    try {
        return { payload: buildPayload(endpoint), error: null };
    } catch (error) {
        return { payload: null, error };
    }
}

export function expectedDelay(endpoint) {
    const state = endpoint.instructions?.delay;
    if (!state?.enabled) return 0;
    return Math.min(Math.max(Number(state.value) || 0, 0), MAX_DELAY);
}

export const SNIPPET_LANGS = [
    { id: 'curl', label: 'cURL' },
    { id: 'js', label: 'JS' },
    { id: 'axios', label: 'Axios' },
    { id: 'php', label: 'PHP' },
    { id: 'python', label: 'Python' },
];

export function buildSnippets(payload, url, authToken, method) {
    const upperMethod = String(method).toUpperCase();
    const bodyPretty = JSON.stringify(payload, null, 2);
    const bodyCompact = JSON.stringify(payload);
    const bodyPhp = bodyPretty.replaceAll('\\', '\\\\').replaceAll("'", "\\'");
    const authHeader = `Bearer ${authToken}`;
    const authHeaderJs = JSON.stringify(authHeader);
    const authHeaderCurl = authHeader.replaceAll('\\', '\\\\').replaceAll('"', '\\"');
    const authHeaderPhp = authHeader.replaceAll('\\', '\\\\').replaceAll("'", "\\'");
    // Stubbr reads the body on every method, so every snippet sends it.
    // fetch() is the one client that refuses a GET body, so that snippet
    // carries a note instead of a body for GET/HEAD.
    const browserAllowsBody = methodSendsBody(upperMethod);
    const browserNote = browserAllowsBody
        ? ''
        : `// fetch() refuses to send a body with ${upperMethod}. Mock this call with POST in the\n// browser, or send it from a server; Stubbr reads the body on any method.\n`;
    const bodyLineJs = browserAllowsBody ? `,\n  body: JSON.stringify(${bodyPretty})` : '';
    const axiosDataArg = `,\n  data: ${bodyPretty}`;
    const axiosNote = '';
    const pythonMethod = upperMethod.toLowerCase();
    const pythonRequest = `response = requests.${pythonMethod}('${url}', json=payload, headers=headers)`;
    const phpBodyLine = `\ncurl_setopt($ch, CURLOPT_POSTFIELDS, '${bodyPhp}');`;

    return {
        curl: `curl -X ${upperMethod} ${url} \\
  -H "Authorization: ${authHeaderCurl}" \\
  -H "Content-Type: application/json" \\
  -d '${bodyCompact}'`,
        js: `${browserNote}const response = await fetch('${url}', {
  method: '${upperMethod}',
  headers: {
    'Authorization': ${authHeaderJs},
    'Content-Type': 'application/json'
  }${bodyLineJs}
});

const data = await response.json();`,
        axios: `${axiosNote}import axios from 'axios';

const response = await axios({
  method: '${pythonMethod}',
  url: '${url}',
  headers: {
    Authorization: ${authHeaderJs},
    'Content-Type': 'application/json'
  }${axiosDataArg}
});

const data = response.data;`,
        php: `$ch = curl_init('${url}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, '${upperMethod}');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ${authHeaderPhp}',
    'Content-Type: application/json'
]);${phpBodyLine}

$result = curl_exec($ch);
curl_close($ch);`,
        python: `import requests

payload = ${bodyPretty}
headers = {
    'Authorization': ${authHeaderJs},
    'Content-Type': 'application/json'
}

${pythonRequest}
data = response.json()`,
    };
}

export const escapeHtml = (value) => String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;');

export function highlightCode(text) {
    const tokenRegex = /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|`(?:\\.|[^`\\])*`|#.*$|\/\/.*$|\b(?:const|let|var|import|from|await|async|try|catch|return|new|true|false|null|def|requests|response|json|headers|payload|curl_setopt|curl_exec|curl_init|curl)\b|-?\d+(?:\.\d+)?|[{}[\]().,:;])/gm;
    let result = '';
    let lastIndex = 0;
    let match;
    while ((match = tokenRegex.exec(text)) !== null) {
        result += escapeHtml(text.slice(lastIndex, match.index));
        const token = match[0];
        let cls = 'tok-plain';
        if (token.startsWith('"') || token.startsWith("'") || token.startsWith('`')) cls = 'tok-string';
        else if (token.startsWith('//') || token.startsWith('#')) cls = 'tok-comment';
        else if (/^-?\d/.test(token)) cls = 'tok-number';
        else if (/^[{}[\]().,:;]$/.test(token)) cls = 'tok-punct';
        else cls = 'tok-keyword';
        result += `<span class="${cls}">${escapeHtml(token)}</span>`;
        lastIndex = tokenRegex.lastIndex;
    }
    result += escapeHtml(text.slice(lastIndex));
    return result;
}

export function statusTone(status) {
    const code = Number(status);
    if (!code) return 'neutral';
    if (code < 300) return 'ok';
    if (code < 400) return 'info';
    return 'warn';
}

export function methodTone(method) {
    switch (String(method).toUpperCase()) {
        case 'GET':
            return 'ok';
        case 'PUT':
        case 'PATCH':
        case 'DELETE':
            return 'warn';
        default:
            return 'info';
    }
}

export const formatBytes = (bytes) => (bytes < 1024 ? `${bytes} B` : `${(bytes / 1024).toFixed(1)} kB`);
export const formatMs = (ms) => (ms >= 1000 ? `${(ms / 1000).toFixed(1)} s` : `${ms} ms`);

// ---- Share links -----------------------------------------------------------

const toBase64Url = (text) => btoa(unescape(encodeURIComponent(text)))
    .replaceAll('+', '-').replaceAll('/', '_').replace(/=+$/, '');
const fromBase64Url = (text) => decodeURIComponent(escape(atob(text.replaceAll('-', '+').replaceAll('_', '/'))));

const stripIds = (rows) => rows.map((row) => {
    const { id, ...rest } = row;
    if (rest.kind === 'group') rest.rows = stripIds(rest.rows || []);
    return rest;
});

export function encodeShare(endpoint) {
    const compact = {
        v: 1,
        n: endpoint.name || '',
        m: endpoint.method,
        p: endpoint.path,
        t: endpoint.payloadText || '',
        i: Object.fromEntries(
            enabledInstructions(endpoint).map((def) => [def.key, endpoint.instructions[def.key].value]),
        ),
        r: stripIds(endpoint.rows || []),
    };
    return toBase64Url(JSON.stringify(compact));
}

export function decodeShare(encoded) {
    try {
        const data = JSON.parse(fromBase64Url(encoded));
        if (!data || data.v !== 1) return null;
        const instructions = {};
        Object.entries(data.i || {}).forEach(([key, value]) => {
            instructions[key] = { enabled: true, value };
        });
        return makeEndpoint({
            name: data.n || '',
            method: HTTP_METHODS.includes(data.m) ? data.m : 'POST',
            path: String(data.p || 'demo'),
            payloadText: String(data.t || ''),
            instructions: makeInstructionState(instructions),
            rows: sanitizeRows(data.r || []),
        });
    } catch (_error) {
        return null;
    }
}

// ---- Sanitizing persisted state -------------------------------------------

export function sanitizeRows(rows, depth = 0) {
    if (!Array.isArray(rows)) return [];
    return rows.map((raw) => {
        if (raw?.kind === 'group' && depth < MAX_DEPTH) {
            return makeGroup({
                id: uid(),
                key: String(raw.key ?? ''),
                repeatEnabled: Boolean(raw.repeatEnabled),
                repeat: String(raw.repeat ?? '3'),
                rows: sanitizeRows(raw.rows, depth + 1),
            });
        }
        const type = TYPE_OPTIONS.includes(raw?.type) ? raw.type : 'string';
        return makeField({
            id: uid(),
            key: String(raw?.key ?? ''),
            type,
            random: Boolean(raw?.random) && randomAllowed(type),
            placeholder: typeof raw?.placeholder === 'string' && raw.placeholder ? raw.placeholder : defaultPlaceholder(type),
            value: raw?.value === undefined || raw?.value === null ? '' : String(raw.value),
        });
    });
}

export function sanitizeEndpoint(raw) {
    const base = makeEndpoint();
    return {
        ...base,
        id: typeof raw?.id === 'string' && raw.id ? raw.id : base.id,
        name: String(raw?.name ?? ''),
        method: HTTP_METHODS.includes(raw?.method) ? raw.method : 'POST',
        path: String(raw?.path ?? 'demo'),
        payloadText: String(raw?.payloadText ?? ''),
        instructions: makeInstructionState(raw?.instructions || {}),
        rows: sanitizeRows(raw?.rows),
        createdAt: Number(raw?.createdAt) || Date.now(),
    };
}

// ---- Recipes ----------------------------------------------------------------

const instr = (enabled) => makeInstructionState(
    Object.fromEntries(Object.entries(enabled).map(([key, value]) => [key, { enabled: true, value }])),
);

export const RECIPES = [
    {
        id: 'paged-list',
        method: 'POST',
        path: 'users',
        title: 'Paged list',
        description: 'Ten fake users per page across three pages, pagination meta included.',
        build: () => makeEndpoint({
            name: 'Paged list',
            method: 'POST',
            path: 'users',
            payloadText: '"filters": { "active": true }',
            instructions: instr({ max_pages: '3' }),
            rows: [
                makeGroup({
                    key: 'user',
                    repeatEnabled: true,
                    repeat: '10',
                    rows: [
                        makeField({ key: 'id', type: 'number', random: true, placeholder: '?counter' }),
                        makeField({ key: 'name', random: true, placeholder: '?name' }),
                        makeField({ key: 'email', random: true, placeholder: '?email' }),
                        makeField({ key: 'avatar', random: true, placeholder: '?avatar' }),
                    ],
                }),
            ],
        }),
    },
    {
        id: 'flaky-payment',
        method: 'POST',
        path: 'checkout',
        title: 'Flaky payment',
        description: 'Every third call fails with a 502, and all of them take two seconds.',
        build: () => makeEndpoint({
            name: 'Flaky payment',
            method: 'POST',
            path: 'checkout',
            payloadText: '"cart_id": "c_1042",\n"card": "tok_visa"',
            instructions: instr({ delay: '2000', flaky: { every: '3', codes: '502' } }),
            rows: [
                makeField({ key: 'order_id', random: true, placeholder: '?uuid' }),
                makeField({ key: 'status', value: 'paid' }),
                makeField({ key: 'total', type: 'number', random: true, placeholder: '?price' }),
            ],
        }),
    },
    {
        id: 'mirror',
        method: 'POST',
        path: 'echo',
        title: 'Mirror',
        description: 'No body template, so Stubbr returns exactly what you post.',
        build: () => makeEndpoint({
            name: 'Mirror',
            method: 'POST',
            path: 'echo',
            payloadText: '"hello": "world",\n"nested": { "ok": true }',
            instructions: instr({}),
            rows: [],
        }),
    },
];

// The landing hero starts with this stub.
export const heroEndpoint = () => makeEndpoint({
    name: 'demo',
    method: 'PATCH',
    path: 'demo',
    instructions: instr({ status: '403', delay: '2000' }),
    rows: [
        makeGroup({
            key: 'basic_id',
            repeatEnabled: true,
            repeat: '12',
            rows: [makeField({ key: 'id', random: true, placeholder: '?uuid' })],
        }),
    ],
});
