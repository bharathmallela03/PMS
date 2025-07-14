/**
 * Custom JavaScript for Pharmacy Management System
 */

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Sidebar toggle for mobile
    $('#sidebarToggle').on('click', function() {
        $('.sidebar').toggleClass('show');
        $('.sidebar-overlay').toggleClass('show');
    });

    $('.sidebar-overlay').on('click', function() {
        $('.sidebar').removeClass('show');
        $(this).removeClass('show');
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert:not(.alert-permanent)').fadeOut('slow');
    }, 5000);

    // Confirmation dialogs
    $('.confirm-delete').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href') || $(this).data('url');
        const message = $(this).data('message') || 'Are you sure you want to delete this item?';
        
        if (confirm(message)) {
            if ($(this).data('method') === 'DELETE') {
                deleteResource(url);
            } else {
                window.location.href = url;
            }
        }
    });

    // CSRF token setup for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

/**
 * Show notification
 */
function showNotification(message, type = 'success', duration = 5000) {
    const types = {
        success: 'alert-success',
        error: 'alert-danger',
        warning: 'alert-warning',
        info: 'alert-info'
    };

    const notification = $(`
        <div class="alert ${types[type]} notification slide-in" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    ${message}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    `);

    $('body').append(notification);

    setTimeout(() => {
        notification.fadeOut('slow', function() {
            $(this).remove();
        });
    }, duration);
}

/**
 * Loading state management
 */
function setLoadingState(element, loading = true) {
    const $element = $(element);
    
    if (loading) {
        $element.prop('disabled', true);
        const originalText = $element.text();
        $element.data('original-text', originalText);
        $element.html('<span class="spinner me-2"></span>Loading...');
    } else {
        $element.prop('disabled', false);
        const originalText = $element.data('original-text');
        if (originalText) {
            $element.text(originalText);
        }
    }
}

/**
 * Delete resource with AJAX
 */
function deleteResource(url) {
    $.ajax({
        url: url,
        type: 'DELETE',
        success: function(response) {
            if (response.success) {
                showNotification(response.message || 'Item deleted successfully');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(response.message || 'Error deleting item', 'error');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showNotification(response?.message || 'Error deleting item', 'error');
        }
    });
}

/**
 * Medicine search functionality
 */
function initMedicineSearch() {
    $('#medicineSearch').on('input', function() {
        const query = $(this).val();
        
        if (query.length >= 2) {
            $.ajax({
                url: '/api/medicines/search',
                data: { q: query },
                success: function(medicines) {
                    displaySearchResults(medicines);
                }
            });
        } else {
            clearSearchResults();
        }
    });
}

function displaySearchResults(medicines) {
    const resultsContainer = $('#searchResults');
    resultsContainer.empty();

    if (medicines.length > 0) {
        medicines.forEach(medicine => {
            const item = $(`
                <div class="search-result-item p-2 border-bottom" data-medicine-id="${medicine.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${medicine.name}</strong><br>
                            <small class="text-muted">${medicine.brand} - ₹${medicine.price}</small>
                        </div>
                        <span class="badge bg-${medicine.quantity > 0 ? 'success' : 'danger'}">
                            ${medicine.quantity > 0 ? 'In Stock' : 'Out of Stock'}
                        </span>
                    </div>
                </div>
            `);
            
            item.on('click', function() {
                selectMedicine(medicine);
            });
            
            resultsContainer.append(item);
        });
        
        resultsContainer.show();
    } else {
        resultsContainer.html('<div class="p-2 text-muted">No medicines found</div>').show();
    }
}

function clearSearchResults() {
    $('#searchResults').hide().empty();
}

/**
 * Cart functionality
 */
function addToCart(medicineId, quantity = 1) {
    setLoadingState('#addToCartBtn', true);
    
    $.ajax({
        url: '/customer/cart/add',
        type: 'POST',
        data: {
            medicine_id: medicineId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                showNotification(response.message);
                updateCartCount(response.cart_count);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showNotification(response?.message || 'Error adding to cart', 'error');
        },
        complete: function() {
            setLoadingState('#addToCartBtn', false);
        }
    });
}

function updateCartQuantity(cartItemId, quantity) {
    $.ajax({
        url: `/customer/cart/${cartItemId}`,
        type: 'PUT',
        data: { quantity: quantity },
        success: function(response) {
            if (response.success) {
                $(`#subtotal-${cartItemId}`).text('₹' + response.subtotal.toFixed(2));
                updateCartTotal();
                showNotification(response.message);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showNotification(response?.message || 'Error updating cart', 'error');
        }
    });
}

function removeFromCart(cartItemId) {
    if (confirm('Are you sure you want to remove this item from cart?')) {
        $.ajax({
            url: `/customer/cart/${cartItemId}`,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $(`#cart-item-${cartItemId}`).fadeOut('slow', function() {
                        $(this).remove();
                    });
                    updateCartCount(response.cart_count);
                    updateCartTotal();
                    showNotification(response.message);
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showNotification(response?.message || 'Error removing from cart', 'error');
            }
        });
    }
}

function updateCartCount(count) {
    $('.cart-count').text(count);
    if (count > 0) {
        $('.cart-count').show();
    } else {
        $('.cart-count').hide();
    }
}

function updateCartTotal() {
    let total = 0;
    $('.cart-item').each(function() {
        const quantity = parseInt($(this).find('.quantity-input').val());
        const price = parseFloat($(this).data('price'));
        total += quantity * price;
    });
    $('#cartTotal').text('₹' + total.toFixed(2));
}

/**
 * Stock management
 */
function updateStock(medicineId, action, quantity) {
    $.ajax({
        url: `/pharmacist/medicines/${medicineId}/update-stock`,
        type: 'POST',
        data: {
            action: action,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                $(`#stock-${medicineId}`).text(response.new_quantity);
                showNotification(response.message);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showNotification(response?.message || 'Error updating stock', 'error');
        }
    });
}

/**
 * Order management
 */
function updateOrderStatus(orderId, status) {
    $.ajax({
        url: `/pharmacist/orders/${orderId}/status`,
        type: 'PUT',
        data: { status: status },
        success: function(response) {
            if (response.success) {
                $(`#order-status-${orderId}`).html(`<span class="badge bg-${getStatusColor(status)}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`);
                showNotification(response.message);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showNotification(response?.message || 'Error updating order status', 'error');
        }
    });
}

function getStatusColor(status) {
    const colors = {
        pending: 'warning',
        confirmed: 'info',
        processing: 'primary',
        shipped: 'secondary',
        delivered: 'success',
        cancelled: 'danger'
    };
    return colors[status] || 'secondary';
}

/**
 * Billing functionality
 */
let billingItems = [];

function addToBill(medicineId) {
    $.ajax({
        url: `/api/medicines/${medicineId}/stock`,
        success: function(medicine) {
            if (medicine.is_available) {
                const existingItem = billingItems.find(item => item.medicine_id === medicineId);
                
                if (existingItem) {
                    existingItem.quantity++;
                } else {
                    billingItems.push({
                        medicine_id: medicineId,
                        name: medicine.name,
                        price: medicine.price,
                        quantity: 1
                    });
                }
                
                updateBillingTable();
            } else {
                showNotification('Medicine is out of stock', 'error');
            }
        }
    });
}

function updateBillingTable() {
    const tbody = $('#billingTable tbody');
    tbody.empty();
    
    let total = 0;
    
    billingItems.forEach((item, index) => {
        const subtotal = item.quantity * item.price;
        total += subtotal;
        
        const row = $(`
            <tr>
                <td>${item.name}</td>
                <td>₹${item.price.toFixed(2)}</td>
                <td>
                    <div class="input-group" style="width: 120px;">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateBillingQuantity(${index}, -1)">-</button>
                        <input type="number" class="form-control text-center" value="${item.quantity}" min="1" onchange="setBillingQuantity(${index}, this.value)">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateBillingQuantity(${index}, 1)">+</button>
                    </div>
                </td>
                <td>₹${subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeBillingItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
        
        tbody.append(row);
    });
    
    $('#billingTotal').text('₹' + total.toFixed(2));
}

function updateBillingQuantity(index, change) {
    billingItems[index].quantity = Math.max(1, billingItems[index].quantity + change);
    updateBillingTable();
}

function setBillingQuantity(index, quantity) {
    billingItems[index].quantity = Math.max(1, parseInt(quantity));
    updateBillingTable();
}

function removeBillingItem(index) {
    billingItems.splice(index, 1);
    updateBillingTable();
}

function generateInvoice() {
    if (billingItems.length === 0) {
        showNotification('Please add items to generate invoice', 'warning');
        return;
    }
    
    const formData = {
        customer_name: $('#customerName').val(),
        customer_phone: $('#customerPhone').val(),
        customer_address: $('#customerAddress').val(),
        payment_method: $('#paymentMethod').val(),
        items: billingItems,
        discount_amount: parseFloat($('#discountAmount').val()) || 0,
        tax_rate: parseFloat($('#taxRate').val()) || 0,
        notes: $('#notes').val()
    };
    
    setLoadingState('#generateInvoiceBtn', true);
    
    $.ajax({
        url: '/pharmacist/billing/generate-invoice',
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                showNotification('Invoice generated successfully');
                window.location.href = response.redirect_url;
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showNotification(response?.message || 'Error generating invoice', 'error');
        },
        complete: function() {
            setLoadingState('#generateInvoiceBtn', false);
        }
    });
}

/**
 * File upload with preview
 */
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result).show();
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Data table initialization
 */
function initDataTable(selector, options = {}) {
    const defaultOptions = {
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    };
    
    return $(selector).DataTable({...defaultOptions, ...options});
}

/**
 * Number formatting
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(amount);
}

function formatNumber(number, decimals = 2) {
    return parseFloat(number).toFixed(decimals);
}

/**
 * Date formatting
 */
function formatDate(date, format = 'DD/MM/YYYY') {
    return moment(date).format(format);
}

/**
 * Form validation
 */
function validateForm(formSelector) {
    const form = $(formSelector);
    let isValid = true;
    
    form.find('[required]').each(function() {
        const field = $(this);
        const value = field.val().trim();
        
        if (!value) {
            field.addClass('is-invalid');
            isValid = false;
        } else {
            field.removeClass('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Export functionality
 */
function exportData(url, filename) {
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Print functionality
 */
function printElement(elementId) {
    const element = document.getElementById(elementId);
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Print</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="/css/custom.css" rel="stylesheet">
                <style>
                    body { margin: 0; padding: 20px; }
                    @media print {
                        body { margin: 0; padding: 0; }
                    }
                </style>
            </head>
            <body>
                ${element.innerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

/**
 * Local storage helpers
 */
function saveToStorage(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
}

function getFromStorage(key) {
    const data = localStorage.getItem(key);
    return data ? JSON.parse(data) : null;
}

function removeFromStorage(key) {
    localStorage.removeItem(key);
}

/**
 * API helpers
 */
function apiRequest(url, method = 'GET', data = null) {
    return $.ajax({
        url: url,
        type: method,
        data: data,
        dataType: 'json'
    });
}

/**
 * Utility functions
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}