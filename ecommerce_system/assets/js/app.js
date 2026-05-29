const CURRENCY_SYMBOL = 'KSh ';

// --- Dark Mode Toggle ---
(function() {
    const STORAGE_KEY = 'smartshop-dark-mode';
    const html = document.documentElement;

    function setTheme(dark) {
        if (dark) {
            html.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem(STORAGE_KEY, '1');
        } else {
            html.removeAttribute('data-bs-theme');
            localStorage.setItem(STORAGE_KEY, '0');
        }
        updateIcon();
    }

    function updateIcon() {
        const icon = document.querySelector('.dark-mode-icon');
        if (!icon) return;
        const isDark = html.getAttribute('data-bs-theme') === 'dark';
        icon.className = isDark ? 'bi bi-sun-fill dark-mode-icon' : 'bi bi-moon-fill dark-mode-icon';
    }

    // Restore preference
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored === '1') {
        html.setAttribute('data-bs-theme', 'dark');
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateIcon();
        document.querySelector('.dark-mode-toggle')?.addEventListener('click', function() {
            const isDark = html.getAttribute('data-bs-theme') === 'dark';
            setTheme(!isDark);
        });
    });
})();

document.addEventListener('DOMContentLoaded', function() {

    // --- Search Autocomplete ---
    const searchInput = document.querySelector('.search-input');
    const suggestionsBox = document.querySelector('.search-suggestions');

    if (searchInput && suggestionsBox) {
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                suggestionsBox.classList.remove('show');
                return;
            }

            debounceTimer = setTimeout(() => {
                const autocompleteUrl = this.dataset.autocompleteUrl;
                if (!autocompleteUrl) return;

                fetch(autocompleteUrl + '?q=' + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        suggestionsBox.innerHTML = '';
                        if (data.length === 0) {
                            suggestionsBox.classList.remove('show');
                            return;
                        }
                        data.forEach(product => {
                            const item = document.createElement('a');
                            item.className = 'suggestion-item text-decoration-none text-dark';
                            item.href = APP_BASE_URL + '/product/' + product.slug;
                            const imgSrc = product.image_primary && product.image_primary.startsWith('http')
                                ? product.image_primary
                                : APP_BASE_URL + '/assets/placeholder.php?w=40&h=40&text=N';
                            const price = CURRENCY_SYMBOL + parseFloat(product.price).toFixed(2);
                            item.innerHTML = `
                                <img src="${imgSrc}" alt="${product.name}" loading="lazy">
                                <div class="flex-grow-1">
                                    <small class="fw-medium d-block">${product.name}</small>
                                    <small class="text-muted">${price}</small>
                                </div>
                            `;
                            suggestionsBox.appendChild(item);
                        });
                        suggestionsBox.classList.add('show');
                    })
                    .catch(() => { suggestionsBox.classList.remove('show'); });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.remove('show');
            }
        });
    }

    // --- Add to Wishlist (AJAX) ---
    document.querySelectorAll('.add-to-wishlist').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            if (!productId) return;

            const icon = this.tagName === 'I' ? this : this.querySelector('i');

            fetch(APP_BASE_URL + '/wishlist/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + productId
            })
            .then(res => res.json())
            .then(data => {
                if (data.action === 'added') {
                    icon.className = 'bi bi-heart-fill text-danger';
                    showToast('Added to wishlist!', 'success');
                } else if (data.action === 'removed') {
                    icon.className = 'bi bi-heart';
                    showToast('Removed from wishlist', 'info');
                } else if (!data.success) {
                    if (data.message === 'Please login') {
                        window.location.href = APP_BASE_URL + '/login';
                    }
                }
            })
            .catch(() => showToast('Something went wrong', 'danger'));
        });
    });

    // --- Add to Cart (AJAX) ---
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('cartCount');
                    if (badge && data.count !== undefined) badge.textContent = data.count;
                    showToast(data.message || 'Added to cart!', 'success');
                } else {
                    showToast(data.message || 'Failed to add', 'danger');
                }
            })
            .catch(() => showToast('Something went wrong', 'danger'));
        });
    });

    // --- Chatbot Widget ---
    const chatbotToggle = document.querySelector('.chatbot-toggle');
    const chatbotBox = document.querySelector('.chatbot-box');
    const chatbotMessages = document.querySelector('.chatbot-messages');
    const chatbotInput = document.querySelector('.chatbot-input input');
    const chatbotSend = document.querySelector('.chatbot-send');
    const quickReplyContainer = document.querySelector('.quick-reply-container');

    if (chatbotToggle && chatbotBox) {
        chatbotToggle.addEventListener('click', function() {
            chatbotBox.classList.toggle('show');
            if (chatbotBox.classList.contains('show') && chatbotMessages && chatbotMessages.children.length <= 1) {
                addChatbotMessage('bot', "Hello! I'm SmartShop AI assistant. How can I help you today?");
                addQuickReplies(['Show me featured products', 'Track my order', 'Shipping information']);
            }
        });

        if (chatbotSend && chatbotInput) {
            function sendMessage() {
                const msg = chatbotInput.value.trim();
                if (!msg) return;
                addChatbotMessage('user', msg);
                chatbotInput.value = '';

                const typingDiv = document.createElement('div');
                typingDiv.className = 'd-flex';
                typingDiv.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
                chatbotMessages.appendChild(typingDiv);
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

                fetch(APP_BASE_URL + '/ai/chatbot.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: msg, session_id: getSessionId() })
                })
                .then(res => res.json())
                .then(data => {
                    typingDiv.remove();
                    if (data.success) {
                        addChatbotMessage('bot', data.message);
                        if (data.quick_replies && data.quick_replies.length > 0) {
                            addQuickReplies(data.quick_replies);
                        }
                    } else {
                        addChatbotMessage('bot', "Sorry, I encountered an error. Please try again.");
                    }
                })
                .catch(() => {
                    typingDiv.remove();
                    addChatbotMessage('bot', "Connection error. Please try again.");
                });
            }

            chatbotSend.addEventListener('click', sendMessage);
            chatbotInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') sendMessage();
            });
        }
    }

    function addChatbotMessage(type, text) {
        const div = document.createElement('div');
        div.className = 'chatbot-message ' + type + ' d-flex flex-column';
        div.innerHTML = text;
        chatbotMessages.appendChild(div);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    function addQuickReplies(replies) {
        if (!quickReplyContainer) return;
        quickReplyContainer.innerHTML = '';
        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex flex-wrap gap-1 px-3 pb-2';
        replies.forEach(reply => {
            const btn = document.createElement('button');
            btn.className = 'btn btn-outline-primary btn-sm quick-reply-btn';
            btn.textContent = reply;
            btn.addEventListener('click', function() {
                if (chatbotInput) {
                    chatbotInput.value = reply;
                    if (chatbotSend) chatbotSend.click();
                }
            });
            wrapper.appendChild(btn);
        });
        quickReplyContainer.appendChild(wrapper);
    }

    function getSessionId() {
        let sid = sessionStorage.getItem('chat_session_id');
        if (!sid) {
            sid = 'sess_' + Math.random().toString(36).substr(2, 9) + Date.now().toString(36);
            sessionStorage.setItem('chat_session_id', sid);
        }
        return sid;
    }

    // --- Toast Notifications ---
    function showToast(message, type) {
        const container = document.getElementById('toastContainer');
        if (!container) {
            const c = document.createElement('div');
            c.id = 'toastContainer';
            c.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999';
            document.body.appendChild(c);
        }
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-bg-' + type + ' border-0 show';
        toast.role = 'alert';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.getElementById('toastContainer').appendChild(toast);
        setTimeout(() => toast.remove(), 4000);

        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            new bootstrap.Toast(toast).show();
        }
    }

    // --- Cart Quantity Input Handlers ---
    document.querySelectorAll('.cart-qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input[type="number"]');
            if (!input) return;
            const step = parseInt(this.dataset.step || (this.classList.contains('btn-outline-secondary') ? -1 : 1));
            let val = parseInt(input.value) + step;
            val = Math.max(parseInt(input.min || 1), Math.min(val, parseInt(input.max || 99)));
            input.value = val;
            input.dispatchEvent(new Event('change'));
        });
    });

    // --- SweetAlert2 Delete Confirmations ---
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (typeof Swal === 'undefined') return;
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });

    // --- Auto-dismiss alerts ---
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // --- Image fallback ---
    var phBase = APP_BASE_URL + '/assets/placeholder.php';

    document.querySelectorAll('img').forEach(function(img) {
        img.addEventListener('error', function() {
            if (!this.dataset.fallback) {
                this.dataset.fallback = '1';
                this.src = phBase + '?w=' + (this.width || 300) + '&h=' + (this.height || 300) + '&text=No+Image';
            }
        });
    });

    // Fallback for CSS background-images (product-img-wrapper)
    document.querySelectorAll('.product-img-wrapper').forEach(function(wrapper) {
        var bg = wrapper.style.backgroundImage;
        if (!bg || bg === 'none') return;
        var m = bg.match(/url\(["']?([^"')]+)["']?\)/);
        if (!m) return;
        var imgUrl = m[1];
        var testImg = new Image();
        testImg.addEventListener('error', function() {
            wrapper.style.backgroundImage = 'url(' + phBase + '?w=' + (wrapper.offsetWidth || 300) + '&h=' + (wrapper.offsetHeight || 400) + '&text=Product+Image)';
        });
        testImg.src = imgUrl;
    });

    // --- Shipping: Delivery Option Selection Styling ---
    document.querySelectorAll('.delivery-option-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.delivery-option-card').forEach(card => {
                card.classList.remove('selected');
            });
            if (this.checked) {
                this.closest('.delivery-option-card').classList.add('selected');
            }
        });
    });

    // --- Shipping: Live Cart Summary Update via AJAX ---
    const shippingNotification = document.getElementById('shippingNotification');
    const shippingDisplay = document.getElementById('shippingDisplay');
    const cartTotal = document.getElementById('cartTotal');

    if (shippingNotification && shippingDisplay) {
        fetch(APP_BASE_URL + '/api/shipping/calculate')
            .then(res => res.json())
            .then(data => {
                if (data && !data.error) {
                    if (data.is_free_shipping) {
                        shippingDisplay.innerHTML = '<span class="text-success fw-bold">FREE</span>';
                        shippingDisplay.className = 'fw-medium text-success';
                        shippingNotification.className = 'shipping-notification free-shipping';
                        shippingNotification.innerHTML = '<i class="bi bi-truck"></i><span>Congratulations! Your order qualifies for <strong>FREE SHIPPING</strong>.</span>';
                    } else {
                        shippingDisplay.textContent = CURRENCY_SYMBOL + data.shipping_cost.toFixed(2);
                        shippingDisplay.className = 'fw-medium';
                        shippingNotification.className = 'shipping-notification no-free-shipping';
                        shippingNotification.innerHTML = '<i class="bi bi-info-circle"></i><span>Add <strong class="text-primary">' + CURRENCY_SYMBOL + data.remaining.toFixed(2) + '</strong> more to qualify for <strong>FREE SHIPPING</strong>.</span>';
                    }
                    if (cartTotal) {
                        const total = data.subtotal + data.shipping_cost;
                        cartTotal.textContent = CURRENCY_SYMBOL + total.toFixed(2);
                    }
                }
            })
            .catch(() => {});
    }

});
