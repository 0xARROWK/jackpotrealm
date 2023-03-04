<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5 lg:mt-menu">

        <div class="items-center leading-none p-2 sm:p-4">
            <p class="font-semibold text-md text-th-text-color">{{ __('Banned :banned', 1, ['expressions']) }}</p>
            <p class="text-sm text-th-text-color mt-2">{{ __('Separate banned :banned with "enter"', 1, ['expressions']) }}</p>
            <form-input @validate="moderationBannedExpressionsValidation" id="banned-expressions" label="" :input-value="this.updateBannedExpressions"
                        input-class="mt-5" :textarea="true" textarea-rows="5" ref="moderationBannedExpressions"></form-input>
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

        name: "BannedExpressions",

        components: {
            FormError,
            FormInput,
            ButtonLoader
        },

        data: function() {

            return {

                updateBannedExpressions: null,
                currentBannedExpressions: null,

                checkupUpdateError: []

            }

        },

        created: function() {
            axios
                .get(this.$apiURL + '/user/moderation/expressions/1', {
                    headers: {
                        Authorization: 'Bearer ' + this.getLSI('api_token')
                    }
                })
                .then(response => {
                    if (response.data && response.data.success) {
                        this.updateBannedExpressions = this.currentBannedExpressions = response.data.success.expressions
                    } else {
                        this.checkupUpdateError = ['We are unable to retrieve your data', 1, []]
                    }
                })
                .catch(error => {
                    this.checkupUpdateError = ['We are unable to retrieve your data', 1, []]
                })
        },

        methods: {

            moderationBannedExpressionsValidation(value) {
                if (value !== this.currentBannedExpressions) {
                    this.updateBannedExpressions = value
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
                        action: 1,
                        expressions: this.updateBannedExpressions
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            this.currentBannedExpressions = this.updateBannedExpressions
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
