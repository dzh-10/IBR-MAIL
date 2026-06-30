/**
 * CorpMail - Mail & Chat System Frontend Logic
 * Vanilla JavaScript implementation for Laravel 11 + Reverb
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // State Management
    // -------------------------------------------------------------
    const state = {
        currentMode: 'mail', // 'mail' or 'chat'
        currentMailFolder: 'inbox',
        currentMailSearch: '',
        currentMailPage: 1,
        hasMoreMail: true,
        loadingMail: false,

        activeConversationId: null,
        activeConversationUser: null,
        chatMessagesPage: 1,
        hasMoreChatMessages: true,
        loadingChatMessages: false,

        currentUser: {
            id: null,
            name: null
        },
        mailAccounts: [],
        activeEchoChannels: {}
    };

    // -------------------------------------------------------------
    // DOM Element Selectors
    // -------------------------------------------------------------
    const elements = {
        app: document.getElementById('app'),
        toastContainer: document.getElementById('toast-container'),
        
        // Navigation items
        navMailBtn: document.getElementById('nav-mail-btn'),
        navChatBtn: document.getElementById('nav-chat-btn'),
        themeToggleBtn: document.getElementById('theme-toggle-btn'),
        profileTrigger: document.getElementById('profile-dropdown-trigger'),
        profileDropdown: document.getElementById('profile-dropdown-menu'),
        logoutBtn: document.getElementById('logout-btn'),

        // Panels
        sidebarMailSection: document.getElementById('sidebar-mail-section'),
        sidebarChatSection: document.getElementById('sidebar-chat-section'),
        mailViewContainer: document.getElementById('mail-view-container'),
        chatViewContainer: document.getElementById('chat-view-container'),
        emailDetailContainer: document.getElementById('email-detail-container'),

        // Folders list & Sync Buttons
        folderItems: document.querySelectorAll('.folder-nav-item'),
        mailAccountsSyncList: document.getElementById('mail-accounts-sync-list'),

        // Mail listing elements
        mailRowsContainer: document.getElementById('mail-rows-container'),
        mailSearchInput: document.getElementById('mail-search-input'),
        mailPaginationCount: document.getElementById('mail-pagination-count'),

        // Email detail elements
        closeEmailDetailBtn: document.getElementById('close-email-detail-btn'),
        detailSubject: document.getElementById('detail-subject'),
        detailAvatar: document.getElementById('detail-avatar'),
        detailFromName: document.getElementById('detail-from-name'),
        detailFromEmail: document.getElementById('detail-from-email'),
        detailDate: document.getElementById('detail-date'),
        detailBodyContainer: document.getElementById('detail-body-container'),
        detailAttachmentsContainer: document.getElementById('detail-attachments-container'),
        detailAttachmentsList: document.getElementById('detail-attachments-list'),
        detailStarBtn: document.getElementById('detail-star-btn'),
        detailTrashBtn: document.getElementById('detail-trash-btn'),

        // Compose elements
        composeMailBtn: document.getElementById('compose-mail-btn'),
        composeWindow: document.getElementById('compose-window'),
        closeComposeBtn: document.getElementById('close-compose-btn'),
        minimizeComposeBtn: document.getElementById('minimize-compose-btn'),
        composeForm: document.getElementById('compose-form'),
        composeFromSelect: document.getElementById('compose-from-select'),
        composeTo: document.getElementById('compose-to'),
        composeSubject: document.getElementById('compose-subject'),
        composeBodyText: document.getElementById('compose-body-text'),
        composeFileInput: document.getElementById('compose-file-input'),
        composeAttachmentsPreview: document.getElementById('compose-attachments-preview'),
        composeSaveDraftBtn: document.getElementById('compose-save-draft-btn'),

        // Chat listing and messaging elements
        newChatBtn: document.getElementById('new-chat-btn'),
        chatListContainer: document.getElementById('chat-list-container'),
        chatHeaderAvatar: document.getElementById('chat-header-avatar'),
        chatHeaderName: document.getElementById('chat-header-name'),
        chatHeaderStatus: document.getElementById('chat-header-status'),
        chatMessagesStream: document.getElementById('chat-messages-stream'),
        chatMessageForm: document.getElementById('chat-message-form'),
        chatMessageInput: document.getElementById('chat-message-input'),
        chatTypingIndicator: document.getElementById('chat-typing-indicator'),
        chatTypingName: document.getElementById('chat-typing-name'),
        emojiPickerBtn: document.getElementById('emoji-picker-btn'),

        // Modals
        newChatModal: document.getElementById('new-chat-modal'),
        closeChatModalBtn: document.getElementById('close-chat-modal-btn'),
        employeeAutocompleteInput: document.getElementById('employee-autocomplete-input'),
        employeeResultsList: document.getElementById('employee-results-list'),
        configDiv: document.getElementById('corpchat-config')
    };

    // Initialize local details from configuration
    if (elements.configDiv) {
        state.currentUser.id = parseInt(elements.configDiv.getAttribute('data-user-id'));
        state.currentUser.name = elements.configDiv.getAttribute('data-user-name');
    }

    // -------------------------------------------------------------
    // Core App Initialization & Theme Control
    // -------------------------------------------------------------
    const init = async () => {
        // Dark Mode Initialization
        const storedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', storedTheme);

        setupEventListeners();
        await fetchMailAccounts();
        await loadMails();

        // Subscribe to Reverb channels for real-time notifications
        if (state.currentUser.id && window.Echo) {
            window.Echo.private(`user.${state.currentUser.id}`)
                .listen('.App\\Events\\NewEmailReceived', (e) => {
                    showToast(`New email received from <strong>${escapeHtml(e.from_name || e.from_email)}</strong>: "${escapeHtml(e.subject)}"`);
                    // Reload email list if currently viewing Inbox
                    if (state.currentMode === 'mail' && state.currentMailFolder === 'inbox') {
                        state.currentMailPage = 1;
                        loadMails();
                    }
                    updateMailFolderBadges();
                });
        }
    };

    // -------------------------------------------------------------
    // Event Listeners Registration
    // -------------------------------------------------------------
    const setupEventListeners = () => {
        // Mode Switches (Mail vs Chat)
        elements.navMailBtn.addEventListener('click', () => switchMode('mail'));
        elements.navChatBtn.addEventListener('click', () => switchMode('chat'));

        // Theme Toggle
        elements.themeToggleBtn.addEventListener('click', toggleTheme);

        // Profile Dropdown
        elements.profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            elements.profileDropdown.style.display = elements.profileDropdown.style.display === 'none' ? 'block' : 'none';
        });
        document.addEventListener('click', () => {
            elements.profileDropdown.style.display = 'none';
        });
        elements.logoutBtn.addEventListener('click', logout);

        // Mail Folders selector
        elements.folderItems.forEach(item => {
            item.addEventListener('click', (e) => {
                elements.folderItems.forEach(el => el.classList.remove('active'));
                const target = e.currentTarget;
                target.classList.add('active');
                state.currentMailFolder = target.getAttribute('data-folder');
                state.currentMailPage = 1;
                elements.emailDetailContainer.style.display = 'none';
                loadMails();
            });
        });

        // Mail Search
        let searchTimeout;
        elements.mailSearchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                state.currentMailSearch = e.target.value;
                state.currentMailPage = 1;
                loadMails();
            }, 450);
        });

        // Close Email Detail Pane
        elements.closeEmailDetailBtn.addEventListener('click', () => {
            elements.emailDetailContainer.style.display = 'none';
        });

        // Compose Dialog Open/Close/Save
        elements.composeMailBtn.addEventListener('click', () => openComposeWindow());
        elements.closeComposeBtn.addEventListener('click', closeComposeWindow);
        elements.minimizeComposeBtn.addEventListener('click', toggleMinimizeComposeWindow);
        elements.composeForm.addEventListener('submit', sendEmail);
        elements.composeSaveDraftBtn.addEventListener('click', saveEmailDraft);
        elements.composeFileInput.addEventListener('change', updateComposeAttachmentsPreview);

        // Chat Initiation
        elements.newChatBtn.addEventListener('click', showNewChatModal);
        elements.closeChatModalBtn.addEventListener('click', hideNewChatModal);
        
        let autocompleteTimeout;
        elements.employeeAutocompleteInput.addEventListener('input', (e) => {
            clearTimeout(autocompleteTimeout);
            autocompleteTimeout = setTimeout(() => {
                searchEmployees(e.target.value);
            }, 300);
        });

        // Sending Message in Chat
        elements.chatMessageForm.addEventListener('submit', sendChatMessage);

        // typing whisper triggers
        let typingTimeout;
        elements.chatMessageInput.addEventListener('keypress', () => {
            if (state.activeConversationId) {
                // Whisper typing status
                window.Echo.private(`conversation.${state.activeConversationId}`)
                    .whisper('typing', {
                        userId: state.currentUser.id,
                        name: state.currentUser.name,
                        typing: true
                    });

                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    window.Echo.private(`conversation.${state.activeConversationId}`)
                        .whisper('typing', {
                            userId: state.currentUser.id,
                            typing: false
                        });
                }, 1500);
            }
        });

        // Emoji Picker button integration (Simple inline placeholder popup helper)
        elements.emojiPickerBtn.addEventListener('click', toggleEmojiPalette);

        // Scroll to load older chat messages (scroll to top)
        elements.chatMessagesStream.addEventListener('scroll', handleChatScroll);
    };

    // -------------------------------------------------------------
    // View Switch & State Actions
    // -------------------------------------------------------------
    const switchMode = (mode) => {
        state.currentMode = mode;
        elements.navMailBtn.classList.toggle('active', mode === 'mail');
        elements.navChatBtn.classList.toggle('active', mode === 'chat');

        if (mode === 'mail') {
            elements.sidebarMailSection.style.display = 'flex';
            elements.sidebarChatSection.style.display = 'none';
            elements.mailViewContainer.style.display = 'flex';
            elements.chatViewContainer.style.display = 'none';
            loadMails();
        } else {
            elements.sidebarMailSection.style.display = 'none';
            elements.sidebarChatSection.style.display = 'flex';
            elements.mailViewContainer.style.display = 'none';
            elements.chatViewContainer.style.display = 'flex';
            elements.emailDetailContainer.style.display = 'none';
            loadChatConversations();
        }
    };

    const toggleTheme = () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', nextTheme);
        localStorage.setItem('theme', nextTheme);
    };

    // -------------------------------------------------------------
    // Mail API Operations
    // -------------------------------------------------------------
    const fetchMailAccounts = async () => {
        try {
            const res = await axios.get('/api/mail-accounts');
            state.mailAccounts = res.data;
            populateComposeFromAccounts();
            renderAccountsSyncUI();
        } catch (e) {
            console.error('Failed to retrieve mail accounts config', e);
        }
    };

    const renderAccountsSyncUI = () => {
        elements.mailAccountsSyncList.innerHTML = '';
        state.mailAccounts.forEach(acc => {
            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.justifyContent = 'space-between';
            div.style.padding = '8px 12px';
            div.style.borderRadius = '6px';
            div.style.backgroundColor = 'var(--bg-base)';
            div.style.fontSize = '12px';

            div.innerHTML = `
                <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px;" title="${acc.from_email}">${acc.from_email}</span>
                <button class="admin-btn admin-btn-primary" style="padding:4px 8px; font-size:10px; border-radius:4px;" onclick="window.triggerMailSync(${acc.id}, this)">Sync</button>
            `;
            elements.mailAccountsSyncList.appendChild(div);
        });
    };

    // Expose sync globally for inline onclick
    window.triggerMailSync = async (id, btn) => {
        const originalText = btn.innerText;
        btn.innerText = 'Syncing...';
        btn.disabled = true;

        try {
            const res = await axios.post(`/api/mail-accounts/${id}/sync`);
            showToast(res.data.message || 'Sync job dispatched.');
        } catch (e) {
            showToast('Sync trigger failed.');
        } finally {
            setTimeout(() => {
                btn.innerText = originalText;
                btn.disabled = false;
            }, 3000);
        }
    };

    const loadMails = async () => {
        if (state.loadingMail) return;
        state.loadingMail = true;

        try {
            const res = await axios.get(`/api/messages`, {
                params: {
                    folder: state.currentMailFolder,
                    q: state.currentMailSearch,
                    page: state.currentMailPage
                }
            });

            const messages = res.data.data;
            state.hasMoreMail = res.data.next_page_url !== null;

            if (state.currentMailPage === 1) {
                elements.mailRowsContainer.innerHTML = '';
            }

            if (messages.length === 0) {
                elements.mailRowsContainer.innerHTML = `
                    <div style="padding:40px; text-align:center; color:var(--text-muted); font-size:14px;">
                        No messages found in folder ${state.currentMailFolder}.
                    </div>
                `;
                elements.mailPaginationCount.innerText = '0 - 0 of 0';
                state.loadingMail = false;
                return;
            }

            messages.forEach(msg => {
                const row = document.createElement('div');
                row.className = `mail-list-row ${msg.is_read ? '' : 'unread'}`;
                row.setAttribute('data-id', msg.id);

                const date = msg.received_at ? new Date(msg.received_at).toLocaleDateString() : '';
                const starClass = msg.is_starred ? 'starred' : '';

                row.innerHTML = `
                    <div class="mail-list-col" style="width: 40px; display:flex; align-items:center; justify-content:center;">
                        <span class="mail-star ${starClass}" onclick="window.toggleEmailStar(${msg.id}, event)">★</span>
                    </div>
                    <div class="mail-list-col" style="width: 200px; font-weight: 600;">
                        ${escapeHtml(msg.from_name || msg.from_email)}
                    </div>
                    <div class="mail-list-col" style="flex: 1;">
                        <span class="item-cell-subject">${escapeHtml(msg.subject)}</span>
                        <span style="font-weight:400; color:var(--text-muted); font-size:12px;"> - ${escapeHtml((msg.body_text || '').substring(0, 70))}...</span>
                    </div>
                    <div class="mail-list-col" style="width: 100px; text-align: right; color: var(--text-muted); font-size:12px;">
                        ${date}
                    </div>
                `;

                row.addEventListener('click', () => openEmailDetail(msg));
                elements.mailRowsContainer.appendChild(row);
            });

            elements.mailPaginationCount.innerText = `${res.data.from} - ${res.data.to} of ${res.data.total}`;

        } catch (e) {
            console.error('Failed to load external emails', e);
        } finally {
            state.loadingMail = false;
        }
    };

    // Toggle email star status
    window.toggleEmailStar = async (id, event) => {
        event.stopPropagation();
        const starSpan = event.currentTarget;
        const willStar = !starSpan.classList.contains('starred');

        try {
            await axios.patch(`/api/messages/${id}`, {
                is_starred: willStar
            });
            starSpan.classList.toggle('starred', willStar);
            showToast(willStar ? 'Message Starred' : 'Star Removed');
        } catch (e) {
            console.error('Could not star email', e);
        }
    };

    const openEmailDetail = async (msg) => {
        elements.emailDetailContainer.style.display = 'flex';
        elements.detailSubject.innerText = msg.subject;
        elements.detailFromName.innerText = msg.from_name || 'No Name';
        elements.detailFromEmail.innerText = msg.from_email;
        elements.detailDate.innerText = msg.received_at ? new Date(msg.received_at).toLocaleString() : '';
        
        // Sanitize & insert body (use iframe or sanitize html fallback)
        // Fallback sanitize
        elements.detailBodyContainer.innerHTML = cleanHtml(msg.body_html || nl2br(msg.body_text));

        // Display attachments
        if (msg.attachments && msg.attachments.length > 0) {
            elements.detailAttachmentsContainer.style.display = 'block';
            elements.detailAttachmentsList.innerHTML = '';
            msg.attachments.forEach(att => {
                const sizeKB = Math.round(att.size / 1024);
                const item = document.createElement('div');
                item.style.border = '1px solid var(--border-color)';
                item.style.padding = '10px 14px';
                item.style.borderRadius = 'var(--radius-sm)';
                item.style.backgroundColor = 'var(--bg-base)';
                item.style.display = 'flex';
                item.style.flexDirection = 'column';
                item.style.gap = '4px';

                item.innerHTML = `
                    <span style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(att.filename)}">${escapeHtml(att.filename)}</span>
                    <span style="font-size:11px; color:var(--text-muted);">${sizeKB} KB</span>
                    <a href="/storage/${att.path}" target="_blank" style="margin-top:6px; font-size:12px; font-weight:600; color:var(--primary); text-decoration:none;">Download File</a>
                `;
                elements.detailAttachmentsList.appendChild(item);
            });
        } else {
            elements.detailAttachmentsContainer.style.display = 'none';
        }

        // Action buttons
        elements.detailStarBtn.className = `mail-star ${msg.is_starred ? 'starred' : ''}`;
        elements.detailStarBtn.onclick = (e) => window.toggleEmailStar(msg.id, e);
        
        elements.detailTrashBtn.onclick = async () => {
            if (confirm('Move this message to Trash?')) {
                try {
                    await axios.patch(`/api/messages/${msg.id}`, { folder: 'trash' });
                    elements.emailDetailContainer.style.display = 'none';
                    loadMails();
                } catch (e) { console.error(e); }
            }
        };

        // Mark as Read if unread
        if (!msg.is_read) {
            try {
                await axios.patch(`/api/messages/${msg.id}`, { is_read: true });
                // Update local list visual immediately
                const row = document.querySelector(`.mail-list-row[data-id="${msg.id}"]`);
                if (row) row.classList.remove('unread');
                updateMailFolderBadges();
            } catch (e) {
                console.error('Could not mark message as read', e);
            }
        }
    };

    // -------------------------------------------------------------
    // Compose Form Handling
    // -------------------------------------------------------------
    const populateComposeFromAccounts = () => {
        elements.composeFromSelect.innerHTML = '';
        state.mailAccounts.forEach(acc => {
            const opt = document.createElement('option');
            opt.value = acc.id;
            opt.textContent = `${acc.from_name || 'No Name'} <${acc.from_email}>`;
            elements.composeFromSelect.appendChild(opt);
        });
    };

    const openComposeWindow = () => {
        elements.composeWindow.classList.add('active');
        elements.composeWindow.style.transform = 'translateY(0)';
    };

    const closeComposeWindow = () => {
        elements.composeWindow.classList.remove('active');
        elements.composeWindow.style.transform = 'translateY(100%)';
        elements.composeForm.reset();
        elements.composeAttachmentsPreview.innerHTML = '';
    };

    const toggleMinimizeComposeWindow = () => {
        const isMinimized = elements.composeWindow.style.height === '42px';
        elements.composeWindow.style.height = isMinimized ? '480px' : '42px';
    };

    const updateComposeAttachmentsPreview = () => {
        elements.composeAttachmentsPreview.innerHTML = '';
        const files = elements.composeFileInput.files;
        for (let i = 0; i < files.length; i++) {
            const tag = document.createElement('span');
            tag.style.backgroundColor = 'var(--primary-light)';
            tag.style.color = 'var(--primary)';
            tag.style.padding = '4px 10px';
            tag.style.borderRadius = '4px';
            tag.style.fontSize = '12px';
            tag.textContent = `${files[i].name} (${Math.round(files[i].size / 1024)} KB)`;
            elements.composeAttachmentsPreview.appendChild(tag);
        }
    };

    const sendEmail = async (e) => {
        e.preventDefault();
        const formData = new FormData(elements.composeForm);
        
        // Parse comma-separated recipient addresses to array
        const toRaw = elements.composeTo.value;
        const toArray = toRaw.split(',').map(email => email.trim()).filter(e => e.length > 0);
        
        toArray.forEach((addr, idx) => {
            formData.append(`to[${idx}]`, addr);
        });

        try {
            showToast('Sending email...');
            closeComposeWindow();

            const res = await axios.post('/api/messages/send', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            showToast(res.data.message || 'Email sent successfully!');
            loadMails();
        } catch (error) {
            console.error('Failed to send email', error);
            showToast('Failed to send email. Check configuration.', 'error');
        }
    };

    const saveEmailDraft = async () => {
        const formData = new FormData(elements.composeForm);
        formData.append('is_draft', '1');
        
        const toRaw = elements.composeTo.value;
        const toArray = toRaw.split(',').map(email => email.trim()).filter(e => e.length > 0);
        
        toArray.forEach((addr, idx) => {
            formData.append(`to[${idx}]`, addr);
        });

        try {
            closeComposeWindow();
            const res = await axios.post('/api/messages/send', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            showToast(res.data.message || 'Draft saved.');
            loadMails();
        } catch (e) {
            showToast('Could not save draft.', 'error');
        }
    };

    // -------------------------------------------------------------
    // Chat System API & Broadcasting
    // -------------------------------------------------------------
    const loadChatConversations = async () => {
        try {
            const res = await axios.get('/api/conversations');
            const conversations = res.data.data;
            elements.chatListContainer.innerHTML = '';

            if (conversations.length === 0) {
                elements.chatListContainer.innerHTML = `
                    <div style="padding:20px; text-align:center; color:var(--text-muted); font-size:12px;">
                        No active chats. Click "New Chat" to begin.
                    </div>
                `;
                return;
            }

            conversations.forEach(chat => {
                const recipient = chat.recipient_id === state.currentUser.id ? chat.user : chat.recipient;
                if (!recipient) return;

                const activeClass = state.activeConversationId === chat.id ? 'active' : '';
                const time = chat.last_message_at ? new Date(chat.last_message_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
                
                const cell = document.createElement('div');
                cell.className = `item-cell ${activeClass}`;
                cell.innerHTML = `
                    <div class="item-cell-header">
                        <span class="item-cell-name">${escapeHtml(recipient.name)}</span>
                        <span class="item-cell-time">${time}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="item-cell-preview">${escapeHtml(chat.subject || 'Direct message')}</span>
                        ${chat.unread_count > 0 ? `<span class="badge" style="position:static;">${chat.unread_count}</span>` : ''}
                    </div>
                `;

                cell.addEventListener('click', () => selectChatConversation(chat, recipient));
                elements.chatListContainer.appendChild(cell);
            });

        } catch (e) {
            console.error('Failed to load chat conversations', e);
        }
    };

    const selectChatConversation = async (chat, recipient) => {
        state.activeConversationId = chat.id;
        state.activeConversationUser = recipient;
        state.chatMessagesPage = 1;
        state.hasMoreChatMessages = true;

        // Render header
        elements.chatHeaderAvatar.src = recipient.avatar || 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80';
        elements.chatHeaderName.innerText = recipient.name;
        elements.chatHeaderStatus.innerText = recipient.email;

        // Highlight in list
        document.querySelectorAll('.item-cell').forEach(el => el.classList.remove('active'));
        loadChatMessages(true);

        // Setup echo listener for private channel
        subscribeToChatChannel(chat.id);
    };

    const subscribeToChatChannel = (convoId) => {
        // Disconnect previous channel if we switch
        Object.keys(state.activeEchoChannels).forEach(key => {
            if (parseInt(key) !== convoId) {
                window.Echo.leave(`conversation.${key}`);
                delete state.activeEchoChannels[key];
            }
        });

        if (!state.activeEchoChannels[convoId]) {
            state.activeEchoChannels[convoId] = true;

            window.Echo.private(`conversation.${convoId}`)
                .listen('.App\\Events\\MessageSent', (e) => {
                    appendMessageBubble(e, false);
                    scrollToBottom();
                })
                .listenForWhisper('typing', (e) => {
                    if (e.userId !== state.currentUser.id) {
                        if (e.typing) {
                            elements.chatTypingName.innerText = e.name;
                            elements.chatTypingIndicator.style.display = 'flex';
                        } else {
                            elements.chatTypingIndicator.style.display = 'none';
                        }
                    }
                });
        }
    };

    const loadChatMessages = async (shouldScrollBottom = false) => {
        if (state.loadingChatMessages) return;
        state.loadingChatMessages = true;

        try {
            const res = await axios.get(`/api/conversations/${state.activeConversationId}/messages`, {
                params: { page: state.chatMessagesPage }
            });

            const messages = res.data.data;
            state.hasMoreChatMessages = res.data.next_page_url !== null;

            // Page 1: clean first
            if (state.chatMessagesPage === 1) {
                elements.chatMessagesStream.innerHTML = '';
            }

            // Since API returns newest first (paginated), we reverse the single page array to insert in correct chronological order
            messages.reverse().forEach(msg => {
                appendMessageBubble(msg, msg.sender_id === state.currentUser.id, true);
            });

            if (shouldScrollBottom) {
                scrollToBottom();
            }

        } catch (e) {
            console.error('Failed loading chat message history', e);
        } finally {
            state.loadingChatMessages = false;
        }
    };

    const appendMessageBubble = (msg, isSent, prepend = false) => {
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble-wrapper ${isSent ? 'sent' : 'received'}`;
        
        const date = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

        bubble.innerHTML = `
            ${isSent ? '' : `<img src="${msg.sender.avatar || 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80'}" class="avatar" style="width:30px; height:30px;" alt="Avatar">`}
            <div>
                <div class="chat-bubble">
                    ${escapeHtml(msg.body)}
                </div>
                <div class="chat-meta">
                    <span>${date}</span>
                </div>
            </div>
        `;

        if (prepend) {
            elements.chatMessagesStream.insertBefore(bubble, elements.chatMessagesStream.firstChild);
        } else {
            elements.chatMessagesStream.appendChild(bubble);
        }
    };

    const sendChatMessage = async (e) => {
        e.preventDefault();
        const text = elements.chatMessageInput.value.trim();
        if (!text || !state.activeConversationId) return;

        elements.chatMessageInput.value = '';

        try {
            const res = await axios.post(`/api/conversations/${state.activeConversationId}/messages`, {
                body: text
            });

            appendMessageBubble(res.data, true);
            scrollToBottom();
            
            // Trigger loadChatConversations to update last message preview in list
            loadChatConversations();

        } catch (e) {
            console.error('Message delivery failed', e);
        }
    };

    // Scroll handlers
    const scrollToBottom = () => {
        elements.chatMessagesStream.scrollTop = elements.chatMessagesStream.scrollHeight;
    };

    const handleChatScroll = () => {
        if (elements.chatMessagesStream.scrollTop === 0 && state.hasMoreChatMessages && !state.loadingChatMessages) {
            // Save scroll height to adjust after loading
            const scrollHeight = elements.chatMessagesStream.scrollHeight;
            state.chatMessagesPage++;
            
            loadChatMessages(false).then(() => {
                // Adjust scroll so load doesn't jump
                elements.chatMessagesStream.scrollTop = elements.chatMessagesStream.scrollHeight - scrollHeight;
            });
        }
    };

    // -------------------------------------------------------------
    // Autocomplete & New Chat Modal
    // -------------------------------------------------------------
    const showNewChatModal = () => {
        elements.newChatModal.style.display = 'flex';
        elements.employeeAutocompleteInput.focus();
        searchEmployees('');
    };

    const hideNewChatModal = () => {
        elements.newChatModal.style.display = 'none';
        elements.employeeAutocompleteInput.value = '';
    };

    const searchEmployees = async (query) => {
        try {
            const res = await axios.get('/api/employees', { params: { q: query } });
            const list = res.data;
            elements.employeeResultsList.innerHTML = '';

            if (list.length === 0) {
                elements.employeeResultsList.innerHTML = `
                    <div style="padding:10px; font-size:12px; color:var(--text-muted); text-align:center;">No matching employees.</div>
                `;
                return;
            }

            list.forEach(emp => {
                const item = document.createElement('div');
                item.style.padding = '10px';
                item.style.display = 'flex';
                item.style.alignItems = 'center';
                item.style.gap = '10px';
                item.style.cursor = 'pointer';
                item.style.borderRadius = '6px';
                item.style.transition = 'var(--transition-smooth)';

                item.innerHTML = `
                    <img src="${emp.avatar || 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80'}" class="avatar" style="width:30px; height:30px;">
                    <div>
                        <div style="font-weight:600; font-size:13px;">${escapeHtml(emp.name)}</div>
                        <div style="font-size:11px; color:var(--text-muted);">${escapeHtml(emp.email)}</div>
                    </div>
                `;

                item.addEventListener('mouseenter', () => { item.style.backgroundColor = 'var(--primary-light)'; });
                item.addEventListener('mouseleave', () => { item.style.backgroundColor = 'transparent'; });
                
                item.addEventListener('click', () => {
                    startChatWith(emp);
                });

                elements.employeeResultsList.appendChild(item);
            });

        } catch (e) {
            console.error(e);
        }
    };

    const startChatWith = async (employee) => {
        hideNewChatModal();
        try {
            const res = await axios.post('/api/conversations', {
                recipient_id: employee.id,
                subject: 'Direct Message'
            });

            await loadChatConversations();
            selectChatConversation(res.data, employee);
        } catch (e) {
            console.error('Could not initiate conversation', e);
        }
    };

    // -------------------------------------------------------------
    // Utility Helpers
    // -------------------------------------------------------------
    const updateMailFolderBadges = async () => {
        // Count unreads inside directories locally or make a quick query.
        // For simplicity, we trigger dynamic mail badge checks on inbox folder
        try {
            const res = await axios.get('/api/messages', { params: { folder: 'inbox' } });
            const unreadCount = res.data.data.filter(m => !m.is_read).length;
            const badge = document.getElementById('badge-inbox');
            if (badge) {
                badge.innerText = unreadCount > 0 ? unreadCount : '';
                badge.style.display = unreadCount > 0 ? 'inline-block' : 'none';
            }
        } catch (e) {}
    };

    const toggleEmojiPalette = () => {
        // Light emoji list to append
        const emojis = ['😀', '😂', '👍', '🙏', '❤️', '🔥', '🎉', '💡', '🚀', '⭐', '🤝', '⚠️'];
        
        let container = document.getElementById('emoji-mini-popover');
        if (container) {
            container.remove();
            return;
        }

        container = document.createElement('div');
        container.id = 'emoji-mini-popover';
        container.style.position = 'absolute';
        container.style.bottom = '70px';
        container.style.right = '100px';
        container.style.padding = '12px';
        container.style.borderRadius = '8px';
        container.style.backgroundColor = 'var(--bg-surface)';
        container.style.border = '1px solid var(--border-color)';
        container.style.display = 'grid';
        container.style.gridTemplateColumns = 'repeat(4, 1fr)';
        container.style.gap = '8px';
        container.style.zIndex = '50';

        emojis.forEach(emo => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.style.border = 'none';
            btn.style.background = 'none';
            btn.style.fontSize = '18px';
            btn.style.cursor = 'pointer';
            btn.innerText = emo;
            btn.addEventListener('click', () => {
                elements.chatMessageInput.value += emo;
                elements.chatMessageInput.focus();
                container.remove();
            });
            container.appendChild(btn);
        });

        elements.chatViewContainer.appendChild(container);
    };

    const showToast = (content, type = 'success') => {
        const toast = document.createElement('div');
        toast.className = 'toast';
        if (type === 'error') {
            toast.style.borderLeftColor = 'var(--danger)';
        }

        toast.innerHTML = `
            <div style="font-size:13px; color:var(--text-main);">${content}</div>
        `;

        elements.toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s reverse forwards';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    };

    const logout = async () => {
        try {
            await axios.post('/logout');
            window.location.href = '/login';
        } catch (e) {
            window.location.reload();
        }
    };

    // Helper functions: escape HTML to avoid XSS
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Sanitize HTML body fallback
    function cleanHtml(html) {
        if (!html) return '';
        
        // Remove scripts, frames, objects
        let clean = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
                        .replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '')
                        .replace(/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/gi, '')
                        .replace(/onload\s*=\s*"[^"]*"/gi, '')
                        .replace(/onerror\s*=\s*"[^"]*"/gi, '');
        return clean;
    }

    function nl2br(str) {
        if (!str) return '';
        return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
    }

    // Trigger Initial Load
    init();

    // --- Query Parameter Handling for Contacts Integration ---
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('chat') === '1') {
        const userId = urlParams.get('user');
        switchToChatView();
        if (userId) {
            // Wait a little for users to load then select the chat
            setTimeout(() => {
                const startChatModal = document.getElementById('new-chat-modal');
                if (startChatModal) {
                    startChatModal.style.display = 'flex';
                    // Optional: pre-fill the search with user ID or wait for user to click
                }
            }, 500);
        }
    }
    
    if (urlParams.get('compose') === '1') {
        const toEmail = urlParams.get('to');
        if (elements.composeWindow) {
            elements.composeWindow.style.display = 'flex';
            if (toEmail && elements.composeTo) {
                elements.composeTo.value = toEmail;
            }
        }
    }

});
