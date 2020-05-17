const state = {
  code: null
}

const mutations = {
  SET_CODE (state, code) {
    state.code = code
  }
}

export default {
  namespaced: true,
  state,
  mutations
}