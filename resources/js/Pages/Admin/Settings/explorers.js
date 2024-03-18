import { computed, reactive } from "vue";

import { usePage } from "@inertiajs/vue3";

import arbiscan from "@/logos/explorers/logo-arbiscan.svg?url";
import aurorascan from "@/logos/explorers/logo-aurorascan.svg?url";
import bobascan from "@/logos/explorers/logo-boba.svg?url";
import bscscan from "@/logos/explorers/logo-bscscan.svg?url";
import celoscan from "@/logos/explorers/logo-celoscan.svg?url";
import cronoscan from "@/logos/explorers/logo-cronoscan.svg?url";
import etherscan from "@/logos/explorers/logo-etherscan.svg?url";
import ftmscan from "@/logos/explorers/logo-ftmscan.svg?url";
import gnosiscan from "@/logos/explorers/logo-gnosiscan.svg?url";
import moonbeam from "@/logos/explorers/logo-moonbeam.svg?url";
import moonriver from "@/logos/explorers/logo-moonriver.svg?url";
import optimism from "@/logos/explorers/logo-optimism.svg?url";
import polygonscan from "@/logos/explorers/logo-polygonscan.svg?url";
import snowtrace from "@/logos/explorers/logo-snowtrace.svg?url";
import { SupportedChainId } from "@/Web3Modal";

export const explorers = reactive({
    [SupportedChainId.GNOSIS]: {
        slug: "Gnosisscan",
        url: "https://gnosisscan.io/",
        logo: gnosiscan,
        name: 'GNOSISSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.GNOSISSCAN_API_KEY)
    },
    [SupportedChainId.ARBITRUM_ONE]: {
        slug: "Arbiscan",
        url: "https://arbiscan.io/",
        logo: arbiscan,
        name: 'ARBISCAN_API_KEY',
        key: computed(() => usePage().props.keys?.ARBISCAN_API_KEY)
    },
    [SupportedChainId.BINANCE]: {
        slug: "Bscscan",
        url: "https://bscscan.com/",
        logo: bscscan,
        name: 'BSCSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.BSCSCAN_API_KEY)
    },

    [SupportedChainId.MAINNET]: {
        slug: "Etherscan",
        url: "https://etherscan.io/",
        logo: etherscan,
        name: 'ETHERSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.ETHERSCAN_API_KEY)
    },
    [SupportedChainId.OPTIMISM]: {
        slug: "Optimism",
        url: "https://optimistic.etherscan.io/",
        logo: optimism,
        name: 'OPTIMISTICSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.OPTIMISTICSCAN_API_KEY)
    },
    [SupportedChainId.POLYGON]: {
        slug: "Polygonscan",
        url: "https://polygonscan.com/",
        logo: polygonscan,
        name: 'POLYGONSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.POLYGONSCAN_API_KEY)
    },
    [SupportedChainId.AVALANCHE]: {
        slug: "Snowtrace",
        url: "https://snowtrace.com/",
        logo: snowtrace,
        name: 'SNOWTRACE_API_KEY',
        key: computed(() => usePage().props.keys?.SNOWTRACE_API_KEY)
    },
    [SupportedChainId.MOONRIVER]: {
        slug: "Moonriver",
        url: "https://moonriver.moonscan.io/",
        logo: moonriver,
        name: 'MOONRIVERSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.MOONRIVERSCAN_API_KEY)
    },
    [SupportedChainId.MOONBEAM]: {
        slug: "Moonbeam",
        url: "https://moonbeam.moonscan.io/",
        logo: moonbeam,
        name: 'MOONBEAMSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.MOONBEAMSCAN_API_KEY)
    },

    [SupportedChainId.AURORA]: {
        slug: "Aurorascan",
        url: "https://aurorascan.dev/",
        logo: aurorascan,
        name: 'AURORASCAN_API_KEY',
        key: computed(() => usePage().props.keys?.AURORASCAN_API_KEY)
    },

    [SupportedChainId.BOBA]: {
        slug: "Bobascan",
        url: "https://bobascan.com/",
        logo: bobascan,
        name: 'BOBASCAN_API_KEY',
        key: computed(() => usePage().props.keys?.BOBASCAN_API_KEY)
    },
    [SupportedChainId.CELO]: {
        slug: "Celoscan",
        url: "https://celoscan.io/",
        logo: celoscan,
        name: 'CELOSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.CELOSCAN_API_KEY)
    },
    [SupportedChainId.CRONOS]: {
        slug: "Cronoscan",
        url: "https://cronoscan.com/",
        logo: cronoscan,
        name: 'CRONOSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.CRONOSCAN_API_KEY)
    },
    [SupportedChainId.FANTOM]: {
        slug: "Ftmscan",
        url: "https://ftmscan.com/",
        logo: ftmscan,
        name: 'FTMSCAN_API_KEY',
        key: computed(() => usePage().props.keys?.FTMSCAN_API_KEY)
    },
});
