@extends('layouts.app')

@section('css')
    <style>
        @media screen and (max-width: 480px) {
            #invoiceTable {
                margin-left: -30px !important;
            }
        }
    </style>
@endsection

@section('content')

    {{-- Messages --}}
    <table id="invoiceTable" class="table table-striped table-hover">
        <thead>
        <tr>
            <th>@lang('Invoice ID')</th>
            <th>@lang('Payment Status')</th>
            <th>@lang('Fund Raised')</th>
            <th>@lang('Platform Fee')</th>
            <th>@lang('Date')</th>

        </tr>
        </thead>
    </table>

@endsection
{{-- Create Invoice --}}
@section('modals')
    <div class="modal fade" id="invoiceCreate" tabindex="-1" role="dialog" aria-labelledby="invoiceCreateLabel">
        <div class="modal-dialog" role="document">
            {!! Form::open(['route' => 'invoice.create', 'class' => 'modal-content form-horizontal', 'id' => 'invoiceCreateForm', 'autocomplete' => 'off']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="invoiceCreateLabel">Raise Invoice</h4>
            </div>
            <div class="modal-body">
                {{-- Donor Name --}}

                <p><b>You are going to raise invoice as a payment process. We might require 2/3 business days to verify
                        and
                        release the fund for you.</b><br>
                <p>Thank you for choosing us.</p>
                <p>
                    <small><i>*Donation are subjected to TDS and taxation.</i></small>
                </p>
            </div>
            <div class="modal-footer">
                {{-- Cancel --}}
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">@lang('donations.home.create.close')</button>
                {{-- Submit --}}
                {!! Form::submit(trans('Request'), ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endSection



@section('scripts')
    <script>
        var invoiceTable;
        $(function () {
            var i = 1;
            invoiceTable = $('#invoiceTable').DataTable({
                serverSide: true,
                processing: true,
                sScrollX: "100%",
                iDisplayLength: -1,
                bAutoWidth: false,
                bScrollAutoCss: true,
                sScrollXInner: "100%",
                ajax: `{{ route('invoices.data') }}`,
                columns: [
                    {
                        "render": function() {
                            return i++;
                        }},

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
                    {data: "updated_at"}

                ]
            });

            $('#invoiceCreateForm').ajaxForm({
                dataType: 'json',
                success: function(data) {
                    auto_notify(data);
                    if (typeof data.success != 'undefined') {
                        $('#invoiceCreate').modal('hide');
                       invoiceTable.ajax.reload();
                    }
                },
                error: function(data) { error_notify(data.responseJSON); }
            });
            $('#invoiceTable_length').append(`<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#invoiceCreate" style="margin: 0px 20px;">@lang('invoice.invoice.request')</button>`);

        });
    </script>
@endsection