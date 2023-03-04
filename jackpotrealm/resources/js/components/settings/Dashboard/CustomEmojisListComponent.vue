<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5">

        <div class="flex items-center justify-between leading-tight p-2 sm:p-4">
            <h1 class="text-md font-bold">
                <a class="text-th-text-color">{{ __('List of custom emojis', 1, []) }}</a>
            </h1>
        </div>

        <a v-if="this.emojis" class="flex items-center leading-none px-2 pb-2 sm:px-4 sm:pb-4 text-xs font-bold text-th-text-color">Click on emojis for more action</a>

        <div v-if="this.emojis">
            <div v-for="(tierEmojis, tierName) in emojis" class="px-2 pb-2 sm:px-4 sm:pb-4">
                <p class="text-white font-semibold">{{ __(tierName) }}</p>
                <div class="grid grid-cols-4 sm:grid-cols-8 lg:grid-cols-6 xl:grid-cols-8 items-center leading-none">
                    <div v-for="(emoji, index) in tierEmojis" class="w-14 h-14 border-2 border-th-color m-1">
                        <v-popover offset="10" class="h-full w-full">
                            <!--<img v-tooltip.bottom-center="{content: emoji, placement: 'bottom-center', classes: ['text-xs', 'font-bold', 'info'], offset: 10}"
                                    class="w-full h-full cursor-pointer" :src="'/storage/users/' + user.username + '/emojis/' + emoji + '.png'" :alt="emoji">-->
                            <img class="w-full h-full cursor-pointer" :src="'/storage/users/' + user.username + '/emojis/' + tierName + '/' + emoji.name + '.png?t=' + new Date()" :alt="emoji.name">
                            <template slot="popover">
                                <div class="grid grid-cols-1 items-center leading-none">
                                    <label @click.prevent="updateEmojiTier(emoji, 'free', tierName, index)" :for="'tier-free-' + emoji.name" class="cursor-pointer py-2">
                                        <input :id="'tier-free-' + emoji.name" class="mr-2" type="radio" :name="'tier-' + emoji.name" value="free" :checked="tierName === 'free'">{{ __('free') }}
                                    </label>
                                    <label @click.prevent="updateEmojiTier(emoji, 'tier_1', tierName, index)" :for="'tier-1-' + emoji.name" class="cursor-pointer py-2">
                                        <input :id="'tier-1-' + emoji.name" class="mr-2" type="radio" :name="'tier-' + emoji.name" value="tier_1" :checked="tierName === 'tier_1'">{{ __('tier_1') }}
                                    </label>
                                    <label @click.prevent="updateEmojiTier(emoji, 'tier_2', tierName, index)" :for="'tier-2-' + emoji.name" class="cursor-pointer py-2">
                                        <input :id="'tier-2-' + emoji.name" class="mr-2" type="radio" :name="'tier-' + emoji.name" value="tier_2" :checked="tierName === 'tier_2'">{{ __('tier_2') }}
                                    </label>
                                    <label @click.prevent="updateEmojiTier(emoji, 'tier_3', tierName, index)" :for="'tier-3-' + emoji.name" class="cursor-pointer py-2">
                                        <input :id="'tier-3-' + emoji.name" class="mr-2" type="radio" :name="'tier-' + emoji.name" value="tier_3" :checked="tierName === 'tier_3'">{{ __('tier_3') }}
                                    </label>
                                    <!--<a v-close-popover :id="'close-popover-' + index" class="cursor-pointer text-red-500">x</a>-->
                                    <a class="cursor-pointer bg-th-btn hover:bg-th-btn-hover flex-shrink-0 text-th-text-color text-base font-semibold p-3 rounded-lg shadow-md focus:outline-none py-2"
                                       @click="deleteCustomEmoji(index, emoji.name, tierName)">{{ __('Delete', 1, []) }} '{{ emoji.name }}'</a>
                                    <form-error v-if="updateEmojiError" :error-msg="updateEmojiError[0]" :error-count="updateEmojiError[1]" :error-replace="updateEmojiError[2]"></form-error>
                                </div>
                            </template>
                        </v-popover>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="flex items-center leading-none px-2 pb-2 sm:px-4 sm:pb-4">
            <a class="text-th-text-color text-sm">{{ __('Upload emojis and see it here', 1, []) }}</a>
        </div>

    </div>

</template>

<script>

    import ButtonLoader from "../../general/form/ButtonLoaderComponent";
    import FormError from "../../general/form/FormErrorComponent";

    import GlobalStore from "../../store/GlobalStore";
    import { mapGetters, mapActions } from "vuex";
    import FormInput from "../../general/form/FormInputComponent";

    export default {

        name: "CustomEmojisList",

        components: {
            FormInput,
            ButtonLoader,
            FormError
        },

        store: GlobalStore,

        data: function() {

            return {
                updateEmojiError: []
            }

        },

        computed: {

            ...mapGetters(['user', 'emojis'])

        },

        created: function() {

            if (!this.emojis) {
                axios
                    .get(this.$apiURL + '/chat/list-emojis', {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.emojis) {
                            for (const tier in response.data.emojis) {
                                if (response.data.emojis[tier]) {
                                    for (let i = 0; i < response.data.emojis[tier].length; i++) {
                                        let name = response.data.emojis[tier][i]
                                        this.addEmoji({name, tier});
                                    }
                                }
                            }
                        }
                    })
            }

        },

        methods: {

            ...mapActions(['addEmoji', 'updateEmoji', 'deleteEmoji']),

            updateEmojiTier: function (emoji, newTier, currentTier, index) {
                let name = emoji.name
                axios
                    .post(this.$apiURL + '/user/dashboard/update-emoji', {
                        'name': name,
                        'newTier': newTier,
                        'currentTier': currentTier
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        },
                        responseType: 'json'
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            //console.log(document.querySelectorAll('div[id^="close-popover-"]'))
                            this.updateEmoji({name, newTier, currentTier, index})
                        } else {
                            this.updateEmojiError = ['An error occurred', 1, []]
                        }
                    })
                    .catch(error => {
                        if (error.response && error.response.status === 419) {
                            this.updateEmojiError = ['Session has expired, please reload this page', 1, []]
                        } else {
                            this.updateEmojiError = ['An error occurred', 1, []]
                        }
                    })
            },

            deleteCustomEmoji: function(index, emoji, tier) {
                axios
                    .post(this.$apiURL + '/user/dashboard/delete-emoji', {
                        'emoji': emoji,
                        'tier': tier
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        },
                        responseType: 'json'
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            //console.log(document.querySelectorAll('div[id^="close-popover-"]'))
                            this.deleteEmoji({tier, index})
                            this.updateEmojiError = []
                        } else {
                            this.updateEmojiError = ['An error occurred', 1, []]
                        }
                    })
                    .catch(error => {
                        if (error.response && error.response.status === 419) {
                            this.updateEmojiError = ['Session has expired, please reload this page', 1, []]
                        } else {
                            this.updateEmojiError = ['An error occurred', 1, []]
                        }
                    })


            }

        }

    }

</script>
