<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ana Fae Music — Coming Soon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Montserrat:wght@300;400;500;600&display=swap"
        rel="stylesheet" />

    <style>
        :root {
            --bg: #0c0a09;
            --text: rgba(250, 250, 249, 0.92);
            --muted: rgba(250, 250, 249, 0.70);
            --border: rgba(250, 250, 249, 0.12);
            --panel: rgba(12, 10, 9, 0.20);
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Montserrat, ui-sans-serif, system-ui, -apple-system,
                Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji",
                "Segoe UI Emoji";
        }

        .stage {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 40px 20px;
            position: relative;
            overflow: hidden;
        }

        .stage::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to bottom,
                    rgba(12, 10, 9, 0.18),
                    rgba(12, 10, 9, 0.62),
                    rgba(12, 10, 9, 0.92)),
                url("/coming-soon/hero.jpg");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            filter: saturate(1.02);
            z-index: 0;
        }

        .stage::after {
            content: "";
            position: absolute;
            inset: 0;
            backdrop-filter: blur(1px);
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 28px;
            width: min(980px, 100%);
        }

        .logoMask {
            height: clamp(180px, 26vw, 320px);
            width: min(92vw, 900px);
            display: none;
            background: rgba(250, 250, 249, 0.95);
        }

        .logoImg {
            height: clamp(180px, 26vw, 320px);
            width: auto;
            display: block;
            mix-blend-mode: screen;
        }

        @supports ((-webkit-mask-image: url("")) or (mask-image: url(""))) {
            .logoMask {
                display: block;
                -webkit-mask-image: url("/coming-soon/logo.png");
                -webkit-mask-repeat: no-repeat;
                -webkit-mask-position: center;
                -webkit-mask-size: contain;
                -webkit-mask-mode: luminance;
                mask-image: url("/coming-soon/logo.png");
                mask-repeat: no-repeat;
                mask-position: center;
                mask-size: contain;
                mask-mode: luminance;
            }

            .logoImg {
                display: none;
            }
        }

        .panel {
            border: 1px solid var(--border);
            background: var(--panel);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 20px 28px;
        }

        .comingSoon {
            font-family: "Cormorant Garamond", ui-serif, Georgia, Cambria,
                "Times New Roman", Times, serif;
            font-size: clamp(34px, 5vw, 46px);
            font-weight: 500;
            letter-spacing: 0.02em;
            margin: 0;
            color: rgba(250, 250, 249, 0.94);
        }

        .divider {
            height: 1px;
            width: 64px;
            background: rgba(250, 250, 249, 0.22);
            margin: 14px auto 0;
        }

        .brand {
            margin: 14px 0 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.38em;
            color: var(--muted);
            font-weight: 500;
        }

        @media (prefers-reduced-motion: reduce) {
            .stage::after {
                backdrop-filter: none;
            }
        }
    </style>
</head>

<body>
    <main class="stage">
        <section class="content">
            <div class="logoMask" role="img" aria-label="Ana Fae Music"></div>
            <img class="logoImg" src="/coming-soon/logo.png" alt="" aria-hidden="true" />

            <div class="panel" role="status" aria-live="polite">
                <p class="comingSoon">Coming soon</p>
                <div class="divider"></div>
                <p class="brand">Ana Fae Music</p>
            </div>
        </section>
    </main>
</body>

</html>