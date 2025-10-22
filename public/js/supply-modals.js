// Gestión de Modales para Insumos
class InsumoModals {
    constructor() {
        this.cache = new Map(); // Cache para contenido de modales
        this.preloadTimeout = null;
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

    // Precargar contenido del modal al pasar el mouse
    preloadModal(type, insumoId) {
        if (this.preloadTimeout) {
            clearTimeout(this.preloadTimeout);
        }
        
        this.preloadTimeout = setTimeout(() => {
            const cacheKey = `${type}-${insumoId}`;
            if (!this.cache.has(cacheKey)) {
                this.fetchModalContent(type, insumoId);
            }
        }, 200); // Espera 200ms antes de precargar
    }

    // Obtener contenido del modal
    async fetchModalContent(type, insumoId) {
        const cacheKey = `${type}-${insumoId}`;
        
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        try {
            const url = `/insumos/${insumoId}/${type}-modal`;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const html = await response.text();
            this.cache.set(cacheKey, html);
            return html;
        } catch (error) {
            console.error('Error fetching modal content:', error);
            throw error;
        }
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

        // Mostrar modal inmediatamente
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Verificar si ya está en caché
        const cacheKey = `show-${insumoId}`;
        if (this.cache.has(cacheKey)) {
            content.innerHTML = this.cache.get(cacheKey);
            return;
        }

        // Mostrar loading solo si no está en caché
        content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
        
        try {
            const html = await this.fetchModalContent('show', insumoId);
            content.innerHTML = html;
        } catch (error) {
            content.innerHTML = `
                <div class="error text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <p>Error al cargar los detalles del insumo</p>
                    <button class="btn btn-sm btn-primary" onclick="closeModal('showModal')">Cerrar</button>
                </div>
            `;
        }
    }

    // Abrir modal de editar
    async openEditModal(insumoId) {
        const modal = document.getElementById('editModal');
        const content = document.getElementById('editModalContent');
        
        if (!modal || !content) return;

        // Mostrar modal inmediatamente
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Verificar si ya está en caché
        const cacheKey = `edit-${insumoId}`;
        if (this.cache.has(cacheKey)) {
            content.innerHTML = this.cache.get(cacheKey);
            if (typeof setupEditValidations === 'function') {
                setupEditValidations();
            }
            return;
        }

        // Mostrar loading solo si no está en caché
        content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
        
        try {
            const html = await this.fetchModalContent('edit', insumoId);
            content.innerHTML = html;
            
            // Configurar validaciones después de cargar el contenido
            if (typeof setupEditValidations === 'function') {
                setupEditValidations();
            }
        } catch (error) {
            content.innerHTML = `
                <div class="error text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <p>Error al cargar el formulario de edición</p>
                    <button class="btn btn-sm btn-primary" onclick="closeModal('editModal')">Cerrar</button>
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

function openEditModal(insumoId) {
    if (window.insumoModals) {
        window.insumoModals.openEditModal(insumoId);
    }
}

// Función para precargar modal al pasar el mouse
function preloadShowModal(insumoId) {
    if (window.insumoModals) {
        window.insumoModals.preloadModal('show', insumoId);
    }
}

function preloadEditModal(insumoId) {
    if (window.insumoModals) {
        window.insumoModals.preloadModal('edit', insumoId);
    }
}

function closeModal(modalId) {
    if (window.insumoModals) {
        window.insumoModals.closeModal(modalId);
    }
}