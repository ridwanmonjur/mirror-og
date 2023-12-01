<div class="modal fade" id="payment-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-div" style="display: flex !important; justify-content: space-between !important;">
                <h5 class="modal-title" id="payment-modal-label"> &nbsp; &nbsp;Payment method</h5>
                <button type="button" class="btn-close" id="modal-close" data-bs-dismiss="modal" aria-label="Close">&nbsp;X&nbsp;</button>
            </div>
            <div class="modal-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h4>Make A Payment</h4>
                        @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                        @endif
                        <form id="card-form">
                            @csrf
                            <div class="form-group form-group2">
                                <label for="card-name" class="">Your name</label>
                                <input type="text" name="name" id="card-name" class="">
                            </div>
                            <div class="form-group form-group2">
                                <label for="email" class="">Email</label>
                                <input type="email" name="email" id="email" class="">
                            </div>
                            <div class="form-group form-group2">
                                <label for="card" class="">Card details</label>

                                <div class="form-group form-group2">
                                    <div id="card"></div>
                                </div>
                            </div>
                            <button type="submit" class="oceans-gaming-default-button">Pay 👉</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="oceans-gaming-default-button oceans-gaming-transparent-button" data-bs-dismiss="modal"> Back </button>
            </div>
        </div>
    </div>
</div>