import {computed, toRefs, reactive, h} from 'vue';
export default {
  name: 'Navigation2Icon',
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
          innerHTML:'<polygon points="12 2 19 21 12 17 5 21 12 2"></polygon>',
          ...{"xmlns":"http://www.w3.org/2000/svg","width":24,"height":24,"viewBox":"0 0 24 24","fill":"none","stroke":"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round","class":"feather feather-navigation-2"},
          ...attrs
      })
  }
}