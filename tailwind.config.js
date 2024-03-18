const svgToDataUri = require("mini-svg-data-uri");
const colors = require("tailwindcss/colors");
const defaultTheme = require("tailwindcss/defaultTheme");
/** @type {import('tailwindcss').Config} */
module.exports = {
    variants: {
        ringColor: ["group-focus"],
        ringWidth: ["group-focus"],
        zIndex: ["group-focus"],
        scrollbar: ["dark", "rounded"],
    },
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            order: {
                '13': '13',
                '14': '14',
                '15': '15',
                '16': '16',
                '17': '17',
                '18': '18',
                '19': '19',
                '20': '20',
            },
            fontFamily: {
                sans: ["DM Sans", ...defaultTheme.fontFamily.sans],
            },
            gridTemplateColumns: {
                "token-section": "auto minmax(auto, 1fr) auto",
            },
            backgroundImage: (theme) => ({
                'multiselect-caret': `url("${svgToDataUri(
                    `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg>`,
                )}")`,
                'multiselect-spinner': `url("${svgToDataUri(
                    `<svg viewBox="0 0 512 512" fill="${theme('colors.emerald.500')}" xmlns="http://www.w3.org/2000/svg"><path d="M456.433 371.72l-27.79-16.045c-7.192-4.152-10.052-13.136-6.487-20.636 25.82-54.328 23.566-118.602-6.768-171.03-30.265-52.529-84.802-86.621-144.76-91.424C262.35 71.922 256 64.953 256 56.649V24.56c0-9.31 7.916-16.609 17.204-15.96 81.795 5.717 156.412 51.902 197.611 123.408 41.301 71.385 43.99 159.096 8.042 232.792-4.082 8.369-14.361 11.575-22.424 6.92z"></path></svg>`,
                )}")`,
                'multiselect-remove': `url("${svgToDataUri(
                    `<svg viewBox="0 0 320 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M207.6 256l107.72-107.72c6.23-6.23 6.23-16.34 0-22.58l-25.03-25.03c-6.23-6.23-16.34-6.23-22.58 0L160 208.4 52.28 100.68c-6.23-6.23-16.34-6.23-22.58 0L4.68 125.7c-6.23 6.23-6.23 16.34 0 22.58L112.4 256 4.68 363.72c-6.23 6.23-6.23 16.34 0 22.58l25.03 25.03c6.23 6.23 16.34 6.23 22.58 0L160 303.6l107.72 107.72c6.23 6.23 16.34 6.23 22.58 0l25.03-25.03c6.23-6.23 6.23-16.34 0-22.58L207.6 256z"></path></svg>`,
                )}")`,
                bal: "url('/resources/js/images/p-1.png')",
                crypto: "url('/resources/js/images/crypto.jpg')",
                cta: "url('/resources/js/assets/cta.jpg')",
                bars: "url('/resources/js/images/trident/bars-pattern.png')",
                binary: "url('/resources/js/images/trident/binary-pattern.png')",
                bubble: "url('/resources/js/images/trident/bubble-pattern.png')",
                dots: "url('/resources/js/images/trident/dots-pattern.png')",
                "x-times-y-is-k":
                    "url('/resources/js/images/trident/x-times-y-is-k.png')",
                wavy: "url('/resources/js/images/trident/wavy-pattern.png')",
                chevron:
                    "url('/resources/js/images/trident/chevron-pattern.png')",
                "gradient-radial":
                    "radial-gradient(100% 100% at 50% 25%, var(--tw-gradient-stops))",
            }),
            backgroundSize: {
                stretch: "912px 136px;",
            },
            spacing: {
                960: "60rem",
                4.5: "1.125rem",
                5.5: "1.375rem",
                18: "4.5rem",
            },
            animation: {
                "float-up": "floatup .5s ease-in-out",
                ellipsis: "ellipsis 1.25s infinite",
                "spin-slow": "spin 2s linear infinite",
                fade: "opacity 150ms linear",
            },
            floatup: {
                wiggle: {
                    "20%": { opacity: ".999" },
                    "50%": {
                        transform: " transform: translate3d(-50%,-17px,0)",
                    },
                },
            },
            fontSize: {
                tiny: ["0.625rem", "0.8125rem"],
                "tiny+": ["0.6875rem", "0.875rem"],
                "xs+": ["0.8125rem", "1.125rem"],
                "sm+": ["0.9375rem", "1.375rem"],
                hero: [
                    "48px",
                    {
                        letterSpacing: "-0.02em;",
                        lineHeight: "96px",
                        fontWeight: 700,
                    },
                ],
            },
            colors: () => {
                return {
                    emerald: {
                        '50': '#fdf8ef',
                        '100': '#faeeda',
                        '200': '#f5dbb3',
                        '300': '#eec283',
                        '400': '#e69f51',
                        '500': '#e18937',
                        '600': '#d16c25',
                        '700': '#ae5320',
                        '800': '#8b4421',
                        '900': '#70391e',
                        '950': '#3c1b0e',
                    },
                    gray: colors.stone,
                    slate: {
                        ...colors.stone,
                        '150': colors.stone['100'],
                        '450': colors.stone['500'],
                    },
                    teal: colors.fuchsia,
                    cyan: colors.cyan,
                    fuchsia: colors.fuchsia,
                    lime: colors.lime,
                    orange: colors.orange,
                    "light-blue": colors.sky,
                    "litepie-primary": colors.emerald,
                    "litepie-secondary": colors.gray,
                    purple: {
                        DEFAULT: "#A755DD",
                        50: "#F6EEFC",
                        100: "#EDDDF8",
                        200: "#DCBBF1",
                        300: "#CA99EB",
                        400: "#B977E4",
                        500: "#A755DD",
                        600: "#8E2AD0",
                        700: "#6E20A2",
                        800: "#4E1773",
                        900: "#2E0E44",
                    },
                    blue: {
                        DEFAULT: "#0993EC",
                        50: "#B1DEFC",
                        100: "#9DD6FB",
                        200: "#76C6FA",
                        300: "#4EB6F8",
                        400: "#27A5F7",
                        500: "#0993EC",
                        600: "#0771B6",
                        700: "#055080",
                        800: "#032E4A",
                        900: "#010C14",
                    },
                    pink: {
                        DEFAULT: "#F338C3",
                        50: "#FDE5F7",
                        100: "#FCD2F1",
                        200: "#FAABE6",
                        300: "#F885DA",
                        400: "#F55ECF",
                        500: "#F338C3",
                        600: "#E50EAE",
                        700: "#B00B86",
                        800: "#7B075E",
                        900: "#460435",
                    },

                    red: {
                        DEFAULT: "#FF3838",
                        50: "#FFF0F0",
                        100: "#FFDBDB",
                        200: "#FFB2B2",
                        300: "#FF8A8A",
                        400: "#FF6161",
                        500: "#FF3838",
                        600: "#FF0000",
                        700: "#C70000",
                        800: "#8F0000",
                        900: "#570000",
                    },

                    "opaque-blue": "#0993ec80",
                    "transparent-blue": "#0993EC2A",
                    "transparent-pink": "#FE5A752A",
                    "opaque-pink": "#f338c380",
                    "pink-red": {
                        DEFAULT: "#FE5A75",
                        50: "#FFFFFF",
                        100: "#FFFCFD",
                        200: "#FFD4DB",
                        300: "#FEABB9",
                        400: "#FE8397",
                        500: "#FE5A75",
                        600: "#FE2246",
                        700: "#E60127",
                        800: "#AF011E",
                        900: "#770114",
                    },
                    "light-brown": {
                        DEFAULT: "#FEC464",
                        50: "#FFFFFF",
                        100: "#FFFFFF",
                        200: "#FFF2DE",
                        300: "#FFE3B5",
                        400: "#FED38D",
                        500: "#FEC464",
                        600: "#FEAF2C",
                        700: "#F09602",
                        800: "#B97301",
                        900: "#815101",
                    },
                    "dark-pink": "#221825",
                    "dark-blue": "#0F182A",
                    "dark-1000": colors.stone[900],
                    "dark-900": colors.stone[900],
                    "dark-850": colors.stone[800],
                    "dark-800": colors.stone[800],
                    "dark-700": colors.stone[700],
                    "dark-600": colors.stone[600],
                    "dark-500": colors.stone[500],
                    "dark-400": colors.stone[400],
                    "low-emphesis": "#575757",
                    primary: "#BFBFBF",
                    "high-emphesis": "#E3E3E3",
                    "higher-emphesis": "#FCFCFD",
                    navy: {
                        50: colors.stone["50"],
                        100: colors.stone["100"],
                        200: colors.stone["200"],
                        300: colors.stone["300"],
                        400: colors.stone["400"],
                        450: colors.stone["400"],
                        500: colors.stone["500"],
                        600: colors.stone["600"],
                        700: colors.stone["700"],
                        750: colors.stone["700"],
                        800: colors.stone["800"],
                        900: colors.stone["900"],
                    },
                    "slate-100": "#E9EEF5",
                    // primary: colors.emerald["600"],
                    "primary-focus": colors.purple["700"],
                    "secondary-light": "#ff57d8",
                    secondary: "#F000B9",
                    "secondary-focus": "#BD0090",
                    "accent-light": colors.purple["400"],
                    accent: "#5f5af6",
                    "accent-focus": "#4d47f5",
                    info: colors.amber["500"],
                    "info-focus": colors.amber["600"],
                    success: colors.green["500"],
                    "success-focus": colors.green["600"],
                    warning: "#ff9800",
                    "warning-focus": "#e68200",
                    error: "#ff5724",
                    "error-focus": "#f03000",
                };
            },

            lineHeight: {
                "48px": "48px",
            },

            borderRadius: {
                none: "0",
                px: "1px",
                DEFAULT: "0.625rem",
            },
            boxShadow: {
                swap: "0px 50px 250px -47px rgba(39, 176, 230, 0.29)",
                liquidity: "0px 50px 250px -47px rgba(123, 97, 255, 0.23)",
                "pink-glow": "0px 57px 90px -47px rgba(250, 82, 160, 0.15)",
                "blue-glow": "0px 57px 90px -47px rgba(39, 176, 230, 0.17)",
                "pink-glow-hovered":
                    "0px 57px 90px -47px rgba(250, 82, 160, 0.30)",
                "blue-glow-hovered":
                    "0px 57px 90px -47px rgba(39, 176, 230, 0.34)",
                soft: "0 3px 10px 0 rgb(48 46 56 / 6%)",
                "soft-dark": "0 3px 10px 0 rgb(25 33 50 / 30%)",
            },
            ringWidth: {
                DEFAULT: "1px",
            },
            padding: {
                px: "1px",
                "3px": "3px",
            },
            minHeight: {
                empty: "128px",
                cardContent: "230px",
                fitContent: "fit-content",
                5: "1.25rem",
            },
            minWidth: {
                5: "1.25rem",
            },
            dropShadow: {
                currencyLogo: "0px 3px 6px rgba(15, 15, 15, 0.25)",
            },
            screens: {
                "3xl": "1600px",
            },

            keyframes: {
                ellipsis: {
                    "0%": { content: '"."' },
                    "33%": { content: '".."' },
                    "66%": { content: '"..."' },
                },
                opacity: {
                    "0%": { opacity: 0 },
                    "100%": { opacity: 100 },
                },
                "fade-out": {
                    "0%": {
                        opacity: 1,
                        visibility: "visible",
                    },
                    "100%": {
                        opacity: 0,
                        visibility: "hidden",
                    },
                },
            },
            opacity: {
                15: ".15",
            },
            zIndex: {
                1: "1",
                2: "2",
                3: "3",
                4: "4",
                5: "5",
            },
            linearBorderGradients: (theme) => ({
                directions: {
                    t: "to top",
                    tr: "to top right",
                    r: "to right",
                },
                colors: {
                    "blue-pink": ["#0993ecBF", "#f338c3BF"],
                    "blue-pink-hover": ["#0993ec99", "#f338c399"],
                    "pink-red-light-brown": ["#FE5A75", "#FEC464"],
                },
                background: {
                    "dark-1000": "#0D0415",
                    "dark-900": "#161522BF",
                    "dark-800": "#202231",
                    "dark-pink-red": "#4e3034",
                },
                border: {
                    1: "1px",
                    2: "2px",
                    3: "3px",
                    4: "4px",
                },
            }),
        },
    },

    plugins: [
        // require('@tailwindcss/forms'),
        require("@tailwindcss/typography"),
        require("tailwindcss-gradient"),
        require("tailwind-scrollbar-hide"),
        require("tailwind-scrollbar"),
        require("tailwind-bootstrap-grid")({
            containerMaxWidths: {
                sm: "540px",
                md: "720px",
                lg: "960px",
                xl: "1140px",
            },
        }),
        function ({ addVariant, addUtilities }) {
            addVariant(
                "supports-backdrop-blur",
                "@supports (backdrop-filter: blur(0)) or (-webkit-backdrop-filter: blur(0))"
            );
            addUtilities({
                '.content-contain': {
                    'content': 'contain',
                },
                '.contain-intrinsic-70': {
                    'contain-intrinsic-size': '70px'
                }
            });
        },

    ],
    mode: "jit",
    darkMode: "class",
    corePlugins: {
        container: false,
        textOpacity: false,
        backgroundOpacity: true,
        borderOpacity: false,
        divideOpacity: false,
        placeholderOpacity: false,
        ringOpacity: true,
    },
};
