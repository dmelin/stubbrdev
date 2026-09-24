import { ensureToken, token, tokenKind } from './token';
import { buildPayload, endpointUrl, methodSendsBody } from './builder';

// Sends the endpoint as built. Throws BuilderError for invalid input and a
// plain Error for network failures.
export async function sendEndpoint(endpoint) {
    const payload = buildPayload(endpoint);
    const method = String(endpoint.method).toUpperCase();
    // Browsers refuse to send a body with GET or HEAD, so the builder performs
    // those as POST. Stubbr reads the body on any method, so the reply is the
    // same one a GET from curl or server code would get.
    const wireMethod = methodSendsBody(method) ? method : 'POST';
    const url = endpointUrl(endpoint);

    const run = () => fetch(url, {
        method: wireMethod,
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token.value.trim()}`,
        },
        body: JSON.stringify(payload),
    });

    if (!token.value) await ensureToken();

    const startedAt = performance.now();
    let response = await run();
    if (response.status === 401 && tokenKind.value === 'demo') {
        await ensureToken({ force: true });
        response = await run();
    }
    const raw = await response.text();
    const ms = Math.round(performance.now() - startedAt);

    let data = null;
    let isJson = false;
    if (raw !== '') {
        try {
            data = JSON.parse(raw);
            isJson = true;
        } catch (_error) {
            data = raw;
        }
    }

    const meta = isJson && data && typeof data === 'object' && !Array.isArray(data) ? data.meta : null;

    return {
        status: response.status,
        ok: response.ok,
        ms,
        bytes: new TextEncoder().encode(raw).length,
        raw,
        data,
        isJson,
        empty: raw === '',
        fromCache: response.headers.get('__from_cache') === 'true' || response.headers.get('__from_cache') === '1',
        page: meta && meta.page !== undefined ? Number(meta.page) : null,
        totalPages: meta && meta.total_pages !== undefined ? Number(meta.total_pages) : null,
        flaky: response.headers.get('__flaky'),
        method,
        wireMethod,
        at: Date.now(),
    };
}
