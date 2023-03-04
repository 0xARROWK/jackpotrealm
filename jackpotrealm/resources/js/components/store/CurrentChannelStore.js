const state = {
    infos: null
}

const mutations = {

    LOAD_CHANNEL_INFOS: (state, {channelName, streamTitle, streamDescription, streamStatus}) => {
        state.infos = {
            channelName,
            streamTitle,
            streamDescription,
            streamStatus,
            key: 1
        }
    },

}

const getters = {

    currentChannelInfos: state => state.infos,

}

const actions = {

    loadChannelInfos: (store, {channelName, streamTitle, streamDescription, streamStatus}) => {
        store.commit('LOAD_CHANNEL_INFOS', {channelName, streamTitle, streamDescription, streamStatus})
    }

}

export default {
    state,
    mutations,
    actions,
    getters,
    strict: true
}
