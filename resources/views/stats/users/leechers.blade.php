@extends('layout.default')

@section('title')
<title>{{ trans('stat.stats') }} - {{ Config::get('other.title') }}</title>
@endsection

@section('breadcrumb')
<li class="active">
  <a href="{{ route('stats') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('stat.stats') }}</span>
  </a>
</li>
<li>
  <a href="{{ route('leechers') }}" itemprop="url" class="l-breadcrumb-item-link">
    <span itemprop="title" class="l-breadcrumb-item-link-title">{{ trans('stat.top-leechers') }}</span>
  </a>
</li>
@endsection

@section('content')
<div class="container">
@include('partials.statsusermenu')

<div class="block">
  <h2>{{ trans('stat.top-leechers') }}</h2>
  <hr>
  <div class="row">
    <div class="col-md-12">
      <p class="text-red"><strong><i class="fa fa-arrow-down"></i> {{ trans('stat.top-leechers') }}</strong></p>
      <table class="table table-condensed table-striped table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ trans('common.user') }}</th>
            <th>{{ trans('torrent.leeching') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($leechers as $key => $l)
          <tr>
            <td>
                {{ ++$key }}
            </td>
            <td @if(auth()->user()->username == $l->user->username) class="mentions" @endif>
              @if($l->user->private_profile == 1)
              <span class="badge-user text-bold"><span class="text-orange"><i class="fa fa-eye-slash" aria-hidden="true"></i>{{ strtoupper(trans('common.hidden')) }}</span>@if(auth()->user()->id == $l->user->id || auth()->user()->group->is_modo)<a href="{{ route('profil', ['username' => $l->user->username, 'id' => $l->user->id]) }}">({{ $l->user->username }})</a></span>
              @endif
              @else
              <span class="badge-user text-bold"><a href="{{ route('profil', ['username' => $l->user->username, 'id' => $l->user->id]) }}">{{ $l->user->username }}</a></span>
              @endif
            </td>
            <td>
              <span class="text-red">{{ $l->user->getLeeching() }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
</div>
@endsection
