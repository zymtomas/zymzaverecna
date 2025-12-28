<?php
session_start();

if (isset($_GET['download']) && ($_SESSION['role'] ?? '') === 'Admin' && file_exists('orders.txt')) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="orders.txt"');
    readfile('orders.txt');
    exit;
}

$loginName = $_POST["Login"] ?? "";
$loginPassword = $_POST["Password"] ?? "";
$LoginRole = $_POST["role"] ?? "Regular";
$userList = "userList.txt";
$users = file($userList, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$isLogged = $_SESSION["logged"] ?? false;

// Zpracování objednávek
$M67 = isset($_POST["item_M67"]);
$M67count = $_POST["itemCount_M67"] ?? 0;
if (!$M67) $M67count = 0;
$F1 = isset($_POST["item_F1"]);
$F1count = $_POST["itemCount_F1"] ?? 0;
if (!$F1) $F1count = 0;
$M18 = isset($_POST["item_M18"]);
$M18count = $_POST["itemCount_M18"] ?? 0;
if (!$M18) $M18count = 0;

$grenadeOrder = "Grenade order  : M67: $M67count  F1: $F1count  M18: $M18count";
$grenadeOrderCart = $grenadeOrder;

$Bandage = isset($_POST["item_Bandage"]);
$BandageCount = $_POST["itemCount_Bandage"] ?? 0;
if (!$Bandage) $BandageCount = 0;
$Medkit = isset($_POST["item_Medkit"]);
$MedkitCount = $_POST["itemCount_Medkit"] ?? 0;
if (!$Medkit) $MedkitCount = 0;
$Splint = isset($_POST["item_Splint"]);
$SplintCount = $_POST["itemCount_Splint"] ?? 0;
if (!$Splint) $SplintCount = 0;
$Ibuprofen = isset($_POST["item_Ibuprofen"]);
$IbuprofenCount = $_POST["itemCount_Ibuprofen"] ?? 0;
if (!$Ibuprofen) $IbuprofenCount = 0;

$medicalOrder  = "Medical order  : Bandage: $BandageCount  Medkit: $MedkitCount  Splint: $SplintCount  Ibuprofen: $IbuprofenCount";

$Bread = isset($_POST["item_Bread"]);
$BreadCount = $_POST["itemCount_Bread"] ?? 0;
if (!$Bread) $BreadCount = 0;
$Tuna = isset($_POST["item_Tuna"]);
$TunaCount = $_POST["itemCount_Tuna"] ?? 0;
if (!$Tuna) $TunaCount = 0;
$Soup = isset($_POST["item_Soup"]);
$SoupCount = $_POST["itemCount_Soup"] ?? 0;
if (!$Soup) $SoupCount = 0;
$Water = isset($_POST["item_Water"]);
$WaterCount = $_POST["itemCount_Water"] ?? 0;
if (!$Water) $WaterCount = 0;
$SparklingWater = isset($_POST["item_SparklingWater"]);
$SparklingWaterCount = $_POST["itemCount_SparklingWater"] ?? 0;
if (!$SparklingWater) $SparklingWaterCount = 0;

$foodOrder     = "Food order     : Bread: $BreadCount  Tuna: $TunaCount  Soup: $SoupCount  Water: $WaterCount  SparklingWater: $SparklingWaterCount";

$Ammo556x45 = isset($_POST["item_556x45"]);
$Ammo556x45Count = $_POST["itemCount_556x45"] ?? 0;
if (!$Ammo556x45) $Ammo556x45Count = 0;
$Ammo556x39 = isset($_POST["item_556x39"]);
$Ammo556x39Count = $_POST["itemCount_556x39"] ?? 0;
if (!$Ammo556x39) $Ammo556x39Count = 0;
$Ammo9mm = isset($_POST["item_9mm"]);
$Ammo9mmCount = $_POST["itemCount_9mm"] ?? 0;
if (!$Ammo9mm) $Ammo9mmCount = 0;
$Ammo8mm = isset($_POST["item_8mm"]);
$Ammo8mmCount = $_POST["itemCount_8mm"] ?? 0;
if (!$Ammo8mm) $Ammo8mmCount = 0;
$Ammo45ACP = isset($_POST["item_45ACP"]);
$Ammo45ACPCount = $_POST["itemCount_45ACP"] ?? 0;
if (!$Ammo45ACP) $Ammo45ACPCount = 0;

$GunLicence = isset($_POST["gunlicence"]);
if (!$GunLicence) $ammoOrder = "Cannot order without weapon licence";

else{$ammoOrder = "Ammo order     : 556x45: $Ammo556x45Count  556x39: $Ammo556x39Count  9mm: $Ammo9mmCount  8mm: $Ammo8mmCount  45ACP: $Ammo45ACPCount";}

$ExtraInfo = $_POST["extraInfo"] ?? "";

if (isset($_POST['itemCount_M67'])) {
    $_SESSION['save_grenade'] = $grenadeOrder;
} elseif (isset($_SESSION['save_grenade'])) {
    $grenadeOrder = $_SESSION['save_grenade'];
}

if (isset($_POST['itemCount_Bandage'])) {
    $_SESSION['save_medical'] = $medicalOrder;
} elseif (isset($_SESSION['save_medical'])) {
    $medicalOrder = $_SESSION['save_medical'];
}

if (isset($_POST['itemCount_Bread'])) {
    $_SESSION['save_food'] = $foodOrder;
} elseif (isset($_SESSION['save_food'])) {
    $foodOrder = $_SESSION['save_food'];
}

if (isset($_POST['itemCount_556x45'])) {
    $_SESSION['save_ammo'] = $ammoOrder;
} elseif (isset($_SESSION['save_ammo'])) {
    $ammoOrder = $_SESSION['save_ammo'];
}

if (isset($_POST['extraInfo'])) {
    $_SESSION['save_extra'] = $ExtraInfo;
} elseif (isset($_SESSION['save_extra'])) {
    $ExtraInfo = $_SESSION['save_extra'];
}



// Přihlášení uživatele
foreach ($users as $line) {
    list($username, $password, $userRole) = explode(":", $line);
    if ($loginName === $username && $loginPassword === $password) {
        $_SESSION["logged"] = true;
        $_SESSION["role"] = $userRole;
        $_SESSION["username"] = $loginName;
        $isLogged = true;
        break;
    }
}

// Registrace uživatele
if (isset($_POST["register"])) {
    $exists = false;
    foreach ($users as $line) {
        list($username, $password, $userRole) = explode(":", $line);
        if ($loginName === $username) { $exists = true; break; }
    }
    if (!$exists && $loginName != "" && $loginPassword != "") {
        file_put_contents($userList, $loginName . ":" . $loginPassword . ":" . $LoginRole . PHP_EOL, FILE_APPEND);
        echo '<div class="Alert">User succesfully registered!</div>';
    } elseif ($exists) { echo '<div class="Alert">User with this username already exists!</div>'; }
    else { echo '<div class="Alert">Enter login and password</div>'; }
}

if (isset($_POST['Send'])) {
    $currentUser = $_SESSION['username'] ?? "Neznámý";
    $cas = date("Y-m-d H:i:s");
    $orderText = "$cas | $currentUser | $grenadeOrder | $medicalOrder | $foodOrder | $ammoOrder | Extra: $ExtraInfo";

    file_put_contents("orders.txt", $orderText . PHP_EOL, FILE_APPEND);

    // Smazani kosiku pri odeslanim kosiku
    unset($_SESSION['save_grenade']);
    unset($_SESSION['save_medical']);
    unset($_SESSION['save_food']);
    unset($_SESSION['save_ammo']);
    unset($_SESSION['save_extra']);

    $grenadeOrder = "Grenade order  : M67: 0  F1: 0  M18: 0";
    $medicalOrder = "Medical order  : Bandage: 0  Medkit: 0  Splint: 0  Ibuprofen: 0";
    $foodOrder     = "Food order     : Bread: 0  Tuna: 0  Soup: 0  Water: 0  SparklingWater: 0";
    $ammoOrder     = "Ammo order     : (empty)";
    $ExtraInfo     = "";
}

// Filtrování
$filteredOrders = [];
if (isset($_POST['filterSubmit']) && ($_SESSION['role'] ?? '') === 'Admin') {
    $filterGrenade = $_POST['filterGrenade'] ?? '';
    $filterMed = $_POST['filterMed'] ?? '';
    $orders = file('orders.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($orders as $order) {
        if (($filterGrenade === '' || str_contains($order, $filterGrenade)) &&
            ($filterMed === '' || str_contains($order, $filterMed))) {
            $filteredOrders[] = $order;
        }
    }
}
?>





<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title></title>
</head>
    <body>
<img src="pda2.png" alt="" style="position: absolute;">
<a href="https://www.moddb.com/mods/stalker-anomaly/addons/interactive-pda-01" target="_blank" style="top:750px; position: absolute; left: 35px">Zdroje</a>
    <div class = "warning">
        <h2>Warning, re-sending order for each category will replace the previous one.</h2>
    </div>

<script>
    let isLogged = <?php echo $isLogged ? 'true' : 'false'; ?>;
    let Choice = 0;
    let cart = [];

</script>

<button onclick="zobrazFormular(0)" style="position:absolute; top:120px; left:38px; z-index:10;">Login</button>
<button onclick="zobrazFormular(1)" style="position:absolute; top:180px; left:38px; z-index:10;">Grenades</button>
<button onclick="zobrazFormular(2)" style="position:absolute; top:240px; left:38px; z-index:10;">Medical</button>
<button onclick="zobrazFormular(3)" style="position:absolute; top:300px; left:38px; z-index:10;">Food</button>
<button onclick="zobrazFormular(4)" style="position:absolute; top:360px; left:38px; z-index:10;">Ammo</button>
<button onclick="zobrazFormular(5)" style="position:absolute; top:300px; left:838px; z-index:10;">Extra</button>
<button onclick="zobrazFormular(6)" style="position:absolute; top:360px; left:838px; z-index:10;">View cart</button>
<button onclick="zobrazFormular(8)" style="position:absolute; top:180px; left:838px; z-index:10;">Browse orders</button>



<div class="SelectFunction">
    <script>
        function zobrazFormular(Choice) {
            document.querySelectorAll(".form").forEach(f => f.style.display = "none");

            switch(Choice) {
                case 0:
                    document.querySelector(".Login").style.display = "block";
                    break;
                case 1:
                    if (isLogged){document.querySelector(".Grenades").style.display = "block";}
                    else{
                        alert("Please login first");
                        }
                    break;
                case 2:
                    if (isLogged){document.querySelector(".Medical").style.display = "block";}
                    else{
                        alert("Please login first");
                        }
                    break;
                case 3:
                    if (isLogged){document.querySelector(".Food").style.display = "block";}
                    else{
                        alert("Please login first");
                        }
                    break;
                case 4:
                    if (isLogged){document.querySelector(".Ammunition").style.display = "block";}
                    else {
                        alert("Please login first");
                        }
                    break;
                case 5:
                    if (isLogged){document.querySelector(".Extra").style.display = "block";}
                    else {
                        alert("Please login first");
                    }
                    break;
                case 6:
                    if (isLogged){document.querySelector(".Cart").style.display = "block";}
                    else {
                        alert("Please login first");
                    }
                    break;
                case 8:
                    if (isLogged ){document.querySelector(".Filter").style.display = "block";}
                    else {
                        alert("Please login first");
                    }
                    break;
                default:
                    break;
            }
        }
    </script>
</div>

<div class="form Login">
        <form method="post" name="Login" enctype="multipart/form-data">

            <label for="Login">Enter Login</label>
            <input type="text" name="Login" id="Login">
            <br>
            <label for="Password">Enter Password</label>
            <input type="password" name="Password" id="Password">
            <br>
            <input type="submit" name="submit" id="submit" value="Log in">
            <input type="submit" name="register" id="register" value="Register">
            <input type="reset" name="reset" id="reset" value="Reset">
            <br>
            <label for="role">Register as: (Select only when registering )</label>
            <br>
            <select name="role" id="role">
                <option value="Regular">Regular</option>
                <option value="Admin">Admin</option>
            </select>
        </form>
</div>

<div class="form Grenades">
    <form method="post" name="Grenades" enctype="multipart/form-data">
        <label>M67</label>
        <input type="checkbox" name="item_M67" value="1">
        <input type="number" name="itemCount_M67" max="10" min="0" placeholder="0">
        <br>

        <label>F1</label>
        <input type="checkbox" name="item_F1" value="1">
        <input type="number" name="itemCount_F1" max="10" min="0" placeholder="0">
        <br>

        <label>M18</label>
        <input type="checkbox" name="item_M18" value="1">
        <input type="number" name="itemCount_M18" max="10" min="0" placeholder="0">
        <br>

        <input type="submit" name="submit" id="submit" value="Buy">
        <input type="reset" name="reset" id="reset" value="Reset">
    </form>
</div>

<div class="form Medical">
    <form method="post" name="Medical" enctype="multipart/form-data">
        <label>Bandage</label>
        <input type="checkbox" name="item_Bandage" value="1">
        <input type="number" name="itemCount_Bandage" max="10" min="0" placeholder="0"><br>

        <label>Medkit</label>
        <input type="checkbox" name="item_Medkit" value="1">
        <input type="number" name="itemCount_Medkit" max="10" min="0" placeholder="0"><br>

        <label>Splint</label>
        <input type="checkbox" name="item_Splint" value="1">
        <input type="number" name="itemCount_Splint" max="10" min="0" placeholder="0"><br>

        <label>Ibuprofen</label>
        <input type="checkbox" name="item_Ibuprofen" value="1">
        <input type="number" name="itemCount_Ibuprofen" max="10" min="0" placeholder="0"><br>


        <input type="submit" name="submit" id="submit" value="Buy">
        <input type="reset" name="reset" id="reset" value="Reset">
    </form>
</div>

<div class="form Food">
    <form method="post" name="Food" enctype="multipart/form-data">
        <label>Bread</label>
        <input type="checkbox" name="item_Bread" value="1">
        <input type="number" name="itemCount_Bread" max="10" min="0" placeholder="0"><br>

        <label>Tuna</label>
        <input type="checkbox" name="item_Tuna" value="1">
        <input type="number" name="itemCount_Tuna" max="10" min="0" placeholder="0"><br>

        <label>Soup</label>
        <input type="checkbox" name="item_Soup" value="1">
        <input type="number" name="itemCount_Soup" max="10" min="0" placeholder="0"><br>

        <label>Water</label>
        <input type="checkbox" name="item_Water" value="1">
        <input type="number" name="itemCount_Water" max="10" min="0" placeholder="0"><br>

        <label>Sparkling Water</label>
        <input type="checkbox" name="item_SparklingWater" value="1">
        <input type="number" name="itemCount_SparklingWater" max="10" min="0" placeholder="0"><br>


        <input type="submit" name="submit" id="submit" value="Buy">
        <input type="reset" name="reset" id="reset" value="Reset">
    </form>
</div>

<div class="form Ammunition">
    <form method="post" name="Ammunition" enctype="multipart/form-data">
        <label>5.56x45</label>
        <input type="checkbox" name="item_556x45" value="1">
        <input type="number" name="itemCount_556x45" max="240" min="0" placeholder="0"><br>

        <label>5.56x39</label>
        <input type="checkbox" name="item_556x39" value="1">
        <input type="number" name="itemCount_556x39" max="240" min="0" placeholder="0"><br>

        <label>9mm</label>
        <input type="checkbox" name="item_9mm" value="1">
        <input type="number" name="itemCount_9mm" max="120" min="0" placeholder="0"><br>

        <label>8mm</label>
        <input type="checkbox" name="item_8mm" value="1">
        <input type="number" name="itemCount_8mm" max="120" min="0" placeholder="0"><br>

        <label>.45 ACP</label>
        <input type="checkbox" name="item_45ACP" value="1">
        <input type="number" name="itemCount_45ACP" max="120" min="0" placeholder="0"><br>

        <label>I verify that i own a weapons licence</label>
        <input type="checkbox" name="gunlicence" value="1" required><br>


        <input type="submit" name="submit" id="submit" value="Buy">
        <input type="reset" name="reset" id="reset" value="Reset">
    </form>
</div>

<div class="form Extra">
    <form method="post" name="Extra" enctype="multipart/form-data">
        <label>Extra information / requierements for courier?</label>
        <input type="text" name="extraInfo" maxlength="500" style="width: 312px; height: 100px;">
        <input type="submit" name="submit" id="submit" value="Send">
        <input type="reset" name="reset" id="reset" value="Reset">
    </form>
</div>

<div class="form Download">
    <a href="orders.txt" download="orders">
        <button type="button">Download Orders</button>
    </a>
</div>

<div class="form Cart">
    <h3>Your order:</h3>
    <ul>
        <li><?php echo $grenadeOrder; ?></li>
        <li><?php echo $medicalOrder; ?></li>
        <li><?php echo $foodOrder; ?></li>
        <li><?php echo $ammoOrder; ?></li>
        <li><?php echo "Extra information for courier: $ExtraInfo"; ?></li>
    </ul>

    <form method="post" name="Cart">
        <input type="submit" name="Send" value="Send">
        <input type="reset" name="reset" value="Reset">
    </form>
</div>

<div class="form Filter">
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
        <h3>Filter / Sort orders</h3>
        <form method="post">
            <label>Sort by Time (Newest first)</label>
            <input type="radio" name="sortBy" value="time" checked>
            <br>
            <label>Sort by User (A-Z)</label>
            <input type="radio" name="sortBy" value="alphabetical">
            <br><br>
            <input type="submit" name="filterSubmit" value="Filter">
        </form>
    <?php else: ?>
        <p>You need Admin rights to browse orders.</p>
    <?php endif; ?>

    <?php
    // Načtení a zobrazení objednávek
    if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
        // Načteme soubor
        $allOrders = file('orders.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Pokud bylo kliknuto na "Filter", seřadíme data
        if (isset($_POST['filterSubmit']) && isset($_POST['sortBy'])) {
            if ($_POST['sortBy'] === 'time') {
                rsort($allOrders); // Od nejnovějšího
            } elseif ($_POST['sortBy'] === 'alphabetical') {
                usort($allOrders, function($a, $b) {
                    $partsA = explode(" | ", $a, 3);
                    $partsB = explode(" | ", $b, 3);
                    return strcmp($partsA[1] ?? '', $partsB[1] ?? '');
                });
            }
        }

        // Výpis seznamu (používáme <ul> a <li> odpovídající tvému CSS)
        if(!empty($allOrders)){
            echo "<ul>";
            foreach(array_slice($allOrders, 0, 50) as $line){
                // htmlspecialchars zajistí bezpečnost zobrazení textu
                echo "<li>" . htmlspecialchars($line) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No orders found.</p>";
        }
    }
    ?>
</div>
<!--Konec-->
<script>
    if (<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') ? 'true' : 'false'; ?>) {
        document.querySelector(".Download").style.display = "block";
    }
</script>

    </body>




