<script setup>
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from "vue";

const rootEl = ref(null);
const pageSlug = computed(() => rootEl.value?.dataset?.pageSlug || "home");

const loading = ref(true);
const loadError = ref(null);
const page = ref(null);
const sections = ref([]);

const activeAnchor = ref(null);
const isMobileMenuOpen = ref(false);
const isVideoOpen = ref(false);

const scrollEl = ref(null);

const navItems = computed(() => [
    { anchor: "about", label: "About" },
    { anchor: "services", label: "Services" },
    { anchor: "contact", label: "Contact" },
]);

const DEFAULT_HOME_BG_URL = "/images/home_bg.jpg";
const ABOUT_BG_URL = "/images/about_bg.jpg";
const SERVICES_BG_URL = "/images/services_bg.jpg";
const CONTACT_BG_URL = "/images/contact_bg.jpg";
const ABOUT_IMG_URL = "/images/about_img.jpeg";
const CONTACT_IMG_URL = "/images/contact-img.jpeg";

const setlistTitleOverrides = {
    picknmix: "Pick 'n' Mix",
    "80splayist": "80s Playlist",
};

const setlistFileNames = [
    "40s to Noughties Poster.png",
    "80splayist.jpeg",
    "90s Nostalgia.png",
    "Heart Songs.png",
    "Noughties Nostalgia poster.png",
    "Rock rebels.png.jpeg",
    "picknmix.png",
];

function stripKnownExtensions(value) {
    let normalized = String(value || "");
    const extensionPattern = /\.(png|jpe?g|webp|avif)$/i;

    while (extensionPattern.test(normalized)) {
        normalized = normalized.replace(extensionPattern, "");
    }

    return normalized;
}

function formatSetlistTitle(fileName) {
    const baseName = stripKnownExtensions(fileName);
    const override = setlistTitleOverrides[baseName];
    if (override) return override;

    return baseName
        .replace(/[-_]+/g, " ")
        .replace(/\s+/g, " ")
        .trim()
        .replace(/\b([a-z])/g, (match) => match.toUpperCase());
}

function slugifySetlistId(fileName) {
    return stripKnownExtensions(fileName)
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "");
}

const setlists = setlistFileNames
    .map((fileName) => {
        const baseName = stripKnownExtensions(fileName);

        return {
            id: slugifySetlistId(fileName) || baseName,
            title: formatSetlistTitle(fileName),
            imageUrl: `/images/setlists/${encodeURIComponent(fileName)}`,
        };
    })
    .sort((a, b) =>
        a.title.localeCompare(b.title, undefined, { numeric: true }),
    );

const LMM_PROFILE_URL =
    "https://www.lastminutemusicians.com/members/anafae.html";
const LAST_MINUTE_MUSICIANS_URL = LMM_PROFILE_URL;

const SECTION_ORDER = ["home", "about", "services", "contact"];

function bgForAnchor(anchor) {
    switch (anchor) {
        case "home":
            return DEFAULT_HOME_BG_URL;
        case "about":
            return ABOUT_BG_URL;
        case "services":
            return SERVICES_BG_URL;
        case "contact":
            return CONTACT_BG_URL;
        default:
            return null;
    }
}

const activeSection = computed(() => {
    const anchor = activeAnchor.value || sections.value[0]?.anchor;
    return sections.value.find((s) => s.anchor === anchor) || null;
});

const backgroundUrl = computed(() => {
    const section = activeSection.value;
    const local = bgForAnchor(section?.anchor);
    if (local) return local;
    if (section?.background_image_url) return section.background_image_url;
    return DEFAULT_HOME_BG_URL;
});

const backgroundPosition = computed(() => {
    return activeSection.value?.background_position || "center";
});

// All sections share the same soft overlay treatment.
const backgroundImageOpacityFor = () => 0.65;

const previousBgUrl = ref(null);
const previousBgPosition = ref("center");
const previousBgOpacity = ref(1);

const currentBgUrl = ref(DEFAULT_HOME_BG_URL);
const currentBgPosition = ref("center");
const currentBgOpacity = ref(1);

const bgFadeIn = ref(true);
let bgTransitionTimer = null;

watch(
    [backgroundUrl, backgroundPosition],
    async ([newUrl, newPos], [oldUrl, oldPos]) => {
        if (!oldUrl) {
            currentBgUrl.value = newUrl;
            currentBgPosition.value = newPos || "center";
            currentBgOpacity.value = backgroundImageOpacityFor(newUrl);
            previousBgUrl.value = null;
            bgFadeIn.value = true;
            return;
        }

        if (newUrl === oldUrl && newPos === oldPos) return;

        previousBgUrl.value = oldUrl;
        previousBgPosition.value = oldPos || "center";
        previousBgOpacity.value = backgroundImageOpacityFor(oldUrl);

        currentBgUrl.value = newUrl;
        currentBgPosition.value = newPos || "center";
        currentBgOpacity.value = backgroundImageOpacityFor(newUrl);

        bgFadeIn.value = false;
        await nextTick();
        requestAnimationFrame(() => {
            bgFadeIn.value = true;
        });

        if (bgTransitionTimer) clearTimeout(bgTransitionTimer);
        bgTransitionTimer = setTimeout(() => {
            previousBgUrl.value = null;
        }, 1100);
    },
    { immediate: true },
);

const HERO_LOGO_URL = "/images/logo.png";
const HERO_VIDEO_URL = "/videos/promo_video.mp4";
const HERO_EXTERNAL_URL =
    "https://www.northeastweddingnetwork.co.uk/ana-fae-music";
const HERO_EXTERNAL_ICON_URL = "/images/wedding_network.png";

const servicesTabs = [
    {
        id: "weddings",
        label: "Weddings",
        items: [
            {
                id: "forget-me-not",
                label: "Forget-Me-Not",
                title: "Wedding Packages from £200.00",
                subtitle: "Forget-Me-Not Package:",
                body: "A beautifully personal soundtrack for your ceremony. Choose any song for your walk down the aisle, followed by up to three carefully selected songs while you and your guests enjoy the signing of the register. Perfect for couples seeking an intimate and elegant musical moment.",
                buttonLabel: "Book Now",
                buttonAnchor: "contact",
                imageUrl: "/images/forget-me-not-package.jpeg",
                belowButtonImageUrl: "/images/forget-me-not.png",
            },
            {
                id: "daisy",
                label: "Daisy",
                title: "Wedding Packages from £200.00",
                subtitle: "Daisy Package:",
                body: "An hour of live music to accompany your drinks reception or wedding breakfast.\nIncludes up to five bespoke song requests, tailored especially to you and your day.\nLight, uplifting, and effortlessly refined.",
                buttonLabel: "Book Now",
                buttonAnchor: "contact",
                imageUrl: "/images/daisy-package.jpeg",
                belowButtonImageUrl: "/images/daisy.png",
            },
            {
                id: "foxglove",
                label: "Foxglove",
                title: "Wedding Packages from £200.00",
                subtitle: "Foxglove Package:",
                body: "A seamless musical journey through your day.\nUp to one hour of live music during your drinks reception, followed by up to one hour during your wedding breakfast — with the option to conclude with your chosen first dance song.\nIdeal for couples who want music woven naturally into their celebration.",
                buttonLabel: "Book Now",
                buttonAnchor: "contact",
                imageUrl: "/images/foxglove-package.jpeg",
                belowButtonImageUrl: "/images/foxglove.png",
            },
            {
                id: "evening-primrose",
                label: "Evening Primrose",
                title: "Wedding Packages from £200.00",
                subtitle: "Evening Primrose Package:",
                body: "Two 45-minute live sets designed to bring the evening to life.\nSelect from any of my curated set lists, with up to five personalised song requests.\nYour own chosen playlist will play between sets to keep the atmosphere flowing.\nA first dance song may be added as an optional extra.",
                buttonLabel: "Book Now",
                buttonAnchor: "contact",
                imageUrl: "/images/epp-package.jpeg",
                belowButtonImageUrl: "/images/primrose.png",
            },
            {
                id: "the-full-bouquet",
                label: "The Full Bouquet",
                title: "Wedding Packages from £200.00",
                subtitle: "The Full Bouquet:",
                body: "The complete musical experience for your wedding day.\nA chosen song for your walk down the aisle, plus up to three songs during the signing of the register\nUp to one hour of live music during your drinks reception\nUp to one hour during your wedding breakfast, optionally ending with your first dance\nTwo 45-minute evening sets to get the party started\nUp to five bespoke song requests\nA personalised playlist to play between live sets\nOptional bespoke evening playlist to keep the celebration going late into the night\nThoughtfully curated, entirely personal, and effortlessly elegant.",
                buttonLabel: "Book Now",
                buttonAnchor: "contact",
                imageUrl: "/images/tfb-package.png",
                belowButtonImageUrl: "/images/bouquet.png",
            },
        ],
    },
    {
        id: "wedding-song-writing",
        label: "Wedding Song Writing",
        items: [
            {
                id: "wedding-song-writing",
                label: "Wedding Song Writing",
                title: "Wedding Song Writing",
                subtitle: "Your Story, Set to Music",
                body: "For something truly extraordinary, share your story and four of your most treasured songs, and allow me to compose a short, original piece inspired entirely by you.\n\nExpertly written and thoughtfully arranged, your bespoke song will reflect the essence of your journey together — intimate, meaningful, and unmistakably yours.\n\nPerfect as a first dance or a graceful accompaniment to your walk down the aisle.\n\nYou will receive a professionally recorded digital copy to cherish forever, with the option to have the piece performed live on your wedding day.",
                buttonLabel: "Book Now",
                buttonAnchor: "contact",
                imageUrl: "/images/wedding-song-writing.jpg",
                belowButtonImageUrl: null,
            },
        ],
    },
    {
        id: "private-parties",
        label: "Private Parties",
        items: [
            {
                id: "private-parties",
                label: "Private Parties",
                title: "Private Parties",
                subtitle: "Live music for every occasion",
                body: "Details coming soon.",
                buttonLabel: "Book Now",
                buttonAnchor: "contact",
                imageUrl: "/images/private-parties.png",
                belowButtonImageUrl: null,
            },
        ],
    },
    {
        id: "dj-services",
        label: "DJ Services",
        items: [
            {
                id: "dj-services",
                label: "DJ Services",
                title: "Live Playlist Curation (Coming Soon)",
                subtitle: "Curated Sound Design",
                body: "Every unforgettable evening has a signature sound.\n\nWorking with your chosen songs and inspirations, I craft a tailored musical backdrop designed to shape the atmosphere from the very first note. The energy evolves naturally throughout the night, guided with care to ensure the room always feels exactly as it should.\n\nRequests are embraced, transitions are intentional, and the flow remains uninterrupted — allowing you to host with confidence while the music quietly works its magic.",
                buttonLabel: "Book Now",
                buttonAnchor: "contact",
                imageUrl: "/images/playlist-curration.jpeg",
                belowButtonImageUrl: null,
            },
        ],
    },
];

const activeServicesTabId = ref(servicesTabs[0]?.id || "weddings");
const activeServicesItemId = ref(servicesTabs[0]?.items?.[0]?.id || null);

const activeServicesTab = computed(
    () => servicesTabs.find((t) => t.id === activeServicesTabId.value) || null,
);

const showSetlistsButton = computed(() =>
    ["private-parties", "dj-services"].includes(activeServicesTabId.value),
);

const activeServicesItem = computed(() => {
    const tab = activeServicesTab.value;
    if (!tab) return null;
    const itemId =
        activeServicesItemId.value || tab.items?.[0]?.id || tab.id || null;
    return tab.items.find((i) => i.id === itemId) || tab.items[0] || null;
});

function selectServicesTab(tabId) {
    const tab = servicesTabs.find((t) => t.id === tabId);
    if (!tab) return;
    activeServicesTabId.value = tab.id;
    activeServicesItemId.value = tab.items?.[0]?.id || null;
}

function selectServicesItem(itemId) {
    activeServicesItemId.value = itemId;
}

const contactForm = ref({
    name: "",
    email: "",
    contactNumber: "",
    service: "",
    message: "",
});

const contactFormInitialState = () => ({
    name: "",
    email: "",
    contactNumber: "",
    service: "",
    message: "",
});

const isSubmittingContactForm = ref(false);
const contactFormErrors = ref({});
const contactFormStatus = ref({ type: "", message: "" });

function normalizeServiceLabel(value) {
    return String(value || "")
        .trim()
        .replace(/\s+/g, " ");
}

function humanizePackageName(value) {
    return normalizeServiceLabel(value).replace(/-/g, " ");
}

function serviceLabelFor(tab, item) {
    const tabId = String(tab?.id || "");
    const tabLabel = normalizeServiceLabel(tab?.label || "");
    const itemLabel = normalizeServiceLabel(item?.label || "");
    const itemTitle = normalizeServiceLabel(item?.title || "");

    if (tabId === "weddings") {
        const pkg = humanizePackageName(itemLabel || itemTitle || tabLabel);
        return pkg ? `Wedding - ${pkg} Package` : "Wedding";
    }

    if (tabId === "wedding-song-writing") {
        return "Wedding - Song Writing";
    }

    if (tabId === "private-parties") {
        return "Private Parties";
    }

    if (tabId === "dj-services") {
        const base = tabLabel || "DJ Services";
        const detail = (itemTitle || itemLabel)
            .replace(/\s*\(Coming Soon\)\s*/i, "")
            .trim();
        return detail && detail !== base ? `${base} - ${detail}` : base;
    }

    if (tabLabel && itemLabel && itemLabel !== tabLabel) {
        return `${tabLabel} - ${itemLabel}`;
    }

    return tabLabel || itemLabel || "";
}

const contactServiceOptions = computed(() => {
    const options = [];

    for (const tab of servicesTabs) {
        for (const item of tab?.items || []) {
            const label = serviceLabelFor(tab, item);
            if (label) options.push(label);
        }
    }

    if (contactForm.value.service) {
        options.push(String(contactForm.value.service));
    }

    const unique = Array.from(
        new Set(options.map((o) => normalizeServiceLabel(o))),
    ).filter(Boolean);

    return ["General Enquiry", ...unique, "Other"];
});

function bookNowFromServices() {
    const targetAnchor = activeServicesItem?.value?.buttonAnchor || "contact";

    if (targetAnchor === "contact") {
        const label = serviceLabelFor(
            activeServicesTab.value,
            activeServicesItem.value,
        );
        if (label) {
            contactForm.value.service = label;
        }
    }

    scrollToAnchor(targetAnchor);
}

async function submitContactForm() {
    if (isSubmittingContactForm.value) return;

    isSubmittingContactForm.value = true;
    contactFormErrors.value = {};
    contactFormStatus.value = { type: "", message: "" };

    try {
        const { data } = await window.axios.post("/api/contact", {
            ...contactForm.value,
        });

        contactForm.value = contactFormInitialState();
        contactFormStatus.value = {
            type: "success",
            message: data?.message || "Thanks, your message has been sent.",
        };
    } catch (error) {
        if (error?.response?.status === 422) {
            contactFormErrors.value = error.response.data?.errors || {};
            contactFormStatus.value = {
                type: "error",
                message: "Please check the highlighted fields and try again.",
            };
        } else {
            contactFormStatus.value = {
                type: "error",
                message:
                    "Something went wrong sending your message. Please try again.",
            };
        }
    } finally {
        isSubmittingContactForm.value = false;
    }
}

const lmmReviews = ref([]);
const lmmReviewsLoading = ref(false);
const lmmReviewsError = ref(null);
const activeReviewIndex = ref(0);

const isSetlistsOpen = ref(false);
const activeSetlistIndex = ref(0);

const activeSetlist = computed(() => {
    if (!setlists.length) return null;
    const idx = Math.max(
        0,
        Math.min(setlists.length - 1, activeSetlistIndex.value),
    );
    return setlists[idx] || null;
});

function openSetlists() {
    if (!setlists.length) return;
    isSetlistsOpen.value = true;
    activeSetlistIndex.value = 0;
}

function closeSetlists() {
    isSetlistsOpen.value = false;
}

watch(activeServicesTabId, () => {
    if (!showSetlistsButton.value) {
        closeSetlists();
    }
});

function prevSetlist() {
    if (!setlists.length) return;
    activeSetlistIndex.value =
        (activeSetlistIndex.value - 1 + setlists.length) % setlists.length;
}

function nextSetlist() {
    if (!setlists.length) return;
    activeSetlistIndex.value = (activeSetlistIndex.value + 1) % setlists.length;
}

const activeReview = computed(() => {
    if (!lmmReviews.value.length) return null;
    const idx = Math.max(
        0,
        Math.min(lmmReviews.value.length - 1, activeReviewIndex.value),
    );
    return lmmReviews.value[idx] || null;
});

function prevReview() {
    if (!lmmReviews.value.length) return;
    activeReviewIndex.value =
        (activeReviewIndex.value - 1 + lmmReviews.value.length) %
        lmmReviews.value.length;
}

function nextReview() {
    if (!lmmReviews.value.length) return;
    activeReviewIndex.value =
        (activeReviewIndex.value + 1) % lmmReviews.value.length;
}

async function loadLmmReviews() {
    lmmReviewsLoading.value = true;
    lmmReviewsError.value = null;

    try {
        const res = await fetch("/api/reviews/lmm?limit=8", {
            headers: { Accept: "application/json" },
        });
        if (!res.ok) {
            throw new Error(`Failed to load reviews (${res.status})`);
        }

        const data = await res.json();
        const reviews = Array.isArray(data?.reviews) ? data.reviews : [];

        const items = reviews
            .map((r) => {
                const text = String(r?.text || "").trim();
                const authorRaw = String(r?.author || "").trim();
                const ratingNum = Number(r?.rating);
                return {
                    text,
                    author: authorRaw ? authorRaw : null,
                    rating: Number.isFinite(ratingNum) ? ratingNum : null,
                };
            })
            .filter((r) => r.text);

        lmmReviews.value = items;
        activeReviewIndex.value = 0;

        if (!lmmReviews.value.length) {
            lmmReviewsError.value = "No reviews could be loaded.";
        }
    } catch (err) {
        lmmReviewsError.value =
            err instanceof Error ? err.message : String(err);
    } finally {
        lmmReviewsLoading.value = false;
    }
}

const socialLinks = computed(() => {
    const raw = page.value?.social_links;
    if (Array.isArray(raw) && raw.length) {
        return raw
            .filter((item) => item && typeof item === "object")
            .map((item) => ({
                type: String(item.type || "").toLowerCase(),
                url: String(item.url || ""),
                label: String(item.label || item.type || ""),
            }))
            .filter((item) => item.type && item.url);
    }

    return [
        {
            type: "instagram",
            url: "https://www.instagram.com/anafae.music",
            label: "Instagram",
        },
        {
            type: "facebook",
            url: "https://www.facebook.com/profile.php?id=61560627804645",
            label: "Facebook",
        },
        {
            type: "youtube",
            url: "https://www.youtube.com/@anafae_music",
            label: "YouTube",
        },
        {
            type: "email",
            url: "mailto:anafae.music@gmail.com",
            label: "Email",
        },
    ];
});

function getSocialIconPath(type) {
    switch (type) {
        case "instagram":
            return [
                {
                    d: "M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Z",
                },
                { d: "M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z" },
                { d: "M17.5 6.5h.01" },
            ];
        case "facebook":
            return [
                {
                    d: "M14 8h2V5h-2a4 4 0 0 0-4 4v3H8v3h2v7h3v-7h2.2l.8-3H13V9a1 1 0 0 1 1-1Z",
                },
            ];
        case "youtube":
            return [
                {
                    d: "M10 9.75v4.5L14 12l-4-2.25Z",
                    fill: "currentColor",
                    stroke: "none",
                },
                {
                    d: "M21.6 7.2a3 3 0 0 0-2.1-2.1C17.8 4.6 12 4.6 12 4.6s-5.8 0-7.5.5a3 3 0 0 0-2.1 2.1A31.6 31.6 0 0 0 2 12a31.6 31.6 0 0 0 .4 4.8 3 3 0 0 0 2.1 2.1c1.7.5 7.5.5 7.5.5s5.8 0 7.5-.5a3 3 0 0 0 2.1-2.1A31.6 31.6 0 0 0 22 12a31.6 31.6 0 0 0-.4-4.8Z",
                },
            ];
        case "email":
            return [{ d: "M4 6h16v12H4V6Z" }, { d: "m4 7 8 6 8-6" }];
        default:
            // Generic link icon
            return [
                {
                    d: "M10 13a5 5 0 0 1 0-7l1.5-1.5a5 5 0 0 1 7 7L17 13",
                },
                {
                    d: "M14 11a5 5 0 0 1 0 7L12.5 19.5a5 5 0 0 1-7-7L7 11",
                },
            ];
    }
}

function openVideo() {
    isVideoOpen.value = true;
}

function closeVideo() {
    isVideoOpen.value = false;
}

let scrollRaf = 0;

function syncActiveFromScroll() {
    const el = scrollEl.value;
    if (!el) return;
    const viewport = el.clientHeight || window.innerHeight || 1;
    const scrollTop = el.scrollTop || 0;
    const idx = Math.max(
        0,
        Math.min(
            sections.value.length - 1,
            Math.floor((scrollTop + viewport / 2) / viewport),
        ),
    );
    const anchor = sections.value[idx]?.anchor;
    if (anchor) setActive(anchor);
}

function onScroll() {
    if (scrollRaf) cancelAnimationFrame(scrollRaf);
    scrollRaf = requestAnimationFrame(() => {
        syncActiveFromScroll();
        scrollRaf = 0;
    });
}

function setActive(anchor) {
    if (!anchor || anchor === activeAnchor.value) return;
    activeAnchor.value = anchor;
}

function scrollToAnchor(anchor) {
    const target = document.getElementById(anchor);
    if (!target) return;

    setActive(anchor);
    target.scrollIntoView({ behavior: "smooth", block: "start" });
    history.replaceState(null, "", `#${anchor}`);
    isMobileMenuOpen.value = false;
}

async function fetchPage() {
    loading.value = true;
    loadError.value = null;

    try {
        const response = await fetch(
            `/api/pages/${encodeURIComponent(pageSlug.value)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        if (!response.ok) {
            throw new Error(`Failed to load page: ${response.status}`);
        }

        const data = await response.json();
        page.value = data;
        const rawSections = Array.isArray(data.sections) ? data.sections : [];
        sections.value = rawSections.slice().sort((a, b) => {
            const ai = SECTION_ORDER.indexOf(a?.anchor);
            const bi = SECTION_ORDER.indexOf(b?.anchor);
            const as = ai === -1 ? 999 : ai;
            const bs = bi === -1 ? 999 : bi;
            if (as !== bs) return as - bs;
            return String(a?.anchor || "").localeCompare(
                String(b?.anchor || ""),
            );
        });

        await nextTick();

        const hashAnchor = location.hash && location.hash.replace(/^#/, "");
        const initialAnchor =
            hashAnchor ||
            (sections.value.some((s) => s.anchor === "home")
                ? "home"
                : sections.value[0]?.anchor) ||
            null;
        if (initialAnchor) {
            setActive(initialAnchor);
            if (hashAnchor) {
                scrollToAnchor(initialAnchor);
            }
        }
    } catch (err) {
        loadError.value = err instanceof Error ? err.message : String(err);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchPage();

    nextTick(() => {
        if (scrollEl.value) {
            scrollEl.value.addEventListener("scroll", onScroll, {
                passive: true,
            });
            syncActiveFromScroll();
        }
    });

    window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeVideo();
            closeSetlists();
        }
    });

    nextTick(() => {
        loadLmmReviews();
    });
});

onBeforeUnmount(() => {
    if (bgTransitionTimer) clearTimeout(bgTransitionTimer);
    if (scrollRaf) cancelAnimationFrame(scrollRaf);
    if (scrollEl.value) scrollEl.value.removeEventListener("scroll", onScroll);
});
</script>

<template>
    <div ref="rootEl" class="relative h-screen overflow-hidden text-white">
        <!-- Full-page background image (stored locally in /public/images) -->
        <div class="fixed inset-0 -z-10" aria-hidden="true">
            <!-- Homepage-only overlay behind the image -->
            <div
                class="absolute inset-0 transition-opacity duration-700"
                :class="'bg-softLavender opacity-100'"
            />

            <!-- Background crossfade layers -->
            <div
                v-if="previousBgUrl"
                class="absolute inset-0 bg-cover bg-no-repeat transition-opacity duration-[1100ms] ease-out"
                :style="{
                    backgroundImage: `url(${previousBgUrl})`,
                    backgroundPosition: previousBgPosition,
                    opacity: bgFadeIn ? 0 : previousBgOpacity,
                }"
            />
            <div
                class="absolute inset-0 bg-cover bg-no-repeat transition-opacity duration-[1100ms] ease-out"
                :style="{
                    backgroundImage: `url(${currentBgUrl})`,
                    backgroundPosition: currentBgPosition,
                    opacity: bgFadeIn ? currentBgOpacity : 0,
                }"
            />
        </div>

        <!-- Top nav -->
        <header class="fixed inset-x-0 top-0 z-30">
            <div
                class="absolute inset-0 bg-mutedLilac/50 blur-xl"
                aria-hidden="true"
            />

            <div
                class="relative mx-auto w-full max-w-[1700px] px-4 py-6 sm:px-6 min-[641px]:px-16 md:px-32"
            >
                <!-- Desktop nav -->
                <nav
                    class="hidden w-full items-center justify-between text-[1.5rem] text-white md:flex"
                >
                    <a
                        v-for="item in navItems"
                        :key="item.anchor"
                        :href="`#${item.anchor}`"
                        class="relative font-heading font-bold uppercase transition-opacity duration-700 ease-out after:absolute after:-bottom-1 after:left-0 after:h-px after:w-full after:origin-left after:scale-x-0 after:bg-white/80 after:transition-transform after:duration-700 after:ease-out hover:after:scale-x-100"
                        :class="
                            activeAnchor === item.anchor
                                ? 'opacity-100 after:scale-x-100'
                                : 'opacity-80 hover:opacity-100'
                        "
                        @click.prevent="scrollToAnchor(item.anchor)"
                    >
                        {{ item.label }}
                    </a>
                </nav>

                <!-- Mobile header (logo centered + burger) -->
                <div class="relative flex items-center md:hidden">
                    <a
                        href="#home"
                        class="absolute left-1/2 -translate-x-1/2"
                        @click.prevent="scrollToAnchor('home')"
                        aria-label="Ana Fae Music"
                    >
                        <img
                            :src="HERO_LOGO_URL"
                            alt="Ana Fae Music"
                            class="h-24 w-auto max-w-[70vw] object-contain"
                        />
                    </a>

                    <div class="ml-auto flex items-center justify-end">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-md p-2 text-white transition-opacity duration-700 ease-out hover:opacity-90"
                            :aria-expanded="isMobileMenuOpen ? 'true' : 'false'"
                            aria-controls="mobile-nav"
                            aria-label="Open menu"
                            @click="isMobileMenuOpen = !isMobileMenuOpen"
                        >
                            <svg
                                width="28"
                                height="28"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <template v-if="!isMobileMenuOpen">
                                    <path
                                        d="M4 7h16"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M4 12h16"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M4 17h16"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </template>
                                <template v-else>
                                    <path
                                        d="M6 6l12 12"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M18 6L6 18"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </template>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile slide-out menu (right) -->
        <aside
            id="mobile-nav"
            class="fixed right-0 top-0 z-20 h-full w-72 transform bg-mutedLilac transition-transform duration-300 ease-out md:hidden"
            :class="isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full'"
        >
            <div class="px-6 pb-8 pt-20">
                <nav class="flex flex-col gap-6">
                    <a
                        v-for="item in navItems"
                        :key="item.anchor"
                        :href="`#${item.anchor}`"
                        class="relative font-heading text-[1.5rem] text-white/90 transition-all duration-700 ease-out hover:text-white after:absolute after:-bottom-1 after:left-0 after:h-px after:w-full after:origin-left after:scale-x-0 after:bg-white/80 after:transition-transform after:duration-700 after:ease-out hover:after:scale-x-100"
                        @click.prevent="scrollToAnchor(item.anchor)"
                    >
                        {{ item.label }}
                    </a>
                </nav>

                <div class="mt-10 flex items-center gap-5">
                    <a
                        v-for="link in socialLinks"
                        :key="`${link.type}:${link.url}`"
                        :href="link.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-deepPurple text-softLavender shadow-soft-lavender transition-all duration-700 ease-out hover:bg-softLavender hover:text-deepPurple hover:shadow-deep-purple"
                        :aria-label="link.label || link.type"
                    >
                        <svg
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                v-for="(p, idx) in getSocialIconPath(link.type)"
                                :key="idx"
                                :d="p.d"
                                :fill="p.fill || 'none'"
                                :stroke="p.stroke || 'currentColor'"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <main
            ref="scrollEl"
            class="h-screen snap-y snap-mandatory overflow-y-auto overflow-x-hidden scroll-smooth"
        >
            <!-- Video modal -->
            <div
                v-if="isVideoOpen"
                class="fixed inset-0 z-50 flex items-center justify-center"
                role="dialog"
                aria-modal="true"
                @click.self="closeVideo"
            >
                <div class="absolute inset-0 bg-black/70" />
                <div
                    class="relative mx-auto w-[min(56rem,92vw)] rounded-[18px] bg-deepPurple p-4 shadow-deep-purple"
                >
                    <div
                        class="relative overflow-hidden rounded-[14px] bg-softLavender p-3"
                    >
                        <button
                            type="button"
                            class="absolute right-3 top-3 z-10 rounded-md bg-deepPurple px-3 py-2 text-sm text-softLavender transition-all duration-500 ease-out hover:bg-softLavender hover:text-deepPurple"
                            @click="closeVideo"
                        >
                            Close
                        </button>

                        <div
                            class="aspect-video w-full overflow-hidden rounded-[12px] bg-black"
                        >
                            <video
                                class="h-full w-full"
                                :src="HERO_VIDEO_URL"
                                controls
                                autoplay
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="flex min-h-screen items-center">
                <div class="mx-auto w-full max-w-6xl px-6 py-24 opacity-80">
                    Loading…
                </div>
            </div>

            <div v-else-if="loadError" class="flex min-h-screen items-center">
                <div class="mx-auto w-full max-w-6xl px-6 py-24">
                    <div class="max-w-xl rounded-lg bg-black/50 p-6">
                        <div class="text-lg font-semibold">
                            Couldn’t load content
                        </div>
                        <div class="mt-2 text-sm opacity-80">
                            {{ loadError }}
                        </div>
                    </div>
                </div>
            </div>

            <section
                v-else
                v-for="s in sections"
                :key="s.id || s.anchor"
                :id="s.anchor"
                class="min-h-screen snap-start"
            >
                <!-- Hero section uses a full-height column so bottom row sits at the bottom -->
                <div
                    v-if="s.anchor === 'home'"
                    class="flex min-h-screen w-full flex-col"
                >
                    <div
                        class="flex flex-1 flex-col items-center justify-center px-6 py-24"
                    >
                        <div class="mx-auto w-full max-w-6xl">
                            <div class="mx-auto max-w-3xl text-center">
                                <div class="text-shadow-hero">
                                    <div
                                        class="font-alata text-[36px] uppercase leading-tight text-lightGreenTint min-[641px]:text-[48px]"
                                    >
                                        YOUR STORY, YOUR DAY;
                                    </div>

                                    <div
                                        class="mt-3 font-alata text-[44px] uppercase leading-tight text-lightGreenTint min-[641px]:text-[64px]"
                                    >
                                        YOUR SONGS
                                    </div>

                                    <div
                                        class="mt-2 font-montecarlo text-[72px] leading-none text-deepPurple text-shadow-soft-lavender min-[641px]:text-[128px]"
                                    >
                                        Your Way
                                    </div>
                                </div>

                                <div
                                    class="mt-64 flex w-full -translate-y-32 flex-col items-stretch justify-center gap-4 uppercase min-[641px]:flex-row min-[641px]:items-center min-[641px]:gap-16 lg:gap-32"
                                >
                                    <a
                                        href="#services"
                                        class="inline-flex w-full items-center justify-center rounded-[12px] bg-deepPurple px-4 py-3 font-sans text-[1rem] leading-none text-softLavender shadow-soft-lavender transition-all duration-500 ease-out hover:bg-softLavender hover:text-deepPurple hover:shadow-deep-purple min-[641px]:min-w-0 min-[641px]:flex-1 min-[641px]:basis-0 min-[641px]:px-8 min-[641px]:text-[1.25rem] lg:text-[1.5rem]"
                                        @click.prevent="
                                            scrollToAnchor('services')
                                        "
                                    >
                                        Packages
                                    </a>

                                    <a
                                        href="#contact"
                                        class="inline-flex w-full items-center justify-center rounded-[12px] bg-softLavender px-4 py-3 font-sans text-[1rem] leading-none text-deepPurple shadow-deep-purple transition-all duration-500 ease-out hover:bg-deepPurple hover:text-softLavender hover:shadow-soft-lavender min-[641px]:min-w-0 min-[641px]:flex-1 min-[641px]:basis-0 min-[641px]:px-8 min-[641px]:text-[1.25rem] lg:text-[1.5rem]"
                                        @click.prevent="
                                            scrollToAnchor('contact')
                                        "
                                    >
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mx-auto w-full max-w-[1700px] pb-8 px-4 sm:px-6 min-[641px]:px-16 md:px-32"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex h-[140px] items-center">
                                <button
                                    type="button"
                                    class="group relative inline-flex h-[100px] w-[100px] items-center justify-center rounded-full bg-deepPurple text-softLavender shadow-soft-lavender transition-all duration-500 ease-out hover:bg-softLavender hover:text-deepPurple hover:shadow-deep-purple"
                                    @click="openVideo"
                                    aria-label="Play video"
                                >
                                    <span
                                        aria-hidden="true"
                                        class="absolute inset-0 rounded-full ring-2 ring-softLavender/55 transition-all duration-500 ease-out group-hover:ring-deepPurple/55"
                                    />
                                    <span
                                        aria-hidden="true"
                                        class="absolute inset-[9px] rounded-full bg-deepPurple/70 ring-2 ring-softLavender/55 backdrop-blur-2xl transition-all duration-500 ease-out group-hover:bg-softLavender/70 group-hover:ring-deepPurple/55"
                                    />

                                    <svg
                                        class="relative"
                                        width="40"
                                        height="40"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M15 10.5L19 8v8l-4-2.5V16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v2.5Z"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div
                                class="hidden h-[140px] w-[300px] overflow-hidden rounded-[12px] bg-white/15 backdrop-blur-3xl md:block"
                            >
                                <img
                                    :src="HERO_LOGO_URL"
                                    alt="Ana Fae Music"
                                    class="h-full w-full object-cover"
                                />
                            </div>

                            <div class="flex h-[140px] items-center">
                                <a
                                    :href="HERO_EXTERNAL_URL"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group relative inline-flex h-[100px] w-[100px] items-center justify-center rounded-full bg-deepPurple text-softLavender shadow-soft-lavender transition-all duration-500 ease-out hover:bg-softLavender hover:text-deepPurple hover:shadow-deep-purple"
                                    aria-label="External link"
                                >
                                    <span
                                        aria-hidden="true"
                                        class="absolute inset-0 rounded-full ring-2 ring-softLavender/55 transition-all duration-500 ease-out group-hover:ring-deepPurple/55"
                                    />
                                    <span
                                        aria-hidden="true"
                                        class="absolute inset-[9px] rounded-full bg-deepPurple/70 ring-2 ring-softLavender/55 backdrop-blur-2xl transition-all duration-500 ease-out group-hover:bg-softLavender/70 group-hover:ring-deepPurple/55"
                                    />

                                    <img
                                        :src="HERO_EXTERNAL_ICON_URL"
                                        alt=""
                                        aria-hidden="true"
                                        class="relative h-24 w-24 object-contain"
                                    />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- About section (image + overlapping text card) -->
                <div
                    v-else-if="s.anchor === 'about'"
                    class="flex min-h-screen items-center"
                >
                    <div
                        class="mx-auto w-full max-w-[1700px] px-4 py-24 sm:px-6"
                    >
                        <div class="w-full lg:flex lg:justify-center">
                            <div
                                class="relative flex flex-col gap-10 lg:inline-flex lg:flex-row lg:items-center lg:gap-0"
                            >
                                <div
                                    class="relative z-0 lg:w-[420px] lg:shrink-0 xl:w-[480px] min-[1400px]:w-[567px]"
                                >
                                    <div
                                        class="mx-auto w-full max-w-[360px] overflow-hidden rounded-[18px] bg-white/15 backdrop-blur-3xl sm:max-w-[420px] lg:mx-0 lg:max-w-none"
                                    >
                                        <img
                                            :src="ABOUT_IMG_URL"
                                            alt="Ana Fae"
                                            class="aspect-[567/833] w-full object-cover"
                                        />
                                    </div>
                                </div>

                                <div
                                    class="relative z-10 lg:w-[min(860px,55vw)] lg:-ml-12 xl:w-[min(920px,55vw)] xl:-ml-16 min-[1400px]:w-[min(980px,55vw)] min-[1400px]:-ml-20"
                                >
                                    <div
                                        class="w-full rounded-[18px] bg-softLavender p-7 text-deepPurple shadow-deep-purple md:p-8"
                                    >
                                        <h2
                                            class="flex items-center gap-2 font-heading text-[32px] font-bold leading-tight text-deepPurple"
                                        >
                                            <span aria-hidden="true">🌿</span>
                                            <span>About Ana Fae</span>
                                        </h2>

                                        <div
                                            class="mt-3 max-w-none space-y-4 font-sans text-[16px] font-normal leading-relaxed text-deepPurple"
                                        >
                                            <p>
                                                Ana Fae is an accomplished
                                                vocalist whose passion and
                                                artistry have been captivating
                                                audiences across the UK for over
                                                a decade. From timeless
                                                classical pieces to contemporary
                                                pop, rock, and musical theatre,
                                                Ana’s versatile repertoire
                                                ensures your wedding music is as
                                                unique as your love story.
                                            </p>
                                            <p>
                                                With a warm, expressive voice
                                                and a natural ability to create
                                                unforgettable moments, Ana
                                                offers beautifully tailored
                                                performances for every part of
                                                your day—from your walk down the
                                                aisle to your first dance and
                                                evening celebrations.
                                            </p>
                                            <p>
                                                Over the years, Ana has
                                                performed in some of the
                                                country’s most prestigious
                                                venues, including the Royal
                                                Albert Hall, Royal Festival
                                                Hall, and the Sage Gateshead.
                                                Her experience on renowned
                                                stages, combined with countless
                                                performances at private events,
                                                means you can feel confident
                                                that your special day is in
                                                expert hands.
                                            </p>
                                            <p>
                                                Ana’s love of music began early.
                                                From the age of 13, she trained
                                                at the Sage Gateshead’s Young
                                                Musicians Programme, working
                                                alongside acclaimed artists such
                                                as conductor Vasily Petrenko and
                                                composer Judith Weir. She went
                                                on to earn two diplomas in
                                                popular music vocals and studied
                                                at the prestigious Chetham’s
                                                School of Music, further
                                                enriching her classical
                                                foundations. Ana recently
                                                completed a BA in Musical
                                                Theatre and is currently
                                                pursuing her MA in Popular Music
                                                Practice at BIMM University in
                                                Manchester.
                                            </p>
                                        </div>

                                        <div
                                            class="mt-8 flex w-full flex-col items-stretch justify-center gap-4 uppercase min-[641px]:flex-row min-[641px]:items-center min-[641px]:gap-8 lg:gap-10 xl:gap-20"
                                        >
                                            <a
                                                href="#services"
                                                class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-[12px] bg-deepPurple px-4 py-3 font-sans text-[1rem] leading-none text-softLavender shadow-soft-lavender transition-all duration-500 ease-out hover:bg-softLavender hover:text-deepPurple hover:shadow-deep-purple min-[641px]:min-w-0 min-[641px]:flex-1 min-[641px]:basis-0 min-[641px]:px-6 min-[641px]:text-[1.15rem] lg:px-7 lg:text-[1.25rem] xl:px-8 xl:text-[1.5rem]"
                                                @click.prevent="
                                                    scrollToAnchor('services')
                                                "
                                            >
                                                Packages
                                            </a>

                                            <a
                                                href="#contact"
                                                class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-[12px] bg-softLavender px-4 py-3 font-sans text-[1rem] leading-none text-deepPurple shadow-deep-purple transition-all duration-500 ease-out hover:bg-deepPurple hover:text-softLavender hover:shadow-soft-lavender min-[641px]:min-w-0 min-[641px]:flex-1 min-[641px]:basis-0 min-[641px]:px-6 min-[641px]:text-[1.15rem] lg:px-7 lg:text-[1.25rem] xl:px-8 xl:text-[1.5rem]"
                                                @click.prevent="
                                                    scrollToAnchor('contact')
                                                "
                                            >
                                                Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services section (tabs + content panel) -->
                <div
                    v-else-if="s.anchor === 'services'"
                    class="relative flex min-h-screen items-center"
                >
                    <div class="w-full">
                        <!-- Left tabs (desktop, hard-left) -->
                        <aside
                            class="absolute inset-y-0 left-0 hidden w-[280px] bg-deepPurple/85 shadow-deep-purple lg:block"
                        >
                            <div class="h-full overflow-hidden">
                                <nav
                                    class="flex h-full flex-col justify-center py-24"
                                >
                                    <div
                                        v-for="t in servicesTabs"
                                        :key="t.id"
                                        class="flex flex-col"
                                    >
                                        <button
                                            type="button"
                                            class="w-full px-6 py-4 text-left font-heading text-[20px] uppercase tracking-wide transition-colors"
                                            :class="
                                                activeServicesTabId === t.id
                                                    ? 'bg-softLavender text-deepPurple'
                                                    : 'text-softLavender hover:bg-softLavender/10'
                                            "
                                            @click="selectServicesTab(t.id)"
                                        >
                                            {{ t.label }}
                                        </button>

                                        <div
                                            v-if="
                                                t.id === activeServicesTabId &&
                                                t.items?.length > 1
                                            "
                                            class="pb-2"
                                        >
                                            <button
                                                v-for="i in t.items"
                                                :key="i.id"
                                                type="button"
                                                class="w-full px-10 py-2.5 text-left font-sans text-[13px] uppercase tracking-wide transition-colors"
                                                :class="
                                                    activeServicesItem?.id ===
                                                    i.id
                                                        ? 'bg-softLavender text-deepPurple'
                                                        : 'text-softLavender/85 hover:bg-softLavender/10 hover:text-softLavender'
                                                "
                                                @click="
                                                    selectServicesItem(i.id)
                                                "
                                            >
                                                {{ i.label }}
                                            </button>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </aside>

                        <!-- Content area (padded to clear left tabs on desktop) -->
                        <div
                            class="mx-auto w-full max-w-[1700px] px-4 py-24 sm:px-6 lg:pl-[320px]"
                        >
                            <div class="flex flex-col gap-8">
                                <!-- Mobile controls -->
                                <div class="lg:hidden">
                                    <div
                                        class="rounded-[16px] bg-deepPurple/80 p-4 text-softLavender shadow-deep-purple"
                                    >
                                        <div class="grid gap-3">
                                            <label
                                                class="text-sm font-medium uppercase tracking-wide opacity-90"
                                            >
                                                Category
                                                <select
                                                    class="mt-2 w-full rounded-md bg-softLavender px-3 py-2 text-deepPurple"
                                                    :value="activeServicesTabId"
                                                    @change="
                                                        selectServicesTab(
                                                            $event.target.value,
                                                        )
                                                    "
                                                >
                                                    <option
                                                        v-for="t in servicesTabs"
                                                        :key="t.id"
                                                        :value="t.id"
                                                    >
                                                        {{ t.label }}
                                                    </option>
                                                </select>
                                            </label>

                                            <label
                                                v-if="
                                                    activeServicesTab?.items
                                                        ?.length > 1
                                                "
                                                class="text-sm font-medium uppercase tracking-wide opacity-90"
                                            >
                                                Option
                                                <select
                                                    class="mt-2 w-full rounded-md bg-softLavender px-3 py-2 text-deepPurple"
                                                    :value="
                                                        activeServicesItem?.id
                                                    "
                                                    @change="
                                                        selectServicesItem(
                                                            $event.target.value,
                                                        )
                                                    "
                                                >
                                                    <option
                                                        v-for="i in activeServicesTab.items"
                                                        :key="i.id"
                                                        :value="i.id"
                                                    >
                                                        {{ i.label }}
                                                    </option>
                                                </select>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right content panel -->
                                <div
                                    class="rounded-[18px] bg-mutedLilac/90 p-6 shadow-deep-purple sm:p-8"
                                >
                                    <div
                                        class="grid gap-8 lg:grid-cols-2 lg:items-center"
                                    >
                                        <div
                                            class="overflow-hidden rounded-[18px] bg-white/15 backdrop-blur-3xl"
                                        >
                                            <img
                                                v-if="
                                                    activeServicesItem?.imageUrl
                                                "
                                                :src="
                                                    activeServicesItem.imageUrl
                                                "
                                                alt=""
                                                aria-hidden="true"
                                                class="aspect-[4/3] h-full w-full object-cover"
                                            />
                                        </div>

                                        <div class="w-full">
                                            <div
                                                class="rounded-[18px] bg-softLavender p-4 text-deepPurple shadow-deep-purple"
                                            >
                                                <h3
                                                    class="font-heading text-[32px] font-bold leading-tight"
                                                >
                                                    {{
                                                        activeServicesItem?.title
                                                    }}
                                                </h3>
                                                <div
                                                    v-if="
                                                        activeServicesItem?.subtitle
                                                    "
                                                    class="mt-4 font-heading text-[24px] font-bold"
                                                >
                                                    {{
                                                        activeServicesItem.subtitle
                                                    }}
                                                </div>
                                                <p
                                                    v-if="
                                                        activeServicesItem?.body
                                                    "
                                                    class="mt-2 whitespace-pre-line font-sans text-[18px] leading-relaxed"
                                                >
                                                    {{
                                                        activeServicesItem.body
                                                    }}
                                                </p>

                                                <div
                                                    class="mt-6 flex flex-wrap items-center gap-3"
                                                >
                                                    <a
                                                        href="#contact"
                                                        class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-[12px] bg-mutedLilac px-6 py-3 font-sans text-[0.95rem] uppercase leading-none text-softLavender shadow-soft-lavender transition-all duration-500 ease-out hover:bg-deepPurple min-[641px]:w-auto"
                                                        @click.prevent="
                                                            bookNowFromServices
                                                        "
                                                    >
                                                        {{
                                                            activeServicesItem?.buttonLabel ||
                                                            "Book Now"
                                                        }}
                                                    </a>

                                                    <button
                                                        type="button"
                                                        class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-[12px] bg-deepPurple/10 px-6 py-3 font-sans text-[0.95rem] uppercase leading-none text-deepPurple shadow-soft-lavender transition-colors hover:bg-deepPurple/15 min-[641px]:w-auto"
                                                        @click="openSetlists"
                                                        v-if="
                                                            showSetlistsButton
                                                        "
                                                    >
                                                        View Setlists
                                                    </button>
                                                </div>

                                                <div
                                                    v-if="
                                                        activeServicesItem?.belowButtonImageUrl
                                                    "
                                                    class="-mx-4 -mb-4 mt-6 h-[205px]"
                                                >
                                                    <img
                                                        :src="
                                                            activeServicesItem.belowButtonImageUrl
                                                        "
                                                        alt=""
                                                        aria-hidden="true"
                                                        :class="
                                                            activeServicesItem?.id ===
                                                            'daisy'
                                                                ? 'h-full w-full object-cover'
                                                                : 'h-full w-full object-contain'
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Setlists popup -->
                        <div
                            v-if="isSetlistsOpen"
                            class="fixed inset-0 z-50 flex items-center justify-center px-4 py-10"
                            role="dialog"
                            aria-modal="true"
                            aria-label="Setlists"
                        >
                            <div
                                class="absolute inset-0 bg-deepPurple/60"
                                aria-hidden="true"
                            />

                            <div class="relative w-full max-w-[920px]">
                                <div
                                    class="rounded-[18px] bg-softLavender p-5 text-deepPurple shadow-deep-purple sm:p-7"
                                >
                                    <div
                                        class="flex items-start justify-between gap-4"
                                    >
                                        <div>
                                            <div
                                                class="font-heading text-[26px] font-bold"
                                            >
                                                Setlists
                                            </div>
                                            <div
                                                v-if="activeSetlist?.title"
                                                class="mt-1 font-sans text-[12px] opacity-80"
                                            >
                                                {{ activeSetlist.title }}
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-[12px] bg-deepPurple/10 text-deepPurple transition-colors hover:bg-deepPurple/15"
                                            aria-label="Close"
                                            @click="closeSetlists"
                                        >
                                            ✕
                                        </button>
                                    </div>

                                    <div
                                        class="mt-5 overflow-hidden rounded-[18px] bg-white/20 backdrop-blur-3xl"
                                    >
                                        <img
                                            v-if="activeSetlist?.imageUrl"
                                            :src="activeSetlist.imageUrl"
                                            alt=""
                                            aria-hidden="true"
                                            class="max-h-[70vh] w-full object-contain"
                                        />
                                    </div>

                                    <div
                                        class="mt-5 flex items-center justify-between"
                                    >
                                        <div
                                            class="font-sans text-[11px] opacity-70"
                                        >
                                            {{ activeSetlistIndex + 1 }} /
                                            {{ setlists.length }}
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] bg-deepPurple/10 text-deepPurple transition-colors hover:bg-deepPurple/15"
                                                :disabled="setlists.length < 2"
                                                @click="prevSetlist"
                                            >
                                                ‹
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] bg-deepPurple/10 text-deepPurple transition-colors hover:bg-deepPurple/15"
                                                :disabled="setlists.length < 2"
                                                @click="nextSetlist"
                                            >
                                                ›
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact section (image + reviews + form) -->
                <div
                    v-else-if="s.anchor === 'contact'"
                    class="flex min-h-screen items-center"
                >
                    <div
                        class="mx-auto w-full max-w-[1700px] px-4 py-24 sm:px-6"
                    >
                        <div
                            class="rounded-[18px] bg-mutedLilac/90 p-6 shadow-deep-purple sm:p-8"
                        >
                            <div
                                class="grid gap-8 lg:grid-cols-2 lg:items-stretch"
                            >
                                <!-- Left: image + (future) reviews slider -->
                                <div class="flex flex-col gap-6">
                                    <div
                                        class="ml-0 mt-0 overflow-hidden rounded-[18px] bg-white/15 backdrop-blur-3xl sm:-ml-8 sm:-mt-8"
                                    >
                                        <img
                                            :src="CONTACT_IMG_URL"
                                            alt=""
                                            aria-hidden="true"
                                            class="aspect-[4/3] h-full w-full object-cover"
                                        />
                                    </div>

                                    <!-- Reviews slider -->
                                    <div
                                        class="rounded-[18px] bg-softLavender p-4 text-deepPurple shadow-deep-purple"
                                    >
                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >
                                            <div>
                                                <div
                                                    class="font-heading text-[20px] font-bold"
                                                >
                                                    Reviews
                                                </div>
                                                <div
                                                    class="mt-1 font-sans text-[12px] opacity-80"
                                                >
                                                    From Last Minute Musicians
                                                </div>
                                            </div>

                                            <div
                                                class="flex shrink-0 items-center gap-2"
                                            >
                                                <button
                                                    type="button"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] bg-deepPurple/10 text-deepPurple transition-colors hover:bg-deepPurple/15"
                                                    :disabled="
                                                        !lmmReviews.length
                                                    "
                                                    @click="prevReview"
                                                >
                                                    ‹
                                                </button>
                                                <button
                                                    type="button"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] bg-deepPurple/10 text-deepPurple transition-colors hover:bg-deepPurple/15"
                                                    :disabled="
                                                        !lmmReviews.length
                                                    "
                                                    @click="nextReview"
                                                >
                                                    ›
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <p
                                                v-if="lmmReviewsLoading"
                                                class="font-sans text-[12px] opacity-80"
                                            >
                                                Loading reviews…
                                            </p>

                                            <p
                                                v-else-if="activeReview"
                                                class="font-sans text-[12px] leading-relaxed opacity-90"
                                            >
                                                “{{ activeReview.text }}”
                                            </p>

                                            <div
                                                v-if="
                                                    activeReview &&
                                                    (activeReview.author ||
                                                        activeReview.rating)
                                                "
                                                class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 font-sans text-[11px] opacity-75"
                                            >
                                                <div v-if="activeReview.author">
                                                    — {{ activeReview.author }}
                                                </div>
                                                <div v-if="activeReview.rating">
                                                    {{ activeReview.rating }}/10
                                                </div>
                                            </div>

                                            <p
                                                v-else
                                                class="font-sans text-[12px] leading-relaxed opacity-80"
                                            >
                                                {{
                                                    lmmReviewsError ||
                                                    "Reviews are available on Last Minute Musicians."
                                                }}
                                                <a
                                                    :href="LMM_PROFILE_URL"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="underline"
                                                >
                                                    View reviews
                                                </a>
                                            </p>

                                            <div
                                                v-if="lmmReviews.length"
                                                class="mt-3 font-sans text-[11px] opacity-70"
                                            >
                                                {{ activeReviewIndex + 1 }} /
                                                {{ lmmReviews.length }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: contact form -->
                                <div class="w-full">
                                    <div
                                        class="h-full rounded-[18px] bg-softLavender p-6 text-deepPurple shadow-deep-purple sm:p-8"
                                    >
                                        <h2
                                            class="font-heading text-[32px] font-bold leading-tight"
                                        >
                                            Get In Touch
                                        </h2>

                                        <div
                                            class="mt-4 space-y-4 font-sans text-[16px] leading-relaxed"
                                        >
                                            <p class="font-semibold">
                                                Your wedding day deserves a
                                                soundtrack that feels truly
                                                yours.
                                            </p>
                                            <p>
                                                Tell me a little about your
                                                plans using the form below, and
                                                I’ll be delighted to talk
                                                through your ideas, answer any
                                                questions, and help you find the
                                                perfect musical fit.
                                            </p>
                                            <p>
                                                You can also contact me on
                                                <a
                                                    :href="
                                                        LAST_MINUTE_MUSICIANS_URL
                                                    "
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="underline"
                                                >
                                                    Last Minute Musicians
                                                </a>
                                            </p>
                                        </div>

                                        <form
                                            class="mt-6"
                                            @submit.prevent="submitContactForm"
                                        >
                                            <div
                                                v-if="contactFormStatus.message"
                                                class="mb-4 rounded-[10px] px-4 py-3 font-sans text-[13px]"
                                                :class="
                                                    contactFormStatus.type ===
                                                    'success'
                                                        ? 'bg-green-100 text-green-900'
                                                        : 'bg-red-100 text-red-900'
                                                "
                                            >
                                                {{ contactFormStatus.message }}
                                            </div>

                                            <div
                                                class="grid gap-4 sm:grid-cols-2"
                                            >
                                                <label
                                                    class="sr-only"
                                                    for="contact-name"
                                                    >Name</label
                                                >
                                                <input
                                                    id="contact-name"
                                                    v-model="contactForm.name"
                                                    type="text"
                                                    autocomplete="name"
                                                    placeholder="Name"
                                                    class="w-full rounded-[10px] border border-deepPurple/70 bg-softLavender px-4 py-3 font-sans text-[14px] text-deepPurple placeholder:text-deepPurple/60 focus:outline-none focus:ring-2 focus:ring-deepPurple/40"
                                                    :aria-invalid="
                                                        Boolean(
                                                            contactFormErrors
                                                                .name?.length,
                                                        )
                                                    "
                                                />

                                                <p
                                                    v-if="
                                                        contactFormErrors.name
                                                            ?.length
                                                    "
                                                    class="sm:col-span-2 -mt-2 font-sans text-[12px] text-red-700"
                                                >
                                                    {{
                                                        contactFormErrors
                                                            .name[0]
                                                    }}
                                                </p>

                                                <label
                                                    class="sr-only"
                                                    for="contact-email"
                                                    >Email</label
                                                >
                                                <input
                                                    id="contact-email"
                                                    v-model="contactForm.email"
                                                    type="email"
                                                    autocomplete="email"
                                                    placeholder="Email"
                                                    class="w-full rounded-[10px] border border-deepPurple/70 bg-softLavender px-4 py-3 font-sans text-[14px] text-deepPurple placeholder:text-deepPurple/60 focus:outline-none focus:ring-2 focus:ring-deepPurple/40"
                                                    :aria-invalid="
                                                        Boolean(
                                                            contactFormErrors
                                                                .email?.length,
                                                        )
                                                    "
                                                />

                                                <p
                                                    v-if="
                                                        contactFormErrors.email
                                                            ?.length
                                                    "
                                                    class="sm:col-span-2 -mt-2 font-sans text-[12px] text-red-700"
                                                >
                                                    {{
                                                        contactFormErrors
                                                            .email[0]
                                                    }}
                                                </p>

                                                <label
                                                    class="sr-only"
                                                    for="contact-number"
                                                    >Contact Number</label
                                                >
                                                <input
                                                    id="contact-number"
                                                    v-model="
                                                        contactForm.contactNumber
                                                    "
                                                    type="tel"
                                                    autocomplete="tel"
                                                    placeholder="Contact Number"
                                                    class="w-full rounded-[10px] border border-deepPurple/70 bg-softLavender px-4 py-3 font-sans text-[14px] text-deepPurple placeholder:text-deepPurple/60 focus:outline-none focus:ring-2 focus:ring-deepPurple/40"
                                                    :aria-invalid="
                                                        Boolean(
                                                            contactFormErrors
                                                                .contactNumber
                                                                ?.length,
                                                        )
                                                    "
                                                />

                                                <p
                                                    v-if="
                                                        contactFormErrors
                                                            .contactNumber
                                                            ?.length
                                                    "
                                                    class="sm:col-span-2 -mt-2 font-sans text-[12px] text-red-700"
                                                >
                                                    {{
                                                        contactFormErrors
                                                            .contactNumber[0]
                                                    }}
                                                </p>

                                                <label
                                                    class="sr-only"
                                                    for="contact-service"
                                                    >Service</label
                                                >
                                                <select
                                                    id="contact-service"
                                                    v-model="
                                                        contactForm.service
                                                    "
                                                    class="w-full rounded-[10px] border border-deepPurple/70 bg-softLavender px-4 py-3 font-sans text-[14px] text-deepPurple focus:outline-none focus:ring-2 focus:ring-deepPurple/40"
                                                    :aria-invalid="
                                                        Boolean(
                                                            contactFormErrors
                                                                .service
                                                                ?.length,
                                                        )
                                                    "
                                                >
                                                    <option value="" disabled>
                                                        Service
                                                    </option>
                                                    <option
                                                        v-for="opt in contactServiceOptions"
                                                        :key="opt"
                                                        :value="opt"
                                                    >
                                                        {{ opt }}
                                                    </option>
                                                </select>

                                                <p
                                                    v-if="
                                                        contactFormErrors
                                                            .service?.length
                                                    "
                                                    class="sm:col-span-2 -mt-2 font-sans text-[12px] text-red-700"
                                                >
                                                    {{
                                                        contactFormErrors
                                                            .service[0]
                                                    }}
                                                </p>
                                            </div>

                                            <div class="mt-4">
                                                <label
                                                    class="sr-only"
                                                    for="contact-message"
                                                    >Message</label
                                                >
                                                <textarea
                                                    id="contact-message"
                                                    v-model="
                                                        contactForm.message
                                                    "
                                                    rows="6"
                                                    placeholder="Message"
                                                    class="w-full resize-none rounded-[10px] border border-deepPurple/70 bg-softLavender px-4 py-3 font-sans text-[14px] text-deepPurple placeholder:text-deepPurple/60 focus:outline-none focus:ring-2 focus:ring-deepPurple/40"
                                                    :aria-invalid="
                                                        Boolean(
                                                            contactFormErrors
                                                                .message
                                                                ?.length,
                                                        )
                                                    "
                                                />

                                                <p
                                                    v-if="
                                                        contactFormErrors
                                                            .message?.length
                                                    "
                                                    class="mt-2 font-sans text-[12px] text-red-700"
                                                >
                                                    {{
                                                        contactFormErrors
                                                            .message[0]
                                                    }}
                                                </p>
                                            </div>

                                            <div class="mt-6">
                                                <button
                                                    type="submit"
                                                    class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-[12px] bg-mutedLilac px-10 py-3 font-sans text-[0.95rem] uppercase leading-none text-softLavender shadow-soft-lavender transition-all duration-500 ease-out hover:bg-deepPurple min-[641px]:w-auto"
                                                    :disabled="
                                                        isSubmittingContactForm
                                                    "
                                                >
                                                    {{
                                                        isSubmittingContactForm
                                                            ? "Sending..."
                                                            : "Send"
                                                    }}
                                                </button>

                                                <div
                                                    class="mt-6 flex flex-col items-center gap-5"
                                                >
                                                    <div
                                                        class="flex flex-wrap items-center justify-center gap-5"
                                                    >
                                                        <a
                                                            href="https://encoremusicians.com/hire/singers?utm_source=badge&utm_medium=web&utm_campaign=verified_badge&utm_content=light-medium"
                                                            target="_parent"
                                                            rel="noopener noreferrer"
                                                            aria-label="Book Ana Fae on Encore Musicians"
                                                        >
                                                            <img
                                                                src="https://encoremusicians.com/img/embeds/badge-light.svg"
                                                                alt="Book Ana Fae on Encore Musicians"
                                                                class="h-[150px] w-[150px]"
                                                            />
                                                        </a>

                                                        <a
                                                            href="https://www.lastminutemusicians.com/members/anafae.html"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            aria-label="Ana Fae on Last Minute Musicians"
                                                            class="inline-flex h-[150px] w-[150px] items-center justify-center"
                                                        >
                                                            <img
                                                                src="https://www.lastminutemusicians.com/images/v5/LMM-White.png"
                                                                alt="Ana Fae on Last Minute Musicians"
                                                                class="h-[150px] w-[150px] object-contain"
                                                            />
                                                        </a>
                                                    </div>

                                                    <div
                                                        class="flex items-center gap-5"
                                                    >
                                                        <a
                                                            v-for="link in socialLinks"
                                                            :key="`contact:${link.type}:${link.url}`"
                                                            :href="link.url"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-deepPurple text-softLavender shadow-soft-lavender transition-all duration-700 ease-out hover:bg-softLavender hover:text-deepPurple hover:shadow-deep-purple"
                                                            :aria-label="
                                                                link.label ||
                                                                link.type
                                                            "
                                                        >
                                                            <svg
                                                                width="22"
                                                                height="22"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                aria-hidden="true"
                                                            >
                                                                <path
                                                                    v-for="(
                                                                        p, idx
                                                                    ) in getSocialIconPath(
                                                                        link.type,
                                                                    )"
                                                                    :key="idx"
                                                                    :d="p.d"
                                                                    :fill="
                                                                        p.fill ||
                                                                        'none'
                                                                    "
                                                                    :stroke="
                                                                        p.stroke ||
                                                                        'currentColor'
                                                                    "
                                                                    stroke-width="2"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Default layout for other sections -->
                <div v-else class="flex min-h-screen items-center">
                    <div class="mx-auto w-full max-w-6xl px-6 py-24">
                        <div class="mx-auto max-w-3xl text-center">
                            <h2
                                class="break-words font-sans text-[2.25rem] leading-tight text-lightGreenTint text-shadow-hero sm:text-[3rem]"
                            >
                                {{ s.heading }}
                            </h2>
                            <div
                                v-if="s.body"
                                class="mt-6 max-w-none break-words font-sans text-[2.75rem] leading-tight text-lightGreenTint text-shadow-hero sm:text-[4rem]"
                                v-html="s.body"
                            />

                            <div v-if="s.cta_label && s.cta_url" class="mt-8">
                                <a
                                    class="inline-flex items-center rounded-md bg-white/15 px-4 py-2 text-sm font-medium hover:bg-white/20"
                                    :href="s.cta_url"
                                    >{{ s.cta_label }}</a
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>
