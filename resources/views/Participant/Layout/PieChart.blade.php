<div class="pie-chart ms-3 ps-3 d-flex justify-content-center">
    <div>
        <canvas class={{'myChart' . $isInvited }}></canvas>
        <p> Total Entry Fee: <u>RM {{$joinEvent->tier->tierEntryFee * $joinEvent->tier->tierTeamSlot}} </u></p>
        <p> Paid: <u class="text-success">RM  </u> Pending: <u class="text-danger">RM </u> </p>
    </div>
</div>