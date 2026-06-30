@extends('layouts.app_corpmail')

@section('title', 'Admin Dashboard - CorpMail')

@section('content')
<style>
    .admin-container {
        display: grid;
        grid-template-columns: 240px 1fr;
        height: 100vh;
        background-color: var(--bg-base);
    }
    
    .admin-sidebar {
        background-color: var(--bg-surface);
        border-right: 1px solid var(--border-color);
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .admin-menu-item {
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-weight: 500;
        font-size: 14px;
        color: var(--text-muted);
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .admin-menu-item:hover, .admin-menu-item.active {
        background-color: var(--primary-light);
        color: var(--primary);
    }

    .admin-content {
        padding: 40px;
        overflow-y: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: var(--bg-surface);
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .admin-table th, .admin-table td {
        padding: 14px 20px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
    }

    .admin-table th {
        font-weight: 600;
        color: var(--text-muted);
        background-color: var(--bg-base);
    }

    .admin-btn {
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
        transition: var(--transition-smooth);
        border: none;
        font-family: var(--font-primary);
    }

    .admin-btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .admin-btn-primary:hover {
        background-color: var(--primary-hover);
    }

    .admin-btn-secondary {
        background-color: var(--border-color);
        color: var(--text-main);
    }

    .admin-btn-danger {
        background-color: var(--danger);
        color: white;
    }

    .badge-admin {
        padding: 2px 8px;
        border-radius: var(--radius-full);
        font-size: 11px;
        font-weight: 600;
        background-color: var(--primary-light);
        color: var(--primary);
    }

    .badge-employee {
        padding: 2px 8px;
        border-radius: var(--radius-full);
        font-size: 11px;
        font-weight: 600;
        background-color: #f1f5f9;
        color: #475569;
    }
</style>

<div class="admin-container">
    
    <!-- Sidebar navigation -->
    <aside class="admin-sidebar">
        <h2 style="font-size: 20px; font-weight: 700; background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Admin Panel</h2>
        
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <div class="admin-menu-item active" id="menu-users" onclick="switchTab('users')">Manage Users</div>
            <div class="admin-menu-item" id="menu-accounts" onclick="switchTab('accounts')">Mail Accounts</div>
            <div class="admin-menu-item" id="menu-conversations" onclick="switchTab('conversations')">Global Conversations</div>
            <a href="{{ route('admin.settings.index') }}" class="admin-menu-item" style="text-decoration: none;">Global Settings</a>
        </div>

        <a href="/" class="admin-btn admin-btn-secondary" style="margin-top: auto; text-align: center; text-decoration: none;">Back to App</a>
    </aside>

    <!-- Main Content Panel -->
    <main class="admin-content">
        
        <!-- SECTION: MANAGE USERS -->
        <section id="section-users">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h1 style="font-size: 24px; font-weight: 700;">User Management</h1>
                <button class="admin-btn admin-btn-primary" onclick="showCreateUserModal()">+ Add User</button>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </section>

        <!-- SECTION: MAIL ACCOUNTS -->
        <section id="section-accounts" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h1 style="font-size: 24px; font-weight: 700;">Mail Accounts Configuration</h1>
                <button class="admin-btn admin-btn-primary" onclick="showCreateAccountModal()">+ Configure Account</button>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User Email</th>
                        <th>IMAP settings</th>
                        <th>SMTP settings</th>
                        <th>Last Synced</th>
                    </tr>
                </thead>
                <tbody id="accounts-table-body">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </section>

        <!-- SECTION: GLOBAL CONVERSATIONS AUDIT -->
        <section id="section-conversations" style="display: none;">
            <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 24px;">Global Chat Audit</h1>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Initiator</th>
                        <th>Recipient</th>
                        <th>Last Message At</th>
                    </tr>
                </thead>
                <tbody id="conversations-table-body">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </section>
    </main>
</div>

<!-- Modal Create User -->
<div id="modal-create-user" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center;">
    <div style="background-color: var(--bg-surface); padding: 32px; border-radius: var(--radius-md); width: 420px;">
        <h3 style="margin-bottom: 20px; font-weight: 600;">Create Employee / Admin</h3>
        <form id="form-create-user" onsubmit="createUser(event)">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Name</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Email</label>
                <input type="email" name="email" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Password</label>
                <input type="password" name="password" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Role</label>
                <select name="role" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);">
                    <option value="employee">Employee</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="hideModal('modal-create-user')">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Configure Mail Account -->
<div id="modal-create-account" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center;">
    <div style="background-color: var(--bg-surface); padding: 32px; border-radius: var(--radius-md); width: 500px; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-bottom: 20px; font-weight: 600;">SMTP/IMAP Configuration</h3>
        <form id="form-create-account" onsubmit="createAccount(event)">
            <!-- Select user -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Target User Email</label>
                <input type="email" name="email" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" placeholder="employee@company.com" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Account Display Name</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" placeholder="e.g. John Doe (Company)" required>
            </div>

            <!-- SMTP Settings -->
            <h4 style="margin: 16px 0 8px 0; font-weight: 600; font-size: 13px; border-bottom: 1px solid var(--border-color); padding-bottom: 4px;">SMTP Outgoing Configuration</h4>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 11px;">SMTP Host</label>
                    <input type="text" name="smtp_host" style="width:100%; padding:8px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
                </div>
                <div>
                    <label style="font-size: 11px;">Port</label>
                    <input type="number" name="smtp_port" value="587" style="width:100%; padding:8px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 11px;">SMTP Username</label>
                    <input type="text" name="smtp_username" style="width:100%; padding:8px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
                </div>
                <div>
                    <label style="font-size: 11px;">SMTP Password</label>
                    <input type="password" name="smtp_password" style="width:100%; padding:8px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
                </div>
            </div>

            <!-- IMAP Settings -->
            <h4 style="margin: 16px 0 8px 0; font-weight: 600; font-size: 13px; border-bottom: 1px solid var(--border-color); padding-bottom: 4px;">IMAP Incoming Configuration</h4>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 11px;">IMAP Host</label>
                    <input type="text" name="imap_host" style="width:100%; padding:8px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
                </div>
                <div>
                    <label style="font-size: 11px;">Port</label>
                    <input type="number" name="imap_port" value="993" style="width:100%; padding:8px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
                <div>
                    <label style="font-size: 11px;">IMAP Username</label>
                    <input type="text" name="imap_username" style="width:100%; padding:8px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
                </div>
                <div>
                    <label style="font-size: 11px;">IMAP Password</label>
                    <input type="password" name="imap_password" style="width:100%; padding:8px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main);" required>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="hideModal('modal-create-account')">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Save Account</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tab switching logic
    function switchTab(tabName) {
        document.querySelectorAll('.admin-menu-item').forEach(el => el.classList.remove('active'));
        document.getElementById('menu-' + tabName).classList.add('active');

        document.getElementById('section-users').style.display = tabName === 'users' ? 'block' : 'none';
        document.getElementById('section-accounts').style.display = tabName === 'accounts' ? 'block' : 'none';
        document.getElementById('section-conversations').style.display = tabName === 'conversations' ? 'block' : 'none';

        if (tabName === 'users') loadUsers();
        if (tabName === 'accounts') loadAccounts();
        if (tabName === 'conversations') loadGlobalConversations();
    }

    // Modal helpers
    function showCreateUserModal() { document.getElementById('modal-create-user').style.display = 'flex'; }
    function showCreateAccountModal() { document.getElementById('modal-create-account').style.display = 'flex'; }
    function hideModal(id) { document.getElementById(id).style.display = 'none'; }

    // Fetch and populate user list
    async function loadUsers() {
        try {
            const res = await axios.get('/api/admin/users');
            const data = res.data;
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = '';

            data.data.forEach(user => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${user.name}</strong></td>
                    <td>${user.email}</td>
                    <td><span class="${user.is_admin ? 'badge-admin' : 'badge-employee'}">${user.role}</span></td>
                    <td>
                        <button class="admin-btn admin-btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (e) { console.error('Error loading users', e); }
    }

    // Fetch and populate accounts
    async function loadAccounts() {
        try {
            // Re-use user list or fetch direct
            const res = await axios.get('/api/mail-accounts');
            const data = res.data;
            const tbody = document.getElementById('accounts-table-body');
            tbody.innerHTML = '';

            data.forEach(account => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${account.email}</strong><br><span style="font-size:11px;color:var(--text-muted)">Owner ID: ${account.user_id}</span></td>
                    <td>${account.imap_host}:${account.imap_port}</td>
                    <td>${account.smtp_host}:${account.smtp_port}</td>
                    <td>${account.last_synced_at ? new Date(account.last_synced_at).toLocaleString() : 'Never synced'}</td>
                `;
                tbody.appendChild(tr);
            });
        } catch (e) { console.error('Error loading accounts', e); }
    }

    // Load global chats
    async function loadGlobalConversations() {
        try {
            const res = await axios.get('/api/admin/conversations');
            const data = res.data;
            const tbody = document.getElementById('conversations-table-body');
            tbody.innerHTML = '';

            data.data.forEach(chat => {
                const u1 = chat.users && chat.users.length > 0 ? chat.users[0] : null;
                const u2 = chat.users && chat.users.length > 1 ? chat.users[1] : null;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${u1 ? u1.name : 'Unknown'} (${u1 ? u1.email : ''})</td>
                    <td>${u2 ? u2.name : 'Unknown'} (${u2 ? u2.email : ''})</td>
                    <td>${chat.last_message_at ? new Date(chat.last_message_at).toLocaleString() : 'No messages'}</td>
                `;
                tbody.appendChild(tr);
            });
        } catch (e) { console.error('Error loading conversations', e); }
    }

    // Actions: Create User
    async function createUser(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const res = await axios.post('/api/admin/users', data);

            if (res.status === 200 || res.status === 201) {
                hideModal('modal-create-user');
                form.reset();
                loadUsers();
            }
        } catch (e) {
            console.error('Failed to create user', e);
            if (e.response && e.response.data && e.response.data.message) {
                alert(e.response.data.message);
            } else {
                alert('Failed to create user.');
            }
        }
    }

    // Actions: Create account
    async function createAccount(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const res = await axios.post('/api/mail-accounts', data);

            if (res.status === 200 || res.status === 201) {
                hideModal('modal-create-account');
                form.reset();
                loadAccounts();
            }
        } catch (e) {
            console.error('Failed to configure account', e);
            if (e.response && e.response.data && e.response.data.message) {
                alert(e.response.data.message);
            } else {
                alert('Failed to configure account.');
            }
        }
    }

    // Actions: Delete User
    async function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;
        try {
            const res = await axios.delete(`/api/admin/users/${id}`);

            if (res.status === 200) {
                loadUsers();
            }
        } catch (e) { console.error('Failed to delete user', e); }
    }

    // Initial load
    window.addEventListener('DOMContentLoaded', () => {
        loadUsers();
    });
</script>
@endsection
