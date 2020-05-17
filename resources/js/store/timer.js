const state = {
    timers: [],
}

const getters = {}

const mutations = {
    SET_TIMERS(state, timers) {
        state.timers = timers
    }
}

const actions = {
    async fetchTimers(context) {
        const response = await axios.get('/api/timers')

        //通信に失敗した場合
        if (response.status !== OK) {
            this.$store.commit('error/SET_CODE', response.data)
            return false
        }

        context.commit('SET_TIMERS', response.data)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
