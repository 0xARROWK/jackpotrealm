<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5 lg:mt-menu">

        <div class="flex items-center justify-between leading-tight p-2 sm:p-4">
            <h1 class="text-md font-bold">
                <a class="text-th-text-color">
                    {{ __('Twitch stream', 1, []) }}
                </a>
            </h1>
        </div>

        <div class="items-center leading-none px-2 pb-2 sm:px-4 sm:pb-4">
            <form-input @validate="multistreamUrlUpdateValidation" key="update-twitch-url"
                        :input-value="this.currentMultistreamUrl" label="Streaming URL"
                        placeholder="Twitch stream url to connect" ref="updateMultistreamUrl"></form-input>

            <form-input @validate="multistreamKeyUpdateValidation" key="update-twitch-key" input-type="password"
                        :input-value="this.currentMultistreamKey" label="Streaming key"
                        placeholder="Twitch stream key" ref="updateMultistreamKey"></form-input>

            <form-error :error-msg="this.checkupUpdateError[0]" :error-count="this.checkupUpdateError[1]" :error-replace="this.checkupUpdateError[2]"></form-error>

            <div class="w-full mt-5 flex justify-between">
                <label for="toggleMultistream" class="flex items-center cursor-pointer" @click.prevent="toggleMultistream">
                    <div class="relative">
                        <input id="toggleMultistream" type="checkbox" class="sr-only" :checked="this.updateMultistreamStatus"/>
                        <div class="w-10 h-4 bg-gray-400 rounded-full shadow-inner"></div>
                        <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition"></div>
                    </div>
                    <div class="ml-3 text-white text-sm">{{ __('Multistream is :', 1, []) }}<p :class="this.toggleStatusClass[0]">{{ __(this.toggleStatusClass[1], 1, []) }}</p></div>
                </label>
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

        name: "Multistream",

        components: {
            FormInput,
            FormError,
            ButtonLoader
        },

        data: function() {

            return {

                updateMultistreamUrl: null,
                currentMultistreamUrl: null,
                updateMultistreamKey: null,
                currentMultistreamKey: null,
                updateMultistreamStatus: 0,
                toggleStatusClass: ['text-th-color', 'Disabled'],
                checkupUpdateError: []

            }

        },

        created: function () {
    	    axios
                .get(this.$apiURL + '/user/dashboard/multistream-infos', {
                    headers: {
                        Authorization: 'Bearer ' + this.getLSI('api_token')
                    },
                    responseType: 'json'
                })
                .then(response => {
                    if (response.data && response.data.success) {
                        let {url, key, status} = response.data.success
                        this.updateMultistreamUrl = this.currentMultistreamUrl = url
                        this.updateMultistreamKey = this.currentMultistreamKey = key
                        this.updateMultistreamStatus = status
                        if (status === 1) this.toggleStatusClass = ['text-th-color-2', 'Enabled']
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

            multistreamUrlUpdateValidation: function (value) {
                this.updateMultistreamUrl = value
                if (value.length < 100) {
                    this.$refs.updateMultistreamUrl.resetErrorMsg()
                } else {
                    this.$refs.updateMultistreamUrl.showErrorMsg('The value must contain less than n characters', 1, ['URL', '100'])
                }
                this.checkupUpdateForm()
            },

            multistreamKeyUpdateValidation: function (value) {
                this.updateMultistreamKey = value
                if (value.length < 100) {
                    this.$refs.updateMultistreamKey.resetErrorMsg()
                } else {
                    this.$refs.updateMultistreamKey.showErrorMsg('The value must contain less than n characters', 1, ['stream key', '100'])
                }
                this.checkupUpdateForm()
            },

            toggleMultistream: function() {
                this.checkupUpdateError = []
                if (this.currentMultistreamUrl && this.currentMultistreamKey) {
                    if (this.updateMultistreamStatus === 0) {
                        this.updateMultistreamStatus = 1
                        this.toggleStatusClass = ['text-th-color-2', 'Enabled']
                    } else {
                        this.updateMultistreamStatus = 0
                        this.toggleStatusClass = ['text-th-color', 'Disabled']
                    }
                    this.saveMultistreamChoice(this.updateMultistreamStatus)
                } else {
                    this.updateMultistreamStatus = 0
                    this.toggleStatusClass = ['text-th-color', 'Disabled']
                    this.checkupUpdateError = ['To share your stream, you must specify a playback URL and your stream key', 1, []]
                }
                //this.saveMultistreamChoice(this.updateMultistreamStatus, true)
            },

            saveMultistreamChoice: function(status, error = false) {
                if (!error) this.checkupUpdateError = []
                axios
                    .post(this.$apiURL + '/user/dashboard/multistream-infos', {
                        _token: this.getCSRFToken(),
                        action: 'changeStatus',
                        url: null,
                        key: null,
                        status: status
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        },
                        responseType: 'json'
                    })
                    .catch(error => {
                        if (error.response.status === 429) {
                            this.updateMultistreamStatus = 0
                            this.toggleStatusClass = ['text-th-color', 'Disabled']
                            this.checkupUpdateError = ['Too many requests', 1, []]
                        }
                    })
            },

            checkupUpdateForm: function() {
                if (this.updateMultistreamUrl !== this.currentMultistreamUrl || this.updateMultistreamKey !== this.currentMultistreamKey) {
                    this.$refs.submitUpdateButton.setSubmitError(false)
                } else {
                    this.$refs.submitUpdateButton.setSubmitError(true)
                }
            },

            submitUpdate: function () {
                this.checkupUpdateError = []

                let url = this.$apiURL + '/user/dashboard/multistream-infos',
                    data = {
                        _token: this.getCSRFToken(),
                        action: 'changeData',
                        url: this.updateMultistreamUrl,
                        key: this.updateMultistreamKey,
                        status: null
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
                            this.currentMultistreamUrl = this.updateMultistreamUrl
                            this.currentMultistreamKey = this.updateMultistreamKey
                            if ((!this.currentMultistreamUrl || !this.currentMultistreamKey) && this.updateMultistreamStatus === 1) {
                                this.updateMultistreamStatus = 0
                                this.toggleStatusClass = ['text-th-color', 'Disabled']
                                this.checkupUpdateError = ['To share your stream, you must specify a playback URL and your stream key', 1, []]
                                this.saveMultistreamChoice(this.updateMultistreamStatus, true)
                            }
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

<style>

    input:checked ~ .dot {
        transform: translateX(100%);
        background-color: var(--theme-color-2);
    }

</style>
