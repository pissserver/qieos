<?php
include '../sessions/session.php';

header('Content-Type: application/json');

if ($_GET['action'] === 'add') {

    $cashout_date = $_POST['cashout_date'];
    $category = (int) $_POST['category'];
    $expense_name = $_POST['expense_name'];
    $quantity = (int) $_POST['quantity'];
    $unit = $_POST['unit'];
    $price = (int) $_POST['price'];
    $amount = $quantity * $price;

    $query = "INSERT INTO cashouts 
    (cashout_date, category_id, expense_name, quantity, unit, price, amount) 
    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sisissd", 
        $cashout_date, 
        $category, 
        $expense_name, 
        $quantity, 
        $unit, 
        $price, 
        $amount
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Kas keluar berhasil ditambahkan"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menambahkan kas keluar"
        ]);
    }
}

if ($_GET['action'] === 'edit') {

    $id = (int) $_POST['id'];
    $cashout_date = $_POST['cashout_date'];
    $category = (int) $_POST['category'];
    $expense_name = $_POST['expense_name'];
    $quantity = (int) $_POST['quantity'];
    $unit = $_POST['unit'];
    $price = (int) $_POST['price'];
    $amount = $quantity * $price;

    $query = "UPDATE cashouts SET 
        cashout_date=?, 
        category_id=?, 
        expense_name=?, 
        quantity=?, 
        unit=?, 
        price=?, 
        amount=? 
        WHERE id=?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sisisiii", 
        $cashout_date, 
        $category, 
        $expense_name, 
        $quantity, 
        $unit, 
        $price, 
        $amount,
        $id
    );

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Kas keluar berhasil diperbarui",
            "data" => [
                "cashout_date" => $cashout_date,
                "category" => $category,
                "expense_name" => $expense_name,
                "quantity" => $quantity,
                "unit" => $unit,
                "price" => $price,
                "amount" => $amount
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal memperbarui kas keluar"
        ]);
    }
}

if ($_GET['action'] === 'delete') {
    $id = (int) $_POST['id'];

    $query = "DELETE FROM cashouts WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Kas keluar berhasil dihapus"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Gagal menghapus kas keluar"
        ]);
    }
}