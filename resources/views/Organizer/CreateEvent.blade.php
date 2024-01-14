    @include('Organizer.Layout.CreateEventHeadTag')
    <!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->

    <body>
        @include('CommonLayout.NavbarGoToSearchPage')
        <main>
            <div>
                <div>
                    <form enctype="multipart/form-data" onkeydown="return event.key != 'Enter';"
                        action="{{ route('event.store') }}" method="post" name="create-event-form" novalidate>
                        @csrf
                        <input type="hidden" name="livePreview" id="livePreview" value="false">
                        <input type="hidden" name="gameTitle" id="gameTitle">
                        <input type="hidden" name="eventTier" id="eventTier">
                        <input type="hidden" name="eventType"  id="eventType">
                        <input type="hidden" name="gameTitleId" id="gameTitleId">
                        <input type="hidden" name="eventTierId" id="eventTierId">
                        <input type="hidden" name="eventTypeId"  id="eventTypeId">
                        <input type="hidden" name="isPaymentDone"  id="isPaymentDone">
                        <input type="hidden" name="paymentMethod"  id="paymentMethod">
                        @include('Organizer.CreateEdit.CreateEventTimelineBox')
                        @if (session()->has('error'))
                            @include('Organizer.CreateEdit.CreateEventTimelineWelcome', [
                                'error' => session()->get('error'),
                            ])
                        @else
                            @include('Organizer.CreateEdit.CreateEventTimelineWelcome')
                        @endif
                        @include('Organizer.CreateEdit.CreateEventStepOneCreate')
                        @include('CommonLayout.BootstrapJs')
                        @include('Organizer.CreateEdit.CreateEventForm')
                        @if (session()->has('success'))
                            @include('Organizer.CreateEdit.CreateEventSuccess')
                        @endif
                    </form>
                </div>
                @include('Organizer.CreateEdit.CreateEventPaymentModal')
            </div>
            <br><br>
        </main>
        @include('Organizer.CreateEdit.CreateEventScripts')

    </body>
