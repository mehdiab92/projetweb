<?php
include_once "../../Controller/serviceC.php";
include_once "../../Controller/categorieC.php";
include_once "../../Controller/RecommendationC.php";

$serviceC = new ServiceC();
$categorieC = new CategorieC();
$recommendationC = new RecommendationC();

$categories = $categorieC->getCategories();

$userId = 1; 

$priceRange = $serviceC->getMinMaxPrices($userId);

$minPrice = floor($priceRange['min_price']); // Dynamic minimum price
$maxPrice = ceil($priceRange['max_price']);  // Dynamic maximum price

$currentMin = isset($_GET['min_price']) ? (int)$_GET['min_price'] : $minPrice;
$currentMax = isset($_GET['max_price']) ? (int)$_GET['max_price'] : $maxPrice;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 4; 
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';

$db = config::getConnexion();

try {
    $sql = "SELECT 
                s.*, 
                c.nom_categorie, 
                usr.discounted_price 
            FROM Services s 
            JOIN Categorie c ON s.id_categorie = c.id_categorie
            LEFT JOIN User_Service_Recommendation usr 
                ON usr.id_service = s.id_service AND usr.id_user = :userId
            WHERE 1=1";

    $params = ['userId' => $userId];

    if (!empty($search)) {
        $sql .= " AND s.service_name LIKE :search";
        $params['search'] = '%' . $search . '%';
    }

    if (!empty($categorie)) {
        $sql .= " AND s.id_categorie = :categorie";
        $params['categorie'] = $categorie;
    }

    // 🚨 ADD THIS to filter with slider min/max price!
    if (isset($_GET['min_price']) && isset($_GET['max_price']) && $_GET['min_price'] !== '' && $_GET['max_price'] !== '') {
        $sql .= " AND (COALESCE(usr.discounted_price, s.price) BETWEEN :min_price AND :max_price)";
        $params['min_price'] = $_GET['min_price'];
        $params['max_price'] = $_GET['max_price'];
    }

    $sql .= " LIMIT $limit OFFSET $offset";

    $query = $db->prepare($sql);
    $query->execute($params);

    $listServices = $query->fetchAll(PDO::FETCH_ASSOC);

    // For pagination count
    $countSql = "SELECT COUNT(*) FROM Services s 
                 LEFT JOIN User_Service_Recommendation usr 
                 ON usr.id_service = s.id_service AND usr.id_user = :userId
                 WHERE 1=1";

    $countParams = ['userId' => $userId];

    if (!empty($search)) {
        $countSql .= " AND s.service_name LIKE :search";
        $countParams['search'] = '%' . $search . '%';
    }
    if (!empty($categorie)) {
        $countSql .= " AND s.id_categorie = :categorie";
        $countParams['categorie'] = $categorie;
    }
    if (isset($_GET['min_price']) && isset($_GET['max_price']) && $_GET['min_price'] !== '' && $_GET['max_price'] !== '') {
        $countSql .= " AND (COALESCE(usr.discounted_price, s.price) BETWEEN :min_price AND :max_price)";
        $countParams['min_price'] = $_GET['min_price'];
        $countParams['max_price'] = $_GET['max_price'];
    }

    $countQuery = $db->prepare($countSql);
    $countQuery->execute($countParams);

    $totalServices = $countQuery->fetchColumn();
    $totalPages = ceil($totalServices / $limit);

} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}

?>



<!doctype html>
<html lang="en">

<head>

    <!-- Basic Page Needs
================================================== -->
    <title>Mentoriel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
================================================== -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/colors/blue.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.js"></script>

</head>

<body>

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Header Container
================================================== -->
        <header id="header-container" class="fullwidth">

            <!-- Header -->
            <div id="header">
                <div class="container">

                    <!-- Left Side Content -->
                    <div class="left-side">

                        <!-- Logo -->
                        <div id="logo">
                            <a href="index.html"><img src="images/logomentoriel.png" alt=""></a>
                        </div>

                        <!-- Main Navigation -->
                        <nav id="navigation">
                            <ul id="responsive">

                                <li><a href="#" class="current">Accueil</a>
                                    <ul>
                                    </ul>
                                </li>

                                <li><a href="#">Parcourir</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="liste-formations.html">Formations</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="liste-formations.html">Parcourir Formations</a></li>
                                                <li><a href="#">Ajouter une formation</a></li>
                                                <li><a href="#">Gérer vos formations</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="liste-entreprises.html">Entreprises</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="#">Parcourir les Entreprises</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="liste-seances.html">Séances</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="liste-seances.html">Parcourir les séance</a></li>
                                                <li><a href="#">Ajouter une séance</a></li>
                                                <li><a href="#">Gérer vos séances</a></li>
                                            </ul>
                                        </li>

                                        <li><a href="liste-services.html">Services</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="liste-services.html">Parcourir les services</a></li>
                                                <li><a href="#">Ajouter un service</a></li>
                                                <li><a href="#">Gérer vos services</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="liste-entrepreneurs.html">Entrepreneurs</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="liste-entrepreneurs.html">Liste des services</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <li><a href="liste-services.html">Marché</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="liste-services.html">Parcourir</a></li>
                                        <li><a href="#">Ajouter un service</a></li>
                                        <li><a href="#">Gérer vos services</a></li>
                                    </ul>
                                </li>
                                <li><a href="dashboard-settings.html">Paramètres</a>
                                    <ul class="dropdown-nav">
                                    </ul>
                                </li>
                                </li>
                                <li><a href="dashboard.html">Tableau de Bord</a>
                                    <ul class="dropdown-nav"></ul>
                            </ul>
                            </li>

                            </ul>


                        </nav>
                        <div class="clearfix"></div>
                        <!-- Main Navigation / End -->

                    </div>
                    <!-- Left Side Content / End -->


                    <!-- Right Side Content / End -->
                    <div class="right-side">

                        <!--  User Notifications -->
                        <div class="header-widget hide-on-mobile">

                            <!-- Notifications -->
                            <div class="header-notifications">

                                <!-- Trigger -->
                                <div class="header-notifications-trigger">
                                    <a href="#"><i class="icon-feather-bell"></i><span>4</span></a>
                                </div>

                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">

                                    <div class="header-notifications-headline">
                                        <h4>Notifications</h4>
                                        <button class="mark-as-read ripple-effect-dark" title="Mark all as read"
                                            data-tippy-placement="left">
                                            <i class="icon-feather-check-square"></i>
                                        </button>
                                    </div>

                                    <div class="header-notifications-content">
                                        <div class="header-notifications-scroll" data-simplebar>
                                            <ul>
                                                <!-- Notification -->
                                                <li class="notifications-not-read">
                                                    <a href="dashboard-manage-candidates.html">
                                                        <span class="notification-icon"><i
                                                                class="icon-material-outline-group"></i></span>
                                                        <span class="notification-text">
                                                            <strong>Michael Shannah</strong> applied for a job <span
                                                                class="color">Full Stack Software Engineer</span>
                                                        </span>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li>
                                                    <a href="dashboard-manage-bidders.html">
                                                        <span class="notification-icon"><i
                                                                class=" icon-material-outline-gavel"></i></span>
                                                        <span class="notification-text">
                                                            <strong>Gilbert Allanis</strong> placed a bid on your <span
                                                                class="color">iOS App Development</span> project
                                                        </span>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li>
                                                    <a href="dashboard-manage-jobs.html">
                                                        <span class="notification-icon"><i
                                                                class="icon-material-outline-autorenew"></i></span>
                                                        <span class="notification-text">
                                                            Your job listing <span class="color">Full Stack PHP
                                                                Developer</span> is expiring.
                                                        </span>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li>
                                                    <a href="dashboard-manage-candidates.html">
                                                        <span class="notification-icon"><i
                                                                class="icon-material-outline-group"></i></span>
                                                        <span class="notification-text">
                                                            <strong>Sindy Forrest</strong> applied for a job <span
                                                                class="color">Full Stack Software Engineer</span>
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <!-- Messages -->
                            <div class="header-notifications">
                                <div class="header-notifications-trigger">
                                    <a href="#"><i class="icon-feather-mail"></i><span>3</span></a>
                                </div>

                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">

                                    <div class="header-notifications-headline">
                                        <h4>Messages</h4>
                                        <button class="mark-as-read ripple-effect-dark" title="Mark all as read"
                                            data-tippy-placement="left">
                                            <i class="icon-feather-check-square"></i>
                                        </button>
                                    </div>

                                    <div class="header-notifications-content">
                                        <div class="header-notifications-scroll" data-simplebar>
                                            <ul>
                                                <!-- Notification -->
                                                <li class="notifications-not-read">
                                                    <a href="dashboard-messages.html">
                                                        <span class="notification-avatar status-online"><img
                                                                src="images/user-avatar-small-03.png" alt=""></span>
                                                        <div class="notification-text">
                                                            <strong>David Peterson</strong>
                                                            <p class="notification-msg-text">Thanks for reaching out.
                                                                I'm quite busy right now on many...</p>
                                                            <span class="color">4 hours ago</span>
                                                        </div>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li class="notifications-not-read">
                                                    <a href="dashboard-messages.html">
                                                        <span class="notification-avatar status-offline"><img
                                                                src="images/user-avatar-small-02.png" alt=""></span>
                                                        <div class="notification-text">
                                                            <strong>Sindy Forest</strong>
                                                            <p class="notification-msg-text">Hi Tom! Hate to break it to
                                                                you, but I'm actually on vacation until...</p>
                                                            <span class="color">Yesterday</span>
                                                        </div>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li class="notifications-not-read">
                                                    <a href="dashboard-messages.html">
                                                        <span class="notification-avatar status-online"><img
                                                                src="images/user-avatar-placeholder.png" alt=""></span>
                                                        <div class="notification-text">
                                                            <strong>Marcin Kowalski</strong>
                                                            <p class="notification-msg-text">I received payment. Thanks
                                                                for cooperation!</p>
                                                            <span class="color">Yesterday</span>
                                                        </div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <a href="dashboard-messages.html"
                                        class="header-notifications-button ripple-effect button-sliding-icon">View All
                                        Messages<i class="icon-material-outline-arrow-right-alt"></i></a>
                                </div>
                            </div>

                        </div>
                        <!--  User Notifications / End -->

                        <!-- User Menu -->
                        <div class="header-widget">

                            <!-- Messages -->
                            <div class="header-notifications user-menu">
                                <div class="header-notifications-trigger">
                                    <a href="#">
                                        <div class="user-avatar status-online"><img
                                                src="images/user-avatar-small-01.png" alt=""></div>
                                    </a>
                                </div>

                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">

                                    <!-- User Status -->
                                    <div class="user-status">

                                        <!-- User Name / Avatar -->
                                        <div class="user-details">
                                            <div class="user-avatar status-online"><img
                                                    src="images/user-avatar-small-01.png" alt=""></div>
                                            <div class="user-name">
                                                Tom Smith <span>Freelancer</span>
                                            </div>
                                        </div>

                                        <!-- User Status Switcher -->
                                        <div class="status-switch" id="snackbar-user-status">
                                            <label class="user-online current-status">Online</label>
                                            <label class="user-invisible">Invisible</label>
                                            <!-- Status Indicator -->
                                            <span class="status-indicator" aria-hidden="true"></span>
                                        </div>
                                    </div>

                                    <ul class="user-menu-small-nav">
                                        <li><a href="dashboard.html"><i class="icon-material-outline-dashboard"></i>
                                                Dashboard</a></li>
                                        <li><a href="dashboard-settings.html"><i
                                                    class="icon-material-outline-settings"></i> Settings</a></li>
                                        <li><a href="index-logged-out.html"><i
                                                    class="icon-material-outline-power-settings-new"></i> Logout</a>
                                        </li>
                                    </ul>

                                </div>
                            </div>

                        </div>
                        <!-- User Menu / End -->

                        <!-- Mobile Navigation Button -->
                        <span class="mmenu-trigger">
                            <button class="hamburger hamburger--collapse" type="button">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </span>

                    </div>
                    <!-- Right Side Content / End -->

                </div>
            </div>
            <!-- Header / End -->

        </header>
        <div class="clearfix"></div>
        <!-- Header Container / End -->

        <!-- Spacer -->
        <div class="margin-top-90"></div>
        <!-- Spacer / End-->

        <!-- Page Content
================================================== -->
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="sidebar-container">

                        <!-- Location -->
                        <div class="sidebar-widget">
                            <h3>Emplacement</h3>
                            <div class="input-with-icon">
                                <div id="autocomplete-container">
                                    <input id="autocomplete-input" type="text" placeholder="Emplacement">
                                </div>
                                <i class="icon-material-outline-location-on"></i>
                            </div>
                        </div>

                        <!-- Category -->
                        <form method="GET" action="listeservice.php">
                            <label for="categorie">Catégorie</label>
                            <select name="categorie" id="categorie" onchange="this.form.submit()">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $cat) { ?>
                                <option value="<?= $cat['id_categorie']; ?>"
                                    <?= (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id_categorie']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($cat['nom_categorie']); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </form>



                        <!-- Keywords -->
                        <div class="sidebar-widget">
                            <h3>Mots Clés</h3>
                            <div class="keywords-container">
                                <div class="keyword-input-container">
                                    <input type="text" class="keyword-input" placeholder="e.g. titre" />
                                    <button class="keyword-input-button ripple-effect"><i
                                            class="icon-material-outline-add"></i></button>
                                </div>
                                <div class="keywords-list">
                                    <!-- keywords go here -->
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <!-- Sidebar for slider -->
                        <div class="sidebar-widget">
                            <h3>Prix (€)</h3>
                            <div id="price-range-slider" style="margin-top: 20px;"></div>

                            <div style="margin-top: 10px; display: flex; justify-content: space-between;">
                                <span id="price-min"><?php echo $minPrice; ?>€</span>
                                <span id="price-max"><?php echo $maxPrice; ?>€</span>
                            </div>

                            <!-- 🛑 REMOVE hidden inputs from here -->
                        </div>


                        <!-- Tags -->
                        <div class="sidebar-widget">
                            <h3>Compétences</h3>

                            <div class="tags-container">
                                <div class="tag">
                                    <input type="checkbox" id="tag1" />
                                    <label for="tag1">front-end dev</label>
                                </div>
                                <div class="tag">
                                    <input type="checkbox" id="tag2" />
                                    <label for="tag2">angular</label>
                                </div>
                                <div class="tag">
                                    <input type="checkbox" id="tag3" />
                                    <label for="tag3">react</label>
                                </div>
                                <div class="tag">
                                    <input type="checkbox" id="tag4" />
                                    <label for="tag4">vue js</label>
                                </div>
                                <div class="tag">
                                    <input type="checkbox" id="tag5" />
                                    <label for="tag5">web apps</label>
                                </div>
                                <div class="tag">
                                    <input type="checkbox" id="tag6" />
                                    <label for="tag6">design</label>
                                </div>
                                <div class="tag">
                                    <input type="checkbox" id="tag7" />
                                    <label for="tag7">wordpress</label>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <!-- More Skills -->
                            <div class="keywords-container margin-top-20">
                                <div class="keyword-input-container">
                                    <input type="text" class="keyword-input" placeholder="add more skills" />
                                    <button class="keyword-input-button ripple-effect"><i
                                            class="icon-material-outline-add"></i></button>
                                </div>
                                <div class="keywords-list">
                                    <!-- keywords go here -->
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 content-left-offset">

                    <h3 class="page-title">Liste des services</h3>

                    <div class="notify-box margin-top-15"
                        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">

                        <form id="filterForm" method="GET"
                            style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">

                            <!-- Search input -->
                            <input type="text" name="search" id="searchInput" placeholder="Rechercher un service..."
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                style="
                padding: 10px 14px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 14px;
                width: 250px;
            ">

                            <!-- Category filter -->
                            <select name="categorie" onchange="document.getElementById('filterForm').submit();" style="
                padding: 10px 14px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 14px;
                background-color: white;
            ">
                                <option value="">Toutes les catégories</option>
                                <?php foreach($categories as $categorie): ?>
                                <option value="<?php echo $categorie['id_categorie']; ?>"
                                    <?php echo (isset($_GET['categorie']) && $_GET['categorie'] == $categorie['id_categorie']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categorie['nom_categorie']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Filtrer Button -->
                            <button type="submit" style="
    background-color: #2a41e8;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    height: 42px; /* 🆕 fix same height */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
">
                                Filtrer
                            </button>

                            <!-- Réinitialiser Button -->
                            <a href="listeservice.php" style="
    background-color: #f44336;
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    height: 42px; /* 🆕 same height as Filtrer */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
">
                                Réinitialiser
                            </a>
                            <input type="hidden" name="min_price" id="min_price" value="<?php echo $currentMin; ?>">
                            <input type="hidden" name="max_price" id="max_price" value="<?php echo $currentMax; ?>">


                        </form>

                    </div>


                    <!-- JavaScript for dynamic search -->
                    <script>
                    const searchInput = document.getElementById('searchInput');
                    let typingTimer;
                    const doneTypingInterval = 500; // 0.5 second after user stops typing

                    searchInput.addEventListener('keyup', function() {
                        clearTimeout(typingTimer);
                        typingTimer = setTimeout(() => {
                            document.getElementById('filterForm').submit();
                        }, doneTypingInterval);
                    });

                    searchInput.addEventListener('keydown', function() {
                        clearTimeout(typingTimer);
                    });
                    </script>



                    <!-- Services List Container -->
                    <div class="services-container list-layout margin-top-35">

                        <?php foreach($listServices as $serviceData): 
    $isRecommended = $recommendationC->isServiceRecommended(1, $serviceData['id_service']);
?>
                        <div class="service-card">

                            <div class="service-content">

                                <a href="single-service-page.php?id=<?php echo $serviceData['id_service']; ?>"
                                    class="service-link">
                                    <div class="service-details">

                                        <h3 class="service-title">
                                            <?php echo htmlspecialchars($serviceData['service_name']); ?>
                                        </h3>

                                        <div class="service-meta">
                                            <span class="service-category">
                                                <i class="icon-material-outline-category"></i>
                                                <?php echo htmlspecialchars($serviceData['nom_categorie']); ?>
                                            </span>

                                            <span
                                                class="service-eco <?php echo $serviceData['eco_friendly'] ? 'eco' : 'non-eco'; ?>">
                                                <i class="icon-material-outline-eco"></i>
                                                <?php echo $serviceData['eco_friendly'] ? 'Écologique' : 'Non écologique'; ?>
                                            </span>
                                        </div>

                                        <p class="service-description">
                                            <?php echo htmlspecialchars(substr($serviceData['service_description'], 0, 100)); ?>...
                                        </p>

                                    </div>
                                </a>

                                <div class="service-action">

                                    <div>
                                        <span class="button button-sliding-icon ripple-effect">
                                            Voir détails <i class="icon-material-outline-arrow-right-alt"></i>
                                        </span>

                                        <?php if (!$isRecommended): ?>
                                        <form action="recommend_service.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_service"
                                                value="<?php echo $serviceData['id_service']; ?>">
                                            <button type="submit" class="recommend-btn">
                                                ➕ Recommend
                                            </button>
                                        </form>
                                        <?php else: ?>
                                        <button class="recommended-btn" disabled>
                                            ✅ Recommended
                                        </button>
                                        <?php endif; ?>
                                    </div>

                                    <div class="service-price">
                                        <?php if (!empty($serviceData['discounted_price'])): ?>
                                        <!-- Discounted Price -->
                                        <span class="price" style="color: #4CAF50; font-weight: bold;">
                                            <?php echo htmlspecialchars($serviceData['discounted_price']); ?> €
                                        </span>
                                        <br>
                                        <small style="text-decoration: line-through; color: gray;">
                                            <?php echo htmlspecialchars($serviceData['price']); ?> €
                                        </small>
                                        <!-- Small badge -->
                                        <div
                                            style="background-color: #4CAF50; color: white; font-size: 10px; padding: 2px 5px; border-radius: 4px; display: inline-block; margin-top: 5px;">
                                            Promo !
                                        </div>
                                        <?php else: ?>
                                        <!-- Normal Price -->
                                        <span class="price">
                                            <?php echo htmlspecialchars($serviceData['price']); ?> €
                                        </span>
                                        <?php endif; ?>

                                        <span class="price-label">Prix TTC</span>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <?php endforeach; ?>

                    </div> <!-- End of services-container -->


                    <style>
                    .services-container {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                        gap: 20px;
                    }

                    .service-card {
                        background: #fff;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                    }

                    .service-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                    }

                    .service-link {
                        display: block;
                        text-decoration: none;
                        color: inherit;
                    }

                    .service-content {
                        display: flex;
                        flex-direction: column;
                        height: 100%;
                        padding: 20px;
                    }

                    .service-image {
                        height: 180px;
                        overflow: hidden;
                        border-radius: 6px;
                        margin-bottom: 15px;
                    }

                    .service-image img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        transition: transform 0.3s ease;
                    }

                    .service-card:hover .service-image img {
                        transform: scale(1.05);
                    }

                    .service-title {
                        font-size: 18px;
                        margin: 0 0 10px;
                        color: #2a41e8;
                    }

                    .service-meta {
                        display: flex;
                        gap: 15px;
                        margin-bottom: 12px;
                        font-size: 14px;
                    }

                    .service-category {
                        color: #666;
                    }

                    .service-eco.eco {
                        color: #4CAF50;
                    }

                    .service-eco.non-eco {
                        color: #F44336;
                    }

                    .service-description {
                        color: #666;
                        font-size: 14px;
                        line-height: 1.5;
                        margin: 0 0 15px;
                        flex-grow: 1;
                    }

                    .service-action {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-top: auto;
                    }

                    .service-price {
                        text-align: right;
                    }

                    .service-price .price {
                        font-size: 20px;
                        font-weight: 700;
                        color: #2a41e8;
                        display: block;
                    }

                    .service-price .price-label {
                        font-size: 12px;
                        color: #888;
                    }

                    @media (max-width: 768px) {
                        .services-container {
                            grid-template-columns: 1fr;
                        }
                    }
                    </style>
                    <!-- Freelancers Container / End -->


                    <!-- Pagination -->
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Pagination -->
                            <div class="pagination-container margin-top-40 margin-bottom-60">
                                <nav class="pagination">
                                    <ul>
                                        <?php if ($page > 1): ?>
                                        <li class="pagination-arrow">
                                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>"
                                                class="ripple-effect">
                                                <i class="icon-material-outline-keyboard-arrow-left"></i>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li>
                                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                                                class="ripple-effect <?php echo $i == $page ? 'current-page' : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $totalPages): ?>
                                        <li class="pagination-arrow">
                                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>"
                                                class="ripple-effect">
                                                <i class="icon-material-outline-keyboard-arrow-right"></i>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>

                        </div>
                    </div>
                    <!-- Pagination / End -->

                </div>
            </div>
        </div>


        <!-- Footer
================================================== -->
        <div id="footer">

            <!-- Footer Top Section -->
            <div class="footer-top-section">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">

                            <!-- Footer Rows Container -->
                            <div class="footer-rows-container">

                                <!-- Left Side -->
                                <div class="footer-rows-left">
                                    <div class="footer-row">
                                        <div class="footer-row-inner footer-logo">
                                            <img src="images/logo2.png" alt="">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Side -->
                                <div class="footer-rows-right">

                                    <!-- Social Icons -->
                                    <div class="footer-row">
                                        <div class="footer-row-inner">
                                            <ul class="footer-social-links">
                                                <li>
                                                    <a href="#" title="Facebook" data-tippy-placement="bottom"
                                                        data-tippy-theme="light">
                                                        <i class="icon-brand-facebook-f"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" title="Twitter" data-tippy-placement="bottom"
                                                        data-tippy-theme="light">
                                                        <i class="icon-brand-twitter"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" title="Google Plus" data-tippy-placement="bottom"
                                                        data-tippy-theme="light">
                                                        <i class="icon-brand-google-plus-g"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" title="LinkedIn" data-tippy-placement="bottom"
                                                        data-tippy-theme="light">
                                                        <i class="icon-brand-linkedin-in"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                    <!-- Language Switcher -->
                                    <div class="footer-row">
                                        <div class="footer-row-inner">
                                            <select class="selectpicker language-switcher"
                                                data-selected-text-format="count" data-size="5">
                                                <option selected>English</option>
                                                <option>Français</option>
                                                <option>Español</option>
                                                <option>Deutsch</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- Footer Rows Container / End -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Top Section / End -->

            <!-- Footer Middle Section -->
            <div class="footer-middle-section">
                <div class="container">
                    <div class="row">

                        <!-- Links -->
                        <div class="col-xl-2 col-lg-2 col-md-3">
                            <div class="footer-links">
                                <h3>For Candidates</h3>
                                <ul>
                                    <li><a href="#"><span>Browse Jobs</span></a></li>
                                    <li><a href="#"><span>Add Resume</span></a></li>
                                    <li><a href="#"><span>Job Alerts</span></a></li>
                                    <li><a href="#"><span>My Bookmarks</span></a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Links -->
                        <div class="col-xl-2 col-lg-2 col-md-3">
                            <div class="footer-links">
                                <h3>For Employers</h3>
                                <ul>
                                    <li><a href="#"><span>Browse Candidates</span></a></li>
                                    <li><a href="#"><span>Post a Job</span></a></li>
                                    <li><a href="#"><span>Post a Task</span></a></li>
                                    <li><a href="#"><span>Plans & Pricing</span></a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Links -->
                        <div class="col-xl-2 col-lg-2 col-md-3">
                            <div class="footer-links">
                                <h3>Helpful Links</h3>
                                <ul>
                                    <li><a href="#"><span>Contact</span></a></li>
                                    <li><a href="#"><span>Privacy Policy</span></a></li>
                                    <li><a href="#"><span>Terms of Use</span></a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Links -->
                        <div class="col-xl-2 col-lg-2 col-md-3">
                            <div class="footer-links">
                                <h3>Account</h3>
                                <ul>
                                    <li><a href="#"><span>Log In</span></a></li>
                                    <li><a href="#"><span>My Account</span></a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Newsletter -->
                        <div class="col-xl-4 col-lg-4 col-md-12">
                            <h3><i class="icon-feather-mail"></i> Sign Up For a Newsletter</h3>
                            <p>Weekly breaking news, analysis and cutting edge advices on job searching.</p>
                            <form action="#" method="get" class="newsletter">
                                <input type="text" name="fname" placeholder="Enter your email address">
                                <button type="submit"><i class="icon-feather-arrow-right"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Middle Section / End -->

            <!-- Footer Copyrights -->
            <div class="footer-bottom-section">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            © 2018 <strong>Hireo</strong>. All Rights Reserved.
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Copyrights / End -->

        </div>
        <!-- Footer / End -->

    </div>
    <!-- Wrapper / End -->

    <!-- Scripts
================================================== -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery-migrate-3.0.0.min.js"></script>
    <script src="js/mmenu.min.js"></script>
    <script src="js/tippy.all.min.js"></script>
    <script src="js/simplebar.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/snackbar.js"></script>
    <script src="js/clipboard.min.js"></script>
    <script src="js/counterup.min.js"></script>
    <script src="js/magnific-popup.min.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/custom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.js"></script>

    <!-- Snackbar // documentation: https://www.polonel.com/snackbar/ -->
    <script>
    // Snackbar for user status switcher
    $('#snackbar-user-status label').click(function() {
        Snackbar.show({
            text: 'Your status has been changed!',
            pos: 'bottom-center',
            showAction: false,
            actionText: "Dismiss",
            duration: 3000,
            textColor: '#fff',
            backgroundColor: '#383838'
        });
    });
    </script>

    <!-- Google Autocomplete -->
    <script>
    function initAutocomplete() {
        var options = {
            types: ['(cities)'],
            // componentRestrictions: {country: "us"}
        };

        var input = document.getElementById('autocomplete-input');
        var autocomplete = new google.maps.places.Autocomplete(input, options);
    }
    </script>


    <script>
    var minPrice = <?php echo $minPrice; ?>;
    var maxPrice = <?php echo $maxPrice; ?>;
    var initialMin = <?php echo $currentMin; ?>;
    var initialMax = <?php echo $currentMax; ?>;

    var priceSlider = document.getElementById('price-range-slider');

    noUiSlider.create(priceSlider, {
        start: [initialMin, initialMax],
        connect: true,
        range: {
            'min': minPrice,
            'max': maxPrice
        },
        step: 1,
        format: {
            to: function(value) {
                return Math.round(value);
            },
            from: function(value) {
                return Number(value);
            }
        }
    });

    priceSlider.noUiSlider.on('update', function(values, handle) {
        document.getElementById('price-min').innerHTML = values[0] + "€";
        document.getElementById('price-max').innerHTML = values[1] + "€";
        document.getElementById('min_price').value = values[0];
        document.getElementById('max_price').value = values[1];
    });

    priceSlider.noUiSlider.on('change', function() {
        document.getElementById('filterForm').submit();
    });
    </script>




    <!-- Google API & Maps -->
    <!-- Geting an API Key: https://developers.google.com/maps/documentation/javascript/get-api-key -->
    <script src="https://maps.googleapis.com/maps/api/js?key=&libraries=places"></script>

</body>

</html>