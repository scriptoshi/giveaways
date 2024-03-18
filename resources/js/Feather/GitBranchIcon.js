import {computed, toRefs, reactive, h} from 'vue';
export default {
  name: 'GitBranchIcon',
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
          innerHTML:'<line x1="6" y1="3" x2="6" y2="15"></line><circle cx="18" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path>',
          ...{"xmlns":"http://www.w3.org/2000/svg","width":24,"height":24,"viewBox":"0 0 24 24","fill":"none","stroke":"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round","class":"feather feather-git-branch"},
          ...attrs
      })
  }
}