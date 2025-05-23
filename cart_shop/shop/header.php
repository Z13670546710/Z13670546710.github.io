<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    Car Rental
                </a>
            </div>
            <div class="search-bar">
                <form id="search-form" method="GET" action="index.php">
                    <select class="search-select" name="type" id="type-select">
                        <option value="">Select Type</option>
                        <?php
                        // 动态加载类型选项
                        require '../tools.func.php';
                        require '../db.func.php';
                        $prefix = getDBPrefix();
                        $types = query("SELECT * FROM {$prefix}product_type");
                        foreach ($types as $type) {
                            $selected = isset($_GET['type']) && $_GET['type'] == $type['id'] ? 'selected' : '';
                            echo "<option value='{$type['id']}' $selected>{$type['name']}</option>";
                        }
                        ?>
                    </select>

                    <select class="search-select" name="brand" id="brand-select">
                        <option value="">Select Brand</option>
                        <?php
                        // 动态加载品牌选项
                        $brands = query("SELECT * FROM {$prefix}product_brand");
                        foreach ($brands as $brand) {
                            $selected = isset($_GET['brand']) && $_GET['brand'] == $brand['id'] ? 'selected' : '';
                            echo "<option value='{$brand['id']}' $selected>{$brand['name']}</option>";
                        }
                        ?>
                    </select>
                    <div class="autocomplete">
                        <input type="text" name="keyword" id="search-input" 
                            placeholder="what are you looking for?" autocomplete="off"
                            value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                        <div id="suggestions" class="suggestions-box"></div>
                        <ul class="searchMList" id="search-suggestions" style="display: none;"></ul>
                    </div>
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container">
            <ul class="nav-list">
                <li class="hover"><a href="index.php">Home</a></li>
            </ul>
        </div>
    </nav>