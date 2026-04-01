<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$staff = $_SESSION['staff'] ?? null;
?>

<h2>Welcome <?= htmlspecialchars($user['name']) ?></h2>
<p>Email: <?= htmlspecialchars($user['email']) ?></p>
<img src="<?= htmlspecialchars($user['picture']) ?>" width="100">

<hr>

<?php if ($staff): ?>
    <h3>ข้อมูลบุคลากร</h3>
    <p>รหัสพนักงาน: <?= htmlspecialchars($staff['staffid']) ?></p>
    <p>ชื่อ-นามสกุล: <?= htmlspecialchars($staff['namefully']) ?></p>
    <p>ตำแหน่ง: <?= htmlspecialchars($staff['posnameth']) ?></p>
    <p>หน่วยงาน: <?= htmlspecialchars($staff['departmentname']) ?></p>
    <p>ฝ่าย/กลุ่มงาน: <?= htmlspecialchars($staff['programname']) ?></p>
    <p>เบอร์โทร: <?= htmlspecialchars($staff['staffphone1']) ?></p>
 <hr>

    <h3>Raw JSON</h3>
    <pre>
<?= json_encode($staff, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
    </pre>
<?php else: ?>
    <p>ไม่พบข้อมูลบุคลากร</p>
<?php endif; ?>

<br><br>
<a href="logout.php">Logout</a>