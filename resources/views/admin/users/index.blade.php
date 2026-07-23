@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }
    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
    }
    .btn-gold:hover {
        background-color: #B58D1B;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(212, 175, 55, 0.3);
    }
    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
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
        max-width: 500px;
    }
    .search-input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
    }
    .search-input:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-glow);
    }
    .btn-search {
        background-color: var(--text-primary);
        color: #FFFFFF;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }
    .btn-search:hover { background-color: #1E293B; }
    .btn-reset {
        padding: 10px 14px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        transition: var(--transition);
    }
    .btn-reset:hover { color: var(--text-primary); }
    .table-container { width: 100%; overflow-x: auto; }
    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .premium-table th {
        padding: 14px 16px;
        background: #F9FAFB;
        color: var(--text-secondary);
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .premium-table td {
        padding: 16px;
        border-bottom: 1px solid #F1F5F9;
        color: var(--text-primary);
        vertical-align: middle;
    }
    .premium-table tr:last-child td { border-bottom: none; }
    .premium-table tbody tr:hover { background-color: #F9FAFB; }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: uppercase;
    }
    .badge-active { background: rgba(34,197,94,0.1); color: #16803D; }
    .badge-inactive { background: rgba(239,68,68,0.1); color: #B91C1C; }
    .action-links {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .action-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13px;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .action-link:hover { color: var(--text-primary); }
    .action-link.view:hover { color: #0EA5E9; }
    .action-link.edit:hover { color: #2563EB; }
    .action-link.toggle-active { color: var(--text-secondary); background: none; border: none; cursor: pointer; font-family: var(--font-primary); font-size: 13px; display: inline-flex; align-items: center; gap: 4px; padding: 0; }
    .action-link.toggle-active:hover { color: #16803D; }
    .action-link.toggle-inactive { color: var(--text-secondary); background: none; border: none; cursor: pointer; font-family: var(--font-primary); font-size: 13px; display: inline-flex; align-items: center; gap: 4px; padding: 0; }
    .action-link.toggle-inactive:hover { color: #B91C1C; }
    .action-link.delete-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-secondary);
        font-family: var(--font-primary);
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0;
    }
    .action-link.delete-btn:hover { color: #EF4444; }
    .alert-success {
        background: rgba(34,197,94,0.08);
        border: 1px solid rgba(34,197,94,0.2);
        color: #16803D;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-danger {
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.2);
        color: #B91C1C;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13.5px;
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
        background: rgba(0,0,0,0.55);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
    }
    .modal-backdrop.active { display: flex; }
    .modal-box {
        background: var(--card-bg);
        border-radius: 14px;
        padding: 32px;
        width: 100%;
        max-width: 760px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 8px 40px rgba(0,0,0,0.22);
        position: relative;
        animation: modalIn 0.22s cubic-bezier(0.4,0,0.2,1) both;
    }
    @keyframes modalIn {
        from { opacity:0; transform: scale(0.94) translateY(10px); }
        to   { opacity:1; transform: scale(1) translateY(0); }
    }
    .modal-close {
        position: absolute;
        top: 16px; right: 18px;
        background: none;
        border: none;
        font-size: 20px;
        color: var(--text-secondary);
        cursor: pointer;
        transition: color 0.18s;
    }
    .modal-close:hover { color: #EF4444; }
    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .modal-subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 24px;
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
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 7px;
    }
    .form-label span { color: #EF4444; }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
        background-color: #FFFFFF;
    }
    .form-control:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-glow);
    }
    .text-error { color: #EF4444; font-size: 12px; margin-top: 5px; font-weight: 500; }
    .form-hint { font-size: 11.5px; color: var(--text-secondary); margin-top: 4px; }
    .modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
    .btn-cancel {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 10px 22px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }
    .btn-cancel:hover { background: #F9FAFB; color: var(--text-primary); border-color: #D1D5DB; }
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
            <button type="submit" class="btn-search">Search</button>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
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
                        <td>{{ $users->firstItem() + $key }}</td>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->mobile_number ?? '-' }}</td>
                        <td>{{ is_object($user->role) ? ($user->role->role_name ?? $user->role->name) : ucfirst($user->role ?? '-') }}</td>
                        <td>{{ $user->firm->firm_name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $user->status }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('users.show', $user->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                {{-- Status Toggle --}}
                                <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->status === 'active')
                                        <button type="submit" class="action-link toggle-inactive" title="Set Inactive">
                                            <i class="fa-solid fa-toggle-on" style="color:#16803D;"></i> Active
                                        </button>
                                    @else
                                        <button type="submit" class="action-link toggle-active" title="Set Active">
                                            <i class="fa-solid fa-toggle-off" style="color:#94A3B8;"></i> Inactive
                                        </button>
                                    @endif
                                </form>
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
                        <td colspan="8" align="center" style="padding: 30px; color: var(--text-secondary);">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $users->appends(request()->query())->links() }}
    </div>
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

