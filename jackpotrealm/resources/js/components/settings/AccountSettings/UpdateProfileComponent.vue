<template>

    <form class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-menu">

        <div class="items-center leading-none px-2 pb-2 sm:px-4 sm:pb-4">
            <form-input @validate="usernameUpdateValidation" key="update-username" :input-value="this.user.username"
                        label="Username" placeholder="Enter your username" ref="updateUsername"></form-input>

            <form-input @validate="emailUpdateValidation" key="update-email" input-type="email" :input-value="this.user.email"
                        label="Email address" placeholder="Enter your email address" ref="updateEmail"></form-input>

            <form-input key="update-birthdate" :input-value="this.user.birthdate" label="Date of birth" :input-disabled="true"></form-input>

            <form-input @validate="passwordUpdateValidation" key="update-password" input-type="password"
                        label="New password" placeholder="************" :strong="this.updatePasswordStrength" ref="updatePassword"></form-input>

            <form-input @validate="passwordConfirmationUpdateValidation" key="update-password-confirmation" input-type="password"
                        label="New password confirmation" placeholder="************" ref="updatePasswordConfirmation"></form-input>

            <form-input @validate="passwordOldUpdateValidation" key="update-password-old" input-type="password"
                        label="Old password" placeholder="************" ref="updatePasswordOld"></form-input>

            <form-error :error-msg="this.checkupUpdateError[0]" :error-count="this.checkupUpdateError[1]" :error-replace="this.checkupUpdateError[2]"></form-error>

            <div class="w-full mt-5 flex justify-end">
                <button-loader @submitted="submitUpdate" :take-full-width="false" :finish-loading-animation="true" finish-loading-msg="Updated"
                               key="updateSubmitButton" text="Update" ref="submitUpdateButton"></button-loader>
            </div>
        </div>

    </form>

</template>

<script>

    import FormInput from "../../general/form/FormInputComponent";
    import FormError from "../../general/form/FormErrorComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";

    import { mapActions, mapGetters } from 'vuex'
    import GlobalStore from "../../store/GlobalStore";

    export default {

        name: "UpdateProfile",

        store: GlobalStore,

        components: {
            FormInput,
            FormError,
            ButtonLoader
        },

        data: function () {

            return {

                // update form values
                updateUsername: null,
                updateUsernameError: false,
                updateEmail: null,
                updateEmailError: false,
                updatePassword: null,
                updatePasswordError: false,
                updatePasswordStrength: 50,
                updatePasswordConfirmation: null,
                updatePasswordConfirmationError: false,
                updatePasswordOld: null,

                // last password entered for update even if value is wrong, for password confirmation verification
                lastUpdatePassword: null,
                // last password confirmation entered even if value not match, for password verification
                lastUpdatePasswordConfirmation: null,

                // checkup forms error
                checkupUpdateError: []

            }

        },

        computed: {

            ...mapGetters(['user'])

        },

        methods: {

            ...mapActions(['updateUser']),

            // check if username is valid
            usernameUpdateValidation: function(value) {

                if (/^[_a-zA-Z0-9]{3,25}$/.test(value) && value !== this.user.username) {

                    this.$refs.updateUsername.setLoadData(true)

                    axios
                        .get(this.$apiURL + '/verification/username/' + value)
                        .then(response => {
                            if (response.data) {
                                if (response.data.used === true) {
                                    this.updateUsername = null
                                    this.updateUsernameError = true
                                    this.$refs.updateUsername.showErrorMsg('Unique.username')
                                } else {
                                    this.updateUsername = value
                                    this.updateUsernameError = false
                                    this.$refs.updateUsername.resetErrorMsg()
                                }
                                this.checkUpdateForm()
                            }
                        })
                        .finally(() => {
                            this.$refs.updateUsername.setLoadData(false)
                        })

                } else if (value.length === 0 || value === this.user.username) {
                    this.updateUsername = null
                    this.updateUsernameError = false
                    this.$refs.updateUsername.resetErrorMsg()
                } else {
                    this.updateUsername = null
                    this.updateUsernameError = true
                    this.$refs.updateUsername.showErrorMsg("Regex.username")
                }
                this.checkUpdateForm()

            },

            // check if email address is valid
            emailUpdateValidation: function(value) {

                if (/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/.test(value) && value !== this.user.email) {

                    this.$refs.updateEmail.setLoadData(true)

                    axios
                        .get(this.$apiURL + '/verification/email/' + value)
                        .then(response => {
                            if (response.data) {
                                if (response.data.used === true) {
                                    this.updateEmail = null
                                    this.updateEmailError = true
                                    this.$refs.updateEmail.showErrorMsg("Unique.email")
                                } else {
                                    this.updateEmail = value
                                    this.updateEmailError = false
                                    this.$refs.updateEmail.resetErrorMsg()
                                }
                                this.checkUpdateForm()
                            }
                        })
                        .finally(() => {
                            this.$refs.updateEmail.setLoadData(false)
                        })

                } else if (value.length === 0 || value === this.user.email) {
                    this.updateEmail = null
                    this.updateEmailError = false
                    this.$refs.updateEmail.resetErrorMsg()
                } else {
                    this.updateEmail = null
                    this.updateEmailError = true
                    this.$refs.updateEmail.showErrorMsg("Email.email")
                }
                this.checkUpdateForm()

            },

            // check if password is valid
            passwordUpdateValidation: function(value) {

                // check if confirmation password match
                if (value !== this.lastUpdatePasswordConfirmation) {
                    this.updatePasswordConfirmation = null
                    this.updatePasswordConfirmationError = true
                    this.$refs.updatePasswordConfirmation.showErrorMsg('Confirmed.password')
                } else {
                    this.updatePasswordConfirmation = value
                    this.updatePasswordConfirmationError = false
                    this.$refs.updatePasswordConfirmation.resetErrorMsg()
                }
                this.checkUpdateForm()

                // save the last value for check with the confirmation password input
                this.lastUpdatePassword = value

                // check password strength
                this.$refs.updatePassword.setLoadData(true)

                axios
                    .post(this.$apiURL + '/verification/password/', {password: value}, {responseType: 'json'})
                    .then(response => {
                        let strength = response.data.strength*25
                        if (strength === 0) strength = 10
                        if (strength === 10 && value.length === 0 || isNaN(strength)) strength = 0
                        this.updatePasswordStrength = strength
                        if (value.length === 0) {
                            this.updatePassword = null
                            this.updatePasswordError = false
                            this.$refs.updatePassword.resetErrorMsg()
                        } else if (!/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/.test(value)) {
                            this.updatePassword = null
                            this.updatePasswordError = true
                            this.$refs.updatePassword.showErrorMsg('Regex.password')
                        } else if (this.updatePasswordStrength < 75) {
                            this.updatePassword = null
                            this.updatePasswordError = true
                            this.$refs.updatePassword.showErrorMsg('Strength.password')
                        } else {
                            this.updatePassword = value
                            this.updatePasswordError = false
                            this.$refs.updatePassword.resetErrorMsg()
                        }
                        this.checkUpdateForm()
                    })
                    .finally(() => {
                        this.$refs.updatePassword.setLoadData(false)
                    })

            },

            // check if passwords match
            passwordConfirmationUpdateValidation: function(value) {

                this.lastUpdatePasswordConfirmation = value

                if (value.length === 0) {
                    this.updatePasswordConfirmation = null
                    this.updatePasswordConfirmationError = false
                    this.$refs.updatePasswordConfirmation.resetErrorMsg()
                } else if (value !== this.lastUpdatePassword) {
                    this.updatePasswordConfirmation = null
                    this.updatePasswordConfirmationError = true
                    this.$refs.updatePasswordConfirmation.showErrorMsg('Confirmed.password')
                } else {
                    this.updatePasswordConfirmation = value
                    this.updatePasswordConfirmationError = false
                    this.$refs.updatePasswordConfirmation.resetErrorMsg()
                }

                this.checkUpdateForm()

            },

            passwordOldUpdateValidation: function (value) {

                if (value.length === 0) {
                    this.updatePasswordOld = null
                } else {
                    this.updatePasswordOld = value
                }

                this.checkUpdateForm()

            },

            // verifies that all fields are correctly filled in and authorises the submission of the update form
            checkUpdateForm: function() {

                if (!this.updateUsernameError && !this.updateEmailError && !this.updatePasswordError && !this.updatePasswordConfirmationError) {
                    if (this.updateUsername || this.updateEmail || (this.updatePassword && this.updatePasswordConfirmation && this.updatePasswordOld)) {
                        this.$refs.submitUpdateButton.setSubmitError(false)
                    } else {
                        this.$refs.submitUpdateButton.setSubmitError(true)
                    }
                } else {
                    this.$refs.submitUpdateButton.setSubmitError(true)
                }

            },

            // submit update form
            submitUpdate: function() {

                this.checkupUpdateError = []

                let url = this.$apiURL + '/user/account-settings/update',
                    data = {
                        _token: this.getCSRFToken(),
                        username: this.updateUsername,
                        email: this.updateEmail,
                        password: this.updatePassword,
                        password_confirmation: this.updatePasswordConfirmation,
                        old_password: this.updatePasswordOld
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
                            this.updateUser({
                                id: this.user.id,
                                username: this.updateUsername ? this.updateUsername : this.user.username,
                                email: this.updateEmail ? this.updateEmail : this.user.email,
                                verified: response.data.email_updated ? null : this.user.verified,
                                role: this.user.role,
                                birthdate: this.user.birthdate
                            })
                            this.updatePassword = this.updatePasswordConfirmation = this.updatePasswordOld = null
                            this.$refs.submitUpdateButton.setSubmitError(true)
                            if (response.data.email_updated) window.location.href = this.$appURL + '/app/verify'
                        } else {
                            this.checkupUpdateError = [response.data.error, 1, []]
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 419) {
                            this.checkupUpdateError = ['Session has expired, please reload this page', 1, []]
                        } else {
                            this.checkupUpdateError = ['Your data could not be saved', 1, []]
                        }
                    })
                    .finally(() => {
                        this.$refs.submitUpdateButton.stopLoader()
                    })

            }

        }

    }

</script>
