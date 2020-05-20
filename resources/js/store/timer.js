import moment from "moment";
import {
    OK,
} from '../util';

const state = {
    timers: [],
}

const getters = {
    // storeからtimersArrayを生成する場合
    timersArray: state => {
        if (state.timers[0] !== null) {
            let timersArray = [[]]
            // timers[0]をtimesArray[0]に格納する
            timersArray[0].push(state.timers[0]);

            //timersでループを回す
            for (let i = 0; i < state.timers.length - 1; i++) {
                // timers[1]のstarted_atの年月日がtimers[0]のstarted_atの年月日と同じ場合は、timers[1]を配列に格納
                if (
                    moment(state.timers[i].started_at).isSame(
                        moment(state.timers[i + 1].started_at),
                        "day"
                    )
                ) {
                    timersArray[timersArray.length - 1].push(
                        state.timers[i + 1]
                    );
                } else {
                    // 異なる場合は新たに配列を作成して格納
                    timersArray.push([]);
                    timersArray[timersArray.length - 1].push(
                        state.timers[i + 1]
                    );
                }
            }
            return timersArray
        }
    }
}

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
