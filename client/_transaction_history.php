<div class="container-fluid d-none" id="shopHistory">
<ul class="nav nav-pills nav-justified justify-content-center" id="shopHistoryNav" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="shoplist-tab" data-bs-toggle="tab" data-bs-target="#shop-list-pane"
            type="button" role="tab" aria-controls="shop-list-pane" aria-selected="true">
            <span class="small fw-bold">Unpaid Shop List</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pnd-tab" data-bs-toggle="tab" data-bs-target="#paid-no-driver-pane"
            type="button" role="tab" aria-controls="paid-no-driver-pane" aria-selected="false">
            <span class="small fw-bold">Paid No Rider</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pwd-tab" data-bs-toggle="tab" data-bs-target="#paid-with-driver-pane"
            type="button" role="tab" aria-controls="paid-with-driver-pane" aria-selected="false">
            <span class="small fw-bold">Paid With Rider</span>
        </button>
    </li>
</ul>
<div class="tab-content px-2" id="shopHistoryTabs">
    <div class="tab-pane fade show active" id="shop-list-pane" role="tabpanel" aria-labelledby="shoplist-tab"
        tabindex="0">Loading...</div>
    <div class="tab-pane fade" id="paid-no-driver-pane" role="tabpanel" aria-labelledby="pnd-tab" tabindex="0">
        Loading</div>
    <div class="tab-pane fade" id="paid-with-driver-pane" role="tabpanel" aria-labelledby="pwd-tab"
        tabindex="0">.3..</div>
</div>
</div>
<div class="container-fluid mx-1">
<div class="row g-1">

    <div class="col-12 clear-fix px-2">
        <input type="text" class="rounded-4 form-control form-control-sm d-flex w-100 my-1" id="SearhItems"
            placeholder="Search Items, Merchants">
    </div>
</div>
</div>