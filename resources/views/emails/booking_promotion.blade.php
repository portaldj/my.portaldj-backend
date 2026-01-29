<x-mail::message>
    # New Booking Promotion Request

    **DJ:** {{ $djName }}
    **Event:** {{ $eventTitle }}
    **Date:** {{ $eventDate }}

    ## Press Release Note:
    > {{ $pressRelease }}

    <x-mail::button :url="route('admin.dashboard')">
        View in Admin Panel
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>