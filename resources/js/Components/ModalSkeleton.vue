<script setup>
import { computed, ref } from "vue";

import { XMarkIcon } from "@heroicons/vue/24/outline";
import { useDraggable } from "@vueuse/core";

const props = defineProps({
  isOpen: Boolean,
  draggable: Boolean,
  sm: Boolean,
  md: Boolean,
  lg: Boolean,
  xl: Boolean,
  xl2: Boolean,
  footerBorder: Boolean,
  headerBorder: Boolean,
});
const emit = defineEmits(["close"]);
const close = () => emit("close");
const size = computed(() => {
  if (props.sm) return "max-w-sm";
  if (props.md) return "max-w-md";
  if (props.lg) return "max-w-lg";
  if (props.xl) return "max-w-xl";
  return "max-w-2xl";
});
const modal = ref();
const drag = ref();
const { style } = useDraggable(modal, {
  initialValue: { x: 725, y: 152 },
  handle: drag,
});
</script>
<template>
  <div v-if="isOpen">
    <teleport to="body">
      <div
        tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full justify-center items-center flex"
        aria-modal="true"
        role="dialog"
      >
        <div
          :class="size"
          :style="draggable ? style : {}"
          ref="modal"
          style="position: fixed"
          class="relative w-full h-full md:h-auto"
        >
          <!-- Modal content -->
          <div
            class="relative border border-gray-300 dark:border-gray-600 bg-white rounded-lg p-2 shadow dark:bg-gray-800"
          >
            <!-- Modal header -->
            <div
              ref="drag"
              :class="[
                headerBorder ? 'border-b' : '',
                draggable ? 'cursor-move' : '',
              ]"
              class="flex items-start justify-between p-4 rounded-t dark:border-gray-600"
            >
              <h3
                v-if="$slots.header"
                class="text-xl font-semibold text-gray-900 dark:text-white"
              >
                <slot name="header" />
              </h3>
              <div v-else />
              <button
                type="button"
                @click="close"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                data-modal-hide="staticModal"
              >
                <XMarkIcon class="w-5 h-5" />
              </button>
            </div>
            <!-- Modal body -->
            <div class="px-6 space-y-6">
              <slot />
            </div>
            <!-- Modal footer -->
            <div
              v-if="$slots.footer"
              :class="footerBorder ? 'border-t' : ''"
              class="p-6 border-gray-200 rounded-b dark:border-gray-600"
            >
              <slot name="footer" />
            </div>
          </div>
        </div>
      </div>
    </teleport>
    <div
      @click="close"
      class="bg-gray-900 opacity-50 dark:opacity-80 fixed inset-0 z-40"
    ></div>
  </div>
</template>
