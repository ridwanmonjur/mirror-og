<input type="hidden" name="livePreview" id="livePreview" value="false">
<input type="hidden" name="gameTitle" id="gameTitle" value="{{ $event->game?->gameTitle }}">
<input type="hidden" name="eventTier" id="eventTier" value="{{ $event->tier?->eventTier }}">
<input type="hidden" name="eventType"  id="eventType" value="{{ $event->type?->eventType }}">
<input type="hidden" name="isPaymentDone" id="isPaymentDone"
    value="{{ $event && $event->payment_transaction_id ? 'done' : '' }}">
<input type="hidden" name="paymentMethod" id="paymentMethod"
    value="{{ $event && $event->payment_transaction_id ? 'done' : '' }}">
<input type="hidden" name="gameTitleId" id="gameTitleId" value="{{ $event->event_category_id }}">
<input type="hidden" name="eventTierId" id="eventTierId" value="{{ $event->event_tier_id }}">
<input type="hidden" name="eventTypeId"  id="eventTypeId" value="{{ $event->event_type_id }}">
<input type="hidden" name="goToCheckoutPage"  id="goToCheckoutPage">
