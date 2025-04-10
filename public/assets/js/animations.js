// public/assets/js/animations.js

document.addEventListener('DOMContentLoaded', function() {
    // Thêm class 'fade-in' cho các phần tử khi trang được tải
    const fadeElements = document.querySelectorAll('.fade-in-trigger');
    fadeElements.forEach(element => {
        element.classList.add('fade-in');
    });
    
    // Thêm hiệu ứng scroll reveal
    const scrollRevealElements = document.querySelectorAll('.scroll-reveal');
    
    // Hàm kiểm tra phần tử có trong viewport không
    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Hàm xử lý sự kiện scroll
    function handleScrollAnimation() {
        scrollRevealElements.forEach(element => {
            if (isElementInViewport(element)) {
                const animationClass = element.dataset.animation || 'fade-in';
                element.classList.add(animationClass);
            }
        });
    }
    
    // Đăng ký sự kiện scroll
    window.addEventListener('scroll', handleScrollAnimation);
    
    // Gọi hàm xử lý lần đầu khi trang được tải
    handleScrollAnimation();
    
    // Hiệu ứng hover cho các nút
    const hoverButtons = document.querySelectorAll('.btn-hover');
    hoverButtons.forEach(button => {
        button.addEventListener('mouseover', function() {
            this.classList.add('hover-effect');
        });
        
        button.addEventListener('mouseout', function() {
            this.classList.remove('hover-effect');
        });
    });
    
    // Hiệu ứng hover cho các card
    const hoverCards = document.querySelectorAll('.card-hover');
    hoverCards.forEach(card => {
        card.addEventListener('mouseover', function() {
            this.classList.add('hover-effect');
        });
        
        card.addEventListener('mouseout', function() {
            this.classList.remove('hover-effect');
        });
    });
    
    // Hiệu ứng ripple cho các nút
    const rippleButtons = document.querySelectorAll('.ripple');
    rippleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple-effect');
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Hiệu ứng counter
    const counterElements = document.querySelectorAll('.counter');
    counterElements.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const step = Math.ceil(target / (duration / 16)); // 60fps
        
        let current = 0;
        const updateCounter = () => {
            current += step;
            if (current >= target) {
                counter.textContent = target;
            } else {
                counter.textContent = current;
                requestAnimationFrame(updateCounter);
            }
        };
        
        if (isElementInViewport(counter)) {
            updateCounter();
        } else {
            window.addEventListener('scroll', function scrollHandler() {
                if (isElementInViewport(counter)) {
                    updateCounter();
                    window.removeEventListener('scroll', scrollHandler);
                }
            });
        }
    });
    
    // Hiệu ứng typing
    const typingElements = document.querySelectorAll('.typing');
    typingElements.forEach(element => {
        const text = element.textContent;
        element.textContent = '';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };
        
        if (isElementInViewport(element)) {
            typeWriter();
        } else {
            window.addEventListener('scroll', function scrollHandler() {
                if (isElementInViewport(element)) {
                    typeWriter();
                    window.removeEventListener('scroll', scrollHandler);
                }
            });
        }
    });
    
    // Hiệu ứng parallax
    const parallaxElements = document.querySelectorAll('.parallax');
    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            element.style.transform = `translateY(${scrollTop * speed}px)`;
        });
    });
    
    // Hiệu ứng toast message
    window.showToast = function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} show`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 500);
        }, 3000);
    };
});

// Thêm hiệu ứng loading
window.showLoading = function() {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    
    const spinner = document.createElement('div');
    spinner.className = 'spinner';
    
    overlay.appendChild(spinner);
    document.body.appendChild(overlay);
};

window.hideLoading = function() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
        overlay.classList.add('fade-out');
        setTimeout(() => {
            document.body.removeChild(overlay);
        }, 500);
    }
};

// Hàm tạo hiệu ứng số đếm
window.animateValue = function(id, start, end, duration) {
    const obj = document.getElementById(id);
    if (!obj) return;
    
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
};

// Hàm tạo hiệu ứng progress bar
window.animateProgressBar = function(id, value, duration) {
    const progressBar = document.getElementById(id);
    if (!progressBar) return;
    
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        progressBar.style.width = (progress * value) + '%';
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
};