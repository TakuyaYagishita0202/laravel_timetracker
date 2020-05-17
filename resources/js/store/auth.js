import {
    OK,
    CREATED,
    UNPROCESSABLE_ENTITY
} from '../util';

const state = {
    user: null,
    apiStatus: null,
    loginErrorMessages: null,
    registerErrorMessages: null
}

const getters = {
    check: state => !!state.user,
    username: state => state.user ? state.user.name : ''
}

const mutations = {
    SET_USER(state, user) {
        state.user = user
    },
    SET_API_STATUS(state, status) {
        state.apiStatus = status
    },
    SET_LOGIN_ERROR_MESSAGES(state, messages) {
        state.loginErrorMessages = messages
    },
    SET_REGISTER_ERROR_MESSAGES(state, messages) {
        state.registerErrorMessages = messages
    }
}

const actions = {
    async register(context, data) {
        context.commit('SET_API_STATUS', null)
        const response = await axios.post('/api/register', data)

        if (response.status === CREATED) {
            context.commit('SET_API_STATUS', true)
            context.commit('SET_USER', response.data)
            return false
        }

        context.commit('SET_API_STATUS', false)
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit('SET_REGISTER_ERROR_MESSAGES', response.data.errors)
        } else {
            context.commit('error/SET_CODE', response.status, {
                root: true
            })
        }
    },

    async login(context, data) {
        context.commit('SET_API_STATUS', null)
        const response = await axios.post('/api/login', data)

        if (response.status === OK) {
            context.commit('SET_API_STATUS', true)
            context.commit('SET_USER', response.data)
            return false
        }

        context.commit('SET_API_STATUS', false)
        if (response.status === UNPROCESSABLE_ENTITY) {
            context.commit('SET_LOGIN_ERROR_MESSAGES', response.data.errors)
        } else {
            context.commit('error/SET_CODE', response.status, {
                root: true
            })
        }

    },

    async logout(context) {
        context.commit('SET_API_STATUS', null)
        const response = await axios.post('/api/logout')

        if (response.status == OK) {
            context.commit('SET_API_STATUS', true)
            context.commit('SET_USER', null)
            return false
        }

        context.commit('SET_API_STATUS', false)
        context.commit('error/SET_CODE', response.status, {
            root: true
        })

    },

    async currentUser(context) {
        context.commit('SET_API_STATUS', null)
        const response = await axios.get('/api/user')
        const user = response.data || null
        if (response.status === OK) {
            context.commit('SET_API_STATUS', true)
            context.commit('SET_USER', user)
            return false
        }

        context.commit('SET_API_STATUS', false)
        context.commit('error/SET_CODE', response.status, {
            root: true
        })
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
