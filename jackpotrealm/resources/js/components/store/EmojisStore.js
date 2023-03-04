const state = {
    emojis: null
}

const mutations = {

    ADD_EMOJI: (state, {name, tier}) => {
        if (state.emojis === null) state.emojis = {}
        if (!state.emojis[tier]) state.emojis = Object.assign({}, state.emojis, {[tier]: []})
        //state.emojis[tier] = state.emojis[tier] ? state.emojis[tier] : []
        state.emojis[tier].push({name})
    },

    UPDATE_EMOJI: (state, {name, newTier, currentTier, index}) => {
        state.emojis[currentTier].splice(index, 1)
        if (state.emojis[currentTier].length === 0) delete state.emojis[currentTier]
        if (!state.emojis[newTier]) state.emojis = Object.assign({}, state.emojis, {[newTier]: []})
        state.emojis[newTier].push({name})
    },

    DELETE_EMOJI: (state, {tier, index}) => {
        state.emojis[tier].splice(index, 1)
        if (state.emojis[tier].length === 0) delete state.emojis[tier]
        if (state.emojis && Object.keys(state.emojis).length === 0) state.emojis = null
    }

}

const getters = {

    emojis: state => state.emojis,

}

const actions = {

    addEmoji: (store, {name, tier}) => {
        store.commit('ADD_EMOJI', {name, tier})
    },

    updateEmoji: (store, {name, newTier, currentTier, index}) => {
        store.commit('UPDATE_EMOJI', {name, newTier, currentTier, index})
    },

    deleteEmoji: (store, {tier, index}) => {
        store.commit('DELETE_EMOJI', {tier, index})
    }

}

export default {
    state,
    mutations,
    actions,
    getters,
    strict: true
}
