<?php
// reserve.php
require_once 'header.php';
?>

<main class="main-content">
    <div id="vehicle-detail-container">
        <div class="loading-indicator">
            <i class="fas fa-spinner fa-spin"></i> Loading vehicle details...
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>

    <script>
        // 本地存储处理
        const FORM_KEY = 'car_rental_form_data';
        
        // 获取URL参数
        function getQueryParam(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        // 加载车辆详情
        function loadVehicleDetails() {
            const container = document.getElementById('vehicle-detail-container');
            const carId = getQueryParam('id');
            const storedCar = localStorage.getItem('last_viewed_car');

            // 情况1：有本地存储数据且无URL参数
            if (!carId && storedCar) {
                try {
                    const carData = JSON.parse(storedCar);
                    renderVehicleDetails(carData);
                    return;
                } catch (e) {
                    console.error('Error parsing local storage data:', e);
                    localStorage.removeItem('last_viewed_car');
                }
            }

            // 情况2：有URL参数（优先使用最新参数）
            if (carId) {
                fetchVehicleFromAPI(carId);
                return;
            }

            // 情况3：无任何可用数据
            showNoVehicleMessage();
        }
        function fetchVehicleFromAPI(carId) {
            const container = document.getElementById('vehicle-detail-container');
            
            container.innerHTML = `
                <div class="loading-indicator">
                    <i class="fas fa-spinner fa-spin"></i> Loading vehicle details...
                </div>
            `;

            fetch(`../api/get_vehicle_details.php?id=${carId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success' && data.data) {
                        // 保存到本地存储并渲染
                        localStorage.setItem('last_viewed_car', JSON.stringify(data.data));
                        renderVehicleDetails(data.data);
                    } else {
                        showNoVehicleMessage();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `
                        <section class="no-vehicle">
                            <div class="container">
                                <div class="warning-box">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <h3>Error loading vehicle details</h3>
                                    <p>${error.message}</p>
                                    <a href="index.php" class="btn btn-primary">Return to vehicle list</a>
                                </div>
                            </div>
                        </section>
                    `;
                });
        }
        // 渲染车辆详情
        function renderVehicleDetails(car) {
        const container = document.getElementById('vehicle-detail-container');
        
        // 基础车辆信息部分
        let html = `
            <section class="vehicle-detail">
                <div class="container">
                    <div class="detail-card">
                        <div class="detail-images">
                            <img src="../${car.images}" alt="${car.name}">
                        </div>
                        <div class="detail-info">
                            <h2>${car.name}</h2>
                            <div class="detail-specs">
                                <div class="spec-item">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <div>
                                    <span>MILEAGE</span>
                                        <strong>${car.code} km</strong>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-chair"></i>
                                    <div>
                                    <span>SEATS</span>
                                        <strong>${car.stock} seat</strong>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-gas-pump"></i>
                                    <div>
                                    <span>FUEL</span>
                                        <strong>${car.fuel || 'gasoline'}</strong>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                    <span>Production Year</span>
                                        <strong>${car.year || '2023'}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="detail-price">
                            <span>Production Year</span>
                                <span class="price-value">¥${car.price}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        `;

        // 检查车辆状态
        if (car.status == 0) {
            // 车辆不可用
            html += `
                <section class="unavailable-section">
                    <div class="container">
                        <div class="warning-box">
                            <i class="fas fa-times-circle" style="color: #ff4757; font-size: 3rem;"></i>
                            <h3>The vehicle is currently not available for rent</h3>
                            <a href="index.php" class="btn btn-primary">
                            View other available vehicles
                            </a>
                        </div>
                    </div>
                </section>
            `;
        } else {
            // 车辆可用 - 显示预订表单
            html += `
                <section class="booking-section">
                    <div class="container">
                        <h2>Vehicle reservation</h2>
                        <form id="booking-form">
                            <div class="form-columns">
                                <div class="form-col">
                                    <div class="form-group">
                                        <label for="full-name">Full Name *</label>
                                        <input type="text" id="full-name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone *</label>
                                        <input type="tel" id="phone" pattern="[0-9]{10,11}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" id="email" required>
                                    </div>
                                </div>
        
                                <!-- 租赁信息列 -->
                                <div class="form-col">
                                    <div class="form-group">
                                        <label for="license">License Number *</label>
                                        <input type="text" id="license" pattern="\\d{17}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="start-date">Lease Start Date *</label>
                                        <input type="date" id="start-date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="days">Lease Days *</label>
                                        <input type="number" id="days" min="1" value="1" required>
                                    </div>
                                </div>
                            </div>
                            <div class="price-summary">
                                <h3>Cost Details</h3>
                                <div class="price-row">
                                    <span>Daily rent</span>
                                    <span class="daily-price">¥${car.price}</span>
                                </div>
                                <div class="price-row total">
                                    <span>Estimated Total Price</span>
                                    <span class="total-price">¥${car.price}</span>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit Order</button>
                            </div>
                        </form>
                    </div>
                </section>
            `;
        }

        container.innerHTML = html;

        // 如果车辆可用，初始化表单交互
        if (car.status != 0) {
            initFormInteraction(car.price, car.id);
        }
    }


        // 显示无车辆信息
        function showNoVehicleMessage() {
            const container = document.getElementById('vehicle-detail-container');
            container.innerHTML = `
                <section class="no-vehicle">
                    <div class="container">
                        <div class="warning-box">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h3>No vehicle has been selected yet</h3>
                            <p>Please return to the homepage and select the vehicle you want to rent</p>
                            <a href="index.php" class="btn btn-primary">Return to vehicle list</a>
                        </div>
                    </div>
                </section>
            `;
        }

        // 初始化表单交互
        function initFormInteraction(dailyPrice, carId) {
            // 验证规则
            const validationRules = {
                'full-name': {
                    validate: (value) => value.trim() !== '',
                    message: 'Please enter your full name'
                },
                'phone': {
                    validate: (value) => /^\d{10,11}$/.test(value.trim()),
                    message: 'Please enter a valid 10-11 digit phone number'
                },
                'email': {
                    validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim()),
                    message: 'Please enter a valid email address'
                },
                'license': {
                    validate: (value) => /^\d{17}$/.test(value.trim()),
                    message: 'License number must be 17 digits'
                },
                'start-date': {
                    validate: (value) => new Date(value) > new Date(),
                    message: 'Start date must be in the future'
                },
                'days': {
                    validate: (value) => parseInt(value) >= 1,
                    message: 'Must rent for at least 1 day'
                }
            };

            // 为每个输入字段创建错误提示元素
            function createErrorElements() {
                const formGroups = document.querySelectorAll('.form-group');
                formGroups.forEach(group => {
                    const inputId = group.querySelector('input').id;
                    if (inputId && validationRules[inputId]) {
                        const errorElement = document.createElement('div');
                        errorElement.className = 'error-message';
                        errorElement.id = `${inputId}-error`;
                        group.appendChild(errorElement);
                    }
                });
            }

            // 验证单个字段
            function validateField(input) {
                const inputId = input.id;
                const value = input.value;
                const rule = validationRules[inputId];
                const errorElement = document.getElementById(`${inputId}-error`);

                if (!rule) return true;

                const isValid = rule.validate(value);
                
                if (errorElement) {
                    if (!isValid && value.trim() !== '') {
                        errorElement.textContent = rule.message;
                        errorElement.style.display = 'block';
                        input.classList.add('error-input');
                        input.classList.remove('valid-input');
                    } else if (value.trim() === '') {
                        errorElement.style.display = 'none';
                        input.classList.remove('error-input', 'valid-input');
                    } else {
                        errorElement.style.display = 'none';
                        input.classList.remove('error-input');
                        input.classList.add('valid-input');
                    }
                }

                return isValid;
            }

            // 验证整个表单
            function validateForm() {
                let isValid = true;
                const inputs = document.querySelectorAll('#booking-form input:required');
                
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });

                const submitBtn = document.querySelector('#booking-form button[type="submit"]');
                submitBtn.disabled = !isValid;
                submitBtn.style.backgroundColor = isValid ? '#369' : '#ccc';
                
                return isValid;
            }

            // 初始化表单交互
            function setupFormInteraction() {
                createErrorElements();
                
                // 绑定输入事件
                document.querySelectorAll('#booking-form input').forEach(input => {
                    input.addEventListener('input', () => {
                        validateField(input);
                        validateForm();
                        saveFormData();
                        
                        // 特殊处理：天数变化时重新计算价格
                        if (input.id === 'days') {
                            calculateTotal();
                        }
                    });
                    
                    // 失去焦点时验证
                    input.addEventListener('blur', () => {
                        validateField(input);
                        validateForm();
                    });
                });

                // 表单提交处理
                // document.getElementById('booking-form').addEventListener('submit', (e) => {
                //     e.preventDefault();
                    
                //     if (validateForm()) {
                //         localStorage.removeItem(FORM_KEY);
                //         alert('Order submitted successfully!');
                //         window.location.href = 'index.php';
                //     }
                // });
              // 修改表单提交处理
                    document.getElementById('booking-form').addEventListener('submit', async (e) => {
                        e.preventDefault();
                        
                        if (!validateForm()) return;

                        try {
                            // 使用FormData收集表单数据
                            const formData = new FormData(document.getElementById('booking-form'));
                            console.log(formData);
                            formData.append('pid', carId);
                            formData.append('fullname', document.getElementById('full-name').value);
                            formData.append('email', document.getElementById('email').value);
                            formData.append('phone', document.getElementById('phone').value);
                            formData.append('LicenseNumber', document.getElementById('license').value);
                            formData.append('LeaseStartDate', document.getElementById('start-date').value);
                            
                            // 计算总价并添加
                            const days = parseInt(document.getElementById('days').value);
                            formData.append('price', (dailyPrice * days).toString());
                            // console.log(formData);
                            
                            // 提交订单数据
                            const response = await fetch('../api/submit_order.php', {
                                method: 'POST',
                                body: formData
                            });

                            const result = await response.json();

                            if (result.status === 'success') {
                                localStorage.removeItem(FORM_KEY);
                                alert('Order submitted successfully!');
                                window.location.href = 'orderSuccess.php';
                            } else {
                                throw new Error(result.message || 'Failed to submit order');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert(`Error submitting order: ${error.message}`);
                        }
                    });
            }

            // 自动保存表单数据
            function saveFormData() {
                const formData = {
                    fullName: document.getElementById('full-name').value,
                    phone: document.getElementById('phone').value,
                    email: document.getElementById('email').value,
                    license: document.getElementById('license').value,
                    startDate: document.getElementById('start-date').value,
                    days: document.getElementById('days').value
                };
                localStorage.setItem(FORM_KEY, JSON.stringify(formData));
            }

            // 加载保存的数据
            function loadFormData() {
                const savedData = localStorage.getItem(FORM_KEY);
                if (savedData) {
                    const data = JSON.parse(savedData);
                    document.getElementById('full-name').value = data.fullName || '';
                    document.getElementById('phone').value = data.phone || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('license').value = data.license || '';
                    document.getElementById('start-date').value = data.startDate || '';
                    document.getElementById('days').value = data.days || 1;
                    
                    // 加载后验证所有字段
                    setTimeout(() => {
                        validateForm();
                    }, 100);
                }
            }

            // 计算总价
            function calculateTotal() {
                const days = parseInt(document.getElementById('days').value) || 0;
                const totalPrice = (dailyPrice * days).toFixed(2);
                document.querySelector('.daily-price').textContent = `¥${dailyPrice}`;
                document.querySelector('.total-price').textContent = `¥${totalPrice}`;
            }

            // 初始化
            setupFormInteraction();
            loadFormData();
            calculateTotal();

            document.getElementById('cancel-btn').addEventListener('click', () => {
                if(confirm('Are you sure you want to cancel the reservation? All input content will be cleared')) {
                    localStorage.removeItem(FORM_KEY);
                    document.getElementById('booking-form').reset();
                    window.location.href = 'index.php';
                }
            });

            // 页面离开提示
            window.addEventListener('beforeunload', (e) => {
                const formData = localStorage.getItem(FORM_KEY);
                if (formData) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }

        // 页面加载时获取车辆详情
        document.addEventListener('DOMContentLoaded', loadVehicleDetails);
    </script>
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