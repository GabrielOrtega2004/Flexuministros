-- Esquema del panel administrativo de Flexuministros.
-- Importar en phpMyAdmin (o `mysql -u ... -p flexuministros < schema.sql`)
-- antes de correr sql/semilla.php.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS usuarios_admin (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(60) NOT NULL UNIQUE,
  contrasena_hash VARCHAR(255) NOT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categorias (
  id VARCHAR(40) PRIMARY KEY,          -- mismo id corto usado en las URLs (?cat=...)
  nombre VARCHAR(120) NOT NULL,
  descripcion TEXT NULL,
  icono VARCHAR(40) NULL,
  orden INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS marcas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  logo VARCHAR(255) NOT NULL,
  ancho INT NOT NULL DEFAULT 260,
  alto INT NOT NULL DEFAULT 84,
  enlace VARCHAR(255) NULL,
  orden INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS productos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(180) NOT NULL,
  descripcion VARCHAR(255) NOT NULL DEFAULT '',
  slug VARCHAR(200) NOT NULL UNIQUE,
  categoria_id VARCHAR(40) NOT NULL,
  marca_id INT UNSIGNED NULL,
  imagen VARCHAR(255) NULL,
  imagen_ancho INT NULL,
  imagen_alto INT NULL,
  visible TINYINT(1) NOT NULL DEFAULT 1,
  orden INT NOT NULL DEFAULT 0,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON UPDATE CASCADE,
  FOREIGN KEY (marca_id) REFERENCES marcas(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS producto_imagenes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  producto_id INT UNSIGNED NOT NULL,
  ruta VARCHAR(255) NOT NULL,
  orden INT NOT NULL DEFAULT 0,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS equipos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(200) NOT NULL UNIQUE,
  nombre VARCHAR(180) NOT NULL,
  eyebrow VARCHAR(120) NULL,           -- etiqueta pequeña arriba del título (ej. "Fabricación propia")
  descripcion_corta TEXT NOT NULL,     -- primer párrafo (lead)
  descripcion TEXT NOT NULL,           -- segundo párrafo
  tipo ENUM('nuevo','usado') NOT NULL DEFAULT 'nuevo',
  icono VARCHAR(40) NULL,
  imagen_principal VARCHAR(255) NULL,
  imagen_ancho INT NULL,
  imagen_alto INT NULL,
  whatsapp_texto VARCHAR(255) NOT NULL DEFAULT '',
  visible TINYINT(1) NOT NULL DEFAULT 1,
  orden INT NOT NULL DEFAULT 0,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS equipo_imagenes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  equipo_id INT UNSIGNED NOT NULL,
  ruta VARCHAR(255) NOT NULL,
  orden INT NOT NULL DEFAULT 0,
  FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS servicios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(200) NOT NULL UNIQUE,
  titulo VARCHAR(180) NOT NULL,
  eyebrow VARCHAR(120) NULL,
  texto_principal TEXT NOT NULL,
  texto_secundario TEXT NOT NULL,
  texto_extra TEXT NULL,               -- párrafo opcional con enlace (ej. "Ver catálogo de productos")
  icono VARCHAR(40) NULL,
  imagen VARCHAR(255) NULL,
  imagen_ancho INT NULL,
  imagen_alto INT NULL,
  whatsapp_texto VARCHAR(255) NOT NULL DEFAULT '',
  visible TINYINT(1) NOT NULL DEFAULT 1,
  orden INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS marcas_servicio (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  logo VARCHAR(255) NOT NULL,
  descripcion VARCHAR(255) NOT NULL DEFAULT '',
  orden INT NOT NULL DEFAULT 0,
  visible TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Configuración general del sitio: pares clave/valor para no requerir
-- migraciones cada vez que se agrega un campo editable nuevo.
CREATE TABLE IF NOT EXISTS configuracion (
  clave VARCHAR(80) PRIMARY KEY,
  valor TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
