<template>

    <div id="stream-chat" class="bg-th-chat flex flex-col h-th-mobile-chat-height md:h-th-chat-height md:border-l md:border-th-color-2">

        <div class="shadow-md px-5 py-3 text-lg font-semibold text-th-color-2 flex-none">
            {{ __('Chat', 1, []) }}
        </div>

        <div id="stream-chat-messages-space" class="overflow-x-hidden overflow-y-auto no-scrollbar h-full">

            <div v-for="message in messages">
                <chat-message
                    :id="message.id"
                    :username="message.user"
                    :message="message.text"
                    :color="message.color"
                    :role="message.role"
                    :mid="message.mid">
                </chat-message>
            </div>

        </div>

        <custom-emojis-suggestions @useEmoji="this.useCustomEmoji" ref="customEmojisSuggestions"></custom-emojis-suggestions>

        <div class="px-5 py-3 text-lg font-semibold flex-none bg-th-menu w-full">
            <span v-if="!this.logged" class="grid grid-cols-12 gap-2">
                <p class="col-span-12 text-sm text-red-500">{{ __('You must be logged in to send messages in the chat') }}</p>
            </span>
            <span v-else-if="this.logged && this.user && !this.user.verified">
                <p class="col-span-12 text-sm text-red-500">{{ __('You must have verified your email address to send messages in the chat') }}</p>
            </span>
            <span v-else-if="this.connectionStatus === 0" class="grid grid-cols-12 gap-2">
                <p class="col-span-12 text-sm text-red-500">{{ __('You have been disconnected from the chat') }}</p>
            </span>
            <span v-else-if="this.connectionStatus === 1" class="grid grid-cols-12 gap-2">
                <p class="col-span-12 text-sm text-red-500">{{ __('Connection to the chat failed, retrying') }}</p>
            </span>
            <span v-else-if="this.connectionStatus === 2" class="grid grid-cols-12 gap-2">
                <p class="col-span-12 text-sm">{{ __('Connection to the chat') }}</p>
            </span>
            <span v-else-if="this.logged && this.user && this.user.verified && this.connectionStatus === 3" class="grid grid-cols-12 gap-2">
                <span class="hidden lg:block absolute" style="position: absolute;">
                    <VEmojiPicker v-if="this.showEmojisBox" v-click-outside="closeEmojisBoxOnOutsideClick" id="emojisBox" class="absolute" style="margin-top: -450px" @select="selectEmoji"></VEmojiPicker>
                </span>
                <input class="col-span-12 xs:col-span-9 lg:col-span-12 xl:col-span-7 bg-th-body flex-1 appearance-none w-full py-1 px-2 border-2 border-th-btn text-th-text-color rounded-lg text-sm focus:outline-none"
                       :placeholder='__("Try to type \":\"")' v-on:keyup="showCustomEmojis" v-on:keyup.enter="sendMessage()" v-model="newMessage" ref="newMessage"/>
                <button-loader @submitted="openEmojisBox()" text="😀" btn-class="col-span-2 hidden lg:block" :can-load="false" :take-full-width="false" :prevent-submit="false"></button-loader>
                <button-loader @submitted="sendMessage()" :text="__('Send')" btn-class="col-span-3" :can-load="false" :take-full-width="false" :prevent-submit="false"></button-loader>
                <form-error class="col-span-12" v-if="this.sendMessageError" :error-msg="this.sendMessageError[0]" :error-count="this.sendMessageError[1]" :error-replace="this.sendMessageError[2]"></form-error>
            </span>

        </div>

    </div>

</template>

<script>

    import Echo from "laravel-echo";
    import { VEmojiPicker } from 'v-emoji-picker';

    import ChatMessage from "./ChatMessageComponent";
    import CustomEmojisSuggestions from "./CustomEmojisSuggestionsComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";
    import FormError from "../../general/form/FormErrorComponent";

    import { mapGetters } from "vuex";
    import GlobalStore from "../../store/GlobalStore";

    export default {

        name: "StreamChat",

        store: GlobalStore,

        components: {
            ChatMessage,
            ButtonLoader,
            FormError,
            VEmojiPicker,
            CustomEmojisSuggestions
        },

        computed: {

            ...mapGetters(['user', 'logged'])

        },

        data: function () {
            return {
                connectionStatus: 2,
                messages: [],
                newMessage: '',
                showEmojisBox: false,
                canHideEmojisBox: false,
                sendMessageError: [],
                echo: null
            }
        },

        created: function () {
            if (!this.getLSI('mid')) this.setLSI('mid', 1)
        },

        mounted: function () {

            this.echo = new Echo({
                broadcaster: 'pusher',
                key: process.env.MIX_PUSHER_APP_KEY,
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                encrypted: false,
                disableStats: true,
                wsHost: process.env.MIX_WEBSOCKET_HOST,
                wsPort: process.env.MIX_WEBSOCKET_PORT,
                enabledTransports: ['ws']
            });

            this.echo.channel('chat')
                .listen('NewChatMessage', (e) => {
                    if (e.message && e.user && e.id && e.color) {
                        this.messages.push({
                            id: e.id,
                            user: e.user,
                            text: e.message,
                            color: e.color,
                            role: e.role,
                            mid: e.mid
                        });
                    }
                });

            this.echo.connector.pusher.connection.bind('disconnected', () => {
                this.connectionStatus = 0
            });

            this.echo.connector.pusher.connection.bind('unavailable', () => {
                this.connectionStatus = 1
            });

            this.echo.connector.pusher.connection.bind('connected', () => {
                this.connectionStatus = 3
            });

            this.echo.channel('delete-message')
                .listen("DeleteChatMessage", (e) => {
                    this.messages.find(x => x.mid === e.mid).text = 'This message has been deleted'
                })

        },

        updated: function () {

            this.scrollToBottom()

        },

        methods: {

            showCustomEmojis: function (event) {
                const beforeContent = this.newMessage.slice(0, event.target.selectionStart).split(' ');
                const afterContent = this.newMessage.slice(event.target.selectionStart, this.newMessage.length).split(' ');
                const wordBeforeCursor = beforeContent[beforeContent.length - 1].trim()
                const wordAfterCursor = afterContent[0].trim()
                const word = wordBeforeCursor + wordAfterCursor
                const start = event.target.selectionStart - wordBeforeCursor.length
                const end = start + word.length
                const positions = start + ':' + end
                if (/^:([a-zA-Z0-9]{0,25})([^:])?$/.test(word)) {
                    this.$refs.customEmojisSuggestions.showSuggestionsBox(word, positions)
                } else {
                    this.$refs.customEmojisSuggestions.hideSuggestionsBox()
                }
            },

            useCustomEmoji: function (emoji) {
                this.$refs.customEmojisSuggestions.hideSuggestionsBox()
                const data = emoji.split(':')
                this.newMessage = this.newMessage.substring(0, data[0]) + ':' + data[2] + ':' + this.newMessage.substring(data[1])
                this.$refs.newMessage.focus()
            },

            selectEmoji: function (emoji) {
                this.newMessage += emoji.data
            },

            openEmojisBox: function () {
                this.showEmojisBox ? this.showEmojisBox = false : this.showEmojisBox = true
                this.canHideEmojisBox = !this.showEmojisBox && this.canHideEmojisBox;
            },

            closeEmojisBoxOnOutsideClick: function () {
                if (this.showEmojisBox && this.canHideEmojisBox) this.showEmojisBox = false;
                this.canHideEmojisBox = this.showEmojisBox && !this.canHideEmojisBox;
                return true
            },

            sendMessage: function () {
                this.sendMessageError = []
                if (this.user.id && this.user.username && this.newMessage) {
                    axios
                        .post(this.$apiURL + '/chat/send-message', {
                            id: this.user.id,
                            user: this.user.username,
                            text: this.newMessage,
                            color: this.user.color,
                            mid: this.getLSI('mid')
                        }, {
                            headers: {
                                Authorization: 'Bearer ' + this.getLSI('api_token')
                            }
                        })
                        .then(response => {
                            if (response.data && response.data.moderate === true) {
                                this.sendMessageError = ['Your message contain a banned word', 1, []]
                            } else {
                                this.newMessage = '';
                                this.setLSI('mid', parseInt(this.getLSI('mid')) + 1)
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 503) {
                                this.connectionStatus = 1
                            } else {
                                this.sendMessageError = [error.response.data.error, 1, []]
                            }
                        });
                }
            },

            scrollToBottom: function () {
                let container = this.$el.querySelector("#stream-chat-messages-space");
                container.scrollTop = container.scrollHeight;
            }
        }

    }

</script>
