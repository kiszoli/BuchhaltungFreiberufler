<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

session_start();
if (isset($_GET['logout'])) $_SESSION['userId'] = 0;
if (!isset($_SESSION['userId']) || $_SESSION['userId'] < 1) header('Location: login.php');
?>
<!doctype html>
<html lang="de">

<head>
    <title>Buchhaltung</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/accordion.js"></script>
</head>

<body>
    <div id="menu">
        <div class="logo"><img src="images/logo_tn.png"></div>
        <div class="bilanzjahr"><img src="images/arrow-left.png">2022<img src="images/arrow-right.png"></div>
        <div class="navigation">
            <a href="index.php"><img src="images/home.png" alt="Home"></a>
            <a href="rechnungen.php"><img src="images/einnahmen.png" alt="Rechnungen"></a>
            <a href="ausgaben.php"><img src="images/ausgaben.png" alt="Ausgaben"></a>
            <a href="adressen.php"><img src="images/addressbook.png" alt="Adressbuch"></a>
            <a href="einstellungen.php"><img src="images/settings.png" alt="Einstellungen"></a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?logout=true"><img src="images/logout.png" alt="Logout"></a>
        </div>
        <div class="clearfix"></div>
    </div>
    <div id="wrapper">
        <h1>Überschrift h1</h1>
        <h2>Überschrift h2</h2>
        <h3>Überschrift h3</h3>
        <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore
            magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren,
            no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
            sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam
            et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
        <a href="#">This is a link</a>
        <h2>Tabellen</h2>
        <table>
            <caption>
                This is the table caption
            </caption>
            <thead>
                <th>Company</th>
                <th>Contact</th>
                <th class="tright">Offer</th>
            </thead>
            <tbody>
                <tr>
                    <td>Alfreds Futterkiste</td>
                    <td>Maria Anders</td>
                    <td class="tright">878,00 €</td>
                </tr>
                <tr>
                    <td>Berglunds snabbköp</td>
                    <td>Christina Berglund</td>
                    <td class="tright">654,00 €</td>
                </tr>
                <tr>
                    <td>Centro comercial Moctezuma</td>
                    <td>Francisco Chang</td>
                    <td class="tright">56,00 €</td>
                </tr>
                <tr>
                    <td>Ernst Handel</td>
                    <td>Roland Mendel</td>
                    <td class="tright">6.844,00 €</td>
                </tr>
                <tr>
                    <td>Island Trading</td>
                    <td>Helen Bennett</td>
                    <td class="tright">5.745,00 €</td>
                </tr>
                <tr>
                    <td>Königlich Essen</td>
                    <td>Philip Cramer</td>
                    <td class="tright">3.156.516,00 €</td>
                </tr>
                <tr>
                    <td>Laughing Bacchus Winecellars</td>
                    <td>Yoshi Tannamuri</td>
                    <td class="tright">1,00 €</td>
                </tr>
                <tr>
                    <td>Magazzini Alimentari Riuniti</td>
                    <td>Giovanni Rovelli</td>
                    <td class="tright">351,00 €</td>
                </tr>
                <tr>
                    <td>North/South</td>
                    <td>Simon Crowther</td>
                    <td class="tright">798,00 €</td>
                </tr>
                <tr>
                    <td>Paris spécialités</td>
                    <td>Marie Bertrand</td>
                    <td class="tright">132,00 €</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Colspan is 3</th>
                </tr>
            </tfoot>
        </table>
        <h2>Form Elemente</h2>
        <form action="style.php" method="post" enctype="application/x-www-form-urlencoded">
            <label for="fname">Text</label>
            <input type="text" id="fname" name="firstname" placeholder="Your name..">

            <label for="fpassword">Password</label>
            <input type="password" id="fpassword" name="fpassword" placeholder="Your Password">

            <label for="country">Select</label>
            <select id="country" name="country">
                <option value="australia">Australia</option>
                <option value="canada">Canada</option>
                <option value="usa">USA</option>
            </select>

            <h3>Custom Checkboxes</h3>
            <label class="container">One
                <input type="checkbox" checked="checked">
                <span class="checkmark"></span>
            </label>
            <label class="container">Two
                <input type="checkbox">
                <span class="checkmark"></span>
            </label>
            <label class="container">Three
                <input type="checkbox">
                <span class="checkmark"></span>
            </label>
            <label class="container">Four
                <input type="checkbox">
                <span class="checkmark"></span>
            </label>

            <h3>Custom Radio Buttons</h3>
            <label class="container">One
                <input type="radio" checked="checked" name="radio">
                <span class="radiobtn"></span>
            </label>
            <label class="container">Two
                <input type="radio" name="radio">
                <span class="radiobtn"></span>
            </label>
            <label class="container">Three
                <input type="radio" name="radio">
                <span class="radiobtn"></span>
            </label>
            <label class="container">Four
                <input type="radio" name="radio">
                <span class="radiobtn"></span>
            </label>

            <input type="submit" value="Submit">
        </form>
        <h2>Animated Accordion</h2>
        <button class="accordion">
            <div class="leftalign">Budapest</div>
            <div class="rightalign">1.700.000</div>
        </button>
        <div class="panel">
            <div class="accordionitem">
                <div class="leftalign">Autos</div>
                <div class="rightalign">123,00</div>
            </div>
            <div class="accordionitem">
                <div class="leftalign">Fahrräder</div>
                <div class="rightalign">45,00</div>
            </div>
        </div>
        <button class="accordion">
            <div class="leftalign">Lissabon</div>
            <div class="rightalign">552.700</div>
        </button>
        <div class="panel">
            <div class="accordionitem">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
        </div>

        <button class="accordion">
            <div class="leftalign">Sidney</div>
            <div class="rightalign">4.700.000</div>
        </button>
        <div class="panel">
            <div class="accordionitem">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
        </div>

        <button class="accordion">
            <div class="leftalign">Wien</div>
            <div class="rightalign">1.931.593</div>
        </button>
        <div class="panel">
            <div class="accordionitem">
                <div class="leftalign">Autos</div>
                <div class="rightalign">123,00</div>
            </div>
            <div class="accordionitem">
                <div class="leftalign">Fahrräder</div>
                <div class="rightalign">45,00</div>
            </div>
        </div>

        <script>
            var acc = document.getElementsByClassName("accordion");
            var i;

            for (i = 0; i < acc.length; i++) {
                acc[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    var panel = this.nextElementSibling;
                    if (panel.style.maxHeight) {
                        panel.style.maxHeight = null;
                    } else {
                        panel.style.maxHeight = panel.scrollHeight + "px";
                    }
                });
            }
        </script>
    </div>
    <div class="clearfix"></div>
</body>

</html>