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
                </select>

                <select class="search-select" name="brand" id="brand-select">
                    <option value="">Select Brand</option>
                </select>
                    <div class="autocomplete">
                        <input type="text" name="keyword" id="search-input" 
                            placeholder="what are you looking for?"   autocomplete="off"
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

    <main class="main-content">
        <section class="hero">
            <div class="container">
                <h1>Enjoy a journey of freedom and depart from here</h1>
                <a href="#" class="btn btn-primary">Rent Now</a>
            </div>
        </section>

        <section class="featured-products">
            <div class="container">
                <section id="frozen-foods">
                    <div class="product-grid" id="product-container">
                    </div>
                    <div class="loading-indicator" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> LOADING...
                    </div>
                    <div class="no-results" style="display: none;">
                        <h3>No vehicles found that meet the criteria</h3>
                    </div>
                </section>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 Car Rental. All rights reserved.</p>
        </div>
    </footer>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    const searchForm = document.getElementById('search-form');

    // 监听输入框内容变化
    searchInput.addEventListener('input', function() {
        const keyword = this.value.trim();
        
        if (keyword.length >= 1) { // 至少输入2个字符才触发搜索
            fetchSuggestions(keyword);
        } else {
            searchSuggestions.style.display = 'none';
        }
    });
    searchInput.addEventListener('input', function() {
        const keyword = this.value.trim();
        
        if (keyword.length >= 1) { // 至少输入2个字符才触发搜索
            fetchSuggestions(keyword);
        } else {
            searchSuggestions.style.display = 'none';
        }
    });
    // 点击建议项时填充搜索框并提交表单
    searchSuggestions.addEventListener('click', function(e) {
        if (e.target.tagName === 'LI') {
            searchInput.value = e.target.textContent.trim();
            searchSuggestions.style.display = 'none';
            searchForm.submit();
        }
    });
    document.addEventListener('click', function(e) {
        // 如果点击的不是搜索输入框或建议列表，则隐藏建议列表
        if (e.target !== searchInput && e.target !== searchSuggestions && 
            !searchForm.contains(e.target)) {
            searchSuggestions.style.display = 'none';
        }
    });
    // 获取搜索建议
    function fetchSuggestions(keyword) {
        fetch(`../api/get_suggestions.php?keyword=${encodeURIComponent(keyword)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    searchSuggestions.innerHTML = data.map(item => 
                        `<li class="ellipsis"><i class="fas fa-search"></i> ${item}</li>`
                    ).join('');
                    searchSuggestions.style.display = 'block';
                } else {
                    searchSuggestions.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                searchSuggestions.style.display = 'none';
            });
    }
});
</script>
<script>
// 初始化加载数据
document.addEventListener('DOMContentLoaded', function() {
    // 加载筛选条件和产品数据
    loadFilters().then(loadProducts);
    
    // 监听表单提交
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        loadProducts();
    });

    // 监听筛选条件变化
    document.querySelectorAll('.search-select').forEach(select => {
        select.addEventListener('change', loadProducts);
    });
});

// 加载筛选条件
async function loadFilters() {
    try {
        const response = await fetch('../api/get_filters.php');
        const result = await response.json();
        
        if (result.status === 'success') {
            // 填充类型选择框
            const typeSelect = document.getElementById('type-select');
            typeSelect.innerHTML = '<option value="">Select Type</option>' + 
                result.data.types.map(type => 
                    `<option value="${type.id}" ${getSelected('type', type.id)}>
                        ${escapeHtml(type.name)}
                    </option>`
                ).join('');
            
            // 填充品牌选择框
            const brandSelect = document.getElementById('brand-select');
            brandSelect.innerHTML = '<option value="">Select Brand</option>' + 
                result.data.brands.map(brand => 
                    `<option value="${brand.id}" ${getSelected('brand', brand.id)}>
                        ${escapeHtml(brand.name)}
                    </option>`
                ).join('');
        }
    } catch (error) {
        console.error('Failed to load filters:', error);
    }
}

// 辅助函数：检查是否选中
function getSelected(name, value) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name) == value ? 'selected' : '';
}

// 辅助函数：HTML转义
function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}


async function loadProducts() {
    const container = document.getElementById('product-container');
    const loading = document.querySelector('.loading-indicator');
    const noResults = document.querySelector('.no-results');

    // 显示加载状态
    loading.style.display = 'block';
    container.innerHTML = '';
    noResults.style.display = 'none';

    try {
        // 获取查询参数
        const params = new URLSearchParams({
            keyword: document.getElementById('search-input').value,
            brand: document.querySelector('select[name="brand"]').value,
            type: document.querySelector('select[name="type"]').value
        });

        const response = await fetch(`../api/get_products.php?${params}`);
        const result = await response.json();

        if (result.status === 'success' && result.data.length > 0) {
            container.innerHTML = renderProducts(result.data);
        } else {
            noResults.style.display = 'block';
        }
    } catch (error) {
        console.error('Load Fail:', error);
        noResults.innerHTML = '<h3>Data loading failed, please try again later</h3>';
        noResults.style.display = 'block';
    } finally {
        loading.style.display = 'none';
    }
}

function renderProducts(products) {
    return products.map(product => `
        <div class="product-card">
            <div class="product-image">
                <img src="../${product.images}">
            </div>
            <div class="product-info">
                <h3 class="product-title">${product.name}</h3>
                <div class="product-price">
                    <span class="current-price">$${product.price}/day</span>
                </div>
                <p class="product-brand">${product.stock} seats</p>
                <p class="product-stock in-stock">${product.code}km</p>
                <a href="reserve.php?id=${product.id}" class="add-to-cart ${product.status == 0 ? 'disabled' : ''}">
                    ${product.status == 0 ? 'Already Rented' : 'Rent'}
                </a>
            </div>
        </div>
    `).join('');
}
</script>
</body>
</html>