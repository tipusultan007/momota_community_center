<x-tabler-layout :title="'হলের বিস্তারিত: ' . $hall->name">
    <x-slot:header>
        <div class="page-header d-print-none">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">হলের বিস্তারিত: {{ $hall->name }}</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('halls.edit', $hall) }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                            এডিট করুন
                        </a>
                        <a href="{{ route('halls.index') }}" class="btn btn-white">
                            তালিকায় ফিরে যান
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:header>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row row-cards">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">হলের তথ্য</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">হলের নাম</div>
                                    <div class="datagrid-content">{{ $hall->name }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">ধারণক্ষমতা</div>
                                    <div class="datagrid-content">{{ $hall->capacity ?? 'নাই' }} জন</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">বুকিং রেট (প্রতি স্লট)</div>
                                    <div class="datagrid-content">৳ {{ number_format($hall->price_per_slot, 2) }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">অবস্থা</div>
                                    <div class="datagrid-content">
                                        @if($hall->is_active)
                                            <span class="status status-green">সচল</span>
                                        @else
                                            <span class="status status-red">অচল</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">যোগাযোগের তথ্য (ইনভয়েসে ব্যবহৃত)</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">ঠিকানা</div>
                                    <div class="datagrid-content">{{ $hall->address ?? 'ঠিকানা দেওয়া নেই' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">মোবাইল নম্বর</div>
                                    <div class="datagrid-content">{{ $hall->phone ?? 'মোবাইল নম্বর দেওয়া নেই' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($hall->description)
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">বিস্তারিত বিবরণ ও সুযোগ-সুবিধা</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-secondary">{{ $hall->description }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-tabler-layout>
