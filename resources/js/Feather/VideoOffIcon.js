import {computed, toRefs, reactive, h} from 'vue';
export default {
  name: 'VideoOffIcon',
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
          innerHTML:'<path d="M16 16v1a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2m5.66 0H14a2 2 0 0 1 2 2v3.34l1 1L23 7v10"></path><line x1="1" y1="1" x2="23" y2="23"></line>',
          ...{"xmlns":"http://www.w3.org/2000/svg","width":24,"height":24,"viewBox":"0 0 24 24","fill":"none","stroke":"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round","class":"feather feather-video-off"},
          ...attrs
      })
  }
}