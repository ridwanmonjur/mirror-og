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
                        <input type="hidden" name="gameTitle" id="gameTitle">
                        <input type="hidden" name="eventTier" id="eventTier">
                        <input type="hidden" name="eventType"  id="eventType">
                        <input type="hidden" name="gameTitleId" id="gameTitleId">
                        <input type="hidden" name="eventTierId" id="eventTierId">
                        <input type="hidden" name="eventTypeId"  id="eventTypeId">
                        <input type="hidden" name="isPaymentDone"  id="isPaymentDone">
                        <input type="hidden" name="paymentMethod"  id="paymentMethod">
                        @include('Organizer.Layout.CreateEventTimelineBox')
                        @include('Organizer.Layout.CreateEventTimelineWelcome')
                        @include('Organizer.Layout.CreateEventStepOneHide')
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