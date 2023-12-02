    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        <main>
            <div>
                @include('CommonLayout.Navbar')
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        <input type="hidden" name="livePreview" id="livePreview" value="false">
                        <input type="hidden" name="gameTitle" id="gameTitle" value="{{ $event ? $event->gameTitle: ''  }}">
                        <input type="hidden" name="eventTier" id="eventTier" value="{{ $event ? $event->eventTier: ''  }}">
                        <input type="hidden" name="eventType"  id="eventType" value="{{ $event ? $event->eventType: ''  }}">
                        <input type="hidden" name="isPaymentDone"  id="isPaymentDone">
                        <input type="hidden" name="paymentMethod"  id="paymentMethod">
                        <input type="hidden" name="gameTitleId" id="gameTitleId" value="{{ $event ? $event->gameTitleId: ''  }}">
                        <input type="hidden" name="eventTierId" id="eventTierId" value="{{ $event ? $event->eventTierId: ''  }}">
                        <input type="hidden" name="eventTypeId"  id="eventTypeId" value="{{ $event ? $event->eventTypeId: ''  }}">

                        @include('Organizer.Layout.CreateEventTimelineBox')
                        @include('Organizer.Layout.CreateEventStepOneShow')
                        @include('Organizer.Layout.CreateEventForm')
                        @if (session()->has('success'))
                        @include('Organizer.Layout.CreateEventSuccess')
                        @endif
                    </form>
                </div>
                @include('Organizer.Layout.CreateEventPaymentModal')
            </div>
            <br><br>
        </main>
        @include('Organizer.Layout.CreateEventScripts')


    </body>