<script setup>
import { computed, nextTick, onMounted, onUnmounted, reactive, ref, watch } from 'vue';

const token = ref('');
const tokenLoading = ref(true);
const endpointPath = ref('demo');
const requestMethod = ref('POST');
const requestFieldsRef = ref(null);
const isSending = ref(false);
const statusLine = ref('Initializing token...');
const responseText = ref('');
const responseOpacity = ref(1);
const autoTokenEmail = 'somerandomstring@stubbr.dev';
const ownTokenEmail = ref('');
const ownTokenLoading = ref(false);
const ownTokenError = ref('');
const ownTokenVisible = ref(false);
const responseFadeMs = 300;
let responseFadeRun = 0;
const topbarLogoSrc = '/media/stubbr-logo-white.png';

const filtersText = ref('');
const filtersError = ref('');
const builderError = ref('');
const draggingRowId = ref(null);
const dragOverRowId = ref(null);
const dragSource = reactive({
    rows: null,
    index: -1,
    rowId: null,
});
const builderHistory = ref([]);
const builderHistoryIndex = ref(-1);
const isApplyingBuilderHistory = ref(false);
const canStepBack = computed(() => builderHistoryIndex.value > 0);
const canStepForward = computed(() => builderHistoryIndex.value >= 0 && builderHistoryIndex.value < builderHistory.value.length - 1);
const BUILDER_HISTORY_MAX = 30;
const BUILDER_HISTORY_STORAGE_KEY = 'stubbr_builder_history_v1';
let builderHistoryTimer = null;

const placeholderOptionsByType = {
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
const typeOptions = ['string', 'number', 'boolean', 'object', 'array', 'null'];
const booleanOptions = ['true', 'false'];
const rowKindOptions = ['field', 'group'];

const selectModalOpen = ref(false);
const selectModalTitle = ref('');
const selectModalOptions = ref([]);
const selectModalOnPick = ref(null);
const selectPopoverStyle = ref({});
const codeExampleLanguage = ref('curl');
const codeCopied = ref(false);
const heroModalOpen = ref(true);
const HERO_DISMISSED_STORAGE_KEY = 'stubbr_hero_dismissed_v1';
const THEME_STORAGE_KEY = 'stubbr_theme_v1';
const themeMenuOpen = ref(false);
const activeThemeId = ref('stubbr');
const themeOptions = [
    { id: 'stubbr', label: 'Stubbr' },
    { id: 'ice', label: 'Ice' },
    { id: 'mustard', label: 'Mustard' },
    { id: 'techno', label: 'Techno' },
    { id: 'retro', label: 'Retro' },
];
const isPhoneView = ref(false);
const sidebarOpen = ref(false);
const sidebarWidth = ref(450);
const isResizingSidebar = ref(false);
const contentRef = ref(null);
const builderWidthPercent = ref(50);
const isResizingPanels = ref(false);
const PANEL_WIDTH_STORAGE_KEY = 'stubbr_panel_builder_width_percent_v1';
const activeSidebarSectionId = ref('how-it-works');
const readmePanelRef = ref(null);
const sidebarSectionRefs = new Map();
const sidebarOpenWidth = 450;
const sidebarOpenTransitionMs = 190;
const readmeScrollOffset = 64;
const readmeSnapDebounceMs = 180;
const readmeSnapDurationMs = 420;
let readmeSnapTimer = null;
let readmeAutoScrollUnlockTimer = null;
let isReadmeAutoScrolling = false;
const phoneViewMediaQuery = '(max-width: 900px)';

const randomizerTableTabs = [
    {
        id: 'personal',
        label: 'Personal',
        rows: [
            { placeholder: '?name', description: 'Full name', example: 'Jane Smith' },
            { placeholder: '?firstName', description: 'First name', example: 'John' },
            { placeholder: '?lastName', description: 'Last name', example: 'Doe' },
            { placeholder: '?email', description: 'Email address', example: 'john@example.com' },
            { placeholder: '?username', description: 'Username', example: 'john_doe_92' },
            { placeholder: '?phone', description: 'Phone number', example: '+1-555-123-4567' },
            { placeholder: '?company', description: 'Company name', example: 'Acme Corp' },
            { placeholder: '?jobTitle', description: 'Job title', example: 'Software Engineer' },
        ],
    },
    {
        id: 'address',
        label: 'Address',
        rows: [
            { placeholder: '?address', description: 'Full address', example: '742 Evergreen Terrace, Springfield' },
            { placeholder: '?street', description: 'Street address', example: '123 Main Street' },
            { placeholder: '?city', description: 'City', example: 'New York' },
            { placeholder: '?state', description: 'State', example: 'California' },
            { placeholder: '?zip', description: 'Postal code', example: '90210' },
            { placeholder: '?country', description: 'Country', example: 'United States' },
        ],
    },
    {
        id: 'numbers',
        label: 'Numbers',
        rows: [
            { placeholder: '?number', description: 'Number 1-10000', example: '4721' },
            { placeholder: '?numberSmall', description: 'Number 1-10', example: '7' },
            { placeholder: '?numberLarge', description: 'Number 10000-1000000', example: '842531' },
            { placeholder: '?decimal', description: 'Decimal number', example: '342.87' },
            { placeholder: '?price', description: 'Price value', example: '49.99' },
            { placeholder: '?id', description: 'ID number', example: '12345' },
            { placeholder: '?counter', description: 'Incrementing number', example: '0, 1, 2...' },
        ],
    },
    {
        id: 'text',
        label: 'Text',
        rows: [
            { placeholder: '?word', description: 'Single word', example: 'example' },
            { placeholder: '?sentence', description: 'One sentence', example: 'This is a sample sentence.' },
            { placeholder: '?paragraph', description: 'One paragraph', example: 'Lorem ipsum dolor sit amet...' },
            { placeholder: '?text', description: 'About 200 characters', example: 'Lorem ipsum dolor...' },
            { placeholder: '?lorem', description: 'Lorem sentence', example: 'Lorem ipsum dolor sit amet.' },
            { placeholder: '?loremShort', description: '3 words', example: 'lorem ipsum dolor' },
            { placeholder: '?loremLong', description: 'Multiple paragraphs', example: 'Lorem ipsum...' },
        ],
    },
    {
        id: 'internet',
        label: 'Internet',
        rows: [
            { placeholder: '?url', description: 'URL', example: 'https://example.com/path' },
            { placeholder: '?domain', description: 'Domain', example: 'example.com' },
            { placeholder: '?ip', description: 'IP address', example: '192.168.1.1' },
            { placeholder: '?slug', description: 'URL slug', example: 'sample-slug-text' },
        ],
    },
    {
        id: 'date-time',
        label: 'Date & Time',
        rows: [
            { placeholder: '?date', description: 'Date (ISO)', example: '2024-03-15' },
            { placeholder: '?dateTime', description: 'DateTime', example: '2024-03-15 14:30:00' },
            { placeholder: '?stupidDateTime', description: 'US date format', example: '03/15/2024 14:30:00' },
            { placeholder: '?time', description: 'Time', example: '14:30:00' },
            { placeholder: '?timestamp', description: 'Unix timestamp', example: '1710514200' },
        ],
    },
    {
        id: 'other',
        label: 'Other',
        rows: [
            { placeholder: '?boolean', description: 'Boolean value', example: 'true' },
            { placeholder: '?uuid', description: 'Random UUID', example: 'a3bb189e-8bf9-3888-9912-ace4e6543002' },
            { placeholder: '?counterUuid', description: 'Incrementing UUID', example: '00000000-0000-0000-0000-000000000001' },
            { placeholder: '?color', description: 'Hex color', example: '#3498db' },
            { placeholder: '?colorName', description: 'Color name', example: 'Blue' },
            { placeholder: '?creditCard', description: 'Card number', example: '4532-1234-5678-9010' },
            { placeholder: '?image', description: 'Image URL', example: 'https://via.placeholder.com/640x480' },
            { placeholder: '?avatar', description: 'Avatar URL', example: 'https://via.placeholder.com/200x200' },
        ],
    },
];
const readmeTabBySection = reactive({
    randomizers: randomizerTableTabs[0].id,
});

const readmeSections = [
    {
        id: 'how-it-works',
        title: 'How It Works',
        icon: 'workflow',
        paragraphs: [
            'Each request has production payload fields plus a dev-only "__instructions" object.',
            'When you move to production, only the API host changes and your payload stays the same.',
        ],
        bullets: [
            'Payload: real backend data (filters, page, etc.)',
            '__instructions: mock response behavior for development',
            'Real backend ignores "__instructions"',
        ],
        examples: [
            {
                label: 'Example request',
                code: `POST /api/users
{
  "filters": { "active": true },
  "page": 1,
  "__instructions": {
    "body": {
      "user": {
        "__repeat": 2,
        "id": "?counter",
        "name": "?name"
      }
    }
  }
}`,
            },
        ],
    },
    {
        id: 'quick-start',
        title: 'Quick Start',
        icon: 'play',
        paragraphs: [
            'Start by requesting a token, then send a POST request with Authorization and a body containing "__instructions".',
        ],
        bullets: [
            'GET /api/__token/request?email=you@example.com',
            'POST /api/users with Bearer token',
            'Use "__instructions.body" to define response shape',
        ],
        examples: [
            {
                label: 'Get token',
                code: 'curl "https://stubbr.dev/api/__token/request?email=you@example.com"',
            },
            {
                label: 'First API call',
                code: `curl -X POST https://stubbr.dev/api/users \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Content-Type: application/json" \\
  -d '{"__instructions":{"body":{"user":{"__repeat":3,"id":"?counter","name":"?name"}}}}'`,
            },
        ],
    },
    {
        id: 'authentication',
        title: 'Authentication',
        icon: 'key',
        paragraphs: [
            'Include your token on every API request using either Bearer auth or X-API-Token.',
            'One token per email; inactive tokens are removed after 30 days.',
        ],
        bullets: [
            'Authorization: Bearer YOUR_TOKEN',
            'or X-API-Token: YOUR_TOKEN',
            'Token endpoints: /api/__token/request and /api/__token/recover',
            'Token endpoint rate limit: 1 request every 10 seconds per IP',
        ],
    },
    {
        id: 'instructions',
        title: '__instructions',
        icon: 'sliders',
        paragraphs: [
            'Use "__instructions" to control response body and behavior.',
        ],
        bullets: [
            'body: response payload with placeholders and __repeat',
            'status: custom HTTP status (default 200)',
            'delay: response delay in ms (max 5000)',
            'headers: custom response headers',
            'max_pages: enable pagination metadata',
            'no_cache: bypass cache for this request',
        ],
        examples: [
            {
                label: '__instructions sample',
                code: `{
  "__instructions": {
    "status": 201,
    "delay": 500,
    "headers": { "X-Request-Id": "abc-123" },
    "body": { "success": true }
  }
}`,
            },
        ],
    },
    {
        id: 'repeat',
        title: '__repeat Arrays',
        icon: 'repeat',
        paragraphs: [
            'Add "__repeat" in a body block to generate arrays from object templates.',
        ],
        bullets: [
            'Output key auto-pluralizes (user -> users)',
            'Use "__as" to override output key name',
            'Max 20 items per array',
            'Max nesting depth: 2',
            '?counter increments globally across the whole response',
        ],
        examples: [
            {
                label: '__repeat',
                code: `{
  "__instructions": {
    "body": {
      "user": {
        "__repeat": 5,
        "id": "?counter",
        "name": "?name"
      }
    }
  }
}`,
            },
        ],
    },
    {
        id: 'randomizers',
        title: 'Randomizer Options',
        icon: 'dice',
        paragraphs: [
            'Pick any placeholder below and use it as a value in your builder (for example: "name": "?name").',
            'This list mirrors the exact placeholder choices defined in the builder.',
        ],
        bullets: [
            'Click a tab to filter by category.',
            'Use placeholders as string values (for example: "total": "?price").',
        ],
        tableTabs: randomizerTableTabs,
    },
    {
        id: 'caching',
        title: 'Response Caching',
        icon: 'database',
        paragraphs: [
            'Identical requests return cached responses so generated fake data stays stable.',
        ],
        bullets: [
            'Cache key includes token, method, path, query, and request body',
            'Bypass with "__instructions.no_cache": true',
            'Or change request body/query for a new cache key',
            'Clear cache endpoint: POST /api/__cache/clear',
            'Cached responses include "__from_cache: true" header',
        ],
        examples: [
            {
                label: 'Clear cache',
                code: `curl -X POST https://stubbr.dev/api/__cache/clear \\
  -H "Authorization: Bearer YOUR_TOKEN"`,
            },
        ],
    },
    {
        id: 'rate-limits',
        title: 'Rate Limits',
        icon: 'gauge',
        paragraphs: [
            'Rate limiting protects both token and API endpoints.',
        ],
        bullets: [
            'API requests: 10 requests per second per token',
            'Token requests: 1 request per 10 seconds per IP',
            'When delay is used, throttle window adapts to delay',
        ],
    },
    {
        id: 'limits',
        title: 'Limits',
        icon: 'shield',
        paragraphs: [
            'Hard constraints from the API readme:',
        ],
        bullets: [
            'Max request body: 100 KB',
            'Max delay: 5000 ms',
            'Max items per __repeat: 20',
            'Max nesting depth: 2 levels',
            'Token inactivity timeout: 30 days',
        ],
    },
    {
        id: 'errors',
        title: 'Error Responses',
        icon: 'alert',
        paragraphs: [
            'Common error status codes returned by the API.',
        ],
        bullets: [
            '400: Invalid JSON',
            '401: Missing or invalid API token',
            '403: Token not verified',
            '413: Request body too large',
            '429: Rate limit exceeded',
        ],
    },
    {
        id: 'technical',
        title: 'Technical Details',
        icon: 'wrench',
        paragraphs: [
            'Implementation stack and platform details from the README.',
        ],
        bullets: [
            'Framework: Laravel 12',
            'Fake data: FakerPHP',
            'UUID: Ramsey UUID (UUID7)',
            'Rate limiting: adaptive throttling',
            'Email privacy: HMAC-SHA256 hashing',
            'Deployment: Google Cloud Run',
        ],
    },
    {
        id: 'roadmap',
        title: 'About',
        icon: 'info',
        paragraphs: [
            'About this service.',
            'Content coming soon.',
        ],
        bullets: [],
    },
];

const codeExampleLanguages = [
    { id: 'curl', label: 'cUrl' },
    { id: 'js', label: 'JS' },
    { id: 'axios', label: 'Axios' },
    { id: 'php', label: 'PHP' },
    { id: 'python', label: 'Python' },
];
const httpMethodOptions = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];

const shellGridTemplate = computed(() => (sidebarOpen.value ? `${sidebarWidth.value}px minmax(0, 1fr)` : undefined));
const endpointRoute = computed(() => {
    let path = endpointPath.value.trim();
    if (!path) return '/api/demo';
    path = path.replace(/^\/+/, '');
    if (path.toLowerCase().startsWith('api/')) {
        path = path.slice(4);
    }
    return `/api/${path}`;
});

const nextRowId = ref(1);

const getPlaceholderOptions = (type) => placeholderOptionsByType[type] || [];
const defaultPlaceholderForType = (type) => getPlaceholderOptions(type)[0] || '';
const randomAllowed = (type) => getPlaceholderOptions(type).length > 0;

const makeFieldRow = (overrides = {}) => ({
    id: nextRowId.value++,
    kind: 'field',
    key: '',
    type: 'string',
    random: false,
    placeholder: '?name',
    value: '',
    ...overrides,
});

const makeGroupRow = (overrides = {}) => ({
    id: nextRowId.value++,
    kind: 'group',
    key: 'order',
    repeatEnabled: false,
    repeat: '1',
    rows: [makeFieldRow()],
    ...overrides,
});

const applyRowKind = (row, kind) => {
    row.kind = kind;
    if (kind === 'group') {
        delete row.type;
        delete row.random;
        delete row.placeholder;
        delete row.value;
        row.repeatEnabled = row.repeatEnabled ?? false;
        row.repeat = row.repeat ?? '1';
        row.rows = Array.isArray(row.rows) && row.rows.length ? row.rows : [makeFieldRow()];
        return;
    }

    delete row.repeatEnabled;
    delete row.repeat;
    delete row.rows;
    row.type = row.type ?? 'string';
    row.random = row.random ?? false;
    row.placeholder = row.placeholder ?? defaultPlaceholderForType(row.type);
    row.value = row.value ?? '';
};

const bodyRows = ref([
    makeFieldRow({ key: 'id', type: 'number', random: true, placeholder: '?number' }),
    makeFieldRow({ key: 'name', type: 'string', random: true, placeholder: '?name' }),
    makeFieldRow({ key: 'email', type: 'string', random: true, placeholder: '?email' }),
]);

const instructions = reactive({
    status: { enabled: false, value: '200' },
    delay: { enabled: false, value: '500' },
    max_pages: { enabled: false, value: '3' },
    no_cache: { enabled: false, value: true },
});

const extraInstructionCount = computed(() =>
    ['status', 'delay', 'max_pages', 'no_cache'].filter((key) => instructions[key].enabled).length,
);

const snippetToken = computed(() => token.value.trim() || 'YOUR_API_TOKEN');

const requestTokenByEmail = async (email) => {
    let response = await fetch(`/api/__token/request?email=${encodeURIComponent(email)}`);
    let data;
    try {
        data = await response.json();
    } catch (_error) {
        data = {};
    }

    if (response.status === 409) {
        response = await fetch(`/api/__token/recover?email=${encodeURIComponent(email)}`);
        try {
            data = await response.json();
        } catch (_error) {
            data = {};
        }
    }

    if (!data?.token) {
        const message = data?.message || data?.error || 'Failed to fetch token for that email.';
        throw new Error(message);
    }

    return data.token;
};

const bootstrapToken = async (forceRefresh = false) => {
    const cachedToken = localStorage.getItem('stubbr_token');
    if (!forceRefresh && cachedToken) {
        token.value = cachedToken;
        ownTokenVisible.value = true;
        tokenLoading.value = false;
        statusLine.value = 'Token ready. No request sent yet.';
        return;
    }

    try {
        const bootstrappedToken = await requestTokenByEmail(autoTokenEmail);
        token.value = bootstrappedToken;
        localStorage.setItem('stubbr_token', bootstrappedToken);
        statusLine.value = 'Token ready. No request sent yet.';
    } catch (error) {
        statusLine.value = 'Failed to initialize token';
        responseText.value = String(error);
    } finally {
        tokenLoading.value = false;
    }
};

const getOwnToken = async () => {
    const email = ownTokenEmail.value.trim();
    ownTokenError.value = '';

    if (!email) {
        ownTokenError.value = 'Email is required.';
        return;
    }

    ownTokenLoading.value = true;
    try {
        const userToken = await requestTokenByEmail(email);
        token.value = userToken;
        localStorage.setItem('stubbr_token', userToken);
        ownTokenVisible.value = true;
        statusLine.value = 'Token ready. No request sent yet.';
    } catch (error) {
        ownTokenError.value = String(error?.message || error);
    } finally {
        ownTokenLoading.value = false;
    }
};

const addRow = (targetRows = bodyRows.value) => {
    targetRows.push(makeFieldRow());
};

const removeBodyRow = (rowId) => {
    bodyRows.value = bodyRows.value.filter((row) => row.id !== rowId);
};

const addChildRow = (groupRow) => {
    groupRow.rows.push(makeFieldRow());
};

const removeGroupField = (groupRow, fieldId) => {
    groupRow.rows = groupRow.rows.filter((row) => row.id !== fieldId);
};

const cloneValue = (value) => JSON.parse(JSON.stringify(value));

const createBuilderStateSnapshot = () => ({
    requestMethod: requestMethod.value,
    endpointPath: endpointPath.value,
    filtersText: filtersText.value,
    instructions: cloneValue(instructions),
    bodyRows: cloneValue(bodyRows.value),
});

const maxRowIdInRows = (rows) => rows.reduce((maxId, row) => {
    const own = Math.max(maxId, Number(row.id) || 0);
    if (row.kind === 'group' && Array.isArray(row.rows)) {
        return Math.max(own, maxRowIdInRows(row.rows));
    }
    return own;
}, 0);

const clearBuilderHistoryTimer = () => {
    if (!builderHistoryTimer) return;
    window.clearTimeout(builderHistoryTimer);
    builderHistoryTimer = null;
};

const persistBuilderHistory = () => {
    try {
        localStorage.setItem(BUILDER_HISTORY_STORAGE_KEY, JSON.stringify(builderHistory.value.slice(-BUILDER_HISTORY_MAX)));
    } catch (_error) {
        // Ignore storage quota / private mode failures.
    }
};

const commitBuilderHistorySnapshot = () => {
    if (isApplyingBuilderHistory.value) return;

    const snapshot = createBuilderStateSnapshot();
    const snapshotJson = JSON.stringify(snapshot);
    const current = builderHistory.value[builderHistoryIndex.value];
    if (current && JSON.stringify(current) === snapshotJson) {
        return;
    }

    if (builderHistoryIndex.value < builderHistory.value.length - 1) {
        builderHistory.value.splice(builderHistoryIndex.value + 1);
    }

    builderHistory.value.push(snapshot);
    if (builderHistory.value.length > BUILDER_HISTORY_MAX) {
        builderHistory.value = builderHistory.value.slice(-BUILDER_HISTORY_MAX);
    }
    builderHistoryIndex.value = builderHistory.value.length - 1;
    persistBuilderHistory();
};

const scheduleBuilderHistorySnapshot = () => {
    clearBuilderHistoryTimer();
    builderHistoryTimer = window.setTimeout(() => {
        commitBuilderHistorySnapshot();
    }, 1000);
};

const applyBuilderHistoryState = (targetIndex) => {
    const snapshot = builderHistory.value[targetIndex];
    if (!snapshot) return;

    isApplyingBuilderHistory.value = true;
    clearBuilderHistoryTimer();
    requestMethod.value = snapshot.requestMethod;
    endpointPath.value = snapshot.endpointPath;
    filtersText.value = snapshot.filtersText;
    bodyRows.value = cloneValue(snapshot.bodyRows);
    instructions.status.enabled = snapshot.instructions.status.enabled;
    instructions.status.value = snapshot.instructions.status.value;
    instructions.delay.enabled = snapshot.instructions.delay.enabled;
    instructions.delay.value = snapshot.instructions.delay.value;
    instructions.max_pages.enabled = snapshot.instructions.max_pages.enabled;
    instructions.max_pages.value = snapshot.instructions.max_pages.value;
    instructions.no_cache.enabled = snapshot.instructions.no_cache.enabled;
    instructions.no_cache.value = snapshot.instructions.no_cache.value;
    nextRowId.value = maxRowIdInRows(bodyRows.value) + 1;
    builderHistoryIndex.value = targetIndex;

    nextTick(() => {
        autoGrowRequestFields();
        isApplyingBuilderHistory.value = false;
    });
};

const stepBuilderBack = () => {
    if (!canStepBack.value) return;
    applyBuilderHistoryState(builderHistoryIndex.value - 1);
};

const stepBuilderForward = () => {
    if (!canStepForward.value) return;
    applyBuilderHistoryState(builderHistoryIndex.value + 1);
};

const loadBuilderHistoryFromStorage = () => {
    try {
        const raw = localStorage.getItem(BUILDER_HISTORY_STORAGE_KEY);
        if (!raw) return false;
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed) || !parsed.length) return false;
        const history = parsed
            .filter((entry) => entry && typeof entry === 'object' && Array.isArray(entry.bodyRows))
            .slice(-BUILDER_HISTORY_MAX);
        if (!history.length) return false;
        builderHistory.value = history;
        applyBuilderHistoryState(history.length - 1);
        return true;
    } catch (_error) {
        return false;
    }
};

const onRowDragStart = (rowId, rows, index, event) => {
    dragSource.rows = rows;
    dragSource.index = index;
    dragSource.rowId = rowId;
    draggingRowId.value = rowId;
    dragOverRowId.value = null;
    if (event?.dataTransfer) {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', String(rowId));
    }
};

const onRowDragOver = (rowId, rows, _index, event) => {
    if (dragSource.rows !== rows || dragSource.rowId === rowId) {
        return;
    }
    dragOverRowId.value = rowId;
    if (event?.dataTransfer) {
        event.dataTransfer.dropEffect = 'move';
    }
};

const onRowDrop = (rows, targetIndex) => {
    if (dragSource.rows !== rows || dragSource.index < 0) {
        return;
    }
    const fromIndex = dragSource.index;
    if (fromIndex === targetIndex) {
        return;
    }

    const [moved] = rows.splice(fromIndex, 1);
    const insertAt = fromIndex < targetIndex ? targetIndex - 1 : targetIndex;
    rows.splice(insertAt, 0, moved);
    onRowDragEnd();
};

const onRowDragEnd = () => {
    dragSource.rows = null;
    dragSource.index = -1;
    dragSource.rowId = null;
    draggingRowId.value = null;
    dragOverRowId.value = null;
};

const updatePhoneViewState = () => {
    const isNarrow = window.matchMedia(phoneViewMediaQuery).matches;
    const isCoarsePointer = window.matchMedia('(pointer: coarse)').matches;
    const isMobileUa = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent || '');
    isPhoneView.value = isNarrow && (isCoarsePointer || isMobileUa);
    document.body.classList.toggle('is-phone-view', isPhoneView.value);
};

const closeHeroModal = () => {
    heroModalOpen.value = false;
    try {
        localStorage.setItem(HERO_DISMISSED_STORAGE_KEY, '1');
    } catch (_error) {
        // Ignore storage failures.
    }
};

const reopenHeroModal = () => {
    try {
        localStorage.removeItem(HERO_DISMISSED_STORAGE_KEY);
    } catch (_error) {
        // Ignore storage failures.
    }
    heroModalOpen.value = true;
};

const applyTheme = (themeId) => {
    const allowed = new Set(themeOptions.map((theme) => theme.id));
    const normalized = allowed.has(themeId) ? themeId : 'stubbr';
    activeThemeId.value = normalized;
    document.documentElement.setAttribute('data-theme', normalized);
    try {
        localStorage.setItem(THEME_STORAGE_KEY, normalized);
    } catch (_error) {
        // Ignore storage failures.
    }
};

const selectTheme = (themeId) => {
    applyTheme(themeId);
    themeMenuOpen.value = false;
};

const onTypeChange = (row) => {
    row.placeholder = defaultPlaceholderForType(row.type);
    if (!randomAllowed(row.type)) {
        row.random = false;
    }
    if (row.type === 'boolean' && row.value === '') {
        row.value = 'true';
    }
    if (row.type === 'null') {
        row.value = '';
    }
};

const openSelectModal = ({ title, options, onPick, anchorEl }) => {
    selectModalTitle.value = title;
    selectModalOptions.value = options.map((value) => ({ label: value, value }));
    selectModalOnPick.value = onPick;
    if (anchorEl) {
        const rect = anchorEl.getBoundingClientRect();
        const margin = 8;
        const width = Math.min(360, Math.max(180, rect.width));
        const estimatedHeight = Math.min(340, 44 + (options.length * 38));
        const spaceBelow = window.innerHeight - rect.bottom - margin;
        const spaceAbove = rect.top - margin;
        const openAbove = spaceBelow < 200 && spaceAbove > spaceBelow;
        const top = openAbove
            ? Math.max(margin, rect.top - estimatedHeight - 6)
            : Math.min(window.innerHeight - estimatedHeight - margin, rect.bottom + 6);
        const left = Math.min(window.innerWidth - width - margin, Math.max(margin, rect.left));
        const maxHeight = Math.max(140, openAbove ? rect.top - margin - 12 : window.innerHeight - rect.bottom - margin - 12);
        selectPopoverStyle.value = {
            top: `${top}px`,
            left: `${left}px`,
            width: `${width}px`,
            maxHeight: `${maxHeight}px`,
        };
    } else {
        selectPopoverStyle.value = {
            top: '80px',
            left: '80px',
            width: '280px',
            maxHeight: '320px',
        };
    }
    selectModalOpen.value = true;
};

const closeSelectModal = () => {
    selectModalOpen.value = false;
    selectModalTitle.value = '';
    selectModalOptions.value = [];
    selectModalOnPick.value = null;
};

const pickSelectOption = (value) => {
    if (selectModalOnPick.value) {
        selectModalOnPick.value(value);
    }
    closeSelectModal();
};

const sidebarIconElements = (name) => {
    if (name === 'workflow') return [
        { tag: 'rect', x: 3, y: 3, width: 7, height: 7, rx: 1 },
        { tag: 'rect', x: 14, y: 3, width: 7, height: 7, rx: 1 },
        { tag: 'rect', x: 14, y: 14, width: 7, height: 7, rx: 1 },
        { tag: 'line', x1: 10, y1: 6.5, x2: 14, y2: 6.5 },
        { tag: 'line', x1: 17.5, y1: 10, x2: 17.5, y2: 14 },
    ];
    if (name === 'play') return [{ tag: 'polygon', points: '8 5 19 12 8 19 8 5' }];
    if (name === 'key') return [
        { tag: 'circle', cx: 7, cy: 15, r: 3 },
        { tag: 'path', d: 'M10 15h11' },
        { tag: 'path', d: 'M18 12v3' },
        { tag: 'path', d: 'M21 12v3' },
    ];
    if (name === 'sliders') return [
        { tag: 'line', x1: 4, y1: 21, x2: 4, y2: 14 },
        { tag: 'line', x1: 4, y1: 10, x2: 4, y2: 3 },
        { tag: 'line', x1: 12, y1: 21, x2: 12, y2: 12 },
        { tag: 'line', x1: 12, y1: 8, x2: 12, y2: 3 },
        { tag: 'line', x1: 20, y1: 21, x2: 20, y2: 16 },
        { tag: 'line', x1: 20, y1: 12, x2: 20, y2: 3 },
        { tag: 'line', x1: 1, y1: 14, x2: 7, y2: 14 },
        { tag: 'line', x1: 9, y1: 8, x2: 15, y2: 8 },
        { tag: 'line', x1: 17, y1: 16, x2: 23, y2: 16 },
    ];
    if (name === 'repeat') return [
        { tag: 'polyline', points: '17 1 21 5 17 9' },
        { tag: 'path', d: 'M3 11V9a4 4 0 0 1 4-4h14' },
        { tag: 'polyline', points: '7 23 3 19 7 15' },
        { tag: 'path', d: 'M21 13v2a4 4 0 0 1-4 4H3' },
    ];
    if (name === 'hash') return [
        { tag: 'line', x1: 4, y1: 9, x2: 20, y2: 9 },
        { tag: 'line', x1: 4, y1: 15, x2: 20, y2: 15 },
        { tag: 'line', x1: 10, y1: 3, x2: 8, y2: 21 },
        { tag: 'line', x1: 16, y1: 3, x2: 14, y2: 21 },
    ];
    if (name === 'sparkles') return [
        { tag: 'path', d: 'M12 3l1.8 4.2L18 9l-4.2 1.8L12 15l-1.8-4.2L6 9l4.2-1.8z' },
    ];
    if (name === 'dice') return [
        { tag: 'rect', x: 4, y: 4, width: 16, height: 16, rx: 3 },
        { tag: 'circle', cx: 9, cy: 9, r: 1.1 },
        { tag: 'circle', cx: 15, cy: 9, r: 1.1 },
        { tag: 'circle', cx: 12, cy: 12, r: 1.1 },
        { tag: 'circle', cx: 9, cy: 15, r: 1.1 },
        { tag: 'circle', cx: 15, cy: 15, r: 1.1 },
    ];
    if (name === 'database') return [
        { tag: 'ellipse', cx: 12, cy: 5, rx: 8, ry: 3 },
        { tag: 'path', d: 'M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5' },
        { tag: 'path', d: 'M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6' },
    ];
    if (name === 'gauge') return [
        { tag: 'path', d: 'M12 14l4-4' },
        { tag: 'path', d: 'M4.9 19a10 10 0 1 1 14.2 0' },
    ];
    if (name === 'shield') return [{ tag: 'path', d: 'M12 2l8 4v6c0 5-3.4 8.7-8 10-4.6-1.3-8-5-8-10V6z' }];
    if (name === 'alert') return [
        { tag: 'path', d: 'M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z' },
        { tag: 'line', x1: 12, y1: 9, x2: 12, y2: 13 },
        { tag: 'line', x1: 12, y1: 17, x2: 12, y2: 17.1 },
    ];
    if (name === 'wrench') return [{ tag: 'path', d: 'M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18a2 2 0 1 0 2.8 2.8l6.3-6.3a4 4 0 0 0 5.4-5.4l-2.1 2.1-2.8-2.8z' }];
    if (name === 'info') return [
        { tag: 'circle', cx: 12, cy: 12, r: 9 },
        { tag: 'line', x1: 12, y1: 10, x2: 12, y2: 16 },
        { tag: 'line', x1: 12, y1: 7, x2: 12, y2: 7.1 },
    ];
    if (name === 'map') return [
        { tag: 'polygon', points: '1 6 8 3 16 6 23 3 23 18 16 21 8 18 1 21 1 6' },
        { tag: 'line', x1: 8, y1: 3, x2: 8, y2: 18 },
        { tag: 'line', x1: 16, y1: 6, x2: 16, y2: 21 },
    ];
    return [{ tag: 'rect', x: 4, y: 4, width: 16, height: 16, rx: 2 }];
};

const toggleSidebarSection = (sectionId) => {
    const wasOpen = sidebarOpen.value;
    activeSidebarSectionId.value = sectionId;
    if (sidebarWidth.value < sidebarOpenWidth) {
        sidebarWidth.value = sidebarOpenWidth;
    }
    sidebarOpen.value = true;
    if (!wasOpen) {
        window.setTimeout(() => {
            scrollReadmeToSection(sectionId, 'smooth');
        }, sidebarOpenTransitionMs);
        return;
    }
    nextTick(() => scrollReadmeToSection(sectionId, 'smooth'));
};

const onSidebarResizeMove = (event) => {
    if (!isResizingSidebar.value) return;
    const width = Math.max(220, Math.min(860, event.clientX));
    sidebarWidth.value = Math.round(width);
};

const stopSidebarResize = () => {
    if (!isResizingSidebar.value) return;
    isResizingSidebar.value = false;
    document.body.classList.remove('is-resizing-sidebar');
    window.removeEventListener('pointermove', onSidebarResizeMove);
    window.removeEventListener('pointerup', stopSidebarResize);
    if (sidebarWidth.value < sidebarOpenWidth) {
        sidebarOpen.value = false;
        sidebarWidth.value = sidebarOpenWidth;
    }
};

const startSidebarResize = (event) => {
    if (!sidebarOpen.value) return;
    event.preventDefault();
    isResizingSidebar.value = true;
    document.body.classList.add('is-resizing-sidebar');
    window.addEventListener('pointermove', onSidebarResizeMove);
    window.addEventListener('pointerup', stopSidebarResize);
};

const onPanelResizeMove = (event) => {
    if (!isResizingPanels.value || !contentRef.value) return;
    const rect = contentRef.value.getBoundingClientRect();
    const relativeX = event.clientX - rect.left;
    const nextPercent = (relativeX / rect.width) * 100;
    builderWidthPercent.value = Math.max(25, Math.min(75, Number(nextPercent.toFixed(2))));
};

const persistPanelWidth = () => {
    try {
        localStorage.setItem(PANEL_WIDTH_STORAGE_KEY, String(builderWidthPercent.value));
    } catch (_error) {
        // Ignore storage failures.
    }
};

const loadPanelWidth = () => {
    try {
        const raw = localStorage.getItem(PANEL_WIDTH_STORAGE_KEY);
        if (!raw) return;
        const parsed = Number(raw);
        if (!Number.isFinite(parsed)) return;
        builderWidthPercent.value = Math.max(25, Math.min(75, Number(parsed.toFixed(2))));
    } catch (_error) {
        // Ignore parse failures.
    }
};

const stopPanelResize = () => {
    if (!isResizingPanels.value) return;
    isResizingPanels.value = false;
    document.body.classList.remove('is-resizing-panels');
    window.removeEventListener('pointermove', onPanelResizeMove);
    window.removeEventListener('pointerup', stopPanelResize);
    persistPanelWidth();
};

const startPanelResize = (event) => {
    event.preventDefault();
    isResizingPanels.value = true;
    document.body.classList.add('is-resizing-panels');
    window.addEventListener('pointermove', onPanelResizeMove);
    window.addEventListener('pointerup', stopPanelResize);
};

const bindSidebarSectionRef = (sectionId) => (el) => {
    if (!el) {
        sidebarSectionRefs.delete(sectionId);
        return;
    }
    sidebarSectionRefs.set(sectionId, el);
};

const clearReadmeSnapTimer = () => {
    if (readmeSnapTimer) {
        window.clearTimeout(readmeSnapTimer);
        readmeSnapTimer = null;
    }
};

const markReadmeAutoScrolling = () => {
    isReadmeAutoScrolling = true;
    if (readmeAutoScrollUnlockTimer) {
        window.clearTimeout(readmeAutoScrollUnlockTimer);
    }
    readmeAutoScrollUnlockTimer = window.setTimeout(() => {
        isReadmeAutoScrolling = false;
    }, readmeSnapDurationMs);
};

const scrollReadmeToSection = (sectionId, behavior = 'smooth') => {
    const panel = readmePanelRef.value;
    const sectionEl = sidebarSectionRefs.get(sectionId);
    if (!panel || !sectionEl) return;
    markReadmeAutoScrolling();
    panel.scrollTo({
        top: Math.max(0, sectionEl.offsetTop - readmeScrollOffset),
        behavior,
    });
};

const snapReadmeToNearestSection = () => {
    const panel = readmePanelRef.value;
    if (!panel || !readmeSections.length) return;

    const targetTop = panel.scrollTop + readmeScrollOffset;
    let nearestId = readmeSections[0].id;
    let nearestDistance = Number.POSITIVE_INFINITY;

    readmeSections.forEach((section) => {
        const sectionEl = sidebarSectionRefs.get(section.id);
        if (!sectionEl) return;
        const distance = Math.abs(sectionEl.offsetTop - targetTop);
        if (distance < nearestDistance) {
            nearestDistance = distance;
            nearestId = section.id;
        }
    });

    activeSidebarSectionId.value = nearestId;
    scrollReadmeToSection(nearestId, 'smooth');
};

const onReadmePanelScroll = () => {
    const panel = readmePanelRef.value;
    if (!panel) return;
    const pivot = panel.scrollTop + 88;
    let currentId = readmeSections[0]?.id || '';

    readmeSections.forEach((section) => {
        const sectionEl = sidebarSectionRefs.get(section.id);
        if (!sectionEl) return;
        if (sectionEl.offsetTop <= pivot) {
            currentId = section.id;
        }
    });

    if (currentId) {
        activeSidebarSectionId.value = currentId;
    }

    if (isReadmeAutoScrolling) return;
    clearReadmeSnapTimer();
    readmeSnapTimer = window.setTimeout(() => {
        snapReadmeToNearestSection();
    }, readmeSnapDebounceMs);
};

const activeReadmeTabId = (section) => {
    if (!section.tableTabs?.length) return '';
    return readmeTabBySection[section.id] || section.tableTabs[0].id;
};

const setReadmeTab = (sectionId, tabId) => {
    readmeTabBySection[sectionId] = tabId;
};

const activeReadmeTabRows = (section) => {
    if (!section.tableTabs?.length) return [];
    const tabId = activeReadmeTabId(section);
    return section.tableTabs.find((tab) => tab.id === tabId)?.rows || section.tableTabs[0].rows;
};

const openTypePicker = (row, event) => {
    openSelectModal({
        title: 'Select Value Type',
        options: typeOptions,
        onPick: (value) => {
            row.type = value;
            onTypeChange(row);
        },
        anchorEl: event?.currentTarget,
    });
};

const openPlaceholderPicker = (row, event) => {
    const options = getPlaceholderOptions(row.type);
    if (!options.length) return;

    openSelectModal({
        title: 'Select Random Format',
        options,
        onPick: (value) => {
            row.placeholder = value;
        },
        anchorEl: event?.currentTarget,
    });
};

const openBooleanPicker = (row, event) => {
    openSelectModal({
        title: 'Select Boolean Value',
        options: booleanOptions,
        onPick: (value) => {
            row.value = value;
        },
        anchorEl: event?.currentTarget,
    });
};

const openRequestMethodPicker = (event) => {
    openSelectModal({
        title: 'Select HTTP Method',
        options: httpMethodOptions,
        onPick: (value) => {
            requestMethod.value = value;
        },
        anchorEl: event?.currentTarget,
    });
};

const getRowKindOptionsByDepth = (depth) => (depth >= 2 ? ['field'] : rowKindOptions);

const openRowKindPicker = (row, depth, event) => {
    const options = getRowKindOptionsByDepth(depth);
    openSelectModal({
        title: 'Select Row Kind',
        options,
        onPick: (value) => {
            applyRowKind(row, value);
        },
        anchorEl: event?.currentTarget,
    });
};

const valuePlaceholderForType = (type) => {
    if (type === 'object') return '{ "x": 1 }';
    if (type === 'array') return '[1,2]';
    return 'value';
};

const escapeHtml = (value) => value
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;');

const highlightJsonText = (text) => {
    const tokenRegex = /("(\\u[\da-fA-F]{4}|\\[^u]|[^\\"])*"(\s*:)?|\btrue\b|\bfalse\b|\bnull\b|-?\d+(?:\.\d+)?(?:[eE][+\-]?\d+)?|[{}\[\],:])/g;
    let result = '';
    let lastIndex = 0;
    let match;

    while ((match = tokenRegex.exec(text)) !== null) {
        result += escapeHtml(text.slice(lastIndex, match.index));
        const token = match[0];
        let tokenClass = 'token-plain';

        if (token[0] === '"') {
            tokenClass = token.endsWith(':') ? 'token-key' : 'token-string';
        } else if (/true|false/.test(token)) {
            tokenClass = 'token-boolean';
        } else if (token === 'null') {
            tokenClass = 'token-null';
        } else if (/^-?\d/.test(token)) {
            tokenClass = 'token-number';
        } else if (/^[{}\[\],:]$/.test(token)) {
            tokenClass = 'token-punctuation';
        }

        result += `<span class="${tokenClass}">${escapeHtml(token)}</span>`;
        lastIndex = tokenRegex.lastIndex;
    }

    result += escapeHtml(text.slice(lastIndex));
    return result;
};

const responseHighlightedHtml = computed(() => {
    const text = responseText.value ?? '';
    if (text.trim() === '') return '';

    const trimmed = text.trim();
    if (trimmed.startsWith('{') || trimmed.startsWith('[')) {
        try {
            JSON.parse(text);
            return highlightJsonText(text);
        } catch (_error) {
            return `<span class="token-plain">${escapeHtml(text)}</span>`;
        }
    }

    return `<span class="token-plain">${escapeHtml(text)}</span>`;
});

const wait = (ms) => new Promise((resolve) => {
    window.setTimeout(resolve, ms);
});

const fadeOutResponseText = async () => {
    const runId = ++responseFadeRun;
    if (!responseText.value.trim()) {
        responseOpacity.value = 0;
        return runId;
    }
    responseOpacity.value = 0;
    await wait(responseFadeMs);
    return runId;
};

const setResponseTextAndFadeIn = async (nextText, runId) => {
    if (runId !== responseFadeRun) return;
    responseText.value = nextText;
    await nextTick();
    requestAnimationFrame(() => {
        if (runId !== responseFadeRun) return;
        responseOpacity.value = 1;
    });
};

const buildCodeSnippets = (payload, path, authToken, method) => {
    const upperMethod = method.toUpperCase();
    const bodyPretty = JSON.stringify(payload, null, 2);
    const bodyCompact = JSON.stringify(payload);
    const bodyPhp = bodyPretty.replaceAll('\\', '\\\\').replaceAll("'", "\\'");
    const authHeader = `Bearer ${authToken}`;
    const authHeaderJs = JSON.stringify(authHeader);
    const authHeaderCurl = authHeader.replaceAll('\\', '\\\\').replaceAll('"', '\\"');
    const authHeaderPhp = authHeader.replaceAll('\\', '\\\\').replaceAll("'", "\\'");
    const allowsBody = !['GET', 'HEAD'].includes(upperMethod);
    const bodyLineJs = allowsBody ? `,\n  body: JSON.stringify(${bodyPretty})` : '';
    const curlBodyLine = allowsBody ? ` \\\n  -d '${bodyCompact}'` : '';
    const axiosDataArg = allowsBody ? `,\n  data: ${bodyPretty}` : '';
    const pythonMethod = upperMethod.toLowerCase();
    const pythonRequest = allowsBody
        ? `response = requests.${pythonMethod}('${path}', json=payload, headers=headers)`
        : `response = requests.${pythonMethod}('${path}', headers=headers)`;
    const phpBodyLine = allowsBody
        ? `\ncurl_setopt($ch, CURLOPT_POSTFIELDS, '${bodyPhp}');`
        : '';

    return {
        curl: `curl -X ${upperMethod} ${path} \\
  -H "Authorization: ${authHeaderCurl}" \\
  -H "Content-Type: application/json"${curlBodyLine}`,
        js: `const response = await fetch('${path}', {
  method: '${upperMethod}',
  headers: {
    'Authorization': ${authHeaderJs},
    'Content-Type': 'application/json'
  }${bodyLineJs}
});

const data = await response.json();`,
        axios: `import axios from 'axios';

const response = await axios({
  method: '${pythonMethod}',
  url: '${path}',
  headers: {
    Authorization: ${authHeaderJs},
    'Content-Type': 'application/json'
  }${axiosDataArg}
});

const data = response.data;`,
        php: `$ch = curl_init('${path}');
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
};

const codeSnippetText = computed(() => {
    const payload = payloadPreview.value;
    const path = endpointRoute.value;

    if (!payload) {
        return '// Build a valid request body to see generated examples.';
    }

    const snippets = buildCodeSnippets(payload, path, snippetToken.value, requestMethod.value);
    return snippets[codeExampleLanguage.value] ?? snippets.curl;
});

const highlightCodeText = (text) => {
    const tokenRegex = /("(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|`(?:\\.|[^`\\])*`|#.*$|\/\/.*$|\b(?:const|let|var|import|from|await|async|try|catch|return|new|true|false|null|def|requests|response|json|headers|payload|curl_setopt|curl_exec|curl_init)\b|-?\d+(?:\.\d+)?|[{}\[\]().,:;])/gm;
    let result = '';
    let lastIndex = 0;
    let match;

    while ((match = tokenRegex.exec(text)) !== null) {
        result += escapeHtml(text.slice(lastIndex, match.index));
        const token = match[0];
        let tokenClass = 'code-token-plain';

        if (token.startsWith('"') || token.startsWith("'") || token.startsWith('`')) {
            tokenClass = 'code-token-string';
        } else if (token.startsWith('//') || token.startsWith('#')) {
            tokenClass = 'code-token-comment';
        } else if (/^-?\d/.test(token)) {
            tokenClass = 'code-token-number';
        } else if (/^[{}\[\]().,:;]$/.test(token)) {
            tokenClass = 'code-token-punctuation';
        } else {
            tokenClass = 'code-token-keyword';
        }

        result += `<span class="${tokenClass}">${escapeHtml(token)}</span>`;
        lastIndex = tokenRegex.lastIndex;
    }

    result += escapeHtml(text.slice(lastIndex));
    return result;
};

const codeSnippetHighlightedHtml = computed(() => highlightCodeText(codeSnippetText.value));

const copyCodeSnippet = async () => {
    try {
        await navigator.clipboard.writeText(codeSnippetText.value);
        codeCopied.value = true;
        window.setTimeout(() => {
            codeCopied.value = false;
        }, 1200);
    } catch (_error) {
        codeCopied.value = false;
    }
};

const autoGrowRequestFields = () => {
    if (!requestFieldsRef.value) return;
    requestFieldsRef.value.style.height = 'auto';
    requestFieldsRef.value.style.height = `${requestFieldsRef.value.scrollHeight}px`;
};

const parseFieldValue = (row, pathLabel) => {
    if (row.random) {
        return row.placeholder || defaultPlaceholderForType(row.type);
    }

    if (row.type === 'string') {
        return row.value;
    }

    if (row.type === 'number') {
        const parsed = Number(row.value);
        if (Number.isNaN(parsed)) {
            throw new Error(`${pathLabel} must have a valid number value.`);
        }
        return parsed;
    }

    if (row.type === 'boolean') {
        return row.value === 'true';
    }

    if (row.type === 'null') {
        return null;
    }

    if (row.type === 'object' || row.type === 'array') {
        let parsed;
        try {
            parsed = JSON.parse(row.value || (row.type === 'object' ? '{}' : '[]'));
        } catch (_error) {
            throw new Error(`${pathLabel} must contain valid JSON.`);
        }

        if (row.type === 'object' && (typeof parsed !== 'object' || parsed === null || Array.isArray(parsed))) {
            throw new Error(`${pathLabel} must be a JSON object.`);
        }
        if (row.type === 'array' && !Array.isArray(parsed)) {
            throw new Error(`${pathLabel} must be a JSON array.`);
        }

        return parsed;
    }

    return row.value;
};

const buildBodyObject = (rows, parentLabel = 'body', depth = 0) => {
    const bodyObject = {};

    rows.forEach((row, index) => {
        const key = row.key.trim();
        if (!key) {
            throw new Error(`${parentLabel}: row ${index + 1} is missing a key.`);
        }

        const rowPath = `${parentLabel}.${key}`;

        if (row.kind === 'group') {
            if (depth >= 2) {
                throw new Error(`${rowPath} exceeds max nesting depth (2).`);
            }

            const groupBody = buildBodyObject(row.rows, rowPath, depth + 1);

            if (row.repeatEnabled) {
                const repeat = Number(row.repeat);
                if (Number.isNaN(repeat) || repeat < 0) {
                    throw new Error(`${rowPath}.__repeat must be a valid non-negative number.`);
                }
                groupBody.__repeat = repeat;
            }

            bodyObject[key] = groupBody;
            return;
        }

        bodyObject[key] = parseFieldValue(row, rowPath);
    });

    return bodyObject;
};

const createRequestPayload = ({ setErrors = false } = {}) => {
    if (setErrors) {
        filtersError.value = '';
        builderError.value = '';
    }

    let parsedRequestFields;
    const requestFieldSource = filtersText.value.trim() === '' ? '{}' : filtersText.value;
    try {
        parsedRequestFields = JSON.parse(requestFieldSource);
    } catch (_error) {
        if (setErrors) {
            filtersError.value = 'Invalid JSON in request fields.';
        }
        throw new Error('Fix request fields JSON before sending.');
    }

    if (typeof parsedRequestFields !== 'object' || parsedRequestFields === null || Array.isArray(parsedRequestFields)) {
        if (setErrors) {
            filtersError.value = 'Request fields must be a JSON object.';
        }
        throw new Error('Request fields must be a JSON object.');
    }

    if (Object.prototype.hasOwnProperty.call(parsedRequestFields, '__instructions')) {
        throw new Error('Do not include "__instructions" in request fields. Use the builder below.');
    }

    const builtInstructions = {
        body: buildBodyObject(bodyRows.value),
    };

    if (instructions.status.enabled) {
        const status = Number(instructions.status.value);
        if (Number.isNaN(status)) {
            throw new Error('"status" must be a number.');
        }
        builtInstructions.status = status;
    }
    if (instructions.delay.enabled) {
        const delay = Number(instructions.delay.value);
        if (Number.isNaN(delay)) {
            throw new Error('"delay" must be a number.');
        }
        builtInstructions.delay = delay;
    }
    if (instructions.max_pages.enabled) {
        const maxPages = Number(instructions.max_pages.value);
        if (Number.isNaN(maxPages)) {
            throw new Error('"max_pages" must be a number.');
        }
        builtInstructions.max_pages = maxPages;
    }
    if (instructions.no_cache.enabled) {
        builtInstructions.no_cache = instructions.no_cache.value;
    }

    return {
        ...parsedRequestFields,
        __instructions: builtInstructions,
    };
};

const buildRequestPayload = () => createRequestPayload({ setErrors: true });

const payloadPreview = computed(() => {
    try {
        return createRequestPayload({ setErrors: false });
    } catch (_error) {
        return null;
    }
});

watch(
    () => JSON.stringify(createBuilderStateSnapshot()),
    () => {
        if (isApplyingBuilderHistory.value) return;
        scheduleBuilderHistorySnapshot();
    },
);

const sendRequest = async () => {
    if (!token.value) {
        statusLine.value = 'No token available';
        return;
    }

    let parsedBody;
    statusLine.value = 'Sending request...';
    const fadeRunId = await fadeOutResponseText();

    try {
        parsedBody = buildRequestPayload();
    } catch (error) {
        builderError.value = String(error.message || error);
        statusLine.value = 'Builder validation failed.';
        await setResponseTextAndFadeIn(builderError.value, fadeRunId);
        return;
    }

    isSending.value = true;

    const headers = {
        'Content-Type': 'application/json',
    };
    headers.Authorization = `Bearer ${token.value.trim()}`;

    const requestUrl = endpointRoute.value;
    const doFetch = () => {
        const method = requestMethod.value.toUpperCase();
        const init = {
            method,
            headers,
        };
        if (!['GET', 'HEAD'].includes(method)) {
            init.body = JSON.stringify(parsedBody);
        }
        return fetch(requestUrl, init);
    };

    try {
        const requestStartedAt = performance.now();
        let response = await doFetch();
        const headersReceivedAt = performance.now();

        if (response.status === 401) {
            await bootstrapToken(true);
            headers.Authorization = `Bearer ${token.value.trim()}`;
            response = await doFetch();
        }

        let formattedResponse = '';
        try {
            const payload = await response.json();
            formattedResponse = JSON.stringify(payload, null, 2);
        } catch (_parseError) {
            formattedResponse = await response.text();
        }
        const bodyLoadedAt = performance.now();

        const waitMs = Math.max(0, Math.round(headersReceivedAt - requestStartedAt));
        const loadingMs = Math.max(0, Math.round(bodyLoadedAt - headersReceivedAt));
        const sizeBytes = new TextEncoder().encode(formattedResponse || '').length;

        statusLine.value = `HTTP ${response.status}, Wait ${waitMs}ms, Loading ${loadingMs}ms, Size ${sizeBytes}b`;
        await setResponseTextAndFadeIn(formattedResponse || '(empty response)', fadeRunId);
    } catch (error) {
        statusLine.value = 'Request failed';
        await setResponseTextAndFadeIn(String(error), fadeRunId);
    } finally {
        isSending.value = false;
    }
};

onMounted(async () => {
    try {
        const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
        if (storedTheme) {
            applyTheme(storedTheme);
        } else {
            applyTheme('stubbr');
        }
    } catch (_error) {
        applyTheme('stubbr');
    }

    try {
        if (localStorage.getItem(HERO_DISMISSED_STORAGE_KEY) === '1') {
            heroModalOpen.value = false;
        }
    } catch (_error) {
        // Ignore storage failures.
    }
    updatePhoneViewState();
    window.addEventListener('resize', updatePhoneViewState);
    loadPanelWidth();
    const restored = loadBuilderHistoryFromStorage();
    await bootstrapToken();
    nextTick(() => {
        autoGrowRequestFields();
        onReadmePanelScroll();
        if (!restored) {
            commitBuilderHistorySnapshot();
        }
    });
    if (token.value) {
        await sendRequest();
    }
});

onUnmounted(() => {
    window.removeEventListener('resize', updatePhoneViewState);
    document.body.classList.remove('is-phone-view');
    stopSidebarResize();
    stopPanelResize();
    clearBuilderHistoryTimer();
    clearReadmeSnapTimer();
    if (readmeAutoScrollUnlockTimer) {
        window.clearTimeout(readmeAutoScrollUnlockTimer);
        readmeAutoScrollUnlockTimer = null;
    }
});
</script>

<template>
    <div v-if="isPhoneView" class="mobile-only-shell">
        <section class="mobile-only-hero">
            <p class="hero-kicker">Mock API Playground</p>
            <h1>This is <img :src="topbarLogoSrc" alt="Stubbr" class="hero-logo"></h1>
            <p class="hero-copy">
                Build production-ready requests from day one with smart mock responses powered by
                <code>__instructions</code>.
            </p>
            <p class="mobile-only-note">
                You need to use a computer to access the full functionality of this website.
            </p>
        </section>
    </div>

    <div v-else class="app-shell" :class="{ 'sidebar-open': sidebarOpen }" :style="{ gridTemplateColumns: shellGridTemplate }">
        <aside class="readme-sidebar">
            <div class="readme-icons">
                <button
                    v-for="section in readmeSections"
                    :key="section.id"
                    type="button"
                    class="sidebar-icon-button"
                    :class="{ active: activeSidebarSectionId === section.id }"
                    :title="section.title"
                    @click="toggleSidebarSection(section.id)"
                >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <template v-for="(part, idx) in sidebarIconElements(section.icon)" :key="idx">
                            <path v-if="part.tag === 'path'" :d="part.d" />
                            <line v-else-if="part.tag === 'line'" :x1="part.x1" :y1="part.y1" :x2="part.x2" :y2="part.y2" />
                            <polyline v-else-if="part.tag === 'polyline'" :points="part.points" />
                            <polygon v-else-if="part.tag === 'polygon'" :points="part.points" />
                            <circle v-else-if="part.tag === 'circle'" :cx="part.cx" :cy="part.cy" :r="part.r" />
                            <ellipse v-else-if="part.tag === 'ellipse'" :cx="part.cx" :cy="part.cy" :rx="part.rx" :ry="part.ry" />
                            <rect v-else-if="part.tag === 'rect'" :x="part.x" :y="part.y" :width="part.width" :height="part.height" :rx="part.rx || 0" />
                        </template>
                    </svg>
                </button>
            </div>
            <div ref="readmePanelRef" class="readme-panel" @scroll="onReadmePanelScroll">
                <div class="readme-panel-header">
                    <h3>Documentation</h3>
                    <button type="button" class="icon-button" @click="sidebarOpen = false">x</button>
                </div>
                <div class="readme-sections">
                    <section
                        v-for="section in readmeSections"
                        :id="`readme-section-${section.id}`"
                        :key="section.id"
                        :ref="bindSidebarSectionRef(section.id)"
                        class="readme-section-block"
                    >
                        <h4>{{ section.title }}</h4>
                        <p
                            v-for="(paragraph, idx) in section.paragraphs"
                            :key="`p-${section.id}-${idx}`"
                        >
                            {{ paragraph }}
                        </p>
                        <ul>
                            <li
                                v-for="(item, idx) in section.bullets"
                                :key="`b-${section.id}-${idx}`"
                            >
                                {{ item }}
                            </li>
                        </ul>
                        <div v-if="section.tableTabs?.length" class="readme-randomizer">
                            <div class="readme-randomizer-tabs">
                                <button
                                    v-for="tab in section.tableTabs"
                                    :key="`${section.id}-${tab.id}`"
                                    type="button"
                                    class="readme-randomizer-tab"
                                    :class="{ active: activeReadmeTabId(section) === tab.id }"
                                    @click="setReadmeTab(section.id, tab.id)"
                                >
                                    {{ tab.label }}
                                </button>
                            </div>
                            <div class="readme-randomizer-table-wrap">
                                <table class="readme-randomizer-table">
                                    <thead>
                                        <tr>
                                            <th>Placeholder</th>
                                            <th>Meaning</th>
                                            <th>Example</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="row in activeReadmeTabRows(section)"
                                            :key="`${section.id}-${activeReadmeTabId(section)}-${row.placeholder}`"
                                        >
                                            <td><code>{{ row.placeholder }}</code></td>
                                            <td>{{ row.description }}</td>
                                            <td><code>{{ row.example }}</code></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div
                            v-for="(example, idx) in (section.examples || [])"
                            :key="`x-${section.id}-${idx}`"
                            class="readme-example"
                        >
                            <p class="readme-example-label">{{ example.label }}</p>
                            <pre><code v-html="highlightCodeText(example.code)"></code></pre>
                        </div>
                        <button
                            v-if="section.id === 'roadmap'"
                            type="button"
                            class="about-hero-link"
                            @click="reopenHeroModal"
                        >
                            Open Intro Hero
                        </button>
                    </section>
                </div>
            </div>
            <button
                v-if="sidebarOpen"
                type="button"
                class="sidebar-resize-handle"
                aria-label="Resize sidebar"
                @pointerdown="startSidebarResize"
            >
                <span class="sidebar-resize-grip"></span>
            </button>
        </aside>

        <div class="page">
            <nav class="topbar">
                <div class="logo-slot">
                    <img :src="topbarLogoSrc" alt="Stubbr" class="topbar-logo">
                </div>
                <div class="topbar-center">
                    <form v-if="!ownTokenVisible" class="token-form" @submit.prevent="getOwnToken">
                        <input
                            v-model="ownTokenEmail"
                            type="email"
                            placeholder="you@example.com"
                            required
                        >
                        <button type="submit" :disabled="ownTokenLoading">
                            {{ ownTokenLoading ? 'Getting token...' : 'Get your own token' }}
                        </button>
                    </form>
                    <div v-else class="token-result">
                        <span class="token-result-label">Token</span>
                        <code>{{ token }}</code>
                    </div>
                    <p v-if="ownTokenError" class="token-form-error">{{ ownTokenError }}</p>
                </div>
                <div class="nav-empty"></div>
            </nav>

            <main ref="contentRef" class="content" :style="{ '--builder-width': `${builderWidthPercent}%` }">
                <section class="panel builder-panel">
                <div class="panel-header-row">
                    <h2>JSON Builder</h2>
                </div>

                <label for="api-endpoint">Endpoint</label>
                <div class="endpoint-row">
                    <button
                        type="button"
                        class="code-input select-trigger method-select"
                        @click="openRequestMethodPicker($event)"
                    >
                        <span>{{ requestMethod }}</span>
                        <span class="select-caret">v</span>
                    </button>
                    <span class="endpoint-prefix">/api/</span>
                    <input
                        id="api-endpoint"
                        v-model="endpointPath"
                        type="text"
                        placeholder="your/endpoint"
                    >
                    <div class="endpoint-history">
                        <button
                            type="button"
                            class="endpoint-history-button"
                            :disabled="!canStepBack"
                            aria-label="Step back"
                            @click="stepBuilderBack"
                        >
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            class="endpoint-history-button"
                            :disabled="!canStepForward"
                            aria-label="Step forward"
                            @click="stepBuilderForward"
                        >
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="builder-workspace">
                    <aside class="instruction-rail">
                        <p>Instructions</p>
                        <button type="button" class="instruction-button" :disabled="instructions.status.enabled" @click="instructions.status.enabled = true">
                            + status
                        </button>
                        <button type="button" class="instruction-button" :disabled="instructions.delay.enabled" @click="instructions.delay.enabled = true">
                            + delay
                        </button>
                        <button type="button" class="instruction-button" :disabled="instructions.max_pages.enabled" @click="instructions.max_pages.enabled = true">
                            + max_pages
                        </button>
                        <button type="button" class="instruction-button" :disabled="instructions.no_cache.enabled" @click="instructions.no_cache.enabled = true">
                            + no_cache
                        </button>
                    </aside>

                    <div class="code-shell syntax-tint">
                        <div class="code-line">{</div>

                        <div class="code-line filters-line">
                            <span class="line-prefix"><span class="indentation"></span></span>
                            <textarea
                                ref="requestFieldsRef"
                                v-model="filtersText"
                                class="filters-editor"
                                spellcheck="false"
                                rows="1"
                                placeholder="// Your own API body request here"
                                @input="autoGrowRequestFields"
                            />
                            <span>,</span>
                        </div>
                        <p v-if="filtersError" class="builder-error">{{ filtersError }}</p>

                        <div class="code-line structure-line">
                            <span class="line-prefix"><span class="indentation"></span></span>
                            <span class="json-keyword">"__instructions"</span><span class="json-punctuation">: {</span>
                        </div>
                        <div class="code-line structure-line">
                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span></span>
                            <span class="json-keyword">"body"</span><span class="json-punctuation">: {</span>
                        </div>

                        <div
                            v-for="(row, index) in bodyRows"
                            :key="row.id"
                            class="draggable-row"
                            :class="{ 'drag-over': dragOverRowId === row.id, dragging: draggingRowId === row.id }"
                            draggable="true"
                            @dragstart="onRowDragStart(row.id, bodyRows, index, $event)"
                            @dragover.prevent="onRowDragOver(row.id, bodyRows, index, $event)"
                            @drop.prevent="onRowDrop(bodyRows, index)"
                            @dragend="onRowDragEnd"
                        >
                            <div v-if="row.kind === 'field'" class="body-row">
                                <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                <button
                                    type="button"
                                    class="code-input select-trigger row-kind-select"
                                    @click="openRowKindPicker(row, 0, $event)"
                                >
                                    <span>{{ row.kind }}</span>
                                    <span class="select-caret">v</span>
                                </button>
                                <span>"</span>
                                <input
                                    v-model="row.key"
                                    class="code-input key-input"
                                    type="text"
                                    placeholder="key"
                                >
                                <span>": </span>

                                <button
                                    type="button"
                                    class="code-input select-trigger type-select"
                                    @click="openTypePicker(row, $event)"
                                >
                                    <span>{{ row.type }}</span>
                                    <span class="select-caret">v</span>
                                </button>

                                <button
                                    type="button"
                                    class="die-toggle"
                                    :class="{ checked: row.random }"
                                    :disabled="!randomAllowed(row.type)"
                                    title="Toggle random value"
                                    @click="row.random = !row.random"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="4" y="4" width="16" height="16" rx="3" />
                                        <circle cx="9" cy="9" r="1.2" />
                                        <circle cx="15" cy="9" r="1.2" />
                                        <circle cx="12" cy="12" r="1.2" />
                                        <circle cx="9" cy="15" r="1.2" />
                                        <circle cx="15" cy="15" r="1.2" />
                                    </svg>
                                </button>

                                <button
                                    v-if="row.random"
                                    type="button"
                                    class="code-input select-trigger format-select"
                                    @click="openPlaceholderPicker(row, $event)"
                                >
                                    <span class="json-placeholder">{{ row.placeholder }}</span>
                                    <span class="select-caret">v</span>
                                </button>

                                <input
                                    v-if="!row.random && row.type !== 'boolean' && row.type !== 'null'"
                                    v-model="row.value"
                                    class="code-input value-input"
                                    type="text"
                                    :placeholder="valuePlaceholderForType(row.type)"
                                >

                                <button
                                    v-if="!row.random && row.type === 'boolean'"
                                    type="button"
                                    class="code-input select-trigger boolean-select"
                                    @click="openBooleanPicker(row, $event)"
                                >
                                    <span>{{ row.value || 'true' }}</span>
                                    <span class="select-caret">v</span>
                                </button>

                                <span v-if="!row.random && row.type === 'null'" class="placeholder-view">null</span>

                                <div class="row-actions">
                                    <button type="button" class="icon-button" @click="removeBodyRow(row.id)" aria-label="Remove row">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 7h16" />
                                            <path d="M10 11v6" />
                                            <path d="M14 11v6" />
                                            <path d="M6 7l1 13h10l1-13" />
                                            <path d="M9 7V4h6v3" />
                                        </svg>
                                    </button>
                                    <button type="button" class="drag-handle" aria-label="Drag row" @mousedown.stop>
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="9" cy="7" r="1.2" />
                                            <circle cx="15" cy="7" r="1.2" />
                                            <circle cx="9" cy="12" r="1.2" />
                                            <circle cx="15" cy="12" r="1.2" />
                                            <circle cx="9" cy="17" r="1.2" />
                                            <circle cx="15" cy="17" r="1.2" />
                                        </svg>
                                    </button>
                                </div>
                                <span v-if="index !== bodyRows.length - 1" class="line-comma">,</span>
                            </div>

                            <div v-else class="group-block">
                                <div class="body-row group-header-row">
                                    <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                    <button
                                        type="button"
                                        class="code-input select-trigger row-kind-select"
                                        @click="openRowKindPicker(row, 0, $event)"
                                    >
                                        <span>{{ row.kind }}</span>
                                        <span class="select-caret">v</span>
                                    </button>
                                    <span>"</span>
                                    <input
                                        v-model="row.key"
                                        class="code-input key-input"
                                        type="text"
                                        placeholder="group key"
                                    >
                                    <span>": {</span>

                                    <div class="row-actions">
                                        <button type="button" class="icon-button" @click="removeBodyRow(row.id)" aria-label="Remove group">
                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M4 7h16" />
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                                <path d="M6 7l1 13h10l1-13" />
                                                <path d="M9 7V4h6v3" />
                                            </svg>
                                        </button>
                                        <button type="button" class="drag-handle" aria-label="Drag group" @mousedown.stop>
                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <circle cx="9" cy="7" r="1.2" />
                                                <circle cx="15" cy="7" r="1.2" />
                                                <circle cx="9" cy="12" r="1.2" />
                                                <circle cx="15" cy="12" r="1.2" />
                                                <circle cx="9" cy="17" r="1.2" />
                                                <circle cx="15" cy="17" r="1.2" />
                                            </svg>
                                        </button>
                                    </div>
                                    <span v-if="index !== bodyRows.length - 1" class="line-comma">,</span>
                                </div>

                                <div class="body-row nested-row repeat-toggle-row">
                                    <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                    <button
                                        type="button"
                                        class="check-toggle repeat-toggle-control"
                                        :class="{ checked: row.repeatEnabled }"
                                        aria-label="Toggle repeat"
                                        @click="row.repeatEnabled = !row.repeatEnabled"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </button>
                                    <div class="repeat-body" :class="{ 'repeat-line-muted': !row.repeatEnabled }">
                                        <span class="json-keyword">"__repeat"</span><span class="json-punctuation">:</span>

                                        <input
                                            v-model="row.repeat"
                                            class="code-input instruction-input"
                                            type="number"
                                            min="0"
                                            :disabled="!row.repeatEnabled"
                                        >
                                    </div>
                                </div>

                                <div
                                    v-for="(groupField, gIndex) in row.rows"
                                    :key="groupField.id"
                                    class="draggable-row"
                                    :class="{ 'drag-over': dragOverRowId === groupField.id, dragging: draggingRowId === groupField.id }"
                                    draggable="true"
                                    @dragstart="onRowDragStart(groupField.id, row.rows, gIndex, $event)"
                                    @dragover.prevent="onRowDragOver(groupField.id, row.rows, gIndex, $event)"
                                    @drop.prevent="onRowDrop(row.rows, gIndex)"
                                    @dragend="onRowDragEnd"
                                >
                                    <div v-if="groupField.kind === 'field'" class="body-row nested-row">
                                        <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                        <button
                                            type="button"
                                            class="code-input select-trigger row-kind-select"
                                            @click="openRowKindPicker(groupField, 1, $event)"
                                        >
                                            <span>{{ groupField.kind }}</span>
                                            <span class="select-caret">v</span>
                                        </button>
                                        <span>"</span>
                                        <input
                                            v-model="groupField.key"
                                            class="code-input key-input"
                                            type="text"
                                            placeholder="key"
                                        >
                                        <span>": </span>

                                        <button
                                            type="button"
                                            class="code-input select-trigger type-select"
                                            @click="openTypePicker(groupField, $event)"
                                        >
                                            <span>{{ groupField.type }}</span>
                                            <span class="select-caret">v</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="die-toggle"
                                            :class="{ checked: groupField.random }"
                                            :disabled="!randomAllowed(groupField.type)"
                                            title="Toggle random value"
                                            @click="groupField.random = !groupField.random"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <rect x="4" y="4" width="16" height="16" rx="3" />
                                                <circle cx="9" cy="9" r="1.2" />
                                                <circle cx="15" cy="9" r="1.2" />
                                                <circle cx="12" cy="12" r="1.2" />
                                                <circle cx="9" cy="15" r="1.2" />
                                                <circle cx="15" cy="15" r="1.2" />
                                            </svg>
                                        </button>

                                        <button
                                            v-if="groupField.random"
                                            type="button"
                                            class="code-input select-trigger format-select"
                                            @click="openPlaceholderPicker(groupField, $event)"
                                        >
                                            <span class="json-placeholder">{{ groupField.placeholder }}</span>
                                            <span class="select-caret">v</span>
                                        </button>

                                        <input
                                            v-if="!groupField.random && groupField.type !== 'boolean' && groupField.type !== 'null'"
                                            v-model="groupField.value"
                                            class="code-input value-input"
                                            type="text"
                                            :placeholder="valuePlaceholderForType(groupField.type)"
                                        >

                                        <button
                                            v-if="!groupField.random && groupField.type === 'boolean'"
                                            type="button"
                                            class="code-input select-trigger boolean-select"
                                            @click="openBooleanPicker(groupField, $event)"
                                        >
                                            <span>{{ groupField.value || 'true' }}</span>
                                            <span class="select-caret">v</span>
                                        </button>

                                        <span v-if="!groupField.random && groupField.type === 'null'" class="placeholder-view">null</span>

                                        <div class="row-actions">
                                            <button type="button" class="icon-button" @click="removeGroupField(row, groupField.id)" aria-label="Remove row">
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 7h16" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M6 7l1 13h10l1-13" />
                                                    <path d="M9 7V4h6v3" />
                                                </svg>
                                            </button>
                                            <button type="button" class="drag-handle" aria-label="Drag row" @mousedown.stop>
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <circle cx="9" cy="7" r="1.2" />
                                                    <circle cx="15" cy="7" r="1.2" />
                                                    <circle cx="9" cy="12" r="1.2" />
                                                    <circle cx="15" cy="12" r="1.2" />
                                                    <circle cx="9" cy="17" r="1.2" />
                                                    <circle cx="15" cy="17" r="1.2" />
                                                </svg>
                                            </button>
                                        </div>
                                        <span v-if="gIndex !== row.rows.length - 1" class="line-comma">,</span>
                                    </div>

                                    <div v-else class="group-block nested-row">
                                        <div class="body-row group-header-row">
                                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                            <button
                                                type="button"
                                                class="code-input select-trigger row-kind-select"
                                                @click="openRowKindPicker(groupField, 1, $event)"
                                            >
                                                <span>{{ groupField.kind }}</span>
                                                <span class="select-caret">v</span>
                                            </button>
                                            <span>"</span>
                                            <input
                                                v-model="groupField.key"
                                                class="code-input key-input"
                                                type="text"
                                                placeholder="group key"
                                            >
                                            <span>": {</span>

                                            <button type="button" class="icon-button" @click="removeGroupField(row, groupField.id)" aria-label="Remove group">
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 7h16" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M6 7l1 13h10l1-13" />
                                                    <path d="M9 7V4h6v3" />
                                                </svg>
                                            </button>
                                            <span v-if="gIndex !== row.rows.length - 1" class="line-comma">,</span>
                                        </div>

                                        <div class="body-row nested-row repeat-toggle-row">
                                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                            <button
                                                type="button"
                                                class="check-toggle repeat-toggle-control"
                                                :class="{ checked: groupField.repeatEnabled }"
                                                aria-label="Toggle repeat"
                                                @click="groupField.repeatEnabled = !groupField.repeatEnabled"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                            </button>
                                            <div class="repeat-body" :class="{ 'repeat-line-muted': !groupField.repeatEnabled }">
                                                <span class="json-keyword">"__repeat"</span><span class="json-punctuation">:</span>

                                                <input
                                                    v-model="groupField.repeat"
                                                    class="code-input instruction-input"
                                                    type="number"
                                                    min="0"
                                                    :disabled="!groupField.repeatEnabled"
                                                >
                                            </div>
                                        </div>

                                        <div
                                            v-for="(deepField, deepIndex) in groupField.rows"
                                            :key="deepField.id"
                                            class="body-row nested-row"
                                        >
                                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                            <button
                                                type="button"
                                                class="code-input select-trigger row-kind-select"
                                                @click="openRowKindPicker(deepField, 2, $event)"
                                            >
                                                <span>{{ deepField.kind }}</span>
                                                <span class="select-caret">v</span>
                                            </button>
                                            <span>"</span>
                                            <input
                                                v-model="deepField.key"
                                                class="code-input key-input"
                                                type="text"
                                                placeholder="key"
                                            >
                                            <span>": </span>

                                            <button
                                                type="button"
                                                class="code-input select-trigger type-select"
                                                @click="openTypePicker(deepField, $event)"
                                            >
                                                <span>{{ deepField.type }}</span>
                                                <span class="select-caret">v</span>
                                            </button>

                                            <button
                                                type="button"
                                                class="die-toggle"
                                                :class="{ checked: deepField.random }"
                                                :disabled="!randomAllowed(deepField.type)"
                                                title="Toggle random value"
                                                @click="deepField.random = !deepField.random"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <rect x="4" y="4" width="16" height="16" rx="3" />
                                                    <circle cx="9" cy="9" r="1.2" />
                                                    <circle cx="15" cy="9" r="1.2" />
                                                    <circle cx="12" cy="12" r="1.2" />
                                                    <circle cx="9" cy="15" r="1.2" />
                                                    <circle cx="15" cy="15" r="1.2" />
                                                </svg>
                                            </button>

                                            <button
                                                v-if="deepField.random"
                                                type="button"
                                                class="code-input select-trigger format-select"
                                                @click="openPlaceholderPicker(deepField, $event)"
                                            >
                                                <span class="json-placeholder">{{ deepField.placeholder }}</span>
                                                <span class="select-caret">v</span>
                                            </button>

                                            <input
                                                v-if="!deepField.random && deepField.type !== 'boolean' && deepField.type !== 'null'"
                                                v-model="deepField.value"
                                                class="code-input value-input"
                                                type="text"
                                                :placeholder="valuePlaceholderForType(deepField.type)"
                                            >

                                            <button
                                                v-if="!deepField.random && deepField.type === 'boolean'"
                                                type="button"
                                                class="code-input select-trigger boolean-select"
                                                @click="openBooleanPicker(deepField, $event)"
                                            >
                                                <span>{{ deepField.value || 'true' }}</span>
                                                <span class="select-caret">v</span>
                                            </button>

                                            <span v-if="!deepField.random && deepField.type === 'null'" class="placeholder-view">null</span>

                                            <button type="button" class="icon-button" @click="removeGroupField(groupField, deepField.id)" aria-label="Remove row">
                                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 7h16" />
                                                    <path d="M10 11v6" />
                                                    <path d="M14 11v6" />
                                                    <path d="M6 7l1 13h10l1-13" />
                                                    <path d="M9 7V4h6v3" />
                                                </svg>
                                            </button>
                                            <span v-if="deepIndex !== groupField.rows.length - 1" class="line-comma">,</span>
                                        </div>

                                        <div class="code-line nested-row add-row-line">
                                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                            <button type="button" class="inline-add-row" @click="addChildRow(groupField)">+ add row</button>
                                        </div>

                                        <div class="code-line nested-row structure-line">
                                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                            <span>}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="code-line nested-row add-row-line">
                                    <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                    <button type="button" class="inline-add-row" @click="addChildRow(row)">+ add row</button>
                                </div>

                                <div class="code-line nested-row structure-line">
                                    <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                                    <span>}</span>
                                </div>
                            </div>
                        </div>

                        <div class="code-line add-row-line">
                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span><span class="indentation"></span></span>
                            <button type="button" class="inline-add-row" @click="addRow()">+ add row</button>
                        </div>

                        <div class="code-line structure-line">
                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span></span>
                            <span>}<span v-if="extraInstructionCount > 0">,</span></span>
                        </div>

                        <div v-if="instructions.status.enabled" class="instruction-line">
                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span></span>
                            <span>"status": </span>
                            <input v-model="instructions.status.value" class="code-input instruction-input" type="number">
                            <button type="button" class="icon-button" @click="instructions.status.enabled = false" aria-label="Remove status instruction">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16" />
                                    <path d="M10 11v6" />
                                    <path d="M14 11v6" />
                                    <path d="M6 7l1 13h10l1-13" />
                                    <path d="M9 7V4h6v3" />
                                </svg>
                            </button>
                            <span v-if="instructions.delay.enabled || instructions.max_pages.enabled || instructions.no_cache.enabled">,</span>
                        </div>

                        <div v-if="instructions.delay.enabled" class="instruction-line">
                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span></span>
                            <span>"delay": </span>
                            <input v-model="instructions.delay.value" class="code-input instruction-input" type="number">
                            <button type="button" class="icon-button" @click="instructions.delay.enabled = false" aria-label="Remove delay instruction">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16" />
                                    <path d="M10 11v6" />
                                    <path d="M14 11v6" />
                                    <path d="M6 7l1 13h10l1-13" />
                                    <path d="M9 7V4h6v3" />
                                </svg>
                            </button>
                            <span v-if="instructions.max_pages.enabled || instructions.no_cache.enabled">,</span>
                        </div>

                        <div v-if="instructions.max_pages.enabled" class="instruction-line">
                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span></span>
                            <span>"max_pages": </span>
                            <input v-model="instructions.max_pages.value" class="code-input instruction-input" type="number">
                            <button type="button" class="icon-button" @click="instructions.max_pages.enabled = false" aria-label="Remove max pages instruction">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16" />
                                    <path d="M10 11v6" />
                                    <path d="M14 11v6" />
                                    <path d="M6 7l1 13h10l1-13" />
                                    <path d="M9 7V4h6v3" />
                                </svg>
                            </button>
                            <span v-if="instructions.no_cache.enabled">,</span>
                        </div>

                        <div v-if="instructions.no_cache.enabled" class="instruction-line">
                            <span class="line-prefix"><span class="indentation"></span><span class="indentation"></span></span>
                            <span>"no_cache": </span>
                            <label class="random-check">
                                <input v-model="instructions.no_cache.value" type="checkbox">
                                true
                            </label>
                            <button type="button" class="icon-button" @click="instructions.no_cache.enabled = false" aria-label="Remove no cache instruction">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7h16" />
                                    <path d="M10 11v6" />
                                    <path d="M14 11v6" />
                                    <path d="M6 7l1 13h10l1-13" />
                                    <path d="M9 7V4h6v3" />
                                </svg>
                            </button>
                        </div>

                        <div class="code-line structure-line">
                            <span class="line-prefix"><span class="indentation"></span></span>
                            <span>}</span>
                        </div>
                        <div class="code-line structure-line">}</div>
                    </div>
                </div>

                <p v-if="builderError" class="builder-error">{{ builderError }}</p>

                <button
                    type="button"
                    class="send-button"
                    :disabled="isSending || tokenLoading"
                    @click="sendRequest"
                >
                    {{ tokenLoading ? 'Preparing token...' : (isSending ? 'Sending...' : 'Send request') }}
                </button>
                </section>

                <button
                    type="button"
                    class="panel-resize-handle"
                    aria-label="Resize builder and response panels"
                    @pointerdown="startPanelResize"
                >
                    <span class="panel-resize-grip"></span>
                </button>

                <section class="panel response-panel">
                    <h2>Request Response</h2>
                    <p class="status">{{ statusLine }}</p>
                    <pre><code class="response-content" :style="{ opacity: responseOpacity }" v-html="responseHighlightedHtml"></code></pre>
                </section>
            </main>

            <section class="panel examples-panel">
                <h2>Code Examples</h2>
                <div class="code-example-toolbar">
                    <div class="code-example-switches">
                        <button
                            v-for="lang in codeExampleLanguages"
                            :key="lang.id"
                            type="button"
                            class="code-switch"
                            :class="{ active: codeExampleLanguage === lang.id }"
                            @click="codeExampleLanguage = lang.id"
                        >
                            {{ lang.label }}
                        </button>
                    </div>
                    <button type="button" class="copy-code-button" @click="copyCodeSnippet">
                        {{ codeCopied ? 'Copied' : 'Copy' }}
                    </button>
                </div>
                <div class="code-example-container">
                    <pre v-html="codeSnippetHighlightedHtml"></pre>
                </div>
            </section>
        </div>

        <div
            v-if="heroModalOpen"
            class="hero-modal-layer"
        >
            <div class="hero-modal" @click.stop>
                <button type="button" class="hero-close" aria-label="Close hero" @click="closeHeroModal">x</button>
                <p class="hero-kicker">Mock API Playground</p>
                <h1>This is <img :src="topbarLogoSrc" alt="Stubbr" class="hero-logo"></h1>
                <p class="hero-copy">
                    Stop wasting time writing throwaway mocks. Stubbr lets you hit realistic endpoints from day one
                    with payloads you can take straight into production.
                </p>
                <p class="hero-copy">
                    Keep your real request contract, add <code>__instructions</code> for response shape, delay,
                    status, repeat arrays, and random placeholder data. When backend is ready, switch host and keep
                    moving.
                </p>
                <div class="hero-points">
                    <p><strong>Fast setup:</strong> generate a token and start calling <code>/api/your/endpoint</code>.</p>
                    <p><strong>Zero refactor later:</strong> same payload structure in dev and prod.</p>
                    <p><strong>Built for frontend flow:</strong> pagination, repeat groups, faker values, cache control.</p>
                    <p><strong>Made for iteration:</strong> tweak response behavior instantly and keep shipping UI.</p>
                </div>
                <button type="button" class="hero-cta">Start Building</button>
            </div>
        </div>

        <div
            v-if="selectModalOpen"
            class="select-popover-layer"
            @click="closeSelectModal"
        >
            <div class="select-popover" :style="selectPopoverStyle" @click.stop>
                <div class="select-popover-header">{{ selectModalTitle }}</div>
                <div class="select-popover-options">
                    <button
                        v-for="option in selectModalOptions"
                        :key="option.value"
                        type="button"
                        class="select-popover-option"
                        @click="pickSelectOption(option.value)"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <button
        class="theme-picker-button"
        type="button"
        aria-label="Select theme"
        @click="themeMenuOpen = !themeMenuOpen"
    >
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 2s6 6.3 6 10.6A6 6 0 0 1 6 12.6C6 8.3 12 2 12 2Z" />
            <path d="M9.2 14.6a3 3 0 0 0 4.8 2.4" />
        </svg>
    </button>

    <div
        v-if="themeMenuOpen"
        class="theme-picker-layer"
        @click="themeMenuOpen = false"
    >
        <div class="theme-picker-menu" @click.stop>
            <button
                v-for="theme in themeOptions"
                :key="theme.id"
                type="button"
                class="theme-picker-option"
                :class="{ active: activeThemeId === theme.id }"
                @click="selectTheme(theme.id)"
            >
                {{ theme.label }}
            </button>
        </div>
    </div>
</template>
