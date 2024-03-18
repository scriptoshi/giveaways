<script setup>
import Layout from "@/Layouts/AdminLayout.vue";
</script>
<template>
    <Layout>
        <div class="h-full max-h-full">
            <div
                class="max-w-7xl relative max-h-full mx-auto pb-10 py-12 lg:px-8"
            >
                <div
                    class="lg:grid lg:grid-cols-12 lg:gap-x-5 max-h-full flex flex-col"
                >
                    <!-- Payment details -->
                    <div
                        class="space-y-6 sm:px-6 lg:px-0 lg:col-span-8 max-h-full"
                    >
                        <section aria-labelledby="type-heading">
                            <form action="#" method="POST">
                                <div class="sm:rounded-md">
                                    <div
                                        class="bg-white dark:bg-gray-800 py-6 px-4 space-y-6 sm:p-6"
                                    >
                                        <slot />
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>
                    <aside
                        class="py-6 relative px-2 sm:px-6 lg:py-0 lg:px-0 lg:col-span-4 justify-center flex flex-col justify-items-center"
                    >
                        <div class="mt-16"></div>
                        <div class="px-3 pb-12 hidden md:block">
                            <h2 class="font-bold my-4 text-2xl">
                                <slot name="about-title" />
                            </h2>
                            <div class="mb-2 regular-24">
                                <slot name="about" />
                            </div>
                        </div>
                        <div
                            class="px-3 sticky top-24 grid grid-cols-2 space-x-4 flex-row"
                        >
                            <slot name="navigation" />
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </Layout>
</template>