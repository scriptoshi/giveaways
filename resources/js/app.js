import { createInertiaApp } from '@inertiajs/vue3';
import { VueQueryPlugin } from '@tanstack/vue-query';
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { OhVueIcon } from "oh-vue-icons";
import { UseWagmiPlugin } from 'use-wagmi';

import "tippy.js/dist/tippy.css";
import "tippy.js/themes/light.css";
import "v-calendar/dist/style.css";
// eslint-disable-next-line import/order
import { createApp, h } from "vue";

import { createI18n } from "vue-i18n";
import VueTippy from "vue-tippy";

import { useWagmiConfig } from '@/Wagmi/hooks/config';
import { vueQueryOptions } from '@/Wagmi/hooks/query';
// eslint-disable-next-line import/order
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import "../css/app.css";
import "./bootstrap";
// eslint-disable-next-line import/order
import messages from "@/vue-i18n-locales.generated";
// Hijack BigInt https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/BigInt#use_within_json
// eslint-disable-next-line no-extend-native
BigInt.prototype.toJSON = function () { return this.toString(); };
createInertiaApp({
    progress: { color: "#4B5563" },
    title: (title) => `${title} | gas`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const i18n = createI18n({
            locale: props.initialPage.props.locale,
            fallbackLocale: "en", // set fallback locale
            messages, // set locale messages,
            legacy: false,
        });

        const config = useWagmiConfig(
            props.initialPage.props.chains,
            {
                showQrModal: true,
                projectId: props.initialPage.props.walletConnectProjectId,
                metadata: {
                    name: props.initialPage.props.config.appName,
                    description: props.initialPage.props.config.appDescription,
                    url: props.initialPage.props.config.appUrl,
                }
            }
        );
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, window.Ziggy)
            .use(i18n)
            .use(VueTippy)
            .use(UseWagmiPlugin, { config })
            .use(VueQueryPlugin, vueQueryOptions)
            .component('OhVueIcon', OhVueIcon)
            .mount(el);
    },
});

