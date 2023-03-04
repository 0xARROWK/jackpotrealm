<template>

    <div class="overflow-hidden rounded-lg shadow-lg bg-th-card mt-5 lg:mt-menu">

        <div class="flex items-center justify-between leading-tight p-2 sm:p-4">
            <h1 class="text-md font-bold">
                <a class="text-white">
                    {{ __('Profile picture') }}
                </a>
            </h1>
        </div>

        <div class="flex items-center leading-none px-2 pb-2 sm:px-4 sm:pb-4">
            <img class="w-14 h-14 sm:w-28 sm:h-28 rounded-full mr-4 border-2 border-th-color" :src="this.image" alt="Avatar">
            <input class="hidden" type="file" ref="profilePictureFile" @change="onFileInput" />
            <div class="text-sm w-full">
                <form-input key="profile-picture-name" input-type="text" :input-value="this.selectedFilename" :input-disabled="true"></form-input>
                <div class="xs:flex xs:justify-between">
                    <button-loader @submitted="$refs.profilePictureFile.click()" btn-class="mt-2 mr-2 xs:mr-0" :text="__('Choose a file')" :take-full-width="false" :prevent-submit="false" :can-load="false"></button-loader>
                    <button-loader @submitted="uploadFile" btn-class="mt-2 xs:ml-2 sm:ml-0" :text="__('Upload')" :take-full-width="false"  :finish-loading-animation="true" finish-loading-msg="Uploaded" ref="submitProfilePictureButton"></button-loader>
                </div>
                <form-error :error-msg="this.uploadProfilePictureError[0]" :error-count="this.uploadProfilePictureError[1]" :error-replace="this.uploadProfilePictureError[2]"></form-error>
            </div>
        </div>

    </div>

</template>

<script>

    import FormInput from "../../general/form/FormInputComponent";
    import FormError from "../../general/form/FormErrorComponent";
    import ButtonLoader from "../../general/form/ButtonLoaderComponent";

    import { mapGetters } from 'vuex';
    import GlobalStore from "../../store/GlobalStore";

    export default {

        name: "ProfilePicture",

        components: {
            FormInput,
            FormError,
            ButtonLoader
        },

        store: GlobalStore,

        data: function () {

            return {

                image: null,
                selectedFile: null,
                selectedFilename: null,
                uploadProfilePictureError: []

            }

        },

        computed: {

            ...mapGetters(['user'])

        },

        created: function () {
            this.image = 'storage/users/' + this.user.username + '/profile_picture.jpg?t=' + new Date()
        },

        methods: {

            onFileInput: function(event) {
                this.uploadProfilePictureError = []
                this.selectedFilename = event.target.files[0].name;
                this.selectedFile = event.target.files[0];
                try {
                    this.image = URL.createObjectURL(this.selectedFile);
                } catch (e) {}
                this.$refs.submitProfilePictureButton.setSubmitError(false)
            },

            uploadFile: function() {

                if (this.selectedFile && (this.selectedFile.type === "image/png" || this.selectedFile.type === "image/jpg" || this.selectedFile.type === "image/jpeg")) {
                    let formData = new FormData();
                    formData.append('profile_picture', this.selectedFile)
                    axios
                        .post(this.$apiURL + "/user/account-settings/update/picture", formData, {
                            headers: {
                                Authorization: 'Bearer ' + this.getLSI('api_token')
                            },
                            responseType: 'json'
                        })
                        .then(response => {
                            if (response.data && response.data.success) {
                                this.selectedFilename = ''
                                this.$refs.profilePictureFile.value = null
                            } else if (response.data && response.data.error) {
                                this.uploadProfilePictureError = [response.data.error, 1, []]
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 419) {
                                this.uploadProfilePictureError = ['Session has expired, please reload this page', 1, []]
                            } else {
                                this.uploadProfilePictureError = ['Your data could not be saved', 1, []]
                            }
                        })
                        .finally(() => {
                            this.$refs.submitProfilePictureButton.stopLoader()
                            this.$refs.submitProfilePictureButton.setSubmitError(true)
                        })
                } else {
                    this.$refs.submitProfilePictureButton.stopLoader()
                    this.uploadProfilePictureError = ['Please select a valid image file (accepted : png, jpg, jpeg)', 1, []]
                }
            }

        }

    }

</script>
