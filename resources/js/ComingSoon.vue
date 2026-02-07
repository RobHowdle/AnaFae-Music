<template>
    <main class="min-h-dvh bg-stone-950 font-sans text-stone-50">
        <div
            class="relative isolate min-h-dvh overflow-hidden"
            :style="heroStyle"
        >
            <div
                class="absolute inset-0 bg-gradient-to-b from-stone-950/30 via-stone-950/65 to-stone-950/90"
            />
            <div class="absolute inset-0 backdrop-blur-[1px]" />

            <div
                class="relative mx-auto flex min-h-dvh max-w-6xl items-center justify-center px-5 py-10 sm:px-8"
            >
                <section class="flex flex-col items-center gap-6 text-center">
                    <template v-if="showLogo && logoSrc">
                        <div
                            class="logoMask h-56 w-[min(92vw,900px)] bg-stone-50/95 sm:h-64 md:h-72"
                            role="img"
                            aria-label="Ana Fae Music"
                            :style="logoMaskStyle"
                        ></div>
                        <img
                            class="logoImg h-56 w-auto mix-blend-screen sm:h-64 md:h-72"
                            :src="logoSrc"
                            alt=""
                            aria-hidden="true"
                            @error="showLogo = false"
                        />
                    </template>
                    <div v-else class="text-2xl font-medium tracking-tight">
                        Ana Fae Music
                    </div>

                    <div
                        class="rounded-2xl border border-stone-50/10 bg-stone-950/20 px-8 py-5 backdrop-blur"
                    >
                        <p
                            class="font-heading text-3xl font-medium tracking-wide text-stone-50/90 sm:text-4xl"
                        >
                            Coming soon
                        </p>
                        <div class="mx-auto mt-3 h-px w-16 bg-stone-50/25" />
                        <p
                            class="mt-3 text-[11px] font-medium uppercase tracking-[0.38em] text-stone-50/70"
                        >
                            Ana Fae Music
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </main>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";

const showLogo = ref(false);

const heroUrl = ref("");
const logoUrl = ref("");

const heroCandidates = [
    "/coming-soon/hero.webp",
    "/coming-soon/hero.jpg",
    "/coming-soon/hero.jpeg",
    "/coming-soon/hero.png",
];

const logoCandidates = [
    "/coming-soon/logo.png",
    "/coming-soon/logo.svg",
    "/coming-soon/logo.webp",
    "/coming-soon/logo.jpg",
    "/coming-soon/logo.jpeg",
];

function probeImage(url) {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => resolve(true);
        img.onerror = () => resolve(false);
        img.src = url;
    });
}

async function pickFirstAvailable(urls) {
    for (const url of urls) {
        // eslint-disable-next-line no-await-in-loop
        const ok = await probeImage(url);
        if (ok) return url;
    }
    return "";
}

// Optional assets (drop files into /public/coming-soon/):
// - hero.(webp|jpg|jpeg|png)
// - logo.(svg|png|webp|jpg|jpeg)
const heroStyle = computed(() => {
    const gradient =
        "linear-gradient(to bottom, rgba(12,10,9,0.05), rgba(12,10,9,0.55), rgba(12,10,9,0.85))";

    return {
        backgroundImage: heroUrl.value
            ? `${gradient}, url('${heroUrl.value}')`
            : gradient,
        backgroundPosition: "center",
        backgroundRepeat: "no-repeat",
        backgroundSize: "cover",
    };
});

const logoSrc = computed(() => logoUrl.value || "");

const logoMaskStyle = computed(() => {
    if (!logoSrc.value) return {};

    return {
        WebkitMaskImage: `url('${logoSrc.value}')`,
        WebkitMaskRepeat: "no-repeat",
        WebkitMaskPosition: "center",
        WebkitMaskSize: "contain",
        WebkitMaskMode: "luminance",
        maskImage: `url('${logoSrc.value}')`,
        maskRepeat: "no-repeat",
        maskPosition: "center",
        maskSize: "contain",
        maskMode: "luminance",
    };
});

onMounted(async () => {
    heroUrl.value = await pickFirstAvailable(heroCandidates);
    logoUrl.value = await pickFirstAvailable(logoCandidates);

    showLogo.value = Boolean(logoUrl.value);
});
</script>

<style scoped>
.logoMask {
    display: none;
}

@supports ((-webkit-mask-image: url("")) or (mask-image: url(""))) {
    .logoMask {
        display: block;
    }

    .logoImg {
        display: none;
    }
}
</style>
