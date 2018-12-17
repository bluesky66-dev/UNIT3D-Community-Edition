@extends('layout.default')

@section('title')
    <title>@lang('user.invite-tree') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="#" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('user.invite-tree')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="block">
            <h2>
                <a href="{{ route('profile', ['username' => $owner->username, 'id' => $owner->id]) }}">
                    {{ $owner->username }}
                </a>
                @lang('user.invite-tree')
            </h2>
            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <h2>@lang('user.invites-send')</h2>
                    <div class="table-responsive">
                    <table class="table table-condensed table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>@lang('user.sender')</th>
                            <th>@lang('common.email')</th>
                            <th>@lang('user.code')</th>
                            <th>@lang('user.created-on')</th>
                            <th>@lang('user.expires-on')</th>
                            <th>@lang('user.accepted-by')</th>
                            <th>@lang('user.accepted-at')</th>
                            <th>@lang('common.resend')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($invites) == 0)
                            <p>@lang('user.no-logs')</p>
                        @else
                            @foreach ($invites as $invite)
                                <tr>
                                    <td>
                                        <a href="{{ route('profile', ['username' => $invite->sender->username, 'id' => $invite->sender->id]) }}">
                                            <span class="text-bold" style="color: {{ $invite->sender->group->color }}">
                                                <i class="{{ $invite->sender->group->icon }}"></i> {{ $invite->sender->username }}
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        {{ $invite->email }}
                                    </td>
                                    <td>
                                        {{ $invite->code }}
                                    </td>
                                    <td>
                                        {{ $invite->created_at }}
                                    </td>
                                    <td>
                                        {{ $invite->expires_on }}
                                    </td>
                                    <td>
                                        @if ($invite->accepted_by != null && $invite->accepted_by != 1)
                                            <a href="{{ route('profile', ['username' => $invite->receiver->username, 'id' => $invite->receiver->id]) }}">
                                                <span class="text-bold" style="color: {{ $invite->receiver->group->color }}">
                                                    <i class="{{ $invite->receiver->group->icon }}"></i> {{ $invite->receiver->username }}
                                                </span>
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invite->accepted_at != null)
                                            {{ $invite->accepted_at }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('reProcess', ['id' => $invite->id]) }}" method="post">
                                            @csrf
                                            <button type="submit" @if ($invite->accepted_at !== null) class="btn btn-xs btn-danger disabled" @endif class="btn btn-xs btn-success">
                                                <i class="{{ config('other.font-awesome') }} fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                        <div class="text-center">
                            {{ $invites->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
