````markdown
# 🔐 Google OAuth (PHP) + MSU Staff API

ตัวอย่างโปรเจกต์สำหรับ Login ด้วย Google OAuth 2.0 และดึงข้อมูลบุคลากรจาก MSU API มาแสดงบน Dashboard

---

## 📦 Features

- Login ด้วย Google
- ดึงข้อมูล Profile (ชื่อ / Email / รูป)
- เรียก MSU API ด้วย access token
- แสดงข้อมูลบุคลากร
- แสดง Raw JSON
- ระบบ Session + Logout

---

## 🧰 Requirements

- PHP 7.4+
- Composer
- Web Server (Apache / Nginx)

---

## 📥 Installation

```bash
git clone https://github.com/your-username/google-oauth-msu.git
cd google-oauth-msu
composer install
````

---

## ⚙️ Configuration

### config.php

```php
<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost:3000/callback.php');

$client->addScope("email");
$client->addScope("profile");
```

---

## 🔑 Google Credentials

1. ไปที่ [https://console.cloud.google.com/](https://console.cloud.google.com/)
2. สร้าง Project
3. ไปที่ APIs & Services > Credentials
4. Create Credentials → OAuth Client ID
5. เลือก Web Application
6. เพิ่ม Redirect URI:

```text
http://localhost:3000/callback.php
```

---

## 📁 Project Structure

```text
project/
│── config.php
│── index.php
│── callback.php
│── dashboard.php
│── logout.php
│── vendor/
```

---

## 🧩 Source Code

### index.php

```php
<?php
require 'config.php';

$login_url = $client->createAuthUrl();
?>

<h2>Login</h2>
<a href="<?= $login_url ?>">Login with Google</a>
```

---

### callback.php

```php
<?php
require 'config.php';
session_start();

if (isset($_GET['code'])) {

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $accessToken = $token['access_token'];

    $oauth = new Google_Service_Oauth2($client);
    $user_info = $oauth->userinfo->get();

    // Call MSU API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://erp.msu.ac.th/service/api/staffinfo");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $accessToken
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $staffData = json_decode($response, true);

    // Save user
    $_SESSION['user'] = [
        'id' => $user_info->id,
        'name' => $user_info->name,
        'email' => $user_info->email,
        'picture' => $user_info->picture
    ];

    // Save staff
    if ($staffData['status'] === true) {
        $_SESSION['staff'] = $staffData['data'];
        $_SESSION['staff_raw'] = $staffData;
    }

    header('Location: dashboard.php');
    exit;
}
```

---

### dashboard.php

```php
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
    <p>รหัส: <?= htmlspecialchars($staff['staffid']) ?></p>
    <p>ชื่อ: <?= htmlspecialchars($staff['namefully']) ?></p>
    <p>ตำแหน่ง: <?= htmlspecialchars($staff['posnameth']) ?></p>
    <p>หน่วยงาน: <?= htmlspecialchars($staff['departmentname']) ?></p>

    <hr>

    <h3>Raw JSON</h3>
    <pre>
<?= json_encode($_SESSION['staff_raw'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
    </pre>

<?php else: ?>
    <p>ไม่พบข้อมูล</p>
<?php endif; ?>

<br><br>
<a href="logout.php">Logout</a>
```

---

### logout.php

```php
<?php
session_start();
session_destroy();

header('Location: index.php');
exit;
```

---

## 🔄 OAuth Flow

```text
Login → Google → callback.php → MSU API → Session → Dashboard
```

---

## 📊 Example API Response

```json
{
  "status": true,
  "message": "Success",
  "data": {
    "staffid": "5006679",
    "namefully": "อัครรินทร์ บุปผา",
    "posnameth": "นักวิชาการคอมพิวเตอร์"
  }
}
```

---

## ⚠️ Notes

* ต้องใช้ HTTPS ใน production
* Redirect URI ต้องตรงกับ Google Console
* ห้ามเปิดเผย Client Secret
* ตรวจสอบว่า API รองรับ Google Token

---

## 🛠 Troubleshooting

```text
redirect_uri_mismatch → ตรวจสอบ Redirect URI
invalid_client → ตรวจสอบ Client ID / Secret
API ไม่ตอบ → ตรวจสอบ Bearer Token
session หาย → ตรวจสอบ session_start()
```

---

## 🚀 Future Improvements

* เพิ่ม UI (Bootstrap / Tailwind)
* ระบบสิทธิ์ (Role / Permission)
* เชื่อม Database
* แปลงเป็น Laravel

---

## 📄 License

MIT License

```
```
