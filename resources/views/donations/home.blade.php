@extends('layouts.app')

@section('css')
    <style>
        @media screen and (max-width: 480px) {
            #donationTable {
                margin-left: -30px !important;
            }
        }
    </style>
@endsection

@section('content')

    {{-- Messages --}}
    <table id="donationTable" class="table table-striped table-hover">
        <thead>
        <tr>
            <th>@lang('donations.home.updated_at')</th>
            <th>@lang('donations.home.status')</th>
            <th>@lang('donations.home.billing_system')</th>
            <th>@lang('donations.home.name')</th>
            <th>@lang('donations.home.amount')</th>
            <th>@lang('donations.home.message')</th>
            <th></th>
        </tr>
        </thead>
    </table>

@endsection




{{-- Create Message --}}
<div class="modal fade" id="donationCreate" tabindex="-1" role="dialog" aria-labelledby="donationCreateLabel">
    <div class="modal-dialog" role="document">
        {!! Form::open(['route' => 'donations.create', 'class' => 'modal-content form-horizontal', 'id' => 'donationCreateForm', 'autocomplete' => 'off']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="donationCreateLabel">@lang('donations.home.create.title')</h4>
        </div>
        <div class="modal-body">
            {{-- Donor Name --}}
            <div class="form-group">
                {!! Form::label('name', trans('donations.home.create.name')) !!}
                {!! Form::text('name', '', [ 'class' => 'form-control', 'id' => 'name' ]) !!}
            </div>
            {{-- Donation Message --}}
            <div class="form-group">
                {!! Form::label('message', trans('donations.home.create.message')) !!}
                {!! Form::textarea('message', '', [ 'class' => 'form-control', 'id' => 'message' ]) !!}
            </div>
            {{-- Donation Amount --}}
            <div class="form-group">
                {!! Form::label('amount', trans('donations.home.create.amount')) !!}
                {!! Form::number('amount', '', ['class' => 'form-control', 'id' => 'amount', 'step' => '0.01', 'min' => '0.01']) !!}
            </div>
            {{-- Date --}}
            <div class="form-group">
                {!! Form::label('updated_at', trans('donations.home.create.updated_at')) !!}
                {!! Form::text('updated_at', \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now())->setTimezone(Auth::user()->timezone), [ 'class' => 'form-control', 'id' => 'updated_at' ]) !!}
            </div>
        </div>
        <div class="modal-footer">
            {{-- Cancel --}}
            <button type="button" class="btn btn-default"
                    data-dismiss="modal">@lang('donations.home.create.cancel')</button>
            {{-- Submit --}}
            {!! Form::submit(trans('donations.home.create.save'), ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>
</div>


{{-- Create Message --}}
<div class="modal fade" id="invoiceCreate" tabindex="-1" role="dialog" aria-labelledby="invoiceCreateLabel">
    <div class="modal-dialog" role="document">
        {!! Form::open(['route' => 'donations.create', 'class' => 'modal-content form-horizontal', 'id' => 'invoiceCreateForm', 'autocomplete' => 'off']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="invoiceCreateLabel">Raise Invoice</h4>
        </div>
        <div class="modal-body">
            {{-- Donor Name --}}

            <p><b>You are going to raise invoice as a payment process. We might require 2/3 business days to verify and
                    release the fund for you.</b><br>
            <p>Thank you for choosing us.</p>
            <p><small><i>*Donation are subjected to TDS and taxation.</i></small></p>
        </div>
        <div class="modal-footer">
            {{-- Cancel --}}
            <button type="button" class="btn btn-default"
                    data-dismiss="modal">@lang('donations.home.create.cancel')</button>
            {{-- Submit --}}
            {!! Form::submit(trans('Request'), ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>
</div>



@section('scripts')
    <script>
        var donationTable;
        $(function () {

            donationTable = $('#donationTable').DataTable({
                serverSide: true,
                processing: true,
                sScrollX: "100%",
                iDisplayLength: -1,
                bAutoWidth: false,
                bScrollAutoCss: true,
                sScrollXInner: "100%",
                ajax: `{{ route('donations.data') }}`,
                columns: [
                    {data: "updated_at"},
                    {
                        data: "status",
                        sortable: true,
                        render: function (data) {
                            console.log(data);
                            var statuses = JSON.parse(`{!! json_encode(trans('donations.home.statuses')) !!}`);
                            var color = 'muted';
                            if (data == 'success')
                                color = 'success';
                            else if (data == 'refund')
                                color = 'danger';
                            return `<span class="text-${color}">${statuses[data]}</span>`;
                        }
                    },
                    {data: "billing_system"},
                    {
                        data: "name",
                        sortable: false,
                        render: function (data) {
                            return `<a href="#" onclick="donationTable.search($(this).text().trim()).draw();">${data}</a>`;
                        }

                    },
                    {
                        data: "amount",
                        render: function (data, type, full, meta) {
                            var html = `<span class="text-success">${data} {!! config('app.currency_icon') !!}</span> `;
                            if (full.status != 'user')
                                html += `<sup class="text-muted">-${full.commission} {!! config('app.currency_icon') !!}</sup>`;
                            else
                                html += `<sup class="text-muted"><i class="fa fa-user fa-fw"></i></sup>`;
                            return html;
                        }
                    },
                    {
                        data: "message",
                        render: function (data, type, full, meta) {
                            return htmlspecialchars_decode(data);
                        }
                    },
                    {
                        data: "id",
                        render: function (data, type, full, meta) {
                            setTimeout(function () {
                                $('#message-delete-' + data).ajaxForm({
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
                            return `{!! Form::open(['route' => 'donations.remove', 'id' => 'message-delete-@{{ id }}']) !!}
                                    {!! Form::hidden('id', '@{{ id }}') !!}
                                    {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>', ['type' => 'submit', 'class' => 'btn btn-danger']) !!}
                                    {!! Form::close() !!}`.replaceAll('@{{ id }}', data);
                        }
                    }
                ]
            });

            $('#donationCreateForm').ajaxForm({
                dataType: 'json',
                success: function (data) {
                    auto_notify(data);
                    if (typeof data.success != 'undefined') {
                        $('#donationCreate').modal('hide');
                        donationTable.ajax.reload();
                    }
                },
                error: function (data) {
                    error_notify(data.responseJSON);
                }
            });
            $('#invoiceCreateForm').ajaxForm({
                dataType: 'json',
                success: function (data) {
                    auto_notify(data);
                    if (typeof data.success != 'undefined') {
                        $('#invoiceCreate').modal('hide');
                        donationTable.ajax.reload();
                    }
                },
                error: function (data) {
                    error_notify(data.responseJSON);
                }
            });

            $('#donationTable_length').append(`<div>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#donationCreate" style="margin: 0px 20px;">@lang('donations.home.create.title')</button>
             <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#invoiceCreate" style="margin: 0px 20px;">Raise Invoice</button>
              </div>`);
        });
    </script>
@endsection