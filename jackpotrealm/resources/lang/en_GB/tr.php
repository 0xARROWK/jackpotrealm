<?php

// EN

return [

	// Common
    'Jackpot Realm' => 'Jackpot Realm',

    // Menu
    'Parameters' => 'Parameters',
    'Log In' => 'Log In',
    'Sign Up' => 'Sign Up',
    'Log Out' => 'Log out',
    'Current language' => App::getLocale(),
    'Default language' => config("app.fallback_locale"),

    // Log In/Sign Up component
    'Username' => 'Username',
    'Enter your username' => 'Enter your username',
    'Password' => 'Password',
    'Password confirmation' => 'Password confirmation',
    '************' => '************',
    'Email address' => 'Email address',
    'Enter your email address' => 'Enter your email address',
    'Date of birth' => 'Date of birth',
    'Day' => 'Day',
    'Month' => 'Month',
    'Year' => 'Year',
    'Sorry, this user is not from here' => 'Sorry, this user is not from here.',
    'Too many login attempts' => 'Too many attempts. Please try again in :seconds seconds.',
    'A verification link has been sent to your email address, if you did not receive the email, you can request another below' => 'A verification link has been sent to your email address. If you did not receive the email, you can request another below.',
    'Click here to request another' => 'Click here to request another',
    'You can request another one in 60 seconds' => 'You can request another one in :s seconds.',
    'Close' => 'Close',
    'Continue' => 'Continue',
    'You already have an account' => 'You already have an account ?',
    'You don\'t have an account' => 'You don\'t have an account ?',
    'Forgot my password' => 'Forgot my password',
    'Verify your email address' => 'Verify your email address',
    'Session has expired, please reload this page' => 'Your session has expired, please reload the page.',
    'Sorry, we are temporarily not able to create your stream key' => 'Sorry, we are temporarily not able to create your stream key.',

    // Reset password
    'Reset password' => 'Reset password',
    'Send password reset link' => 'Send password reset link',
    'Password reset link has been sent' => 'If an account linked to this address exists, an email containing a link to reset the password has been sent.',
    'This password reset link has expired or has already been used' => 'This password reset link has expired or has already been used.',
    'Your password has been reset' => 'Your password has been reset, you can now log in.',

    // Home
    'Chat' => 'Chat',
    'You must be logged in to send messages in the chat' => 'You must be logged in to send messages in the chat.',
    'You must have verified your email address to send messages in the chat' => 'You must have verified your email address to send messages in the chat.',
    'You must be logged in and have verified your email address to send messages in the chat' => 'You must be logged in and have verified your email address to send messages in the chat.',
    'You have been disconnected from the chat' => 'You have been disconnected from the chat.',
    'Connection to the chat failed, retrying' => 'Connection to the chat failed. Retrying...',
    'Connection to the chat' => 'Connection to the chat...',
    'Send' => 'Send',
    'Your message contain a banned word' => 'Your message cannot be published because it contains a banned word or expression.',
    'Try to type ":"' => 'Try to type ":"',
    'There is no result for your search' => 'There is no result for your search.',
    'Emoji(s) corresponding to' => 'Emoji(s) corresponding to',

    // Settings
    'Account settings' => 'Account settings',
    'Dashboard' => 'Dashboard',
    'Moderation' => 'Moderation',
    'Wrong password' => 'Wrong password.',
    'You can\'t reuse your old password' => 'You can\'t reuse your old password.',
    'To change your password, please enter the new password, the confirmation and your old password' => 'To change your password, please enter the new password, the confirmation and your old password.',
    'Update' => 'Update',
    'Updated' => 'Updated',
    'Uploaded' => 'Uploaded',
    'Processing' => 'Processing',
    'Profile picture' => 'Profile picture',
    'Choose a file' => 'Choose a file',
    'Upload' => 'Upload',
    'Please select a png, jpg or jpeg file' => 'Please select a valid image file (accepted : *.png, *.jpg, *.jpeg).',
    'Please select a png, jpg, jpeg or gif file' => 'Please select a valid image file (accepted : *.png, *.jpg, *.jpeg, *.gif).',
    'Chat color' => 'Chat color',
    'Preview' => 'Preview',
    'Just take a look at this chat color !' => 'Just take a look at this chat color !',

    // Moderation settings
    'Banned :banned' => 'Banned :banned',
    'Separate banned :banned with "enter"' => 'Separate banned :banned with "enter".',

    // Dashboard settings
    'Streaming key' => 'Streaming key',
    'Regenerate' => 'Regenerate',
    'Regenerated' => 'Regenerated',
    'Copy' => 'Copy',
    'Copied' => 'Copied',
    'Show' => 'Show',
    'Hide' => 'Hide',
    'Emoji name' => 'Emoji name',
    'Enter name of emoji' => 'Enter name of emoji',
    'Please select a valid tier' => 'Please select a valid tier.',
    'List of custom emojis' => 'List of custom emojis',
    'Upload emojis and see it here' => 'Upload emojis and see it here.',
    'Delete' => 'Delete',
    'Stream information' => 'Stream information',
    'Title' => 'Title',
    'Title of your stream' => 'Title of your stream',
    'Short description of your stream' => 'Short description of your stream',
    'The value must contain less than n characters' => 'The :value must contain less than :n characters',
    'Twitch stream' => 'Twitch stream',
    'Streaming URL' => 'Streaming URL',
    'Multistream is :' => 'Multistream is :',
    'Disabled' => 'Disabled',
    'Enabled' => 'Enabled',
    'To share your stream, you must specify a playback URL and your stream key' => 'To share your stream, you must specify a playback URL and your stream key.',
    'Too many requests' => 'You have made too many requests. Please wait a few seconds before trying again.',
    'Usage' => 'Usage',
    'free' => 'Free',
    'tier_1' => 'Tier 1',
    'tier_2' => 'Tier 2',
    'tier_3' => 'Tier 3',
    'recent_emojis' => 'Recent emojis',

    // CSRF and token errors
    'Invalid token' => 'Invalid CSRF token.',
    'We are unable to get your access token' => 'We are unable to get your access token.',
    'Invalid user access token' => 'Invalid user access token. In order for us to recover your data correctly, please log in again.',

    // Other errors
    'An error occurred' => 'An unknown error has occurred. Please try again later.',
    'Your data could not be saved' => 'Your data could not be saved. Please try again later.',
    'Bad action requested' => 'Bad action requested.',
    'Error' => 'Error',
    'We are unable to retrieve data of the stream' => 'We are unable to retrieve data of the stream.',
    'We are unable to retrieve your data' => 'We are unable to retrieve your data. In order for us to recover your data correctly, please log in again.',
    'Please fill in correctly fields' => 'Please, fill in correctly all the fields.',

    //Form validation errors
    'Required' => [
        'username' => 'The "username" field has not been filled in correctly.',
        'email' => 'The "email" field has not been filled in correctly.',
        'password' => 'The "password" field has not been filled in correctly.',
        'birthdate' => 'The "date of birth" field has not been filled in correctly.',
    ],

    'Unique' => [
        'username' => 'Username must be unique.',
        'email' => 'This email address is already associated with an account.',
        'emojiName' => 'This emoji name is already used.',
    ],

    'String' => [
        'username' => 'The username is not a valid string',
        'email' => 'The email is not a valid string',
        'password' => 'The password is not a valid string',
    ],

    'Regex' => [
        'username' => 'Username must contain between 3 and 25 alphanumeric characters.',
        'password' => 'Password must contain at least 8 characters, including 1 upper case, 1 lower case and 1 number.',
        'emojiName' => 'Emoji name must contain between 3 and 25 characters (only letters, numbers or _ allowed)'
    ],

    'Min' => [
        'password' => 'Password must contain at least 8 characters, including 1 upper case, 1 lower case and 1 number.',
    ],

    'Max' => [
        'email' => 'Email address must not exceed 75 characters.',
    ],

    'Between' => [
        'username' => 'Username must contain between 3 and 25 alphanumeric characters.',
    ],

    'Email' => [
        'email' => 'Please enter a valid email address.',
    ],

    'Strength' => [
        'password' => 'Your password is too easy to guess.'
    ],

    'Confirmed' => [
        'password' => 'Passwords do not match.',
    ],

    'Date' => [
        'birthdate' => 'Please enter a valid date.',
    ],

    'DateFormat' => [
        'birthdate' => 'Date of birth must be in the format mm-dd-yyyy.',
    ],

    'Before' => [
        'birthdate' => 'You must be at least 13 years old to create an account.',
    ],

    'After' => [
        'birthdate' => 'Please select a valid birthdate.',
    ],
];
