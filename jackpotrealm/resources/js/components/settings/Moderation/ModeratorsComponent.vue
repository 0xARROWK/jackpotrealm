<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5">

        <div class="items-center leading-none p-2 sm:p-4">
            <p class="font-semibold text-md text-th-text-color">Add moderator</p>
            <p class="text-sm text-th-text-color mt-2">Username</p>
            <div class="flex justify-between mt-2 text-sm">
                <form-input @validate="addModerator" label="" :input-value="this.add" ref="moderationBannedWords"></form-input>
                <button-loader @submitted="submitUpdate" text="Add" btn-class="ml-5" :take-full-width="false" :finish-loading-animation="true"
                               finish-loading-msg="Added" ref="submitUpdateButton"></button-loader>
            </div>
            <form-error :error-msg="this.moderatorError[0]" :error-count="this.moderatorError[1]" :error-replace="this.moderatorError[2]"></form-error>
            <p class="font-semibold text-md text-th-text-color mt-5">Moderator list</p>
            <div v-if="this.moderators.length !== 0">
                <div v-for="(moderator, index) in moderators" class="w-full flex justify-between text-white p-5 hover:bg-gray-700 rounded-lg mt-2">
                    <span>{{ moderator }}</span>
                    <font-awesome-icon class="text-red-500 cursor-pointer" @click="deleteModerator(moderator, index)" icon="trash"></font-awesome-icon>
                </div>
            </div>
            <div v-else>
                <p class="text-sm text-th-text-color mt-2">There is no moderator</p>
            </div>
            <form-error :error-msg="this.listModeratorError[0]" :error-count="this.listModeratorError[1]" :error-replace="this.listModeratorError[2]"></form-error>
        </div>

    </div>

</template>

<script>

    import FormError from "../../general/form/FormErrorComponent";
    import FormInput from "../../general/form/FormInputComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    export default {

        name: "Moderators",

        components: {
            FormError,
            FormInput,
            ButtonLoader,
            FontAwesomeIcon
        },

        data: function() {
            return {
                add: null,
                moderators: [],
                moderatorError: [],
                listModeratorError: []
            }
        },

        created: function() {
            axios
                .get(this.$apiURL + '/user/moderation/get-moderators', {
                    headers: {
                        Authorization: 'Bearer ' + this.getLSI('api_token')
                    }
                })
                .then(response => {
                    if (response.data && response.data.success) {
                        for (let i = 0; i < response.data.success.length; i++) {
                            this.moderators.push(response.data.success[i].username)
                        }
                    }
                })
                .catch(error => {
                    if (error.response.status === 419) {
                        this.listModeratorError = ['Invalid token', 1, []]
                    } else {
                        this.listModeratorError = ['Your data could not be saved', 1, []]
                    }
                })
        },

        methods: {

            deleteModerator: function (moderator, index) {
                this.moderatorError = []
                axios
                    .post(this.$apiURL + '/user/moderation/delete-moderator', {
                        name: moderator
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            this.moderators.splice(index, 1)
                        } else  {
                            this.moderatorError = [response.data.error, 1, []]
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 419) {
                            this.moderatorError = ['Invalid token', 1, []]
                        } else {
                            this.moderatorError = ['Your data could not be saved', 1, []]
                        }
                    })
            },

            addModerator: function (value) {
                this.add = value
                if (value.length !== 0) {
                    this.$refs.submitUpdateButton.setSubmitError(false)
                } else {
                    this.$refs.submitUpdateButton.setSubmitError(true)
                }
            },

            submitUpdate: function () {
                this.moderatorError = []
                axios
                    .post(this.$apiURL + '/user/moderation/add-moderator', {
                        name: this.add
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            this.$refs.submitUpdateButton.stopLoader()
                            this.moderators.push(this.add)
                            this.add = ''
                        } else  {
                            this.moderatorError = [response.data.error, 1, []]
                            this.$refs.submitUpdateButton.stopLoader(false)
                        }
                    })
                    .catch(error => {
                        this.$refs.submitUpdateButton.stopLoader(false)
                        if (error.response.status === 419) {
                            this.moderatorError = ['Invalid token', 1, []]
                        } else {
                            this.moderatorError = ['Your data could not be saved', 1, []]
                        }
                    })
            }

        }

    }

</script>
