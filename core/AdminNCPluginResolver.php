<?php
defined( 'ABSPATH' ) || exit;

class AdminNCPluginResolver {

	/**
	 * Devuelve "plugin: slug‑del‑plugin" si el callback vive en un plugin.
	 *
	 * @param string $callback  Identificador de callback (Clase::método o func).
	 * @return string|null
	 */
	public function resolve( string $callback ): ?string {

		try {
			/* ── 1. Reflexión para localizar el archivo donde vive el callback ───────── */
			if ( strpos( $callback, '->' ) !== false ) {
				[ $class, $method ] = explode( '->', $callback, 2 );
				$reflector          = new ReflectionClass( $class );
			} elseif ( strpos( $callback, '::' ) !== false ) {
				[ $class, $method ] = explode( '::', $callback, 2 );
				$reflector          = new ReflectionClass( $class );
			} else {
				$reflector = new ReflectionFunction( $callback );
			}

			$file = $reflector->getFileName();
			if ( ! $file ) {
				return null;           // callbacks internos de PHP, etc.
			}

			/* ── 2. Normaliza rutas para Windows/macOS/Linux ────────────────────────── */
			$file           = wp_normalize_path( $file );
			$plugins_root   = wp_normalize_path( WP_PLUGIN_DIR );

			/* ── 3. ¿El archivo está dentro de la carpeta de plugins? ───────────────── */
			if ( strpos( $file, $plugins_root . '/' ) !== 0 ) {
				return null;           // No es un plugin (puede ser un tema).
			}

			/* ── 4. Extrae el slug del plugin ───────────────────────────────────────── */
			$relative = substr( $file, strlen( $plugins_root ) + 1 ); // p.ej. 'akismet/akismet.php'
			$slug     = explode( '/', $relative )[0];                 // 'akismet'

			return 'plugin: ' . $slug;

		} catch ( ReflectionException $e ) {
			return null; // si falla la reflexión devolvemos null silenciosamente
		}
	}
}
