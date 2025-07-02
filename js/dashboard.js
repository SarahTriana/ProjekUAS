         document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const chevron = document.getElementById(id + '-chevron');
            dropdown.classList.toggle('show');
            chevron.classList.toggle('fa-chevron-down');
            chevron.classList.toggle('fa-chevron-up');
            
            document.querySelectorAll('.menu-dropdown').forEach(item => {
                if (item.id !== id && item.classList.contains('show')) {
                    item.classList.remove('show');
                    const otherChevron = document.getElementById(item.id + '-chevron');
                    if (otherChevron) {
                        otherChevron.classList.add('fa-chevron-down');
                        otherChevron.classList.remove('fa-chevron-up');
                    }
                }
            });
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.menu-item') && !event.target.closest('.menu-dropdown')) {
                document.querySelectorAll('.menu-dropdown').forEach(item => {
                    item.classList.remove('show');
                    const chevron = document.getElementById(item.id + '-chevron');
                    if (chevron) {
                        chevron.classList.add('fa-chevron-down');
                        chevron.classList.remove('fa-chevron-up');
                    }
                });
            }
        });

        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                if (!this.classList.contains('active') && !this.querySelector('.fa-chevron-down')) {
                    document.querySelectorAll('.menu-item').forEach(i => {
                        i.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });

        // Animation for stats cards
        const statCards = document.querySelectorAll('.stat-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        statCards.forEach(card => {
            observer.observe(card);
        });
 

        (function () {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const passwordInput = this.previousElementSibling;
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const input = this.value.toLowerCase();
    const rows = document.querySelectorAll('#studentTable tbody tr');
    
    let visibleCount = 0;
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(input)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('rowCount').textContent = visibleCount;
});

// Refresh button
document.getElementById('refreshBtn').addEventListener('click', function() {
    location.reload();
});




 