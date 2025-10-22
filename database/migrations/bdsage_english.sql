-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- NUEVA BASE DE DATOS EN INGLÉS
-- Base de datos: `bdsage`
-- Fecha: Octubre 22, 2025
-- Versión: 2.0 (English Backend)
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bdsage`
--

-- ============================================================================
-- ELIMINAR TABLAS ANTIGUAS SI EXISTEN
-- ============================================================================

DROP TABLE IF EXISTS `supplier_supply`;
DROP TABLE IF EXISTS `supplies`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `tbproveedor_insumo`;
DROP TABLE IF EXISTS `tbinsumo`;
DROP TABLE IF EXISTS `tbproveedor`;

-- ============================================================================
-- ESTRUCTURA DE TABLA: supplies (Insumos)
-- ============================================================================

CREATE TABLE `supplies` (
  `supply_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `current_stock` int(11) DEFAULT 0,
  `minimum_stock` int(11) DEFAULT 0,
  `expiration_date` date DEFAULT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `price` decimal(8,2) DEFAULT NULL,
  `status` enum('Available','Out of Stock','Expired') DEFAULT 'Available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`supply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- DATOS INICIALES: supplies
-- ============================================================================

INSERT INTO `supplies` (`supply_id`, `name`, `current_stock`, `minimum_stock`, `expiration_date`, `unit_of_measure`, `quantity`, `price`, `status`) VALUES
(1, 'Harina de Trigo', 100, 100, NULL, 'kg', 25, 1800.00, 'Available'),
(2, 'Tomate', 200, 50, '2025-11-14', 'kg', 1, 2500.00, 'Available'),
(3, 'Pechuga de Pollo', 150, 30, '2026-07-20', 'kg', 1, 12000.00, 'Available'),
(4, 'Queso Mozzarella', 80, 20, '2026-09-10', 'kg', 5, 15000.00, 'Available'),
(5, 'Aceite de Oliva', 120, 25, '2027-01-15', 'litro', 4, 25000.00, 'Available'),
(6, 'Salmón Fresco', 0, 15, NULL, 'kg', 1, 35000.00, 'Out of Stock'),
(7, 'Arroz Blanco', 300, 75, '2026-03-20', 'kg', 20, 3000.00, 'Available'),
(8, 'Café Colombiano', 100, 25, '2026-10-30', 'kg', 10, 12000.00, 'Available'),
(9, 'Leche Entera', 180, 40, '2025-10-25', 'litro', 3, 3500.00, 'Available'),
(10, 'Chocolate en Polvo', 90, 20, '2026-11-15', 'kg', 4, 8000.00, 'Available'),
(11, 'Harina de Soja', 8, 3, '2026-12-04', 'kg', 8, 0.15, 'Available');

-- ============================================================================
-- ESTRUCTURA DE TABLA: suppliers (Proveedores)
-- ============================================================================

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `total_purchases` decimal(10,2) DEFAULT 0.00,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- DATOS INICIALES: suppliers
-- ============================================================================

INSERT INTO `suppliers` (`supplier_id`, `name`, `phone`, `email`, `address`, `total_purchases`, `status`) VALUES
(1, 'Distribuidora Alimentos Frescos', '3001234567', 'ventas@alimentosfrescos.com', 'Calle 123 #45-67, San jose', 1500000.00, 'Inactive'),
(2, 'Carnes Premium S.A.', '3102345678', 'pedidos@carnespremium.com', 'Av. Principal #89-10, Medellín', 2800000.00, 'Active'),
(3, 'Verduras del Campo', '3203456789', 'info@verdurasdelcampo.com', 'Kr 56 #12-34, Cali', 850000.00, 'Active'),
(4, 'Lácteos La Abundancia', '3154567890', 'contacto@lacteosabundancia.com', 'Carrera 78 #23-45, Barranquilla', 1200000.00, 'Active'),
(5, 'Importaciones Gourmet', '3015678901', 'importaciones@gourmet.com', 'Av. Siempre Viva 742, Cartagena', 3500000.00, 'Active'),
(6, 'Pescados y Mariscos Frescos', '3186789012', 'pescados@frescos.com', 'Calle 90 #34-56, Santa Marta', 1950000.00, 'Active'),
(7, 'Especias del Mundo', '3047890123', 'especias@mundo.com', 'Diagonal 23 #67-89, Bucaramanga', 620000.00, 'Active'),
(8, 'Bebidas y Licores Nacionales', '3128901234', 'bebidas@nacionales.com', 'Transversal 45 #78-90, Pereira', 2100000.00, 'Active');

-- ============================================================================
-- ESTRUCTURA DE TABLA: supplier_supply (Tabla Pivote)
-- ============================================================================

CREATE TABLE `supplier_supply` (
  `supplier_id` int(11) NOT NULL,
  `supply_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`supplier_id`, `supply_id`),
  KEY `supply_id` (`supply_id`),
  CONSTRAINT `fk_supplier_supply_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_supplier_supply_supply` FOREIGN KEY (`supply_id`) REFERENCES `supplies` (`supply_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- DATOS INICIALES: supplier_supply
-- ============================================================================

INSERT INTO `supplier_supply` (`supplier_id`, `supply_id`) VALUES
(1, 7),
(2, 3),
(3, 2),
(4, 4),
(4, 9),
(4, 11),
(5, 5),
(5, 10),
(6, 6),
(7, 1),
(7, 8),
(8, 10);

-- ============================================================================
-- AUTO_INCREMENT
-- ============================================================================

ALTER TABLE `supplies`
  MODIFY `supply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

-- ============================================================================
-- VERIFICACIÓN: Consultas de prueba
-- ============================================================================

-- Ver todas las tablas
SHOW TABLES;

-- Ver estructura de supplies
DESCRIBE `supplies`;

-- Ver estructura de suppliers
DESCRIBE `suppliers`;

-- Ver estructura de supplier_supply
DESCRIBE `supplier_supply`;

-- Contar registros
SELECT 'Supplies' as tabla, COUNT(*) as total FROM `supplies`
UNION ALL
SELECT 'Suppliers' as tabla, COUNT(*) as total FROM `suppliers`
UNION ALL
SELECT 'Supplier-Supply Relations' as tabla, COUNT(*) as total FROM `supplier_supply`;

-- Ver supplies con sus suppliers
SELECT 
    s.supply_id,
    s.name AS supply_name,
    s.current_stock,
    s.status,
    GROUP_CONCAT(sup.name SEPARATOR ', ') AS suppliers
FROM supplies s
LEFT JOIN supplier_supply ss ON s.supply_id = ss.supply_id
LEFT JOIN suppliers sup ON ss.supplier_id = sup.supplier_id
GROUP BY s.supply_id
ORDER BY s.name;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- FIN DEL SCRIPT
-- ============================================================================
