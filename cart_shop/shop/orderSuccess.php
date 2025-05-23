<?php
require_once 'header.php';
?>
    <main class="main-content" style="min-height: 80vh;">
    <div class="confirmation-page">
                <div class="confirmation-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="confirmation-title">Order Confirmed!</h1>
                <div class="confirmation-actions">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>
            </div>
    </main>

   
<?php require_once 'footer.php'; ?>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    const searchForm = document.getElementById('search-form');

    // 输入事件处理
    searchInput.addEventListener('input', function() {
        const keyword = this.value.trim();
        if (keyword.length >= 1) {
            fetchSuggestions(keyword);
        } else {
            searchSuggestions.style.display = 'none';
        }
    });

    // 点击建议项处理
    searchSuggestions.addEventListener('click', function(e) {
        if (e.target.tagName === 'LI') {
            searchInput.value = e.target.textContent.trim();
            searchSuggestions.style.display = 'none';
            searchForm.submit();
        }
    });

    // 外部点击关闭建议
    document.addEventListener('click', function(e) {
        if (!searchForm.contains(e.target)) {
            searchSuggestions.style.display = 'none';
        }
    });

    // 获取建议数据
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
            });
    }
});
</script>
</body>
</html>