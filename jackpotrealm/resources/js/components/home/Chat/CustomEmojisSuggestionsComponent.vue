<template>

    <div v-if="this.emojis" :class="this.status" class="rounded-lg mx-3 p-3 bg-th-menu mb-3">
        <p class="text-sm mb-3">{{ __('Emoji(s) corresponding to') }} "{{ this.search }}"</p>
        <div class="overflow-y-auto no-scrollbar max-h-44">
            <div v-if="tierEmojis && tierEmojis.length > 0" v-for="(tierEmojis, tierName) in emojis">
                <p class="font-semibold text-sm">{{ __(tierName) }}</p>
                <div class="grid grid-cols-4 xs:grid-cols-6 xl:grid-cols-8 items-center">
                    <div v-for="emoji in tierEmojis" @click="use(emoji, tierName)" class="flex justify-center p-3 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 cursor-pointer">
                        <img :class="tierName !== 'free' && !user.role.includes(tierName) ? 'filter grayscale' : ''" :src="'/storage/users/Achilles/emojis/' + tierName + '/' + emoji + '.png'" width="24" height="24" :alt="'emoji ' + emoji">
                        <!--<p>:{{ emoji }}:</p>-->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

    import GlobalStore from "../../store/GlobalStore";
    import {mapGetters} from "vuex";

    export default {

        name: "CustomEmojisSuggestions",

        store: GlobalStore,

        data: function() {
            return {
                status: 'hidden',
                emojis: [],
                search: '',
                positions: ''
            }
        },

        computed: {
            ...mapGetters(['user'])
        },

        methods: {

            showSuggestionsBox: function (search, positions) {
                axios
                    .post(this.$apiURL + '/chat/search-emojis', {
                        search: search
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.emojis) {
                            this.search = search
                            this.positions = positions
                            this.emojis = response.data.emojis
                            this.status = 'block'
                        } else {
                            this.hideSuggestionsBox()
                        }
                    })
                    .catch(error => {
                        this.hideSuggestionsBox()
                    })
            },

            hideSuggestionsBox: function () {
                this.emojis = []
                this.status = 'hidden'
            },

            use: function (emoji, tier) {
                if (this.user.role.includes(tier) || tier === 'free') {
                    this.$emit('useEmoji', this.positions + ':' + emoji)
                }
            }

        }

    }

</script>
