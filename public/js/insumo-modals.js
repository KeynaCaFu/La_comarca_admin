// Gestión de Modales para Insumos
class InsumoModals {
    constructor() {
        this.initEventListeners();
    }

    // Inicializar event listeners
    initEventListeners() {
        // Cerrar modal al hacer clic fuera de él
        window.addEventListener('click', (event) => {
            const modals = ['showModal', 'createModal', 'editModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && event.target === modal) {
                    this.closeModal(modalId);
                }
            });
        });

        // Cerrar modal con la tecla Escape
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const modals = ['showModal', 'createModal', 'editModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && modal.style.display === 'block') {
                        this.closeModal(modalId);
                    }
                });
            }
        });

        // Manejar envío de formularios
        document.addEventListener('submit', (event) => {
            if (event.target.id === 'createForm') {
                this.handleCreateSubmit(event);
            } else if (event.target.id === 'editForm') {
                this.handleEditSubmit(event);
            }
        });
    }

    // Abrir modal de crear
    openCreateModal() {
        const modal = document.getElementById('createModal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // Limpiar formulario
            const form = document.getElementById('createForm');
            if (form) {
                form.reset();
            }
        }
    }

    // Abrir modal de ver detalles
    async openShowModal(insumoId) {
        const modal = document.getElementById('showModal');
        const content = document.getElementById('showModalContent');
        
        if (!modal || !content) return;

        // Mostrar loading
        content.innerHTML = '<div class="loading">Cargando detalles...</div>';
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        try {
            const response = await fetch(`/insumos/${insumoId}/show-modal`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const html = await response.text();
            content.innerHTML = html;
        } catch (error) {
            console.error('Error loading modal content:', error);
            content.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error al cargar los detalles del insumo
                    <br><small>Por favor, inténtalo de nuevo</small>
                </div>
            `;
        }
    }

    // Abrir modal de editar
    openEditModal(insumoId) {
        const modal = document.getElementById('editModal');
        const content = document.getElementById('editModalContent');
        
        if (!modal || !content) return;

        // Mostrar loading
        content.innerHTML = '<div class="loading">Cargando formulario...</div>';
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        fetch(`/insumos/${insumoId}/edit-modal`)
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
                
                // Configurar validaciones después de cargar el contenido
                if (typeof setupEditValidations === 'function') {
                    setupEditValidations();
                }
            })
            .catch(error => {
                console.error('Error loading modal content:', error);
                content.innerHTML = `
                    <div class="error">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error al cargar el formulario de edición
                        <br><small>Por favor, inténtalo de nuevo</small>
                    </div>
                `;
            });
    }

    // Cerrar modal
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Limpiar validaciones cuando se cierre el modal
        if (modalId === 'editModal' && typeof clearValidations === 'function') {
            clearValidations('edit');
        } else if (modalId === 'createModal' && typeof clearValidations === 'function') {
            clearValidations('create');
        }
    }

    // Manejar envío del formulario de crear
    async handleCreateSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Deshabilitar botón y mostrar loading
        this.setButtonLoading(submitButton, true);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                this.closeModal('createModal');
                this.showNotification('success', data.message || 'Insumo creado exitosamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Error al crear el insumo');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('error', 'Error al crear el insumo. Por favor, inténtalo de nuevo.');
        } finally {
            this.setButtonLoading(submitButton, false);
        }
    }

    // Manejar envío del formulario de editar
    async handleEditSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Deshabilitar botón y mostrar loading
        this.setButtonLoading(submitButton, true);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                this.closeModal('editModal');
                this.showNotification('success', data.message || 'Insumo actualizado exitosamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Error al actualizar el insumo');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('error', 'Error al actualizar el insumo. Por favor, inténtalo de nuevo.');
        } finally {
            this.setButtonLoading(submitButton, false);
        }
    }

    // Establecer estado de loading en botón
    setButtonLoading(button, isLoading) {
        if (!button) return;
        
        if (isLoading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || button.innerHTML;
        }
    }

    // Mostrar notificación
    showNotification(type, message) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Agregar estilos si no existen
        if (!document.getElementById('notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'notification-styles';
            styles.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 10001;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    max-width: 400px;
                    animation: slideInRight 0.3s ease-out;
                }
                .notification-success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }
                .notification-error {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex: 1;
                }
                .notification-close {
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    opacity: 0.7;
                    padding: 0;
                    color: inherit;
                }
                .notification-close:hover {
                    opacity: 1;
                }
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(styles);
        }
        
        // Agregar al DOM
        document.body.appendChild(notification);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.insumoModals = new InsumoModals();
});

// Funciones globales para compatibilidad
function openCreateModal() {
    if (window.insumoModals) {
        window.insumoModals.openCreateModal();
    }
}

function openShowModal(insumoId) {
    if (window.insumoModals) {
        window.insumoModals.openShowModal(insumoId);
    }
}

// Función para abrir el modal de editar
function openEditModal(id) {
    fetch(`/insumos/${id}/edit-modal`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('editModalContent').innerHTML = html;
            document.getElementById('editModal').style.display = 'block';
            
            // Configurar validaciones después de cargar el contenido
            if (typeof setupEditValidations === 'function') {
                setupEditValidations();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al cargar el formulario de edición', 'error');
        });
}

function closeModal(modalId) {
    if (window.insumoModals) {
        window.insumoModals.closeModal(modalId);
    }
}