    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        @include('CommonLayout.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.updateForm', $event->id) }}" method="post" name="create-event-form"
                        novalidate>
                        @csrf
                        <input type="hidden" name="livePreview" id="livePreview" value="false">
                        <input type="hidden" name="gameTitle" id="gameTitle"  value="{{$event->game?->gameTitle}}">
                        <input type="hidden" name="eventTier" id="eventTier" value="{{$event->tier?->eventTier}}" >
                        <input type="hidden" name="eventType"  id="eventType"  value="{{$event->type?->eventType}}">
                        @if ($event && $event->payment_transaction_id != null)
                            <input type="hidden" name="isPaymentDone"  id="isPaymentDone" value="done">
                            <input type="hidden" name="paymentMethod"  id="paymentMethod" value="done">
                        @else
                            <input type="hidden" name="isPaymentDone"  id="isPaymentDone">
                            <input type="hidden" name="paymentMethod"  id="paymentMethod">
                        @endif
                        <input type="hidden" name="gameTitleId" id="gameTitleId" value="{{ $event->event_category_id }}">
                        <input type="hidden" name="eventTierId" id="eventTierId" value="{{ $event->event_tier_id }}">
                        <input type="hidden" name="eventTypeId"  id="eventTypeId" value="{{ $event->event_type_id }}">
                        @include('Organizer.CreateEdit.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.CreateEdit.CreateEventStepOneEdit', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.CreateEdit.CreateEventStepOneEdit')
                        @endif
                        @include('Organizer.CreateEdit.CreateEventForm', ['event' => $event])
                        @if (session()->has('success'))
                            @include('Organizer.CreateEdit.CreateEventSuccess')
                        @endif
                    </form>
                </div>
                @include('Organizer.CreateEdit.CreateEventPaymentModal')
            </div>
            <br><br>
        </main>
        @include('CommonLayout.BootstrapJs')
        @include('Organizer.CreateEdit.CreateEventScripts')
        <script>

        </script>
    </body>
