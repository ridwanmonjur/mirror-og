    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        @include('CommonLayout.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';" action="{{ route('event.updateForm', $event->id) }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        <input type="hidden" name="livePreview" id="livePreview" value="false">
                        <input type="hidden" name="gameTitle" id="gameTitle">
                        <input type="hidden" name="eventTier" id="eventTier">
                        <input type="hidden" name="eventType"  id="eventType">
                        @if ($event && $event->payment_transaction_id != null)
                        <input type="hidden" name="isPaymentDone"  id="isPaymentDone" value="done">
                        <input type="hidden" name="paymentMethod"  id="paymentMethod" value="done">
                        <input type="hidden" name="paymentMethod"  id="paymentMethod" value="done">
                        @else
                        <input type="hidden" name="isPaymentDone"  id="isPaymentDone">
                        <input type="hidden" name="paymentMethod"  id="paymentMethod">
                        @endif
                        <input type="hidden" name="gameTitleId" id="gameTitleId" value="{{$event->}}">
                        <input type="hidden" name="eventTierId" id="eventTierId" value="{{$event->}}">
                        <input type="hidden" name="eventTypeId"  id="eventTypeId" value="{{}}">
                        @include('Organizer.Layout.CreateEventTimelineBox')
                        @if (session()->has('error'))
                        @include('Organizer.Layout.CreateEventStepOneEdit', ['error' => session()->get('error')])
                        @else
                        @include('Organizer.Layout.CreateEventStepOneEdit')
                        @endif
                        @include('Organizer.Layout.CreateEventForm' , ['event' => $event])
                        @if (session()->has('success'))
                        @include('Organizer.Layout.CreateEventSuccess')
                        @endif
                    </form>
                </div>
                @include('Organizer.Layout.CreateEventPaymentModal')
            </div>
            <br><br>
        </main>
        @include('CommonLayout.BootstrapJs')
        @include('Organizer.Layout.CreateEventScripts')

    </body>