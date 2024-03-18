 <script setup> 
    import {computed} from "vue";
    import {breakpointsBootstrapV5, useBreakpoints} from "@vueuse/core";
    const props = defineProps({
    	responsive: {type: Boolean, default: false},
    	onlyz: {type: Boolean, default: false},
    });
    const breakpoints = useBreakpoints(breakpointsBootstrapV5);
    const md = breakpoints.smaller("lg");
    const width = computed(() => ((props.responsive && md.value)||props.onlyz ? 33 : 89));
</script>
 <template>
    <svg
        id="Layer_1"
        :width="width"
        height="29"
        data-name="Layer 1"
        xmlns="http://www.w3.org/2000/svg"
        :viewBox="'0 0 '+width+' 29'"
    >
        <title>zobit-logo</title>
        <path
            d="M1.7,5.07,7.52,1.43,9.3.31c.27-.18.41-.17.4.21s0,1,0,1.51.17.61.64.61c4.67,0,9.35,0,14,0,2.34,0,4.67,0,7,0,.44,0,.54.06.3.49Q27,11.38,22.3,19.67c-.1.19-.18.37-.46.37H16.33L23.9,7.25c-.18-.16-.34-.1-.5-.1-4.29,0-8.59,0-12.89,0-.49,0-.66.12-.63.62,0,.72,0,1.44,0,2.27Z"
            style="fill: #faba38"
        ></path>
        <path
            d="M23.9,19.73,32,24.29,23.91,29l-.19-2.33H1.5c.87-1.54,1.69-3,2.51-4.41l3.51-6.15c.82-1.45,1.63-2.92,2.45-4.37.46-.82.92-1.63,1.4-2.43a.45.45,0,0,1,.33-.2H17L9.55,21.94h.64c4.26,0,8.53,0,12.79,0,.48,0,.73-.07.71-.64"
            style="fill: #008d36"
        ></path>
        <path
            v-show="!md || !props.onlyz"
            class="d-none d-lg-block"
            d="M38,11h4.5a4.52,4.52,0,0,1,4.47,4.5v6.72a4.52,4.52,0,0,1-4.47,4.47H38a4.51,4.51,0,0,1-4.47-4.47V15.47A4.51,4.51,0,0,1,38,11Zm0,4.5v6.72a2.13,2.13,0,0,0,.66,1.56,2.25,2.25,0,0,0,3.18,0,2.13,2.13,0,0,0,.66-1.56V15.47a2.25,2.25,0,0,0-4.5,0Z"
        ></path>
        <path
            v-show="!md || !props.onlyz"
            class="d-none d-lg-block"
            d="M51.4,26.66V6.5h4.47v6.72a2.78,2.78,0,0,1,.7-1.69A2.12,2.12,0,0,1,58.12,11h2.25a4.52,4.52,0,0,1,4.47,4.5v6.72a4.52,4.52,0,0,1-4.47,4.47Zm4.47-2.25h2.25a2.17,2.17,0,0,0,1.59-.66,2.13,2.13,0,0,0,.66-1.56V15.47a2.27,2.27,0,0,0-2.25-2.25,2.27,2.27,0,0,0-2.25,2.25Z"
        ></path>
        <path
            v-show="!md || !props.onlyz"
            class="d-none d-lg-block"
            d="M69.31,8.75V6.5h4.47V8.75Zm0,17.91V11h4.47V26.66Z"
        ></path>
        <path
            v-show="!md || !props.onlyz"
            class="d-none d-lg-block"
            d="M80.52,6.5H85V11h2.24v2.25H85v9a2,2,0,0,0,.71,1.65,2.2,2.2,0,0,0,1.32.57h.21v2.25H85a4.51,4.51,0,0,1-4.48-4.47v-9H78.28V11h2.24Z"
        ></path>
    </svg>
</template>