import {computed, toRefs, reactive, h} from 'vue';
export default {
  name: 'RefreshCwIcon',
  props: {
    size: {
      type: String,
      default: '24',
      validator: (s) => (!isNaN(s) || s.length >= 2 && !isNaN(s.slice(0, s.length -1)) && s.slice(-1) === 'x' )
    }
  },

  functional: true,

  setup: function setup(props, { attrs: attributes }) {
        const { size: propSize } = toRefs(props);
        const size = computed(() =>
            propSize.value.slice(-1) === "x"
                ? propSize.value.slice(0, propSize.value.length - 1) + "em"
                : parseInt(propSize.value) + "px"
        );
        const attrs = reactive({
            ...attributes,
            width: computed(() => attributes.width ?? size.value),
            height: computed(() => attributes.height ?? size.value),
        });
      return ()=> h('svg',{
          innerHTML:'<polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>',
          ...{"xmlns":"http://www.w3.org/2000/svg","width":24,"height":24,"viewBox":"0 0 24 24","fill":"none","stroke":"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round","class":"feather feather-refresh-cw"},
          ...attrs
      })
  }
}