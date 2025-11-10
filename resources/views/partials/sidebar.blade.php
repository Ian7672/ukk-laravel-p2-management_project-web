@php
    $user = auth()->user();
    $role = $user->role;
@endphp

@if($role === 'admin')
    @include('admin.sidebar')
@elseif($role === 'team_lead')
    @include('teamlead.sidebar')
@elseif($role === 'developer')
    @include('developer.sidebar')
@elseif($role === 'designer')
    @include('designer.sidebar')
@endif
