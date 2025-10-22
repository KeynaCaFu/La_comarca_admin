// Gestión de Modales para Proveedores
class ProveedorModals {
    constructor() {
        this.initEventListeners();
    }

    // Inicializar event listeners
    initEventListeners() {
        // Cerrar modal al hacer clic fuera de él
        window.addEventListener('click', (event) => {
            const modals = ['showProveedorModal', 'createProveedorModal', 'editProveedorModal'];
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
                const modals = ['showProveedorModal', 'createProveedorModal', 'editProveedorModal'];
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
            if (event.target.id === 'createProveedorForm') {
                this.handleCreateSubmit(event);
            } else if (event.target.id === 'editProveedorForm') {
                this.handleEditSubmit(event);
            }
        });
    }

    // Abrir modal de crear
    openCreateModal() {
        const modal = document.getElementById('createProveedorModal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // Limpiar formulario
            const form = document.getElementById('createProveedorForm');
            if (form) {
                form.reset();
                // Resetear valor por defecto del total de compras
                const totalComprasInput = document.getElementById('create_proveedor_total_compras');
                if (totalComprasInput) {
                    totalComprasInput.value = '0';
                }
            }
        }
    }

    // Abrir modal de ver detalles
    async openShowModal(proveedorId) {
        const modal = document.getElementById('showProveedorModal');
        const content = document.getElementById('showProveedorModalContent');
        
        if (!modal || !content) return;

        // Mostrar loading
        content.innerHTML = '<div class="loading">Cargando detalles del proveedor...</div>';
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        try {
            const response = await fetch(`/proveedores/${proveedorId}/show-modal`);
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
                    Error al cargar los detalles del proveedor
                    <br><small>Por favor, inténtalo de nuevo</small>
                </div>
            `;
        }
    }

    // Abrir modal de editar
    async openEditModal(proveedorId) {
        console.log('Loading edit modal for proveedor:', proveedorId);
        
        const modal = document.getElementById('editProveedorModal');
        const content = document.getElementById('editProveedorModalContent');
        
        if (!modal || !content) {
            console.error('Modal elements not found');
            alert('Error: No se pudieron encontrar los elementos del modal.');
            return;
        }

        // Mostrar loading
        content.innerHTML = '<div class="loading text-center p-4"><i class="fas fa-spinner fa-spin"></i> Cargando formulario de edición...</div>';
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        try {
            const url = `/proveedores/${proveedorId}/edit-modal`;
            console.log('Fetching:', url);
            
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
            
            const html = await response.text();
            content.innerHTML = html;
            console.log('Modal content loaded successfully');
        } catch (error) {
            console.error('Error loading modal:', error);
            content.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h5>Error al cargar el formulario</h5>
                    <p>${error.message}</p>
                    <button class="btn btn-secondary" onclick="closeProveedorModal('editProveedorModal')">Cerrar</button>
                </div>
            `;
        }
    }

    // Cerrar modal
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
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
                this.closeModal('createProveedorModal');
                this.showNotification('success', data.message || 'Proveedor creado exitosamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Error al crear el proveedor');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('error', 'Error al crear el proveedor. Por favor, inténtalo de nuevo.');
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
                this.closeModal('editProveedorModal');
                this.showNotification('success', data.message || 'Proveedor actualizado exitosamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Error al actualizar el proveedor');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('error', 'Error al actualizar el proveedor. Por favor, inténtalo de nuevo.');
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
    window.proveedorModals = new ProveedorModals();
});

// Funciones globales para compatibilidad
function openCreateProveedorModal() {
    if (window.proveedorModals) {
        window.proveedorModals.openCreateModal();
    }
}

function openShowProveedorModal(proveedorId) {
    if (window.proveedorModals) {
        window.proveedorModals.openShowModal(proveedorId);
    }
}

function openEditProveedorModal(proveedorId) {
    console.log('Attempting to open edit modal for proveedor ID:', proveedorId);
    
    if (window.proveedorModals) {
        window.proveedorModals.openEditModal(proveedorId);
    } else {
        console.error('proveedorModals not initialized');
        alert('Error: Sistema de modales no inicializado. Por favor, recargue la página.');
    }
}

function closeProveedorModal(modalId) {
    if (window.proveedorModals) {
        window.proveedorModals.closeModal(modalId);
    }
}