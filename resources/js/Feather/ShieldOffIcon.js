import {computed, toRefs, reactive, h} from 'vue';
export default {
  name: 'ShieldOffIcon',
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
          innerHTML:'<path d="M19.69 14a6.9 6.9 0 0 0 .31-2V5l-8-3-3.16 1.18"></path><path d="M4.73 4.73L4 5v7c0 6 8 10 8 10a20.29 20.29 0 0 0 5.62-4.38"></path><line x1="1" y1="1" x2="23" y2="23"></line>',
          ...{"xmlns":"http://www.w3.org/2000/svg","width":24,"height":24,"viewBox":"0 0 24 24","fill":"none","stroke":"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round","class":"feather feather-shield-off"},
          ...attrs
      })
  }
}