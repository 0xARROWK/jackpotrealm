<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5">

        <div class="items-center leading-none p-2 sm:p-4">
            <p class="font-semibold text-md text-th-text-color">Ban a user</p>
            <p class="text-sm text-th-text-color mt-2">Username</p>
            <div class="flex justify-between mt-2 text-sm">
                <form-input @validate="addBannedUser" label="" :input-value="this.add" ref="moderationBannedWords"></form-input>
                <button-loader @submitted="submitUpdate" text="Add" btn-class="ml-5" :take-full-width="false" :finish-loading-animation="true"
                               finish-loading-msg="Added" ref="submitUpdateButton"></button-loader>
            </div>
            <form-error :error-msg="this.banError[0]" :error-count="this.banError[1]" :error-replace="this.banError[2]"></form-error>
            <p class="font-semibold text-md text-th-text-color mt-5">Banned users list</p>
            <div v-if="this.bannedUsers.length !== 0">
                <div v-for="(banned, index) in bannedUsers" class="w-full flex justify-between text-white p-5 hover:bg-gray-700 rounded-lg mt-2">
                    <span>{{ banned }}</span>
                    <font-awesome-icon class="text-red-500 cursor-pointer" @click="deleteBannedUser(banned, index)" icon="trash"></font-awesome-icon>
                </div>
            </div>
            <div v-else>
                <p class="text-sm text-th-text-color mt-2">There is no banned users</p>
            </div>
            <form-error :error-msg="this.listBanError[0]" :error-count="this.listBanError[1]" :error-replace="this.listBanError[2]"></form-error>
        </div>

    </div>

</template>

<script>

    import FormInput from "../../general/form/FormInputComponent";
    import FormError from "../../general/form/FormErrorComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

    export default {

        name: "BannedUsers",

        components: {
            FormInput,
            FormError,
            ButtonLoader,
            FontAwesomeIcon
        },

        data: function () {
            return {
                bannedUsers: [],
                banError: [],
                listBanError: [],
                add: null,
            }
        },

        created: function () {
            axios
                .get(this.$apiURL + '/user/moderation/get-banned-users', {
                    headers: {
                        Authorization: 'Bearer ' + this.getLSI('api_token')
                    }
                })
                .then(response => {
                    if (response.data && response.data.success) {
                        for (let i = 0; i < response.data.success.length; i++) {
                            this.bannedUsers.push(response.data.success[i].username)
                        }
                    }
                })
                .catch(error => {
                    if (error.response.status === 419) {
                        this.listBanError = ['Invalid token', 1, []]
                    } else {
                        this.listBanError = ['Your data could not be saved', 1, []]
                    }
                })
        },

        methods: {
            addBannedUser: function (value) {
                this.add = value
                if (value.length !== 0) {
                    this.$refs.submitUpdateButton.setSubmitError(false)
                } else {
                    this.$refs.submitUpdateButton.setSubmitError(true)
                }
            },

            submitUpdate: function() {
                this.banError = []
                axios
                    .post(this.$apiURL + '/user/moderation/add-banned-user', {
                        name: this.add
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            this.$refs.submitUpdateButton.stopLoader()
                            this.bannedUsers.push(this.add)
                            this.add = ''
                        } else  {
                            this.banError = [response.data.error, 1, []]
                            this.$refs.submitUpdateButton.stopLoader(false)
                        }
                    })
                    .catch(error => {
                        this.$refs.submitUpdateButton.stopLoader(false)
                        if (error.response.status === 419) {
                            this.banError = ['Invalid token', 1, []]
                        } else {
                            this.banError = ['Your data could not be saved', 1, []]
                        }
                    })
            },

            deleteBannedUser: function(banned, index) {
                this.banError = []
                axios
                    .post(this.$apiURL + '/user/moderation/delete-banned-user', {
                        name: banned
                    }, {
                        headers: {
                            Authorization: 'Bearer ' + this.getLSI('api_token')
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            this.bannedUsers.splice(index, 1)
                        } else  {
                            this.banError = [response.data.error, 1, []]
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 419) {
                            this.banError = ['Invalid token', 1, []]
                        } else {
                            this.banError = ['Your data could not be saved', 1, []]
                        }
                    })
            }
        }

    }

</script>
