import Vuex from 'vuex';
import UserStore from "./UserStore";
import CurrentChannelStore from "./CurrentChannelStore"
import EmojisStore from "./EmojisStore";
import UserNotificationsBannerStore from "./UserNotificationsBannerStore";

export default new Vuex.Store({
    modules: {
        user: UserStore,
        currentChannel: CurrentChannelStore,
        emojis: EmojisStore,
        notification: UserNotificationsBannerStore
    }
})
