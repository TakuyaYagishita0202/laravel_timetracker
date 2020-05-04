<template>
  <div>
    <compact-picker v-model="colors"></compact-picker>
  </div>
</template>

<script>
import { Compact } from 'vue-color'

var defaultProps = {
  hex: '#194d33',
  hsl: {
    h: 150,
    s: 0.5,
    l: 0.2,
    a: 1
  },
  hsv: {
    h: 150,
    s: 0.66,
    v: 0.30,
    a: 1
  },
  rgba: {
    r: 25,
    g: 77,
    b: 51,
    a: 1
  },
  a: 1
}

export default {
  components: {
    'compact-picker': Compact
  },
  data () {
    return {
      colors: defaultProps
    }
  }
}
</script>
