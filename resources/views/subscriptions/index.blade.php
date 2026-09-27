@php
    if (!function_exists('toBangla')) {
        function toBangla($number) {
            $bn = ["০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯"];
            $en = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
            return str_replace($en, $bn, $number);
        }
    }

    $planNames = [
        'basic' => 'স্টার্টআপ',
        'pro' => 'প্রফেশনাল',
        'enterprise' => 'এন্টারপ্রাইজ',
    ];

    $currentPlanName = $planNames[$tenant->plan] ?? 'ফ্রি ট্রায়াল';
@endphp

<x-tabler-layout :title="'সাবস্ক্রিপশন প্ল্যান'">
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="card-title mb-1">আপনার বর্তমান প্ল্যান: {{ $currentPlanName }}</h2>
                            <div class="text-secondary">
                                @if($tenant->subscription_ends_at)
                                    মেয়াদ শেষ হবে: {{ toBangla($tenant->subscription_ends_at->format('d/m/Y')) }}
                                @elseif($tenant->trial_ends_at)
                                    ট্রায়াল শেষ হবে: {{ toBangla($tenant->trial_ends_at->format('d/m/Y')) }}
                                @else
                                    বর্তমানে প্ল্যান সক্রিয় আছে।
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-green-lt">Active Subscription</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="text-center mb-3">
                <h2 class="h1 mb-2">আপনার হলের উপযোগী প্ল্যান বেছে নিন</h2>
                <div class="text-secondary">কোনো লুকানো চার্জ নেই। আপনার প্রয়োজন অনুযায়ী যেকোনো সময় প্ল্যান পরিবর্তন করতে পারেন।</div>
            </div>
        </div>

        @foreach($plans as $id => $plan)
            <div class="col-sm-6 col-lg-4">
                <div class="card card-md h-100 {{ $tenant->plan == $id && $tenant->is_active ? 'border-green' : '' }}">
                    @if($id == 'pro')
                        <div class="ribbon ribbon-top ribbon-bookmark bg-green">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-3" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path>
                            </svg>
                        </div>
                    @endif

                    <div class="card-body text-center d-flex flex-column">
                        <div class="text-uppercase text-secondary fw-medium mb-2">Monthly Billing</div>
                        <div class="h2 mb-1">{{ $plan['name'] }}</div>
                        <div class="display-5 fw-bold mb-3">৳{{ toBangla(number_format($plan['price'])) }}</div>
                        <div class="text-secondary mb-4">প্রতি মাস</div>

                        <ul class="list-unstyled lh-lg text-start mb-4">
                            @foreach($plan['features'] as $feature)
                                <li class="mb-2 d-flex gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1 text-success icon-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12l5 5l10 -10"></path>
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-auto">
                            @if($tenant->plan == $id && $tenant->is_active)
                                <button type="button" class="btn btn-green w-100" data-bs-toggle="modal" data-bs-target="#modal-active-plan-{{ $id }}">
                                    অ্যাক্টিভ প্ল্যান
                                </button>
                            @else
                                <button type="button" class="btn {{ $id == 'pro' ? 'btn-green' : 'btn-primary' }} w-100" data-bs-toggle="modal" data-bs-target="#modal-checkout-{{ $id }}">
                                    সাবস্ক্রাইব করুন
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal modal-blur fade" id="modal-checkout-{{ $id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('subscriptions.checkout') }}" method="POST" x-data="{ selectedGatewayId: '', gateways: {{ $gateways->toJson() }} }">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $id }}">
                            <input type="hidden" name="amount" value="{{ $plan['price'] }}">

                            <div class="modal-header">
                                <h5 class="modal-title">চেকআউট: {{ $plan['name'] }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                        <div>
                                            <strong>প্ল্যান:</strong> {{ $plan['name'] }}
                                        </div>
                                        <div>
                                            <strong>মূল্য:</strong> ৳ {{ toBangla(number_format($plan['price'])) }} / মাস
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">পেমেন্ট মেথড বেছে নিন</label>
                                    <div class="row g-3">
                                        <template x-for="gateway in gateways" :key="gateway.id">
                                            <div class="col-md-4">
                                                <label class="form-selectgroup-item h-100">
                                                    <input type="radio" name="payment_method" :value="gateway.name" x-model="selectedGatewayId" class="form-selectgroup-input" required>
                                                    <span class="form-selectgroup-label d-flex flex-column align-items-center justify-content-center text-center p-3 h-100">
                                                        <span class="avatar avatar-md mb-2 bg-blue-lt">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <rect x="3" y="5" width="18" height="14" rx="3"></rect>
                                                                <path d="M7 15h.01"></path>
                                                                <path d="M11 15h2"></path>
                                                            </svg>
                                                        </span>
                                                        <span class="fw-bold" x-text="gateway.name"></span>
                                                        <span class="text-secondary" x-text="gateway.account_type"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <template x-if="selectedGatewayId">
                                    <div class="alert alert-warning">
                                        <template x-for="g in gateways" :key="g.id">
                                            <div x-show="g.name === selectedGatewayId">
                                                <div><strong x-text="g.name + ' (' + g.account_type + ')'"></strong></div>
                                                <div class="h3 mb-1" x-text="g.account_number"></div>
                                                <div class="text-secondary" x-text="g.instructions"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">সেন্ডার নাম্বার</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="7" y="4" width="10" height="16" rx="1"></rect>
                                                    <path d="M11 5h2"></path>
                                                    <path d="M12 17v.01"></path>
                                                </svg>
                                            </span>
                                            <input type="text" name="sender_number" class="form-control" placeholder="০১XXXXXXXXX" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">TrxID</label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 7v-1a2 2 0 0 1 2 -2h1"></path>
                                                    <path d="M17 4h1a2 2 0 0 1 2 2v1"></path>
                                                    <path d="M20 17v1a2 2 0 0 1 -2 2h-1"></path>
                                                    <path d="M7 20h-1a2 2 0 0 1 -2 -2v-1"></path>
                                                    <path d="M8 7h8v8h-8z"></path>
                                                </svg>
                                            </span>
                                            <input type="text" name="transaction_id" class="form-control text-uppercase" placeholder="ABC123XYZ" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">বাতিল</button>
                                <button type="submit" class="btn btn-primary">পেমেন্ট নিশ্চিত করুন</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if($tenant->plan == $id && $tenant->is_active)
                <div class="modal modal-blur fade" id="modal-active-plan-{{ $id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $plan['name'] }} প্ল্যান</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="text-secondary">বর্তমান স্ট্যাটাস</div>
                                                <div class="h3 mb-0">অ্যাক্টিভ</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="text-secondary">মেয়াদ</div>
                                                <div class="h3 mb-0">
                                                    @if($tenant->subscription_ends_at)
                                                        {{ toBangla($tenant->subscription_ends_at->format('d/m/Y')) }}
                                                    @elseif($tenant->trial_ends_at)
                                                        {{ toBangla($tenant->trial_ends_at->format('d/m/Y')) }}
                                                    @else
                                                        চলমান
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">এই প্ল্যানে যা আছে</h3>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled space-y-2 mb-0">
                                            @foreach($plan['features'] as $feature)
                                                <li class="mb-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1 text-success" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M5 12l5 5l10 -10"></path>
                                                    </svg>
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4 mb-0">
                                    প্ল্যান পরিবর্তন করতে চাইলে অন্য যেকোনো প্ল্যানের “সাবস্ক্রাইব করুন” বাটনে ক্লিক করুন।
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ঠিক আছে</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</x-tabler-layout>
