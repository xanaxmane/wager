<div class="container-fluid">
    <div class="row page-title align-items-center">
        <div class="col-sm-4 col-xl-6">
            <h4 class="mb-1 mt-0">Stats</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">New users</span>
                            <h2 class="mb-0">{{ \App\User::where('created_at', '>=', \Carbon\Carbon::today())->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">Games</span>
                            <h2 class="mb-0">{{ \Illuminate\Support\Facades\DB::table('games')->where('created_at', '>=', \Carbon\Carbon::today())->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="media p-3">
                        <div class="media-body">
                            <span class="text-muted text-uppercase font-size-12 font-weight-bold">Average response time</span>
                            <h2 class="mb-0">{{ number_format(\Illuminate\Support\Facades\Cache::get('average_response_time', 'N/A'), 4, '.', '') }}s</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard">
        <div class="spinner-border d-flex ml-auto mr-auto"></div>
    </div>
    <div class="dashboard_analytics">
        <div class="spinner-border d-flex ml-auto mr-auto mt-3"></div>
    </div>
    <div class="dashboard_games">
        <div class="spinner-border d-flex ml-auto mr-auto mt-3"></div>
    </div>
</div>
