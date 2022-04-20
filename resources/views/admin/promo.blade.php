<div class="row page-title">
    <div class="col-md-12">
        <div class="float-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#create">Create</button>
            <button class="btn btn-danger" onclick="$.request('/admin/promocode/remove_inactive').then(function() {redirect(window.location.pathname);});">Cleanup</button>
        </div>
        <h4 class="mb-1 mt-0">Promocodes</h4>
    </div>
</div>
<div class="row">
    @foreach(\App\Promocode::get() as $promocode)
        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-body">
                    @php
                        $color = 'success';
                        $percent = 100;
                        if($promocode->usages == $promocode->times_used || ($promocode->expires->timestamp != \Carbon\Carbon::minValue()->timestamp && $promocode->expires->isPast())) {
                            $percent = 100;
                            $color = 'danger';
                        } else {
                            if($promocode->usages != -1) {
                                $percent = ($promocode->times_used * $promocode->usages) * 100;
                            } else if($promocode->expires->timestamp != \Carbon\Carbon::minValue()->timestamp) {
                                $percent = (\Carbon\Carbon::now()->timestamp / $promocode->expires->timestamp) * 100;
                            }
                        }
                    @endphp

                    <div class="badge badge-{{ $color }} float-right">{{ number_format($promocode->sum, 8, '.', '') }} {{ \App\Currency\Currency::find($promocode->currency)->name() }}</div>
                    <h5><a href="javascript:void(0)" class="text-dark" onclick="clipboard.writeText('{{ $promocode->code }}')" data-toggle="tooltip" data-placement="top" title="Copy">{{ $promocode->code }}</a></h5>
                    <div class="text-muted mb-4">
                        <div>Created: {{ $promocode->created_at->diffForHumans() }}</div>
                        <div>Usages: {{ $promocode->times_used }}@if($promocode->usages >= 0)/{{ $promocode->usages }} @endif</div>
                        @if($promocode->expires->timestamp != \Carbon\Carbon::minValue()->timestamp)
                            <div>Expires: {{ $promocode->expires->diffForHumans() }}</div>
                        @else
                            <div>Never expires</div>
                        @endif
                        @if($promocode->vip ?? false) <div>VIP promocode</div> @endif
                    </div>
                </div>
                <div class="card-body border-top">
                    <div class="row align-items-center">
                        <div class="col-sm-auto">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item pr-2">
                                    <a data-remove="{{ $promocode->_id }}" href="javascript:void(0)" class="text-muted d-inline-block" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove">
                                        <i class="uil uil-trash-alt mr-1"></i> Remove
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col">
                            <div class="progress" style="height: 5px;" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $percent }}%">
                                <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $percent }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="modal fade" id="create" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-3 px-4 border-bottom-0 d-block">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 class="modal-title">Promocode</h5>
            </div>
            <div class="modal-body p-4">
                <form class="needs-validation" name="event-form" id="form-event" novalidate="">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="control-label">Code</label>
                                <input class="form-control" placeholder="Code" type="text" id="code">
                                <small><a href="javascript:void(0)" onclick="$('#code').val('%random%')">Random</a></small>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Max. usages</label>
                                <input class="form-control" placeholder="Max. usages" type="text" id="usages">
                                <small><a href="javascript:void(0)" onclick="$('#usages').val('%infinite%')">Unlimited</a></small>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Expire</label>
                                <input class="form-control flatpickr-input" placeholder="Time" type="text" id="expires" readonly>
                                <small><a href="javascript:void(0)" onclick="$('#expires').val('%unlimited%')">Unlimited</a></small>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Amount</label>
                                <input class="form-control flatpickr-input" placeholder="Amount" value="0.00000000" type="text" id="sum">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Currency</label>
                                <select class="form-control" id="currency">
                                    @foreach(\App\Currency\Currency::all() as $currency)
                                        <option value="{{ $currency->id() }}">{{ $currency->name() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6"></div>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-light mr-1" id="close" data-dismiss="modal">Close</button>
                            <div class="btn btn-success" id="finish">Create</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
