<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Jackpot Realm - chat widget</title>
        <link href="{{ mix('/app/css/app.css') }}" rel="stylesheet">
        <link rel="preload" href="{{ mix('/app/css/async.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{{ mix('/app/css/async.css') }}"></noscript>
    </head>

    <body class="antialiased bg-transparent h-th-chat-height no-scrollbar">

        <div id="messages">

        </div>

        <script src="{{ mix('/app/js/echoWidget.js') }}"></script>
        <script type="text/javascript">
            Echo.channel('chat')
                .listen('NewChatMessage', (e) => {
                    if (e.message && e.user && e.id && e.color) {
                        let container = document.getElementById('messages')
                        let newMessage = document.createElement('div')
                        let messageContent = e.message.replaceAll(/(:[a-zA-Z0-9_]{3,25}:)/g, function (matches, contents) {
                            return '<img class="display-emoji" height="24" width="24" src="/storage/users/{{ $streamer }}/emojis/' + contents.replaceAll(':', '') + '.png" alt="' + contents.replaceAll(':', '') + '">'
                        })
                        newMessage.innerHTML = '<div id="' + e.mid + '" class="px-5 pt-1 pb-1">' +
                                '<span class="font-semibold" style="color: #' + e.color + ';">' + e.user + '</span> ' +
                                '<span class="font-semibold text-th-chat-text-color">:</span> ' +
                                '<span id="msgContent-' + e.mid + '" class="font-semibold text-th-chat-text-color break-words">' + messageContent + '</span>' +
                            '</div>'
                        container.appendChild(newMessage)
                        window.scrollTo(0, document.body.scrollHeight)
                    }
                });
            Echo.channel('delete-message')
                .listen('DeleteChatMessage', (e) => {
                    document.getElementById('msgContent-' + e.mid).innerHTML = 'This message has been deleted'
                })
        </script>

    </body>

</html>
