// Sistema de Filtrado para Insumos (Supplies) - Cliente lado
class SupplyFilters {
    constructor() {
        this.initializeFilters();
        this.totalSupplies = 0;
        this.setupEventListeners();
    }

    initializeFilters() {
        // Contar total de insumos
        this.countTotalSupplies();
        this.updateResultCounter();
    }

    setupEventListeners() {
        // Filtrado en tiempo real para búsqueda
        document.getElementById('filtroNombre')?.addEventListener('input', () => {
            this.aplicarFiltros();
        });

        // Eventos para links de filtros rápidos (estado)
        document.querySelectorAll('.filtro-estado').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const estado = e.currentTarget.dataset.estado || '';
                this.aplicarFiltroEstado(estado);
            });
        });

        // Eventos para links de filtros de stock y vencimiento
        document.querySelectorAll('.filtro-stock').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const stock = e.currentTarget.dataset.stock || '';
                this.aplicarFiltroStock(stock);
            });
        });

        document.querySelectorAll('.filtro-vencimiento').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const vencimiento = e.currentTarget.dataset.vencimiento || '';
                this.aplicarFiltroVencimiento(vencimiento);
            });
        });
    }

    countTotalSupplies() {
        const tableRows = document.querySelectorAll('.supply-row');
        this.totalSupplies = tableRows.length;
    }

    aplicarFiltroEstado(estado) {
        // Limpiar otros filtros
        document.querySelectorAll('.filtro-stock, .filtro-vencimiento').forEach(link => {
            link.classList.remove('activo');
        });

        // Aplicar filtro de estado
        document.querySelectorAll('.filtro-estado').forEach(link => {
            if (link.dataset.estado === estado) {
                link.classList.add('activo');
            } else {
                link.classList.remove('activo');
            }
        });

        this.aplicarFiltros({ estado });
    }

    aplicarFiltroStock(stock) {
        // Limpiar otros filtros
        document.querySelectorAll('.filtro-estado, .filtro-vencimiento').forEach(link => {
            link.classList.remove('activo');
        });

        // Aplicar filtro de stock
        document.querySelectorAll('.filtro-stock').forEach(link => {
            if (link.dataset.stock === stock) {
                link.classList.add('activo');
            } else {
                link.classList.remove('activo');
            }
        });

        this.aplicarFiltros({ stock });
    }

    aplicarFiltroVencimiento(vencimiento) {
        // Limpiar otros filtros
        document.querySelectorAll('.filtro-estado').forEach(link => {
            link.classList.remove('activo');
        });

        // Aplicar filtro de vencimiento
        document.querySelectorAll('.filtro-vencimiento').forEach(link => {
            if (link.dataset.vencimiento === vencimiento) {
                link.classList.add('activo');
            } else {
                link.classList.remove('activo');
            }
        });

        this.aplicarFiltros({ vencimiento });
    }

    aplicarFiltros(filtrosExtra = {}) {
        const filtros = {
            buscar: document.getElementById('filtroNombre')?.value.toLowerCase().trim() || '',
            estado: filtrosExtra.estado !== undefined ? filtrosExtra.estado : this.obtenerFiltroActivo('.filtro-estado'),
            stock: filtrosExtra.stock !== undefined ? filtrosExtra.stock : this.obtenerFiltroActivo('.filtro-stock'),
            vencimiento: filtrosExtra.vencimiento !== undefined ? filtrosExtra.vencimiento : this.obtenerFiltroActivo('.filtro-vencimiento')
        };

        let visibles = 0;

        // Filtrar filas de tabla
        const tableRows = document.querySelectorAll('.supply-row');
        tableRows.forEach(row => {
            if (this.cumpleFiltros(row, filtros)) {
                row.style.display = '';
                visibles++;
            } else {
                row.style.display = 'none';
            }
        });

        this.updateResultCounter(visibles);
        this.mostrarMensajeVacio(visibles === 0);
    }

    obtenerFiltroActivo(selector) {
        const activo = document.querySelector(`${selector}.activo`);
        if (!activo) return '';
        
        if (selector.includes('estado')) return activo.dataset.estado || '';
        if (selector.includes('stock')) return activo.dataset.stock || '';
        if (selector.includes('vencimiento')) return activo.dataset.vencimiento || '';
        return '';
    }

    cumpleFiltros(row, filtros) {
        const nombre = row.dataset.nombre || '';
        const estado = row.dataset.estado || '';
        const stockBajo = row.dataset.stockBajo === 'true';
        const vencimiento = row.dataset.vencimiento || '';

        // Filtro por nombre/búsqueda
        if (filtros.buscar && !nombre.includes(filtros.buscar)) {
            return false;
        }

        // Filtro por estado
        if (filtros.estado) {
            if (filtros.estado === 'Disponible' && estado !== 'Disponible') return false;
            if (filtros.estado === 'Agotado' && estado !== 'Agotado') return false;
            if (filtros.estado === 'Vencido' && estado !== 'Vencido') return false;
        }

        // Filtro por stock
        if (filtros.stock === 'bajo' && !stockBajo) {
            return false;
        }

        // Filtro por vencimiento
        if (filtros.vencimiento) {
            if (filtros.vencimiento === 'por_vencer' && vencimiento !== 'por_vencer') return false;
            if (filtros.vencimiento === 'vencidos' && vencimiento !== 'vencido') return false;
            if (filtros.vencimiento === 'buenos' && vencimiento !== 'bueno') return false;
        }

        return true;
    }

    updateResultCounter(visibles = null) {
        const totalText = document.getElementById('totalSuppliesText');
        if (!totalText) return;

        if (visibles === null) {
            visibles = this.totalSupplies;
        }

        totalText.innerHTML = `📦 <strong>${visibles}</strong> de <strong>${this.totalSupplies}</strong> insumos`;
    }

    mostrarMensajeVacio(mostrar) {
        const tbody = document.querySelector('.table tbody');
        if (!tbody) return;

        let mensajeVacio = document.getElementById('mensajeSuppliesVacio');

        if (mostrar && !mensajeVacio) {
            // Crear mensaje de no encontrados
            mensajeVacio = document.createElement('tr');
            mensajeVacio.id = 'mensajeSuppliesVacio';
            mensajeVacio.innerHTML = `
                <td colspan="8" class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">😔 No se encontraron insumos</h4>
                    <p class="text-muted">No hay insumos que coincidan con los filtros seleccionados.</p>
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarFiltrosSupply()">
                        <i class="fas fa-eraser"></i> Quitar Filtros
                    </button>
                </td>
            `;
            tbody.appendChild(mensajeVacio);
        } else if (!mostrar && mensajeVacio) {
            mensajeVacio.remove();
        }
    }

    limpiarFiltros() {
        // Limpiar búsqueda
        const filtroNombre = document.getElementById('filtroNombre');
        if (filtroNombre) {
            filtroNombre.value = '';
        }

        // Quitar clase activo de todos los filtros
        document.querySelectorAll('.filtro-estado, .filtro-stock, .filtro-vencimiento').forEach(link => {
            link.classList.remove('activo');
        });

        // Mostrar todos los elementos
        document.querySelectorAll('.supply-row').forEach(row => {
            row.style.display = '';
        });

        // Actualizar contador
        this.updateResultCounter();

        // Quitar mensaje de vacío
        this.mostrarMensajeVacio(false);
    }
}

// Funciones globales
function limpiarFiltrosSupply() {
    if (window.supplyFilters) {
        window.supplyFilters.limpiarFiltros();
    }
}

function buscarEnTiempoReal() {
    if (window.supplyFilters) {
        window.supplyFilters.aplicarFiltros();
    }
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        window.supplyFilters = new SupplyFilters();
    }, 100);
});
