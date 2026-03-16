/**
 * RAMP-BENIN - Scripts JavaScript principaux
 */

// Initialisation générale
document.addEventListener('DOMContentLoaded', function() {
    // Menu Toggle (Hamburger) pour mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
            // Animation du hamburger
            const spans = menuToggle.querySelectorAll('span');
            if (sidebar.classList.contains('open')) {
                spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
            } else {
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
        
        // Fermer le menu en cliquant à l'extérieur (mobile uniquement)
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !menuToggle.contains(event.target) && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    const spans = menuToggle.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            }
        });
    }
    
    // Auto-dismiss des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        const closeBtn = alert.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }
        
        // Auto-hide après 5 secondes
        setTimeout(function() {
            if (alert.parentNode) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 300);
            }
        }, 5000);
    });
    
    // Gestion des checkboxes de sélection (partenaires et activités)
    const selectionCheckboxes = document.querySelectorAll('.selection-checkbox');
    selectionCheckboxes.forEach(function(checkbox) {
        // Mettre à jour l'état initial
        const selectionItem = checkbox.closest('.selection-item');
        if (checkbox.checked && selectionItem) {
            selectionItem.classList.add('checked');
        }
        
        // Gérer le changement d'état
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectionItem.classList.add('checked');
            } else {
                selectionItem.classList.remove('checked');
            }
        });
    });
    
    // Confirmation pour les actions de suppression
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });
    
    // Améliorer les tables pour mobile
    const tables = document.querySelectorAll('.table');
    tables.forEach(function(table) {
        if (window.innerWidth <= 768) {
            table.style.display = 'block';
            table.style.overflowX = 'auto';
            table.style.whiteSpace = 'nowrap';
        }
    });
    
    // Gestion du resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Fermer le menu sur desktop
            if (window.innerWidth > 768 && sidebar) {
                sidebar.classList.remove('open');
                if (menuToggle) {
                    const spans = menuToggle.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            }
        }, 250);
    });
});

// Fonction utilitaire pour fermer les modals
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}

// Fonction utilitaire pour ouvrir les modals
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Gestion du menu mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const menuToggle = document.getElementById('menuToggle');
    if (sidebar && menuToggle) {
        sidebar.classList.toggle('open');
        const spans = menuToggle.querySelectorAll('span');
        if (sidebar.classList.contains('open')) {
            spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
            spans[1].style.opacity = '0';
            spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
        } else {
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        }
    }
}

// Validation de formulaire
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#dc3545';
            field.addEventListener('input', function() {
                this.style.borderColor = '';
            });
        }
    });
    
    return isValid;
}

// Formatage automatique des montants
function formatAmount(input) {
    let value = input.value.replace(/\s/g, '');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    input.value = value;
}

// Formatage automatique des téléphones
function formatPhone(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 0) {
        value = value.match(/.{1,2}/g).join(' ');
    }
    input.value = value;
}

// Fonction utilitaire pour les confirmations
function confirmAction(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir effectuer cette action ?');
}
