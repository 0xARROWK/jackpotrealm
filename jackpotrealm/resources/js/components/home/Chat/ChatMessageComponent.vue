<template>

    <div>
        <v-popover v-if="(this.user.role.includes('moderator') || this.user.role.includes('streamer')) && !this.role.includes('streamer')" placement="bottom-start" offset="0" class="h-full w-full cursor-pointer">
            <div class="px-5 pt-1 pb-1" :class="this.role.includes('sub-1') || this.role.includes('sub-2') || this.role.includes('sub-3') ? 'bg-gray-700' : ''">
                <span class="font-semibold" :style="authorColor">{{ username }}</span>
                <span class="font-semibold text-th-chat-text-color">:</span>
                <span class="font-semibold break-words" :class="this.role.includes('sub-1') || this.role.includes('sub-2') || this.role.includes('sub-3') ? 'text-gray-300' : 'text-th-chat-text-color'" v-html="transformMessage"> {{ transformMessage }}</span>
            </div>
            <template slot="popover">
                <a class="cursor-pointer bg-th-btn hover:bg-th-btn-hover flex-shrink-0 text-th-text-color text-base font-semibold p-3 rounded-lg shadow-md focus:outline-none py-2"
                   @click="deleteMessage()">{{ __('Delete', 1, []) }}</a>
                <form-error v-if="this.actionError" :error-msg="this.actionError[0]" :error-count="this.actionError[1]" :error-replace="this.actionError[2]"></form-error>
            </template>
        </v-popover>
        <div v-else class="px-5 pt-1 pb-1" :class="this.role.includes('sub-1') || this.role.includes('sub-2') || this.role.includes('sub-3') ? 'bg-gray-700' : ''">
            <span class="font-semibold" :style="authorColor">{{ username }}</span>
            <span class="font-semibold text-th-chat-text-color">:</span>
            <span class="font-semibold break-words" :class="this.role.includes('sub-1') || this.role.includes('sub-2') || this.role.includes('sub-3') ? 'text-gray-300' : 'text-th-chat-text-color'" v-html="transformMessage"> {{ transformMessage }}</span>
        </div>
    </div>

</template>

<script>

    import { mapGetters } from "vuex";
    import GlobalStore from "../../store/GlobalStore";
    import FormError from "../../general/form/FormErrorComponent";

    export default {

        name: "ChatMessage",
        components: {FormError},
        store: GlobalStore,

        props: ['id', 'username', 'message', 'color', 'role', 'mid'],

        data: function() {
            return {
                actionError: []
            }
        },

        computed: {

            ...mapGetters(['currentChannelInfos', 'user']),

            authorColor: function () {
                return {
                    color: '#' + this.color
                }
            },

            transformMessage: function() {
                try {
                    let message = this.message
                    let streamer = this.currentChannelInfos.channelName
                    message = message.replaceAll(/(:[a-zA-Z0-9_*]{3,35}:)/g, function (matches, contents) {
                        return '<img class="display-emoji" height="24" width="24" src="/storage/users/' + streamer + '/emojis/' + contents.split('*')[0].replace(':', '') + '/' + contents.split('*')[1].replace(':', '') + '.png" alt="' + contents.replaceAll(':', '') + '">'
                    })
                    return message
                } catch (e) {}
            }

        },

        methods: {

            deleteMessage: function () {
                this.actionError = []
                axios
                    .post(this.$apiURL + '/chat/delete-message', {
                        mid: this.mid
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (!response.data || !response.data.success) this.actionError = ['An error occurred', 1, []]
                    })
                    .catch(error => {
                        this.actionError = ['An error occurred', 1, []]
                    })
            }

        }

    }

</script>
