@extends('layouts.master')

@section('content')

    <div class="card">

        <div class="card-header">

            {{ trans('global.show') }} {{ trans('cruds.finger_device.title') }}

        </div>

        <div class="card-body">

            <div class="form-group">

                <div class="form-group">

                    <a class="btn btn-primary" href="{{ route('finger_device.index') }}">

                        {{ trans('global.back_to_list') }}

                    </a>

                </div>

                <table class="table table-bordered table-striped">

                    <tbody>

                    <tr>

                        <th>

                            {{ trans('cruds.finger_device.fields.id') }}

                        </th>

                        <td>

                            {{ $fingerDevice->id }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            {{ trans('cruds.finger_device.fields.name') }}

                        </th>

                        <td>

                            {{ $fingerDevice->alias }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            {{ trans('cruds.finger_device.fields.ip') }}

                        </th>

                        <td>

                            {{ $fingerDevice->ip_address }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            {{ trans('cruds.finger_device.fields.serialNumber') }}

                        </th>

                        <td>

                            {{ $fingerDevice->sn }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            Status

                        </th>

                        <td>

                            @if($fingerDevice->state == 1)

                            <div class="badge badge-success">

                                Active

                            </div>

                            @else

                            <div class="badge badge-danger">

                                Deactivate

                            </div>

                            @endif

                        </td>

                    </tr>

                    </tbody>

                </table>

                

            </div>

        </div>

    </div>

@endsection

