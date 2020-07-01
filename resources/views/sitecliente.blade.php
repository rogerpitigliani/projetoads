@extends('layouts.app')

@section('content')
<div class="container">
    <h1> Site da Empresa - Exemplo de Chat</h1>
</div>

<script src="https://unpkg.com/blip-chat-widget" type="text/javascript">
</script>
<script>
    (function () {
        window.onload = function () {
            new BlipChat()
            .withAppKey('Y29tdW5pY2FsbDpmZjcwNTU4Ny00YmJjLTQ4OTUtOWU0ZS05ZWU0OTRhMmQ0Mjc=')
            .withButton({"color":"#2CC3D5","icon":"https://s3-sa-east-1.amazonaws.com/msging.net/Services/Images/073add07-2210-44b8-9204-cba27548d699"})
            .withCustomCommonUrl('https://chat.blip.ai/')
            .build();
        }
    })();
</script>

@endsection
