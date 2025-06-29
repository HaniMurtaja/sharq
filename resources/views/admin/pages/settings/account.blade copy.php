<div class="active tab-pane table-responsive p-0" style="height: 450px;" id="account">

    <div class="card-header">
        <h4>Account</h4>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form action="{{route('save-account')}}" method="post">
            @csrf
            <p>Information</p>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                    @include('admin.includes.validation-error', ['input' => 'first name'])

                        <input type="text" class="form-control" value="{{$settings->account['first_name']}}" name="first_name" id="firstName" placeholder="First Name">

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{$settings->account['last_name']}}" name="last_name" id="lastName" placeholder="Last Name">
                        @include('admin.includes.validation-error', ['input' => 'Last Name'])

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{$settings->account['email']}}" name="email" id="firstName" placeholder="Email">
                        @include('admin.includes.validation-error', ['input' => 'Email'])

                    </div>

                </div>
            </div>


            <p>Billing detail</p>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" value="{{$settings->account['billing_vAT_no']}}" name="billing_vAT_no" id="firstName" placeholder="Billing VAT No">
                        @include('admin.includes.validation-error', ['input' => 'Billing VAT No'])

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="lastName" name="billing_name" value="{{$settings->account['billing_name']}}" placeholder="Billing Name">
                        @include('admin.includes.validation-error', ['input' => 'Billing Name'])

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" name="street_name" value="{{$settings->account['street_name']}}" id="firstName" placeholder="Street Name">
                        @include('admin.includes.validation-error', ['input' => 'Street Name'])

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="lastName" name="billing_bulding_no" value="{{$settings->account['billing_bulding_no']}}" placeholder="Billing bulding No">
                        @include('admin.includes.validation-error', ['input' => 'Billing bulding No'])

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="firstName" value="{{$settings->account['billing_district']}}" name="billing_district" placeholder="Billing district">
                        @include('admin.includes.validation-error', ['input' => 'Billing district'])

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="lastName" name="billing_city" value="{{$settings->account['billing_city']}}" placeholder="Billing city">
                        @include('admin.includes.validation-error', ['input' => 'Billing city'])

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="firstName" name="billing_email" value="{{$settings->account['billing_email']}}" placeholder="Billing Email">
                        @include('admin.includes.validation-error', ['input' => 'Billing Email'])

                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-md-3">
                    <button type="submit" class="btn btn-block bg-gradient-primary btn-sm">Save</button>

                </div>
        </form>



    </div>

</div>



</div>