<script setup>
import { computed } from "vue";

import { PopoverPanel } from "@headlessui/vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    CoBuzzfeed,
    CoMixer,
    FaBalanceScale,
    IoWallet,
    MdGeneratingtokensSharp,
    MdLockpersonRound,
    OiRepoLocked,
    RiContrastDrop2Fill,
    RiMapPinUserFill,
    RiRocket2Fill,
} from "oh-vue-icons/icons";
import { uid } from "uid";
import { useAccount } from "use-wagmi";

import VueIcon from "@/Components/VueIcon.vue";

const chId = computed(() => usePage().props.chainId);
const { chainId } = useAccount();
const menu = computed(() => [
    {
        id: uid(),
        name: "ERC20 Token",
        href: window.route("tokens.create", {
            chainId: chainId.value ?? chId.value ?? 5,
            type: "standard",
        }),
        icon: MdGeneratingtokensSharp,
        description: "",
    },
    {
        id: uid(),
        name: "Tax Token",
        href: window.route("tokens.create", {
            chainId: chainId.value ?? chId.value ?? 5,
            type: "taxtoken",
        }),
        icon: MdGeneratingtokensSharp,
        description: "",
    },
    {
        id: uid(),
        name: "Crosschain Token",
        href: window.route("tokens.create", {
            chainId: chainId.value ?? chId.value ?? 5,
            type: "crosschain",
        }),
        icon: MdGeneratingtokensSharp,
        description: "",
    },
    {
        id: uid(),
        name: "Nft Launchpad",
        /* href: window.route("nfts.create", {
            chainId: chainId.value ?? chId.value??5,
        }), */
        href: "#",
        icon: RiMapPinUserFill,
        description: "",
    },
    {
        id: uid(),
        name: "Fairlaunch",
        href: window.route("launchpads.create", {
            launchpad: "fairlaunch",
            chainId: chainId.value ?? chId.value ?? 5,
        }),
        icon: FaBalanceScale,
        description: "",
    },
    {
        id: uid(),
        name: "Fair Liquidity Launch",
        href: window.route("launchpads.create", {
            launchpad: "fairliquidity",
            chainId: chainId.value ?? chId.value ?? 5,
        }),
        icon: FaBalanceScale,
        description: "",
    },
    {
        id: uid(),
        name: "Private Sale",
        href: window.route("launchpads.create", {
            launchpad: "privatesale",
            chainId: chainId.value ?? chId.value ?? 5,
        }),
        icon: OiRepoLocked,
        description: "",
    },
    {
        id: uid(),
        name: "IDO Launchpad",
        href: window.route("launchpads.create", {
            launchpad: "launchpad",
            chainId: chainId.value ?? chId.value ?? 5,
        }),
        icon: RiRocket2Fill,
        description: "",
    },

    {
        id: uid(),
        name: "Token Airdrop",
        /* href: window.route("airdrops.create", {
            chainId: chainId.value ?? chId.value??5,
        }), */
        href: "#",
        icon: RiContrastDrop2Fill,
        description: "",
    },
    {
        id: uid(),
        name: "Token / Lp Lock",
        /* href: window.route("locks.create", {
            chainId: chainId.value ?? chId.value??5,
        }), */
        href: "#",
        icon: MdLockpersonRound,
        description: "",
    },
    {
        id: uid(),
        name: "Token Staking",
        /*
        href: window.route("staking.create", {
            chainId: chainId.value ?? chId.value??5,
        }), */
        href: "#",
        icon: IoWallet,
        description: "",
    },

    {
        id: uid(),
        name: "Liquidity Farm",
        /*
        href: window.route("farming.create", {
            chainId: chainId.value ?? chId.value??5,
        }), */
        href: "#",
        icon: CoBuzzfeed,
        description: "",
    },
    {
        id: uid(),
        name: "Multisend Transaction",
        /* href: window.route("sender.tokens", {
            chainId: chainId.value ?? chId.value??5,
        }), */
        href: "#",
        icon: CoMixer,
        description: "",
    },
]);
</script>
<template>
    <transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-1 opacity-0"
        enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-1 opacity-0">
        <PopoverPanel
            class="w-full bg-white dark:bg-gray-800 sm:w-[320px] fixed bottom-0 top-[55px] shadow-md left-0 right-0 sm:absolute sm:top-[unset] sm:bottom-[unset] sm:left-[unset] mt-2 sm:rounded-sm rounded-b-none border-t sm:border border-gray-300 dark:border-gray-600 z-50">
            <div class="overflow-hidden sm:rounded-sm py-3">
                <div
                    class="relative max-h-screen sm:max-h-96 overflow-auto scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-700 scrollbar-thumb-rounded-sm grid gap-1 py-1.5">
                    <Link v-for="item in menu" :key="item.name" :href="item.href"
                        class="flex items-center space-x-3 px-8 sm:px-4 py-1.5 transition duration-150 ease-in-out hover:bg-emerald-300/40 dark:hover:bg-emerald-400/30 focus:outline-none focus-visible:ring focus-visible:ring-emerald-500 focus-visible:ring-opacity-50">
                    <VueIcon :icon="item.icon" class="flex h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ item.name }}
                    </div>
                    </Link>
                </div>
            </div>
        </PopoverPanel>
    </transition>
</template>
