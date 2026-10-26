<?php

function report_esc($conn, $value)
{
    return mysqli_real_escape_string($conn, (string) $value);
}

function report_dates($conn)
{
    $first = isset($_GET['first_date']) ? $_GET['first_date'] : '';
    $last  = isset($_GET['last_date']) ? $_GET['last_date'] : '';

    return array(
        report_esc($conn, $first),
        report_esc($conn, $last)
    );
}

function report_rp($amount)
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function report_date_id($date)
{
    return date('d M Y', strtotime($date));
}

function report_status_badge($status)
{
    $status = strtolower((string) $status);

    if ($status === 'paid') {
        return '<span class="status-paid">Terbayar</span>';
    }

    if ($status === 'waiting') {
        return '<span class="status-partial">Waiting</span>';
    }

    if ($status === 'cancelled') {
        return '<span class="status-unpaid">Dibatalkan</span>';
    }

    return '<span class="status-partial">' . htmlspecialchars($status) . '</span>';
}

function report_empty($colspan, $message)
{
    echo '<tr>';
    echo '<td colspan="' . (int) $colspan . '" class="text-center py-4 text-muted">';
    echo '<i class="fas fa-inbox mb-2" style="font-size:24px;"></i>';
    echo '<div>' . htmlspecialchars($message) . '</div>';
    echo '</td>';
    echo '</tr>';
}

function report_foot($amount)
{
    echo '<!--SPLIT_FOOT-->';
    echo report_rp($amount);
}

function report_scalar($conn, $sql)
{
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        return 0;
    }

    $row = mysqli_fetch_assoc($query);
    if (!$row) {
        return 0;
    }

    $value = reset($row);
    return (float) $value;
}
