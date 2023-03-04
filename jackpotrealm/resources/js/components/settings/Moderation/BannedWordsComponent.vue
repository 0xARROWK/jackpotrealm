<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-menu">

        <div class="items-center leading-none p-2 sm:p-4">
            <p class="font-semibold text-md text-th-text-color">{{ __('Banned :banned', 1, ['words']) }}</p>
            <p class="text-sm text-th-text-color mt-2">{{ __('Separate banned :banned with "enter"', 1, ['words']) }}</p>
            <form-input @validate="moderationBannedWordsValidation" id="banned-words" label="" :input-value="this.updateBannedWords"
                        input-class="mt-5" :textarea="true" textarea-rows="5" ref="moderationBannedWords"></form-input>
            <form-error v-if="this.checkupUpdateError" :error-msg="this.checkupUpdateError[0]" :error-count="this.checkupUpdateError[1]" :error-replace="this.checkupUpdateError[2]"></form-error>
            <div class="flex justify-end mt-5 text-sm">
                <button-loader @submitted="submitUpdate" text="Update" :take-full-width="false" :finish-loading-animation="true"
                               finish-loading-msg="Updated" ref="submitUpdateButton"></button-loader>
            </div>
        </div>

    </div>

</template>

<script>

    import FormInput from "../../general/form/FormInputComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";
    import FormError from "../../general/form/FormErrorComponent";

    export default {

        name: "BannedWords",

        components: {
            FormError,
            FormInput,
            ButtonLoader
        },

        data: function() {

            return {

                updateBannedWords: null,
                currentBannedWords: null,

                checkupUpdateError: []

            }

        },

        created: function() {
            axios
                .get(this.$apiURL + '/user/moderation/expressions/0', {
                    headers: {
                        Authorization: 'Bearer ' + this.getLSI('api_token')
                    }
                })
                .then(response => {
                    if (response.data && response.data.success) {
                        this.updateBannedWords = this.currentBannedWords = response.data.success.words
                    } else {
                        this.checkupUpdateError = ['We are unable to retrieve your data', 1, []]
                    }
                })
                .catch(error => {
                    this.checkupUpdateError = ['We are unable to retrieve your data', 1, []]
                })
        },

        methods: {

            moderationBannedWordsValidation(value) {
                if (value !== this.currentBannedWords) {
                    this.updateBannedWords = value
                    this.$refs.submitUpdateButton.setSubmitError(false)
                } else {
                    this.$refs.submitUpdateButton.setSubmitError(true)
                }
            },

            submitUpdate: function() {
                this.checkupUpdateError = []

                axios
                    .post(this.$apiURL + '/user/moderation/expressions', {
                        _token: this.getCSRFToken(),
                        action: 0,
                        words: this.updateBannedWords
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            this.currentBannedWords = this.updateBannedWords
                            this.$refs.submitUpdateButton.setSubmitError(true)
                            this.$refs.submitUpdateButton.stopLoader()
                        } else {
                            this.checkupUpdateError = [response.data.error, 1, []]
                            this.$refs.submitUpdateButton.stopLoader(false)
                        }
                    })
                    .catch(error => {
                        this.$refs.submitUpdateButton.stopLoader(false)
                        if (error.response.status === 419) {
                            this.checkupUpdateError = ['Invalid token', 1, []]
                        } else {
                            this.checkupUpdateError = ['Your data could not be saved', 1, []]
                        }
                    })
            }

        }

    }

</script>
