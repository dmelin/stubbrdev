<script setup>
import { ref } from 'vue';
import { sanitizeEndpoint } from '../lib/builder';
import { navigate } from '../lib/router';
import { addEndpoint } from '../lib/workspace';
import LiveBuilder from '../components/builder/LiveBuilder.vue';
import BrandMark from '../components/ui/BrandMark.vue';
import ThemeToggle from '../components/ui/ThemeToggle.vue';
import UiButton from '../components/ui/UiButton.vue';

const live = ref(null);

// Carries the hero's current stub into the workspace and opens the builder.
const buildStub = () => {
    const source = live.value?.endpoint;
    if (source) {
        const copy = sanitizeEndpoint(JSON.parse(JSON.stringify(source)));
        copy.name = copy.name || 'demo';
        addEndpoint(copy);
    }
    navigate('/builder');
};

const values = [
    { numeral: '500', tone: 'accent', title: 'Fail on demand', copy: 'Any status code, on any route, whenever you ask for it.' },
    { numeral: '2s', tone: 'ok', title: 'Real-world slowness', copy: 'Find the missing loading state before your users do.' },
    { numeral: '∞', tone: 'info', title: 'Outlives the mock phase', copy: 'Keep one route stubbed while the rest go live.' },
];
</script>

<template>
    <div class="landing">
        <header class="landing-header wrap">
            <BrandMark version />
            <nav class="landing-nav" aria-label="Primary">
                <RouterLink to="/docs" class="landing-link">Docs</RouterLink>
                <RouterLink to="/docs#recipes" class="landing-link">Recipes</RouterLink>
                <a class="landing-link" href="https://github.com/dmelin/stubbrdev" target="_blank" rel="noopener noreferrer">GitHub</a>
                <ThemeToggle />
                <UiButton variant="primary" to="/builder">Open builder</UiButton>
            </nav>
        </header>

        <section class="hero wrap">
            <div class="hero-copy">
                <span class="tag tag-lg" data-tone="accent">★ no signup, no cleanup</span>
                <h1>Fake the API. <em>Break</em> the API. Keep it forever.</h1>
                <p class="hero-lede">
                    An instant mock API that simulates failed responses, latency and awkward payloads,
                    and that you don't have to rip out once the real backend arrives.
                </p>
                <div class="hero-actions">
                    <UiButton variant="primary" size="lg" @click="buildStub">Build a stub →</UiButton>
                    <UiButton variant="secondary" size="lg" to="/docs#recipes">See recipes</UiButton>
                </div>
                <ul class="hero-caps" aria-label="Capabilities">
                    <li class="cap" data-tone="ok">?uuid</li>
                    <li class="cap" data-tone="ok">?name</li>
                    <li class="cap" data-tone="warn">status: 500</li>
                    <li class="cap" data-tone="info">delay: 2000</li>
                    <li class="cap" data-tone="accent">__repeat: 30</li>
                </ul>
            </div>
            <LiveBuilder ref="live" class="hero-live" />
        </section>

        <section class="values wrap" aria-label="Why Stubbr">
            <article v-for="value in values" :key="value.title" class="card card-pad value">
                <div class="big-numeral" :data-tone="value.tone">{{ value.numeral }}</div>
                <h4>{{ value.title }}</h4>
                <p>{{ value.copy }}</p>
            </article>
        </section>

        <footer class="landing-foot wrap">
            <span>Built with care by Daniel Melin.</span>
            <a href="https://github.com/dmelin/stubbrdev" target="_blank" rel="noopener noreferrer">GitHub</a>
            <a href="https://buymeacoffee.com/melin" target="_blank" rel="noopener noreferrer">☕ Buy me a coffee</a>
        </footer>
    </div>
</template>

<style scoped>
.landing {
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
}

.wrap {
    width: min(1180px, 100%);
    margin-inline: auto;
    padding-inline: 28px;
}

.landing-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding-block: 18px;
}

.landing-nav {
    display: flex;
    align-items: center;
    gap: 8px;
}

.landing-link {
    min-height: 36px;
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: var(--r-sm);
    font: 500 13.5px var(--font-ui);
    color: var(--text-2);
}

.landing-link:hover {
    color: var(--text);
}

.hero {
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
    gap: 44px;
    align-items: center;
    padding-block: 52px 44px;
}

.hero-copy,
.hero-live {
    min-width: 0;
}

.hero h1 {
    margin-top: 18px;
    font-size: clamp(38px, 5vw, 60px);
    line-height: 1.02;
    letter-spacing: -0.035em;
}

.hero h1 em {
    font-style: normal;
    color: var(--accent-text);
}

.hero-lede {
    margin-top: 20px;
    max-width: 46ch;
    font-size: 17px;
    line-height: 1.6;
    color: var(--text-2);
}

.hero-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.hero-caps {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.cap {
    padding: 6px 11px;
    border-radius: 7px;
    background: var(--neutral-tint);
    border: 1px solid var(--border);
    font: 500 11.5px var(--font-mono);
    color: var(--text-2);
}

.cap[data-tone='ok'] {
    color: var(--ok-text);
}

.cap[data-tone='warn'] {
    color: var(--warn-text);
}

.cap[data-tone='info'] {
    color: var(--info-text);
}

.cap[data-tone='accent'] {
    color: var(--accent-text);
}

.values {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    padding-block: 8px 40px;
}

.value h4 {
    margin-top: 6px;
    font: 600 14.5px var(--font-ui);
}

.value p {
    margin-top: 6px;
    font: 400 13px/1.55 var(--font-ui);
    color: var(--text-2);
}

.big-numeral[data-tone='accent'] {
    color: var(--accent-text);
}

.big-numeral[data-tone='ok'] {
    color: var(--ok-text);
}

.big-numeral[data-tone='info'] {
    color: var(--info-text);
}

.landing-foot {
    margin-top: auto;
    display: flex;
    gap: 18px;
    flex-wrap: wrap;
    padding-block: 18px 26px;
    font: 500 12.5px var(--font-ui);
    color: var(--text-3);
}

.landing-foot a {
    color: var(--text-2);
    min-height: 24px;
}

.landing-foot a:hover {
    color: var(--text);
}

@media (max-width: 1000px) {
    .hero {
        grid-template-columns: minmax(0, 1fr);
        gap: 44px;
        padding-top: 36px;
    }

    .hero-live {
        margin-top: 8px;
    }
}

@media (max-width: 760px) {
    .wrap {
        padding-inline: 18px;
    }

    .landing-header {
        flex-wrap: wrap;
    }

    .landing-nav {
        flex-wrap: wrap;
    }

    .values {
        grid-template-columns: 1fr;
    }
}
</style>
