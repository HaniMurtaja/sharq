
<form class="mb-0">



    <div class="row gy-3">






        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">ID</span>
            <input type="text" name="id" value="{{ request()->id }}" id="search"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Name </span>
            <input type="text" name="name" value="{{ request()->name }}"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Email </span>
            <input type="text" name="email" value="{{ request()->email }}"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Phone </span>
            <input type="text" name="phone" value="{{ request()->phone }}"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>








        <div class="col-md-3"></div>
        <div class="col-md-12 text-end">
            <a class="p-9228 black-1a bg-light br-96  fs-112 fw-semibold border"
                href="{{ route('clientupdated') }}">Back</a>
            <button type="submit" class="pxy-828 text-white br-96 bg-red-a3 fs-112 fw-semibold">
                <span>Apply Filter</span>
            </button>

        </div>

    </div>
</form>
