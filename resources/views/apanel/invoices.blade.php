@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                {{-- Menu --}}
                <ul class="nav nav-tabs nav-tabs-bordered">
                    {{-- REQUESTED INVOICE --}}
                    <li class="nav-item">
                        <a href="#configurations-application" class="nav-link active" data-toggle="tab" id="requested"
                           aria-expanded="true">
                            @lang('Requested Invoices')
                        </a>
                    </li>
                    {{-- PAID INVOICE --}}
                    <li class="nav-item">
                        <a data-toggle="tab" id="paid" class="nav-link" data-toggle="tab" aria-expanded="false">
                            @lang('Paid Invoices')
                        </a>
                    </li>


                </ul>

                {{-- Invoices --}}
                <div class="tab-content tabs-bordered">
                    {{-- Requested Invoices --}}
                    <div class="tab-pane fade active in" data-toggle="tab" id="requested-invoice">
                        <table id="invoiceRequestTable" class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>@lang('Invoice ID')</th>
                                <th>@lang('Streamer')</th>
                                <th>@lang('Payment Status')</th>
                                <th>@lang('Fund Raised')</th>
                                <th>@lang('Platform Fee')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Ready')</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                    {{-- Paid Invoice --}}
                    <div class="tab-pane fade " data-toggle="tab" id="paid-invoice">

                    </div>


                </div>
                {{-- End Invoices --}}
                <br>

            </div>
        </div>
    </div>



@endsection

@section('scripts')
    <script>
        let invoiceRequestTable;
        let route;

        function onTabSelected() {

            var $activeTab = $('.nav-item a.active');
            var id = $activeTab.attr('id');

            if (id === "requested") {
                route = `{{route('apanel.requested.invoices')}}`;


            }
            else {

                route = `{{route('apanel.paid.invoices')}}`;


            }
            let i = 1;

            invoiceRequestTable = $('#invoiceRequestTable').DataTable({
                serverSide: true,
                destroy: true,
                processing: true,
                sScrollX: "100%",
                iDisplayLength: -1,
                bAutoWidth: false,
                bScrollAutoCss: true,
                sScrollXInner: "100%",
                ajax: route,
                columns: [
                    {
                        "render": function () {
                            return i++;
                        }
                    },
                    {
                        data: "name"
                    },

                    {

                        data: "invoice_status",

                        sortable: true,
                        render: function (data) {
                            var statuses = JSON.parse(`{!! json_encode(trans('invoice.invoice.statuses')) !!}`);
                            var color = 'danger';
                            if (data == 'paid')
                                color = 'success';
                            else if (data == 'processing')
                                color = 'warning';
                            return `<span class="text-${color}">${statuses[data]}</span>`;
                        }
                    },

                    {data: "amount"},
                    {data: "commission_amount"},
                    {data: "updated_at"},

                    {
                        data: "invoice_id",
                        className: 'dt-body-center',
                        render: function (data, type, full, meta) {
                            setTimeout(function () {
                                $('#invoiceStatus-update-' + data).ajaxForm({
                                    dataType: 'json',
                                    success: function (data) {
                                        auto_notify(data);
                                        if (typeof data.success != 'undefined') donationTable.ajax.reload();
                                    },
                                    error: function (data) {
                                        error_notify(data.responseJSON);
                                    }
                                });
                            }, 500);

                            return `{!! Form::open(['route' => 'apanel.invoice.update', 'id' =>  'invoiceStatus-update-@{{ id }}]) !!}
                                    {!! Form::hidden('id', '@{{ id }}') !!}
                                    {{ Form::checkbox('asap',data==1,false, array('id'=>'asap', 'style'=>'width:20px; height:20px','data-toggle'=>'toggle', 'data-onstyle'=>'success',)) }}
                                    {!! Form::close() !!}`.replaceAll('@{{ id }}', data);
                        }
                    },
                    {
                        data: "id",
                        className: 'dt-body-center',
                        render: function (data, type, full, meta) {
                            setTimeout(function () {
                                $('#generate-invoice-' + data).ajaxForm({
                                    dataType: 'json',
                                    success: function (response) {

                                        if (typeof response.data != 'undefined') donationTable.ajax.reload();
                                        //console.log(response);
                                        let blob = new Blob([response.data], {type: 'application/pdf'}),
                                            url = window.URL.createObjectURL(blob);

                                        window.open(url)

                                    },
                                    error: function (data) {
                                        error_notify(data.responseJSON);
                                    }
                                });
                            }, 500);

                            return `{!! Form::open(['route' => 'apanel.invoice.generate', 'id' =>  'generate-invoice-@{{ id }}]) !!}
                                    {!! Form::hidden('id', '@{{ id }}') !!}
                                    {{ Form::checkbox('asap',true,false, array('id'=>'asap', 'style'=>'width:20px; height:20px','data-toggle'=>'toggle', 'data-onstyle'=>'success',)) }}
                                    {!! Form::close() !!}`.replaceAll('@{{ id }}', data);
                        }
                    },


                    /* {
                         data: "id",
                         render: function ( data, type, full, meta ) {
                             setTimeout(function() {
                                 $('#message-delete-' + data).ajaxForm({
                                     dataType:  'json',
                                     success: function(data) { auto_notify(data); if (typeof data.success != 'undefined') donationTable.ajax.reload();  },
                                     error: function(data) { error_notify(data.responseJSON); }
                                 });
                             }, 500);

                         }
                     }*/

                ]
            });


        }

        $(function () {
            onTabSelected();
        });
        $('.nav-item a').click(function () {
            $(this).tab('show');
            onTabSelected();

        });


    </script>

@endsection

