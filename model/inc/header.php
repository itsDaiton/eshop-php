<header class="p-3 bg-dark text-white">
        <div class="d-flex justify-content-between">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <img src="https://assets.webiconspng.com/uploads/2017/09/Shopping-Cart-PNG-Image-72455.png" alt="Košík" width="65" height="50">
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0" style="margin-left: 25px;">
                    <li><a href="index.php" class="nav-link px-2 text-white">Zboží</a></li>
                    <li><a href="cart.php" class="nav-link px-2 text-white">Košík</a></li>
                    <?php
                        if (isset($_SESSION['cart']) && count(@$_SESSION['cart']) > 0) {
                           echo '<li style="margin-top: 20px; color: deepskyblue; font-size: 12px;">'.count($_SESSION['cart']).'</li>';
                        }
                    ?>
                    <?php
                        if (!empty($_SESSION['user_email'])) {
                            echo '<li><a href="favourites.php" class="nav-link px-2 text-white">Oblíbené</a></li>
                                  <li><a href="orders.php" class="nav-link px-2 text-white">Objednávky</a></li>';
                        }
                    ?>
                </ul>
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <?php
                    //pokud je uživatel přihlášen, tak se mu zobrazí tlačítko na odhlášení, jinak bude zobrazeno tlačítko pro přihlášení
                    if (!empty($_SESSION['user_email'])) {
                        echo '<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0" style="margin-right: 25px;">
                                 <li><a href="logout.php" class="nav-link px-2 text-white" style="margin-right: 25px;">Odhlásit se</a></li>                                 
                                 <li class="nav-link px-2 text-white">Uživatel: '.htmlspecialchars($_SESSION['user_email']).'</li>
                              </ul>';
                    }
                    else {
                        echo '<a href="login.php" class="nav-link px-2 text-white" style="margin-right: 25px;">Přihlásit se</a>';
                    }
                ?>
            </div>
        </div>
</header>
