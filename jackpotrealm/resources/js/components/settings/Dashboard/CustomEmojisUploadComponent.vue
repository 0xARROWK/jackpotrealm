<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5">

        <div class="flex items-center justify-between leading-tight p-2 sm:p-4">
            <h1 class="text-md font-bold">
                <a class="text-th-text-color">
                    Upload custom emojis
                </a>
            </h1>
        </div>

        <div class="flex items-center leading-none px-2 pb-2 sm:px-4 sm:pb-4">
            <div class="w-14 h-12 sm:w-28 sm:h-24 border-2 border-th-color">
                <img v-if="this.image" class="w-full h-full" :src="this.image" alt="New custom emoji">
            </div>
            <input class="hidden" type="file" ref="customEmojisFile" @change="onFileInput" />
            <div class="text-sm w-full ml-4">
                <form-input @validate="customEmojiNameValidation" key="custom-emojis-name" input-type="text" :input-value="this.selectedEmojiName" label="Emoji name" placeholder="Enter name of emoji" ref="customEmojiName"></form-input>
                <div class="mt-2 text-white">
                    <p class="font-semibold mb-2">{{ __('Usage') }}</p>
                    <label for="tier-free" class="cursor-pointer py-2">
                        <input id="tier-free" class="mr-2" type="radio" name="tier" value="free" checked="checked">{{ __('free') }}
                    </label>
                    <label for="tier-1" class="cursor-pointer py-2 ml-2">
                        <input id="tier-1" class="mr-2" type="radio" name="tier" value="tier_1">{{ __('tier_1') }}
                    </label>
                    <label for="tier-2" class="cursor-pointer py-2 ml-2">
                        <input id="tier-2" class="mr-2" type="radio" name="tier" value="tier_2">{{ __('tier_2') }}
                    </label>
                    <label for="tier-3" class="cursor-pointer py-2 ml-2">
                        <input id="tier-3" class="mr-2" type="radio" name="tier" value="tier_3">{{ __('tier_3') }}
                    </label>
                </div>
                <div class="xs:flex xs:justify-between">
                    <button-loader @submitted="$refs.customEmojisFile.click()" btn-class="mt-2 mr-2 xs:mr-0" text="Choose a file" :take-full-width="false" :prevent-submit="false" :can-load="false"></button-loader>
                    <button-loader @submitted="uploadFile" btn-class="mt-2 xs:ml-2 sm:ml-0" text="Upload" :take-full-width="false" :prevent-submit="false"  :finish-loading-animation="true" finish-loading-msg="Uploaded" ref="submitCustomEmojisButton"></button-loader>
                </div>
                <form-error :error-msg="this.uploadEmojiError[0]" :error-count="this.uploadEmojiError[1]" :error-replace="this.uploadEmojiError[2]"></form-error>
            </div>
        </div>

    </div>

</template>

<script>

    import FormInput from "../../general/form/FormInputComponent";
    import FormError from "../../general/form/FormErrorComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";

    import {mapActions, mapGetters} from 'vuex';
    import GlobalStore from "../../store/GlobalStore";

    export default {

        name: "CustomEmojisUpload",

        components: {
            FormInput,
            FormError,
            ButtonLoader
        },

        store: GlobalStore,

        data: function () {

            return {

                image: null,
                selectedEmoji: null,
                selectedEmojiName: null,
                selectedTier: null,
                uploadEmojiError: []

            }

        },

        computed: {

            ...mapGetters(['user', 'emojis'])

        },

        methods: {

            ...mapActions(['addEmoji', 'deleteEmoji']),

            onFileInput: function(event) {
                this.selectedEmoji = event.target.files[0];
                try { this.image = URL.createObjectURL(this.selectedEmoji); } catch (e) {}
            },

            customEmojiNameValidation: function(value) {
                this.selectedEmojiName = value
            },

            // TODO : increase php max file size upload

            uploadFile: function() {

                const elements = document.getElementsByName('tier')
                for (let i = 0, len = elements.length; i < len; i++) {
                    if (elements[i].checked) {
                        this.selectedTier = elements[i].value
                        break
                    }
                }

                if (this.selectedEmojiName && /[A-Za-z0-9_]{3,25}/.test(this.selectedEmojiName)) {
                    if (['free', 'tier_1', 'tier_2', 'tier_3'].includes(this.selectedTier)) {
                        if (this.selectedEmoji && (this.selectedEmoji.type === "image/png" || this.selectedEmoji.type === "image/jpg" || this.selectedEmoji.type === "image/jpeg" || this.selectedEmoji.type === "image/gif")) {
                            this.uploadEmojiError = []
                            let formData = new FormData();
                            formData.append('custom_emoji', this.selectedEmoji)
                            formData.append('custom_emoji_name', this.selectedEmojiName)
                            formData.append('tier', this.selectedTier)
                            axios
                                .post(this.$apiURL + "/user/dashboard/new-emoji", formData, {
                                    headers: {
                                        Authorization: 'Bearer ' + this.getLSI('api_token')
                                    },
                                    responseType: 'json'
                                })
                                .then(response => {
                                    if (response.data && response.data.success) {
                                        let name = this.selectedEmojiName,
                                            tier = this.selectedTier
                                        this.addEmoji({name, tier})
                                        //this.$set(this.emojis[tier], this.emojis[tier], '')
                                        this.selectedEmojiName = this.$refs.customEmojisFile.value =
                                            this.selectedEmoji = this.image = null
                                        this.$refs.submitCustomEmojisButton.stopLoader()
                                    } else if (response.data && response.data.error) {
                                        this.uploadEmojiError = [response.data.error, 1, []]
                                        this.$refs.submitCustomEmojisButton.stopLoader(false)
                                    }
                                })
                                .catch(error => {
                                    this.$refs.submitCustomEmojisButton.stopLoader(false)
                                    if (error.response.status === 419) {
                                        this.uploadEmojiError = ['Session has expired, please reload this page', 1, []]
                                    } else {
                                        this.uploadEmojiError = ['Your data could not be saved', 1, []]
                                    }
                                })
                        } else {
                            this.$refs.submitCustomEmojisButton.stopLoader(false)
                            this.uploadEmojiError = ['Please select a png, jpg, jpeg or gif file', 1, []]
                        }
                    } else {
                        this.$refs.submitCustomEmojisButton.stopLoader(false)
                        this.uploadEmojiError = ['Please select a valid tier', 1, []]
                    }
                } else {
                    this.$refs.submitCustomEmojisButton.stopLoader(false)
                    this.uploadEmojiError = ['Regex.emojiName', 1, []]
                }
            }

        }

    }

</script>
