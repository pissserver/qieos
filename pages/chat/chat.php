<?php
require_once __DIR__ . '/../../sessions/session.php';
$current_page = 'chat.php';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Chat - Qieos</title>
    <?php include '../../script/headscript.php'; ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/pages/chat.css?v=<?php echo filemtime(__DIR__ . '/../../css/pages/chat.css'); ?>">
</head>

<body id="chatPage">
<?php include '../components/sidebar.php'; ?>
<main class="content">
<?php include '../components/navbar.php'; ?>

<div class="chat-page-wrap">
    <div class="chat-shell" id="chatShell">

        <!-- KONTAK -->
        <aside class="chat-panel chat-contacts" id="chatContacts">
            <header class="chat-panel-header">
                <div class="chat-panel-title">
                    <i class="fas fa-comments"></i>
                    <span>Pesan</span>
                </div>
                <div class="chat-panel-sub">Kontak</div>
            </header>
            <div class="chat-contacts-list" id="chatContactsList"></div>
        </aside>

        <!-- PERCAKAPAN -->
        <section class="chat-panel chat-conversation" id="chatConversation">

            <header class="chat-conv-header">
                <button class="chat-back-btn" id="chatBackBtn" aria-label="Kembali ke daftar kontak">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="chat-avatar chat-avatar-mini" id="convAvatar">?</div>
                <div class="chat-conv-user">
                    <div class="chat-conv-name" id="convName">Pilih kontak</div>
                    <div class="chat-conv-status" id="convStatus">&nbsp;</div>
                </div>
                <div class="chat-conv-actions">
                    <button type="button" class="chat-conv-menu-btn" id="chatConvMenuBtn" aria-label="Menu percakapan">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="chat-conv-dropdown" id="chatConvMenu">
                        <button type="button" class="chat-dropdown-item" id="chatClearConversation" data-action="clear">
                            <i class="fas fa-trash-alt"></i>
                            <span>Hapus chat</span>
                        </button>
                    </div>
                </div>
            </header>

            <div class="chat-body">
                <div class="chat-messages" id="chatMessages"></div>

                <div class="chat-empty" id="chatEmpty"></div>
            </div>

            <form class="chat-composer" id="chatComposer">
                <textarea
                    id="chatInput"
                    rows="1"
                    maxlength="2000"
                    placeholder="Tulis pesan..."
                    aria-label="Tulis pesan"></textarea>
                <button type="submit" class="chat-send-btn" id="chatSendBtn" aria-label="Kirim pesan">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

        </section>

    </div>
</div>
</main>

<!-- Modal konfirmasi hapus pesan / chat -->
<div class="chat-modal-backdrop" id="chatConfirmModal" role="dialog" aria-modal="true" aria-labelledby="chatModalTitle">
    <div class="chat-modal">
        <div class="chat-modal-icon"><i class="fas fa-trash-alt"></i></div>
        <h3 class="chat-modal-title" id="chatModalTitle">Hapus pesan?</h3>
        <p class="chat-modal-desc" id="chatModalDesc">Pesan akan dihapus dari percakapan.</p>
        <div class="chat-modal-actions">
            <button type="button" class="chat-modal-btn is-ghost" id="chatModalCancel">Batal</button>
            <button type="button" class="chat-modal-btn is-danger" id="chatModalOk">Hapus</button>
        </div>
    </div>
</div>

<?php include '../../script/footscript.php'; ?>

<script>
(function () {
    'use strict';

    window.__chatActive = true;

    var BASE = BASE_URL + '/pages/chat/chat-api.php';
    var ME = '<?php echo isset($user['fullname']) ? htmlspecialchars($user['fullname'], ENT_QUOTES) : ''; ?>';

    var shell   = document.getElementById('chatShell');
    var listEl  = document.getElementById('chatContactsList');
    var msgEl   = document.getElementById('chatMessages');
    var emptyEl = document.getElementById('chatEmpty');
    var convName   = document.getElementById('convName');
    var convStatus = document.getElementById('convStatus');
    var convAvatar = document.getElementById('convAvatar');
    var inputEl    = document.getElementById('chatInput');
    var textarea   = inputEl;

    var contacts = [];          // array kontak (master)
    var contactMap = {};        // id -> kontak
    var activeId = null;        // id kontak terbuka
    var lastId = 0;             // id pesan terakhir di percakapan aktif
    var lastRenderedDay = null; // hari (YYYY-MM-DD) pesan terakhir yang tampil
    var unreadTotal = 0;
    var initialized = false;
    var lastRenderKey = -1;

    var AVATAR_GRADS = [
        'linear-gradient(135deg,#6366f1,#8b5cf6)',
        'linear-gradient(135deg,#0ea5e9,#6366f1)',
        'linear-gradient(135deg,#f59e0b,#ec4899)',
        'linear-gradient(135deg,#10b981,#0ea5e9)'
    ];

    function initials(name) {
        name = (name || '').trim();
        if (!name) return '?';
        var parts = name.split(/\s+/);
        var ini = parts[0][0];
        if (parts.length > 1) ini += parts[1][0];
        return ini.toUpperCase();
    }

    // Cache & preload foto avatar agar pindah kontak tidak nunggu gambar
    var photoCache = {};

    function preloadPhoto(photo) {
        if (!photo) return null;
        if (!photoCache[photo]) {
            var tmp = new Image();
            tmp.src = BASE_URL + '/assets/img/uploads/' + photo;
            photoCache[photo] = tmp;
        }
        return photoCache[photo];
    }

    // Avatar: foto user jika ada, kalau tidak inisial nama
    function avatarHTML(c, gradIdx, extraClass) {
        var dot = '<span class="online-dot' + (c.online ? '' : ' is-offline') + '"></span>';
        var inner;
        if (c && c.photo) {
            var cached = preloadPhoto(c.photo);
            var img = (cached && cached.complete && cached.naturalWidth > 0) ? cached.cloneNode(true) : new Image();
            img.src = BASE_URL + '/assets/img/uploads/' + c.photo;
            img.className = 'chat-avatar-img';
            img.alt = '';
            img.decoding = 'async';
            if (extraClass && extraClass.indexOf('mini') !== -1) {
                img.loading = 'eager';
                if ('fetchPriority' in img) img.fetchPriority = 'high';
            } else {
                img.loading = 'lazy';
            }
            inner = img.outerHTML;
        } else {
            inner = '<span class="chat-avatar-ini">' + initials(c.fullname) + '</span>';
        }
        return '<div class="chat-avatar ' + (extraClass || '') + '" style="background:' + AVATAR_GRADS[gradIdx % AVATAR_GRADS.length] + '">' + inner + dot + '</div>';
    }

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function fmtClock(createdAt) {
        var t = createdAt.split(' ')[1] || '';
        if (!t) return '';
        var p = t.split(':');
        return p[0] + ':' + p[1];
    }

    function dmy(createdAt) {
        var d = createdAt.split(' ')[0].split('-');
        return d[2] + '/' + d[1] + '/' + d[0];
    }

    function updateBadge() {
        var total = 0;
        contacts.forEach(function (c) { total += c.unread; });
        unreadTotal = total;
        var els = document.querySelectorAll('.chat-unread-badge');
        for (var i = 0; i < els.length; i++) {
            if (total > 0) {
                els[i].textContent = total > 9 ? '9+' : total;
                els[i].classList.remove('d-none');
            } else {
                els[i].classList.add('d-none');
            }
        }
    }

    function renderContacts() {
        if (!listEl) return;
        if (!contacts.length) {
            listEl.innerHTML = '<div class="chat-no-contacts"><i class="fas fa-users"></i><span>Belum ada pengguna lain</span></div>';
            return;
        }
        var html = '';
        contacts.forEach(function (c, i) {
            var last = c.last;
            var preview = '';
            var time = '';
            if (last) {
                var txt = String(last.message).replace(/\s+/g, ' ');
                if (txt.length > 32) txt = txt.slice(0, 32) + '...';
                preview = (last.mine ? 'Kamu: ' : '') + esc(txt);
                var today = new Date().toISOString().slice(0, 10);
                var d = last.created_at.split(' ')[0];
                time = (d === today) ? fmtClock(last.created_at) : dmy(last.created_at);
            } else {
                preview = 'Belum ada pesan';
            }
            html +=
                '<div class="chat-contact' + (c.id === activeId ? ' active' : '') + '" role="button" tabindex="0" data-id="' + c.id + '">' +
                    avatarHTML(c, i) +
                    '<div class="chat-contact-main">' +
                        '<div class="chat-contact-top">' +
                            '<span class="chat-contact-name">' + esc(c.fullname) + '</span>' +
                            '<span class="chat-contact-time">' + time + '</span>' +
                        '</div>' +
                        '<div class="chat-contact-bottom">' +
                            '<span class="chat-contact-preview">' + preview + '</span>' +
                            (c.unread > 0 ? '<span class="chat-unread-pill">' + c.unread + '</span>' : '') +
                        '</div>' +
                    '</div>' +
                '</div>';
        });
        listEl.innerHTML = html;
    }

    function setConvHeader() {
        var c = activeId ? contactMap[activeId] : null;
        if (!c) {
            convName.textContent = 'Pilih kontak';
            convStatus.innerHTML = '&nbsp;';
            convAvatar.textContent = '?';
            shell.classList.remove('chat-open');
            return;
        }
        convName.textContent = c.fullname;
        var newAvatar = document.createElement('div');
        newAvatar.innerHTML = avatarHTML(c, (c.id * 7), 'chat-avatar-mini').trim();
        var avaEl = newAvatar.firstChild;
        convAvatar.replaceWith(avaEl);
        convAvatar = avaEl;
        var online = !!(c.online);
        convStatus.innerHTML =
            '<span class="conv-status-dot' + (online ? '' : ' is-offline') + '"></span>' +
            (online ? 'Online' : 'Offline');
        shell.classList.add('chat-open');
    }

    function scrollToBottom(force) {
        var area = msgEl;
        if (!area) return;
        var apply = function () { area.scrollTop = area.scrollHeight; };
        if (force) { apply(); window.requestAnimationFrame(apply); return; }
        var nearBottom = area.scrollHeight - area.scrollTop - area.clientHeight < 90;
        if (nearBottom) { apply(); window.requestAnimationFrame(apply); }
    }

    function bubbleHTML(m) {
        var ticks = '';
        if (m.mine) {
            ticks = m.read_at
                ? '<span class="msg-ticks is-read"><i class="fas fa-check"></i><i class="fas fa-check ticks-2"></i></span>'
                : '<span class="msg-ticks"><i class="fas fa-check"></i></span>';
        }
        var ops =
            '<button type="button" class="bubble-ops-btn" aria-label="Opsi pesan"><i class="fas fa-ellipsis-v"></i></button>' +
            '<div class="bubble-popover">' +
                '<button type="button" class="bubble-menu-item delete-msg" data-id="' + m.id + '">' +
                    '<i class="fas fa-trash-alt"></i><span>Hapus pesan</span>' +
                '</button>' +
            '</div>';
        return '<div class="chat-msg ' + (m.mine ? 'mine' : 'theirs') + '" data-id="' + m.id + '">' +
                    ops +
                    '<div class="bubble">' +
                        '<div class="bubble-text">' + esc(m.message) + '</div>' +
                        '<div class="meta">' +
                            '<span class="msg-time">' + fmtClock(m.created_at) + '</span>' +
                            ticks +
                        '</div>' +
                    '</div>' +
                '</div>';
    }

    // status read pesan saya: id -> {lastId, read_at}
    var lastSeenRead = {};

    // cache percakapan agar pindah kontak instan (tanpa menunggu server)
    var convCache = {};

    function sortContacts() {
        contacts.sort(function (a, b) {
            var ta = a.last ? new Date(a.last.created_at.replace(' ', 'T')).getTime() : 0;
            var tb = b.last ? new Date(b.last.created_at.replace(' ', 'T')).getTime() : 0;
            if (ta !== tb) return tb - ta;
            if (a.unread !== b.unread) return b.unread - a.unread;
            if (a.online !== b.online) return b.online - a.online;
            return (a.fullname < b.fullname) ? -1 : (a.fullname > b.fullname ? 1 : 0);
        });
    }

    // Update kontak aktif langsung (urutannya naik ke atas) tanpa nunggu poll
    function touchMessage(msg, mine) {
        var c = contactMap[activeId];
        if (!c || !msg) return;
        c.last = {
            id: msg.id,
            mine: !!mine,
            message: msg.message,
            created_at: msg.created_at,
            read_at: msg.read_at || null
        };
        sortContacts();
        renderContacts();
        updateBadge();
    }

    function renderMessages(list) {
        if (!msgEl) return;
        var lastDay = null;
        var html = '';
        lastSeenRead = {};
        list.forEach(function (m) {
            var day = m.created_at.split(' ')[0];
            if (day && day !== lastDay) {
                html += '<div class="chat-day"><span>' + day.split('-').reverse().join('/') + '</span></div>';
                lastDay = day;
            }
            html += bubbleHTML(m);
            if (m.mine) lastSeenRead[m.id] = { read_at: !!m.read_at };
        });
        msgEl.innerHTML = html;
        lastRenderedDay = list.length ? list[list.length - 1].created_at.split(' ')[0] : null;
        scrollToBottom(true);
        refreshReadState();
        if (activeId) {
            convCache[activeId] = { lastId: lastId, list: list.slice() };
        }
    }

    function appendMessages(list) {
        if (!msgEl) return;
        if (!list.length) return;

        var needBeep = false;
        var lastDay = lastRenderedDay;

        var html = '';
        list.forEach(function (m) {
            if (m.id > lastId) lastId = m.id;
            if (!m.mine) needBeep = true;

            var day = m.created_at.split(' ')[0];
            if (day && day !== lastDay) {
                html += '<div class="chat-day"><span>' + day.split('-').reverse().join('/') + '</span></div>';
                lastDay = day;
            }
            html += bubbleHTML(m);
            if (m.mine) lastSeenRead[m.id] = { read_at: !!m.read_at };
        });
        msgEl.insertAdjacentHTML('beforeend', html);
        lastRenderedDay = lastDay;
        scrollToBottom(true);

        // Simpan ke cache
        if (activeId && convCache[activeId]) {
            convCache[activeId].list = convCache[activeId].list.concat(list);
            convCache[activeId].lastId = lastId;
        }

        // Urutan kontak naik instan (kirim/terima)
        var lastNew = list[list.length - 1];
        if (lastNew) touchMessage(lastNew, !!lastNew.mine);

        if (needBeep && document.visibilityState !== 'hidden') beep(2);
    }

    function refreshReadState() {
        // Update centang "dibaca" pada pesan saya bila read_at sudah terisi
        var mine = msgEl.querySelectorAll('.chat-msg.mine');
        for (var i = 0; i < mine.length; i++) {
            var id = parseInt(mine[i].getAttribute('data-id'), 10);
            var seen = lastSeenRead[id];
            if (!seen || !seen.read_at) continue;
            var ticks = mine[i].querySelector('.msg-ticks');
            if (ticks && !ticks.classList.contains('is-read')) {
                ticks.classList.add('is-read');
                ticks.insertAdjacentHTML('beforeend', '<i class="fas fa-check ticks-2"></i>');
            }
        }
    }

    function markMyRead(list) {
        list.forEach(function (m) {
            if (!m.mine || !m.read_at) return;
            if (lastSeenRead[m.id]) lastSeenRead[m.id].read_at = true;
        });
        refreshReadState();
    }

    function fetchContacts() {
        return fetch(BASE + '?action=contacts&_=' + Date.now())
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var prev = contactMap;
                contacts = data.contacts || [];
                contactMap = {};
                contacts.forEach(function (c) {
                    contactMap[c.id] = c;
                    if (c.photo) preloadPhoto(c.photo);
                });

                // Beep bila ada unread baru (kontak lain)
                var newTotal = 0;
                contacts.forEach(function (c) { newTotal += c.unread; });
                if (initialized && newTotal > unreadTotal && document.visibilityState !== 'hidden') beep(2);
                unreadTotal = newTotal;

                // Jika kontak aktif, sinkronkan status online
                var act = activeId ? contactMap[activeId] : null;
                if (act) {
                    var st = document.querySelector('.conv-status-dot');
                    if (st) {
                        st.className = 'conv-status-dot' + (act.online ? '' : ' is-offline');
                        convStatus.childNodes[1].textContent = act.online ? 'Online' : 'Offline';
                    }
                }

                renderContacts();
                updateBadge();
                setConvHeader();
                initialized = true;
            })
            .catch(function () {});
    }

    function fetchMessages() {
        if (!activeId) return Promise.resolve();
        return fetch(BASE + '?action=messages&with=' + activeId + '&after=' + lastId + '&_=' + Date.now())
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var list = data.messages || [];
                var deleted = data.deleted || [];
                if (deleted.length) removeDeletedMessages(deleted);
                if (lastId === 0) {
                    list.forEach(function (m) { if (m.id > lastId) lastId = m.id; });
                    renderMessages(list);
                } else {
                    appendMessages(list);
                    refreshReadFromServer();
                }
            })
            .catch(function () {});
    }

    // Hapus bubble yang dihapus pengguna lain (realtime lewat polling)
    function removeDeletedMessages(ids) {
        if (!activeId || !ids || !ids.length) return;
        var changed = false;
        ids.forEach(function (id) {
            var node = msgEl.querySelector('.chat-msg[data-id="' + id + '"]');
            if (node) { node.remove(); changed = true; }
            var cached = convCache[activeId];
            if (cached && cached.list.some(function (m) { return m.id === id; })) {
                cached.list = cached.list.filter(function (m) { return m.id !== id; });
                changed = true;
            }
        });
        if (!changed) return;

        if (msgEl.querySelector('.chat-msg')) {
            var maxId = 0;
            var ms = msgEl.querySelectorAll('.chat-msg');
            for (var i = 0; i < ms.length; i++) {
                var iid = parseInt(ms[i].getAttribute('data-id'), 10);
                if (iid > maxId) maxId = iid;
            }
            if (lastId > maxId) lastId = maxId;
        } else {
            lastId = 0;
            lastRenderedDay = null;
            msgEl.innerHTML = '';
        }
        if (convCache[activeId]) convCache[activeId].lastId = lastId;
        fetchContacts();
    }

    function refreshReadFromServer() {
        // Ambil status read pesan saya terbaru (batasi query kecil)
        fetch(BASE + '?action=read&with=' + activeId + '&after=' + (lastId - 50) + '&_=' + Date.now())
            .then(function (r) { return r.json(); })
            .then(function (data) {
                markMyRead(data.messages || []);
            })
            .catch(function () {});
    }

    function openChat(id) {
        id = parseInt(id, 10);
        if (isNaN(id)) return;
        activeId = id;
        lastSeenRead = {};
        closeConvMenu();
        closeBubbleMenus();

        // Restore dari cache → pesan langsung tampil tanpa nunggu server
        var cached = convCache[id];
        if (cached) {
            lastId = cached.lastId || 0;
            renderMessages(cached.list || []);
        } else {
            lastId = 0;
            msgEl.innerHTML = '<div class="chat-loading"><span class="chat-loading-spin"></span><span>Memuat pesan...</span></div>';
        }

        var c = contactMap[id];
        convName.textContent = c ? c.fullname : '...';

        // Perbarui URL tanpa reload (deep link)
        try { history.replaceState(null, '', BASE_URL + '/pages/chat/chat.php?with=' + id); } catch (e) {}

        // Sinkron dengan server (juga menandai pesan masuk sebagai dibaca)
        fetchMessages().then(function () {
            // Jangan auto-focus di mobile agar keyboard tidak langsung muncul.
            // Fokus hanya di desktop; pengguna mobile klik sendiri kotak tulis.
            if (window.matchMedia('(min-width: 992px)').matches) inputEl.focus();
        });
    }

    function closeConv() {
        activeId = null;
        lastId = 0;
        lastRenderedDay = null;
        shell.classList.remove('chat-open');
        msgEl.innerHTML = '';
        setConvHeader();
        try { history.replaceState(null, '', BASE_URL + '/pages/chat/chat.php'); } catch (e) {}
        renderContacts();
    }

    function sendMessage() {
        var text = textarea.value.trim();
        if (!text || !activeId) return;
        var btn = document.getElementById('chatSendBtn');
        textarea.value = '';

        fetch(BASE + '?action=send&_=' + Date.now(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'with=' + activeId + '&message=' + encodeURIComponent(text)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.sent) {
                if (data.message.id > lastId) lastId = data.message.id;
                appendMessages([data.message]);
                markMyRead([data.message]);
            }
        })
        .catch(function () {});
    }

    // ===== DELETE / CLEAR =====
    function closeBubbleMenus() {
        var open = msgEl.querySelectorAll('.chat-msg.pop-open');
        for (var i = 0; i < open.length; i++) open[i].classList.remove('pop-open');
    }

    function closeConvMenu() {
        var d = document.getElementById('chatConvMenu');
        if (d) d.classList.remove('open');
    }

    function removeMessageFromState(id) {
        var node = msgEl.querySelector('.chat-msg[data-id="' + id + '"]');
        if (node) node.remove();
        var cached = activeId ? convCache[activeId] : null;
        if (cached) {
            cached.list = cached.list.filter(function (m) { return m.id !== id; });
            cached.lastId = cached.list.length ? cached.list[cached.list.length - 1].id : 0;
        }
        if (lastId === id) lastId = cached ? cached.lastId : 0;
        if (msgEl.querySelector('.chat-msg')) {
            lastRenderedDay = (cached && cached.list.length) ? cached.list[cached.list.length - 1].created_at.split(' ')[0] : null;
        } else {
            lastRenderedDay = null;
            msgEl.innerHTML = '';
        }
        fetchContacts();
    }

    function deleteMessage(id) {
        if (!activeId) return;
        chatConfirm({
            title: 'Hapus pesan?',
            desc: 'Pesan ini akan dihapus dari percakapan kamu dan lawan bicara.',
            okText: 'Hapus'
        }).then(function (ok) {
            if (!ok) return;
            fetch(BASE + '?action=delete&_=' + Date.now(), {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.deleted) return;
                closeBubbleMenus();
                removeMessageFromState(id);
            })
            .catch(function () {});
        });
    }

    function clearConversation() {
        if (!activeId) return;
        var who = contactMap[activeId] ? contactMap[activeId].fullname : 'kontak ini';
        chatConfirm({
            title: 'Hapus chat?',
            desc: 'Seluruh riwayat chat dengan ' + who + ' akan dihapus di kedua pihak.',
            okText: 'Hapus chat'
        }).then(function (ok) {
            if (!ok) return;
            fetch(BASE + '?action=clear&_=' + Date.now(), {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'with=' + activeId
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.cleared) return;
                closeConvMenu();
                lastId = 0;
                lastRenderedDay = null;
                msgEl.innerHTML = '';
                if (convCache[activeId]) convCache[activeId] = { lastId: 0, list: [] };
                fetchContacts();
            })
            .catch(function () {});
        });
    }

    // ===== MODAL KONFIRMASI CANTIK =====
    var chatModalEl = document.getElementById('chatConfirmModal');
    var chatModalTitle = document.getElementById('chatModalTitle');
    var chatModalDesc = document.getElementById('chatModalDesc');
    var chatModalOkBtn = document.getElementById('chatModalOk');
    var chatModalCancelBtn = document.getElementById('chatModalCancel');
    var chatModalCallback = null;

    function chatConfirm(opts) {
        return new Promise(function (resolve) {
            chatModalTitle.textContent = opts.title || 'Yakin menghapus?';
            chatModalDesc.textContent = opts.desc || '';
            chatModalOkBtn.textContent = opts.okText || 'Hapus';
            chatModalCallback = resolve;
            chatModalEl.classList.add('show');
        });
    }

    function chatConfirmClose(result) {
        var cb = chatModalCallback;
        chatModalCallback = null;
        chatModalEl.classList.remove('show');
        if (cb) cb(result);
    }

    chatModalOkBtn.addEventListener('click', function () { chatConfirmClose(true); });
    chatModalCancelBtn.addEventListener('click', function () { chatConfirmClose(false); });
    chatModalEl.addEventListener('click', function (e) {
        if (e.target === chatModalEl) chatConfirmClose(false);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && chatModalCallback) chatConfirmClose(false);
    });

    // ===== AUDIO (WebAudio chime) =====
    var audioCtx = null;
    function ensureAudio() {
        if (!audioCtx) {
            try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch (e) { return; }
        }
        if (audioCtx.state === 'suspended') audioCtx.resume();
    }
    function beep(n) {
        ensureAudio();
        if (!audioCtx) return;
        var t = audioCtx.currentTime;
        var seq = (n > 1) ? [659.25, 880] : [523.25, 659.25];
        for (var j = 0; j < seq.length; j++) {
            var o = audioCtx.createOscillator();
            var g = audioCtx.createGain();
            var start = t + j * 0.09;
            o.type = 'sine';
            o.frequency.value = seq[j];
            g.gain.setValueAtTime(0.0001, start);
            g.gain.exponentialRampToValueAtTime(0.16, start + 0.02);
            g.gain.exponentialRampToValueAtTime(0.0001, start + 0.4);
            o.connect(g); g.connect(audioCtx.destination);
            o.start(start); o.stop(start + 0.45);
        }
    }
    document.addEventListener('pointerdown', ensureAudio, { passive: true });

    // ===== EVENTS =====
    listEl.addEventListener('click', function (e) {
        var el = e.target.closest('.chat-contact');
        if (el) openChat(parseInt(el.getAttribute('data-id'), 10));
    });
    listEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            var el = e.target.closest('.chat-contact');
            if (el) { e.preventDefault(); openChat(parseInt(el.getAttribute('data-id'), 10)); }
        }
    });
    document.getElementById('chatBackBtn').addEventListener('click', closeConv);
    document.getElementById('chatComposer').addEventListener('submit', function (e) {
        e.preventDefault();
        sendMessage();
    });
    textarea.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    msgEl.addEventListener('click', function (e) {
        var ops = e.target.closest('.bubble-ops-btn');
        if (ops) {
            e.stopPropagation();
            closeBubbleMenus();
            closeConvMenu();
            var wrap = ops.closest('.chat-msg');
            if (wrap) {
                wrap.classList.add('pop-open');
                var wr = wrap.getBoundingClientRect();
                var ar = msgEl.getBoundingClientRect();
                wrap.classList.toggle('pop-up', (ar.bottom - wr.bottom) < 170);
            }
            return;
        }
        var del = e.target.closest('.bubble-menu-item.delete-msg');
        if (del) {
            e.stopPropagation();
            deleteMessage(parseInt(del.getAttribute('data-id'), 10));
            return;
        }
        closeBubbleMenus();
    });
    document.getElementById('chatConvMenuBtn').addEventListener('click', function (e) {
        e.stopPropagation();
        closeBubbleMenus();
        closeConvMenu();
        document.getElementById('chatConvMenu').classList.toggle('open');
    });
    document.getElementById('chatClearConversation').addEventListener('click', function (e) {
        e.stopPropagation();
        clearConversation();
    });
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.chat-conv-actions')) closeConvMenu();
        if (!e.target.closest('.bubble-popover') && !e.target.closest('.bubble-ops-btn')) closeBubbleMenus();
    });

    // ===== START =====
    fetchContacts();

    // Buka percakapan dari URL ?with=
    var openId = parseInt(new URLSearchParams(window.location.search).get('with') || '0', 10);
    if (openId > 0) {
        // Tunggu kontak termuat agar header tidak kosong
        var wait = setInterval(function () {
            if (contactMap[openId]) {
                clearInterval(wait);
                openChat(openId);
            }
        }, 50);
        setTimeout(function () { clearInterval(wait); }, 5000);
    }

    setInterval(fetchContacts, 2000);
    setInterval(fetchMessages, 1200);

    // Sinkronkan segera saat kembali ke tab ini (tab aktif)
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            fetchContacts();
            fetchMessages();
        }
    });
})();
</script>

</body>
</html>