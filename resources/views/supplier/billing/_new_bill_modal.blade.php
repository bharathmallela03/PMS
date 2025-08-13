<div class="modal fade" id="newBillModal" tabindex="-1" aria-labelledby="newBillModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newBillModalLabel">Create New Bill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createBillForm" action="{{ route('supplier.billing.store') }}" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="pharmacist_name" class="form-label">Bill To (Pharmacist) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pharmacist_name" placeholder="Type pharmacist name to search..." required>
                            <input type="hidden" id="pharmacist_id" name="pharmacist_id">
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="medicine_search" class="form-label">Add Medicine</label>
                        <input type="text" class="form-control" id="medicine_search" placeholder="Type medicine name to search...">
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="bill-items-tbody">
                                <tr><td colspan="5" class="text-center">No items added.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                 <div class="me-auto">
                    <h5 class="mb-0">Total: <span id="bill-total">₹0.00</span></h5>
                    <input type="hidden" id="total_amount_input" name="total_amount" value="0">
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="createBillForm">Save Bill</button>
            </div>
        </div>
    </div>
</div>