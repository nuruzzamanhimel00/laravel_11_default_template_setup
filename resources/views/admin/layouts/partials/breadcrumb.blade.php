<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8 col-7">

            <h6 class="page-title">{{ getPageMeta('title') }}</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ getPageMeta('title') }}</li>
            </ol>
        </div>
        <div class="col-md-4 col-5 create_btn">
            @php
            $route = getCreateRoute('route') ?? null;
            @endphp
            @if(!is_null($route))
            @php

            $parts = explode('/', $route);
            $name = rtrim(str_replace('-', ' ', $parts[3]),'s'); // "roles" is at index 3

            $name = $name == 'administration' ? 'System User' : $name;
            @endphp
            <button class="btn btn-primary float-end rounded-3" onclick="window.location.href='{{ $route }}'"  type="button"><span><i class="fa fa-plus"></i> Create {{ucwords($name)}}</span></button>
            @endif
        </div>
    </div>
</div>
