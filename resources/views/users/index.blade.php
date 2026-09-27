<x-tabler-layout>
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">ইউজার ও ভূমিকা (Users & Roles)</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('users.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                    নতুন ইউজার যোগ করুন
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>নাম</th>
                        <th>ইমেইল</th>
                        <th>ভূমিকা (Role)</th>
                        <th class="w-1">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex py-1 align-items-center">
                                    <span class="avatar me-2" style="background-image: url({{ $user->hasMedia('profile_photo') ? $user->getFirstMediaUrl('profile_photo') : asset('assets/static/avatars/default.png') }})">
                                        @if(!$user->hasMedia('profile_photo'))
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="7" r="4" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                        @endif
                                    </span>
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">{{ $user->name }}</div>
                                        @if($user->phone)
                                        <div class="text-muted"><a href="#" class="text-reset">{{ $user->phone }}</a></div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td class="text-muted">
                                @php
                                    app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->tenant_id);
                                    $role = $user->roles->first()->name ?? 'No Role';
                                @endphp
                                @if($role == 'Tenant Admin')
                                    <span class="badge bg-primary me-1">অ্যাডমিন</span>
                                @elseif($role == 'Manager')
                                    <span class="badge bg-green me-1">ম্যানেজার</span>
                                @else
                                    <span class="badge bg-secondary me-1">স্টাফ</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-white btn-sm">এডিট</a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('আপনি কি নিশ্চিত?')">ডিলেট</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-tabler-layout>
