<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5">

        <div class="flex items-center justify-between leading-tight p-2 sm:p-4">
            <h1 class="text-md font-bold">
                <a class="text-th-text-color">
                    {{ __('Stream information', 1, []) }}
                </a>
            </h1>
        </div>

        <div class="items-center leading-none px-2 pb-2 sm:px-4 sm:pb-4">
            <form-input @validate="streamTitleUpdateValidation" key="update-stream-title" :textarea="true"
                        :input-value="this.currentStreamTitle" label="Title"
                        placeholder="Title of your stream" ref="updateStreamTitle"></form-input>

            <form-input @validate="streamDescriptionUpdateValidation" key="update-stream-description" :textarea="true"
                        textarea-rows="5" :input-value="this.currentStreamDescription" label="Description"
                        placeholder="Short description of your stream" ref="updateStreamDescription"></form-input>

            <form-error :error-msg="this.checkupUpdateError[0]" :error-count="this.checkupUpdateError[1]" :error-replace="this.checkupUpdateError[2]"></form-error>

            <div class="w-full mt-5 flex justify-end">
                <button-loader @submitted="submitUpdate" :take-full-width="false" :finish-loading-animation="true" finish-loading-msg="Updated"
                               key="update-submit-button" text="Update" ref="submitUpdateButton"></button-loader>
            </div>
        </div>

    </div>

</template>

<script>

    import FormInput from "../../general/form/FormInputComponent";
    import FormError from "../../general/form/FormErrorComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";
    import {mapActions} from "vuex";

    export default {

        name: "StreamInformation",

        components: {
            FormInput,
            FormError,
            ButtonLoader
        },

        data: function() {

            return {

                updateStreamTitle: null,
                currentStreamTitle: null,
                updateStreamDescription: null,
                currentStreamDescription: null,
                checkupUpdateError: []

            }

        },

        created: function () {
    	    axios
                .get(this.$apiURL + '/channel/infos')
                .then(response => {
                    if (response.data && response.data.success) {
                        let {channelName, streamTitle, streamDescription} = response.data.success
                        this.updateStreamTitle = this.currentStreamTitle = streamTitle
                        this.updateStreamDescription = this.currentStreamDescription = streamDescription
                    } else {
                        this.checkupUpdateError = [response.data.error, 1, []]
                    }
                })
                .catch(error => {
                    if (error.response.status === 419) {
                        this.checkupUpdateError = ['Session has expired, please reload this page', 1, []]
                    } else {
                        this.checkupUpdateError = ['We are unable to retrieve your data', 1, []]
                    }
                })
        },

        methods: {

            ...mapActions(['loadChannelInfos']),

            streamTitleUpdateValidation: function(value) {
                if (value.length < 120) {
                    this.updateStreamTitle = value
                    this.$refs.updateStreamTitle.resetErrorMsg()
                } else {
                    this.updateStreamTitle = null
                    this.$refs.updateStreamTitle.showErrorMsg('The value must contain less than n characters', 1, ['title', '120'])
                }
                this.checkUpdateForm()
            },

            streamDescriptionUpdateValidation: function(value) {
                this.updateStreamDescription = value
                if (value.length < 2500) {
                    this.$refs.updateStreamDescription.resetErrorMsg()
                } else {
                    this.$refs.updateStreamDescription.showErrorMsg('The value must contain less than n characters', 1, ['description', '2500'])
                }
                this.checkUpdateForm()
            },

            checkUpdateForm: function() {
                if (this.updateStreamTitle && (this.updateStreamDescription !== this.currentStreamDescription || this.updateStreamTitle !== this.currentStreamTitle)) {
                    if (this.updateStreamDescription.length < 2500) {
                        this.$refs.submitUpdateButton.setSubmitError(false)
                    } else {
                        this.$refs.submitUpdateButton.setSubmitError(true)
                    }
                } else {
                    this.$refs.submitUpdateButton.setSubmitError(true)
                }
            },

            submitUpdate: function() {

                this.checkupUpdateError = []

                let url = this.$apiURL + '/user/dashboard/stream-infos',
                    data = {
                        _token: this.getCSRFToken(),
                        title: this.updateStreamTitle,
                        description: this.updateStreamDescription
                    };

                axios
                    .post(url, data, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        },
                        responseType: 'json'
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
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
                            this.checkupUpdateError = ['Session has expired, please reload this page', 1, []]
                        } else {
                            this.checkupUpdateError = ['Your data could not be saved', 1, []]
                        }
                    })
            }

        }

    }

</script>
