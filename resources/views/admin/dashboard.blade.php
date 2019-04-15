@extends('layout.admin') 

@section('styles')
<link rel="stylesheet" href="/admin/css/pages/dashboard.css"/>
@endsection

@section('content')
<div class="panel-header panel-header-lg">
    <div class="bigDashboardChart_title_area">
        <h2 class="title_bigDashboardChart">Acessos</h2>
    </div>
    <canvas id="bigDashboardChart"></canvas>
</div>
<div class="content">
    <div class="row">
        <div class="col-lg-4 col-sm-4">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="statistics statistics-horizontal">
                        <div class="info info-horizontal">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon icon-primary icon-circle">
                                        <i class="now-ui-icons business_chart-bar-32"></i>
                                    </div>
                                </div>
                                <div class="col-7 text-right">
                                    <h3 class="info-title">{{ $pageviews or '0' }}</h3>
                                    <h6 class="stats-title">Acessos</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="statistics statistics-horizontal">
                        <div class="info info-horizontal">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon icon-warning icon-circle">
                                        <i class="now-ui-icons ui-2_like"></i>
                                    </div>
                                </div>
                                <div class="col-7 text-right">
                                    <h3 class="info-title">{{ $visits or '0' }}</h3>
                                    <h6 class="stats-title">Visitantes Únicos</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4">
            <div class="card card-stats">
                <div class="card-body ">
                    <div class="statistics statistics-horizontal">
                        <div class="info info-horizontal">
                            <div class="row">
                                <div class="col-5">
                                    <div class="icon icon-danger icon-circle">
                                        <i class="now-ui-icons ui-2_chat-round"></i>
                                    </div>
                                </div>
                                <div class="col-7 text-right">
                                    <h3 class="info-title">{{ $messages or '0' }}</h3>
                                    <h6 class="stats-title">Mensagens</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card card-chart">
                <div class="card-header">
                    <h5 class="card-category">Visitantes</h5>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="activeUsers"></canvas>
                    </div>
                </div>              
            </div>
        </div>
        <div class="col-lg-12 col-md-12">
            <div class="card card-chart">
                <div class="card-header">
                    <h5 class="card-category">Mensagens</h5>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="emailsCampaignChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    dashboard = {};
    dashboard.labels = {!! $siteviews_labels !!};
    dashboard.data = {!! $siteviews_data !!};

    visitors = {};
    visitors.labels = {!! $visitors_labels !!};
    visitors.data = {!! $visitors_data !!};

    messages = {};
    messages.labels = {!! $messages_labels !!};
    messages.data = {!! $messages_data !!};
</script>
<script src="/admin/js/pages/dashboard.js"></script>
@endsection