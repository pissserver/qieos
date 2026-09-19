<?php
session_start();
require_once __DIR__ . '/../../script/connection.php';
date_default_timezone_set('Asia/Jakarta');

header('Content-Type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : '';

function chatFail($msg, $code = 400)
{
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

function chatMe()
{
    if (!isset($_SESSION['username'])) {
        chatFail('unauthorized', 401);
    }
    return $_SESSION['username'];
}

// Cari id user saat ini (dipakai berulang, disimpan per request)
$ME = null;
function meId()
{
    global $conn, $ME;
    if ($ME !== null) return $ME;
    $u = chatMe();
    $q = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $q->bind_param('s', $u);
    $q->execute();
    $r = $q->get_result()->fetch_assoc();
    if (!$r) chatFail('unauthorized', 401);
    $ME = (int)$r['id'];
    return $ME;
}

function heartbeat()
{
    global $conn;
    $id = meId();
    $q = $conn->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?");
    $q->bind_param('i', $id);
    $q->execute();
}

switch ($action) {

    case 'contacts': {
        heartbeat();
        $me = meId();

        // Semua user lain
        $users = [];
        $q = $conn->prepare("SELECT id, username, fullname, role, photo, (last_seen > (NOW() - INTERVAL 15 SECOND)) AS online FROM users WHERE id <> ? ORDER BY fullname ASC");
        $q->bind_param('i', $me);
        $q->execute();
        $res = $q->get_result();
        while ($row = $res->fetch_assoc()) {
            $users[(int)$row['id']] = [
                'id'        => (int)$row['id'],
                'username'  => $row['username'],
                'fullname'  => $row['fullname'],
                'role'      => $row['role'],
                'photo'     => $row['photo'],
                'online'    => (bool)$row['online'],
                'unread'    => 0,
                'last'      => null,
            ];
        }

        // Semua pesan yang melibatkan saya, build last message + unread per kontak
        $msgs = [];
        $q = $conn->prepare("SELECT id, sender_id, receiver_id, message, created_at, read_at FROM chat_messages WHERE (sender_id = ? OR receiver_id = ?) AND deleted_at IS NULL ORDER BY id ASC");
        $q->bind_param('ii', $me, $me);
        $q->execute();
        $res = $q->get_result();
        while ($row = $res->fetch_assoc()) {
            $msgs[] = $row;
        }

        foreach ($msgs as $m) {
            $other = ((int)$m['sender_id'] === $me) ? (int)$m['receiver_id'] : (int)$m['sender_id'];
            if (!isset($users[$other])) continue;

            if ((int)$m['receiver_id'] === $me && $m['read_at'] === null) {
                $users[$other]['unread']++;
            }

            // Pesan terakhir = id terbesar untuk pasangan ini
            if (!isset($users[$other]['last']) || (int)$m['id'] > (int)$users[$other]['last']['id']) {
                $users[$other]['last'] = [
                    'id'        => (int)$m['id'],
                    'mine'      => (int)$m['sender_id'] === $me,
                    'message'   => $m['message'],
                    'created_at'=> $m['created_at'],
                    'read_at'   => $m['read_at'],
                ];
            }
        }

        $list = array_values($users);

        // Urutkan: yang paling terakhir ada aktivitas (kirim/terima) di atas
        usort($list, function ($a, $b) {
            $ta = $a['last'] ? strtotime($a['last']['created_at']) : 0;
            $tb = $b['last'] ? strtotime($b['last']['created_at']) : 0;
            if ($ta !== $tb) return $tb - $ta;
            if ($a['unread'] !== $b['unread']) return $b['unread'] - $a['unread'];
            if ($a['online'] !== $b['online']) return $b['online'] - $a['online'];
            return strcmp($a['fullname'], $b['fullname']);
        });

        echo json_encode(['contacts' => $list]);
        break;
    }

    case 'messages': {
        heartbeat();
        $me = meId();
        $with = isset($_GET['with']) ? (int)$_GET['with'] : 0;
        $after = isset($_GET['after']) ? (int)$_GET['after'] : 0;
        if ($with <= 0 || $with === $me) chatFail('invalid contact');

        // Tandai semua pesan masuk sebagai sudah dibaca
        $q = $conn->prepare("UPDATE chat_messages SET read_at = NOW() WHERE sender_id = ? AND receiver_id = ? AND read_at IS NULL");
        $q->bind_param('ii', $with, $me);
        $q->execute();

        if ($after > 0) {
            $q = $conn->prepare("SELECT id, sender_id, message, created_at, read_at FROM chat_messages WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) AND id > ? AND deleted_at IS NULL ORDER BY id ASC");
            $q->bind_param('iiiii', $me, $with, $with, $me, $after);
        } else {
            $q = $conn->prepare("SELECT id, sender_id, message, created_at, read_at FROM chat_messages WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) AND deleted_at IS NULL ORDER BY id DESC LIMIT 50");
            $q->bind_param('iiii', $me, $with, $with, $me);
        }
        $q->execute();
        $res = $q->get_result();

        $messages = [];
        while ($row = $res->fetch_assoc()) {
            $messages[] = [
                'id'         => (int)$row['id'],
                'mine'       => (int)$row['sender_id'] === $me,
                'message'    => $row['message'],
                'created_at' => $row['created_at'],
                'read_at'    => $row['read_at'],
            ];
        }
        if ($after === 0) {
            $messages = array_reverse($messages);
        }

        // Id pesan yang sudah dihapus (soft delete) pada percakapan ini,
        // agar pengguna di sisi lain bisa menghapusnya secara realtime.
        $deleted = [];
        $q2 = $conn->prepare("SELECT id FROM chat_messages WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) AND deleted_at IS NOT NULL ORDER BY id DESC LIMIT 100");
        $q2->bind_param('iiii', $me, $with, $with, $me);
        $q2->execute();
        $res2 = $q2->get_result();
        while ($row2 = $res2->fetch_assoc()) {
            $deleted[] = (int)$row2['id'];
        }

        echo json_encode(['messages' => $messages, 'deleted' => $deleted]);
        break;
    }

    case 'send': {
        heartbeat();
        $me = meId();
        $with = isset($_POST['with']) ? (int)$_POST['with'] : 0;
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        if ($with <= 0 || $with === $me) chatFail('invalid contact');
        if ($message === '') chatFail('message empty');
        if (mb_strlen($message) > 2000) chatFail('message too long');

        $q = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, created_at, read_at) VALUES (?, ?, ?, NOW(), NULL)");
        $q->bind_param('iis', $me, $with, $message);
        $q->execute();

        echo json_encode([
            'sent' => true,
            'with' => $with,
            'message' => [
                'id'         => (int)$conn->insert_id,
                'mine'       => true,
                'message'    => $message,
                'created_at' => date('Y-m-d H:i:s'),
                'read_at'    => null,
            ],
        ]);
        break;
    }

    case 'delete': {
        $me = meId();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) chatFail('invalid message');

        $q = $conn->prepare("UPDATE chat_messages SET deleted_at = NOW() WHERE id = ? AND (sender_id = ? OR receiver_id = ?)");
        $q->bind_param('iii', $id, $me, $me);
        $q->execute();

        echo json_encode(['deleted' => true, 'id' => $id]);
        break;
    }

    case 'clear': {
        $me = meId();
        $with = isset($_POST['with']) ? (int)$_POST['with'] : 0;
        if ($with <= 0 || $with === $me) chatFail('invalid contact');

        $q = $conn->prepare("UPDATE chat_messages SET deleted_at = NOW() WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
        $q->bind_param('iiii', $me, $with, $with, $me);
        $q->execute();

        echo json_encode(['cleared' => true, 'with' => $with]);
        break;
    }

    case 'read': {
        // Ambil status read pesan pada percakapan (tanpa menandai baca)
        $me = meId();
        $with = isset($_GET['with']) ? (int)$_GET['with'] : 0;
        $after = isset($_GET['after']) ? max(0, (int)$_GET['after']) : 0;
        if ($with <= 0 || $with === $me) chatFail('invalid contact');

        $q = $conn->prepare("SELECT id, sender_id, message, created_at, read_at FROM chat_messages WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) AND id >= ? AND deleted_at IS NULL ORDER BY id ASC");
        $q->bind_param('iiiii', $me, $with, $with, $me, $after);
        $q->execute();
        $res = $q->get_result();

        $messages = [];
        while ($row = $res->fetch_assoc()) {
            $messages[] = [
                'id'         => (int)$row['id'],
                'mine'       => (int)$row['sender_id'] === $me,
                'message'    => $row['message'],
                'created_at' => $row['created_at'],
                'read_at'    => $row['read_at'],
            ];
        }

        echo json_encode(['messages' => $messages]);
        break;
    }

    case 'unread': {
        heartbeat();
        $me = meId();
        $q = $conn->prepare("SELECT COUNT(*) AS total FROM chat_messages WHERE receiver_id = ? AND read_at IS NULL AND deleted_at IS NULL");
        $q->bind_param('i', $me);
        $q->execute();
        $total = (int)$q->get_result()->fetch_assoc()['total'];

        echo json_encode(['total' => $total]);
        break;
    }

    default:
        chatFail('unknown action');
}