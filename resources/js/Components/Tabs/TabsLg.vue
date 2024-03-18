<script setup>
import { reactive, ref } from "vue";
const props = defineProps({
    items: Array,
    modelValue: Object,
});
const emit = defineEmits(["update:modelValue"]);
const tab = ref(null);
const pointer = reactive({
    width: null,
    left: null,
});
const select = (selectedStatus, event) => {
    event && event.preventDefault();
    if (selectedStatus.name !== props.currentOrderStatus) {
        const {  clientWidth, offsetLeft } = event.currentTarget.parentNode;
        pointer.width =clientWidth
        pointer.left = offsetLeft+(clientWidth/2)
    }
   
    emit("update:modelValue", selectedStatus);
};

</script>
<template>
    <div class="mb-5 hidden md:block relative box-border">
        <ul
            class="border-b border-gray-300 dark:border-gray-600 relative flex list-none m-0 p-0 align-baseline bg-none tabs"
            ref="tab"
        >
            <li
                v-for="item in items"
                :key="item.name"
                :class="{
                    active: modelValue.slug == item.slug,
                }"
                class="relative mr-3 whitespace-nowrap selected"
            >
                <a
                    :class="[modelValue.slug == item.slug?'text-gray-900 dark:text-emerald-300':'text-gray-400',{active: modelValue.slug == item.slug}]"
                    class=" after:transition-colors after:duration-300 font-medium text-base font-mono"
                    v-bind="{ ...item.count>0&&{'data-count':item.count}}"
                    @click="select(item, $event)"
                    href="#"
                >
                    {{ item.name }}
                </a>
            </li>
            <li
                v-if="pointer.left"
                class="pointer"
                :style="{
                    width:pointer.width,
                    transform: `translateX(${pointer.left}px)`,
                }"
            />
        </ul>
    </div>
</template>
<style scoped>
    li a[data-count]:after {
        content: attr(data-count);
    }
    ul li a {
        display: block;
        padding: 3px 5px 10px;
        line-height: 42px;
        text-decoration: none;
        text-transform: uppercase;
        -webkit-transition: color 0.1s ease-in-out;
        -o-transition: color 0.1s ease-in-out;
        transition: color 0.1s ease-in-out;
    }
    ul li a[data-count]:after {
        background-color: #cbd5e1;
        display: inline-block;
        margin-left: 5px;
        padding: 4px 7px 3px;
        font-size: 12px;
        line-height: 14px;
        font-weight: 700;
        text-align: center;
        min-width: 6px;
        color: #fff;
        border-radius: 50%;
    }
    .dark ul li a:after {
        background-color: #374151;
        color: #fff;
    }
    ul li a.active:after,
    ul li a:hover:after,
    ul .tabs li a:active:after {
        background-color: #1dbf73;
        color: #fff;
    }
  li.pointer:after {
   content: "";
    position: absolute;
    width: 12px;
    height: 12px;
    border-color: #dadbdd;
    border-style: solid;
    border-width: 1px 0 0 1px;
    -webkit-transform: rotate(45deg);
    transform: rotate(45deg);
    left: calc(50% - 6px);
    bottom: -7px;
    background-color: #f5f5f5;
}
.dark .pointer:after{
    background: #111827;
    border-color: #4b5563;
}
li.pointer{
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 0;
    -webkit-transition: width .3s,-webkit-transform .3s;
    transition: width .3s,-webkit-transform .3s;
    -o-transition: transform .3s,width .3s;
    transition: transform .3s,width .3s;
    transition: transform .3s,width .3s,-webkit-transform .3s;
    pointer-events: none
}
</style>