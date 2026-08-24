@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<style>
    /* ── Luxury Dark Glass System ── */
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .crud-title h2 {
        font-size: 26px;
        font-weight: 800;
        color: #FFFFFF !important;
        margin-bottom: 4px;
        letter-spacing: -0.3px;
    }
    .crud-title p {
        font-size: 14px;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        margin: 0;
    }
    .btn-gold, .btn-add-primary {
        background: #2563EB !important;
        color: #FFFFFF !important;
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #3B82F6 !important;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    }
    .btn-gold:hover, .btn-add-primary:hover {
        background: #1D4ED8 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50);
    }
    .card-box {
        background: rgba(20, 27, 41, 0.60) !important;
        backdrop-filter: blur(20px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 24px !important;
        padding: 24px !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    }
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .search-form {
        display: flex;
        gap: 10px;
        flex: 1;
        max-width: 520px;
    }
    .search-input {
        flex: 1;
        padding: 10px 16px;
        background: rgba(16, 22, 34, 0.65) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 10px !important;
        font-size: 13.5px;
        color: #FFFFFF !important;
        outline: none;
        transition: all 0.2s ease;
    }
    .search-input:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    }
    .btn-search {
        background: #2563EB !important;
        color: #FFFFFF !important;
        padding: 10px 20px;
        border-radius: 10px;
        border: 1px solid #3B82F6 !important;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    }
    .btn-search:hover {
        background: #1D4ED8 !important;
        transform: translateY(-2px);
    }
    .btn-reset {
        padding: 10px 16px;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        background: rgba(255, 255, 255, 0.06) !important;
        color: #CBD5E1 !important;
        border-radius: 10px !important;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s ease;
    }
    .btn-reset:hover {
        background: rgba(255, 255, 255, 0.12) !important;
        color: #FFFFFF !important;
    }
    .table-container {
        width: 100%;
        overflow-x: auto;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.10);
        background: rgba(16, 22, 34, 0.70);
    }
    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13.5px;
    }
    .premium-table th {
        padding: 14px 16px !important;
        background: rgba(255, 255, 255, 0.05) !important;
        color: #94A3B8 !important;
        font-weight: 800;
        border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap !important;
    }
    .premium-table td {
        padding: 14px 16px !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        color: #FFFFFF !important;
        font-weight: 600;
        vertical-align: middle;
        white-space: nowrap !important;
    }
    .premium-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.04) !important;
    }

    .badge-status-btn {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        outline: none;
    }
    .badge-status-btn .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        font-size: 11.5px;
        font-weight: 800;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        transition: all 0.2s ease;
    }
    .badge-status-btn .badge:hover {
        transform: scale(1.05);
    }
    .badge-active {
        background: rgba(16, 185, 129, 0.18) !important;
        color: #34D399 !important;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }
    .badge-inactive {
        background: rgba(239, 68, 68, 0.18) !important;
        color: #F87171 !important;
        border: 1px solid rgba(239, 68, 68, 0.35);
    }

    .table-action-buttons {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 8px !important;
        flex-wrap: nowrap !important;
        white-space: nowrap !important;
        justify-content: flex-start !important;
    }
    .table-action-buttons form {
        display: inline-flex !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.16) !important;
        border: 1px solid rgba(16, 185, 129, 0.38) !important;
        color: #34D399 !important;
        padding: 14px 18px !important;
        border-radius: 12px !important;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 700 !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-danger {
        background: rgba(239, 68, 68, 0.16) !important;
        border: 1px solid rgba(239, 68, 68, 0.38) !important;
        color: #F87171 !important;
        padding: 14px 18px !important;
        border-radius: 12px !important;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 700 !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    /* ── Modal ── */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .modal-backdrop.active { display: flex; }
    .modal-box {
        background: rgba(16, 22, 34, 0.95) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 24px !important;
        padding: 32px !important;
        width: 100%;
        max-width: 760px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.50) !important;
        position: relative;
        animation: modalIn 0.22s cubic-bezier(0.4,0,0.2,1) both;
    }
    @keyframes modalIn {
        from { opacity:0; transform: scale(0.94) translateY(10px); }
        to   { opacity:1; transform: scale(1) translateY(0); }
    }
    .modal-close {
        position: absolute;
        top: 20px; right: 22px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        width: 32px; height: 32px;
        border-radius: 8px;
        font-size: 16px;
        color: #CBD5E1;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.18s;
    }
    .modal-close:hover { background: #EF4444; color: #FFFFFF; border-color: #EF4444; }
    .modal-title {
        font-size: 20px;
        font-weight: 800;
        color: #FFFFFF !important;
        margin-bottom: 6px;
    }
    .modal-subtitle {
        font-size: 13.5px;
        color: #94A3B8 !important;
        margin-bottom: 24px;
        font-weight: 600;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    @media(max-width:576px) { .form-row { grid-template-columns: 1fr; gap: 0; } }
    .form-group { margin-bottom: 18px; }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #FFFFFF !important;
        margin-bottom: 7px;
    }
    .form-label span { color: #EF4444; }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 10px !important;
        font-size: 13.5px;
        color: #FFFFFF !important;
        outline: none;
        transition: all 0.2s ease;
        background: rgba(20, 27, 41, 0.70) !important;
        box-sizing: border-box;
    }
    select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
    .form-control:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    }
    .text-error { color: #F87171; font-size: 12px; margin-top: 5px; font-weight: 600; }
    .modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
    }
    .btn-cancel {
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.06);
        color: #CBD5E1;
        padding: 10px 22px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-cancel:hover { background: rgba(255, 255, 255, 0.12); color: #FFFFFF; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>User Management</h2>
        <p>Create, update, and manage administrators and staff accounts.</p>
    </div>
    <button type="button" class="btn-gold" id="openAddUserModal">
        <i class="fa-solid fa-plus"></i>
        <span>Add User</span>
    </button>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('users.index') }}" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, mobile" class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass" style="margin-right:4px;"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="btn-reset">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Role</th>
                    <th>Firm</th>
                    <th>Status</th>
                    <th style="width: 220px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $key => $user)
                    <tr>
                        <td style="color:#94A3B8; font-weight:700;">{{ method_exists($users, 'firstItem') ? ($users->firstItem() + $key) : ($key + 1) }}</td>
                        <td><strong style="color: #FFFFFF !important; font-size:14px;">{{ $user->name }}</strong></td>
                        <td style="color: #CBD5E1;">{{ $user->email }}</td>
                        <td style="color: #CBD5E1;">{{ $user->mobile_number ?? '-' }}</td>
                        <td>
                            <span style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.30); color: #60A5FA; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                                {{ is_object($user->role) ? ($user->role->role_name ?? $user->role->name) : ucfirst($user->role ?? '-') }}
                            </span>
                        </td>
                        <td style="color: #CBD5E1; font-weight: 600;">{{ $user->firm->firm_name ?? '-' }}</td>
                        <td>
                            <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge-status-btn" title="Click to toggle status">
                                    <span class="badge badge-{{ $user->status }}">
                                        <i class="fa-solid {{ $user->status === 'active' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('users.show', $user->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" align="center" style="padding: 36px; color: #94A3B8; font-weight:600;">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($users, 'links'))
        <div class="pagination-wrapper">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- ── Add User Modal ── --}}
<div class="modal-backdrop {{ $errors->any() && old('_modal') === 'add_user' ? 'active' : '' }}" id="addUserModal">
    <div class="modal-box">
        <button type="button" class="modal-close" id="closeAddUserModal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title">Add New User</div>
        <div class="modal-subtitle">Create a new administrator or staff member account.</div>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <input type="hidden" name="_modal" value="add_user" class="@error('_modal') is-invalid @enderror">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="m_name">Full Name <span>*</span></label>
                    <input type="text" name="name" id="m_name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" required>
                    @error('name') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="m_email">Email Address <span>*</span></label>
                    <input type="email" name="email" id="m_email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address" required>
                    @error('email') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="m_mobile">Mobile Number <span>*</span></label>
                    <input type="text" name="mobile_number" id="m_mobile" value="{{ old('mobile_number') }}" class="form-control @error('mobile_number') is-invalid @enderror" placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" required>
                    @error('mobile_number') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="m_status">Status <span>*</span></label>
                    <select name="status" id="m_status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status','active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="m_role_id">Role <span>*</span></label>
                    <select name="role_id" id="m_role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="m_firm_id">Firm <span>*</span></label>
                    <select name="firm_id" id="m_firm_id" class="form-control @error('firm_id') is-invalid @enderror" required>
                        <option value="">Select Firm</option>
                        @foreach($firms as $firm)
                            <option value="{{ $firm->id }}" {{ old('firm_id', Auth::user()->firm_id) == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
                        @endforeach
                    </select>
                    @error('firm_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="m_password">Password <span>*</span></label>
                    <input type="password" name="password" id="m_password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" required>
                    @error('password') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="m_confirm_password">Confirm Password <span>*</span></label>
                    <input type="password" name="confirm_password" id="m_confirm_password" class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Confirm password" required>
                    @error('confirm_password') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-gold">
                    <i class="fa-solid fa-check"></i> Save User
                </button>
                <button type="button" class="btn-cancel" id="cancelAddUserModal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    const addUserModal  = document.getElementById('addUserModal');
    const openBtn       = document.getElementById('openAddUserModal');
    const closeBtn      = document.getElementById('closeAddUserModal');
    const cancelBtn     = document.getElementById('cancelAddUserModal');

    openBtn.addEventListener('click', () => addUserModal.classList.add('active'));
    closeBtn.addEventListener('click', () => addUserModal.classList.remove('active'));
    cancelBtn.addEventListener('click', () => addUserModal.classList.remove('active'));

    // Close on backdrop click
    addUserModal.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
</script>
@endsection
