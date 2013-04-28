<?php
/**
 * Install and use community packages.
 *
 * @package wp-cli
 */
class Package_Command extends WP_CLI_Command {

	/**
	 * List all available community packages.
	 *
	 * @subcommand list [--format]
	 */
	public function _list( $args, $assoc_args ) {

		$defaults = array(
				'format'          => 'table',
			);
		$assoc_args = array_merge( $defaults, $assoc_args );

		$package_directory = \WP_CLI\Package\get_directory_details();

		if ( is_wp_error( $package_directory ) )
			WP_CLI::error( $package_directory->get_error_message() );

		$fields = array(
				'slug',
				'name',
				'installed',
				'description',
				'author',
			);

		foreach( $package_directory as &$package ) {
			$package->installed = ( $package->installed ) ? 'yes' : 'no';
		}

		\WP_CLI\Utils\format_items( $assoc_args['format'], $fields, $package_directory );
	}

	/**
	 * Install a new community package.
	 *
	 * @subcommand install
	 * @synopsis <package-slug>
	 */
	public function install( $args ) {

		list( $package_slug ) = $args;

		$ret = \WP_CLI\Package\install( $package_slug );
		if ( is_wp_error( $ret ) )
			WP_CLI::error( $ret->get_error_message() );
		else
			WP_CLI::success( sprintf( "Package '%s' installed.", $package_slug ) );
	}

	/**
	 * Uninstall an existing community package.
	 *
	 * @subcommand uninstall
	 * @synopsis <package-slug>
	 */
	public function uninstall( $args, $assoc_args ) {

		list( $package_slug ) = $args;

		$ret = \WP_CLI\Package\uninstall( $package_slug );
		if ( is_wp_error( $ret ) )
			WP_CLI::error( $ret->get_error_message() );
		else
			WP_CLI::success( sprintf( "Package '%s' uninstalled.", $package_slug ) );
	}

	/**
	 * Update the directory to the latest version
	 *
	 * @subcommand update-directory
	 */
	public function update_directory() {

		if ( ! \WP_CLI\Package\directory_exists() ) {
			$ret = \WP_CLI\Package\install_directory();
			if ( is_wp_error( $ret ) )
				WP_CLI::error( $ret->get_error_message() );
			else
				WP_CLI::success( "Package directory installed." );
		} else {
			$ret = \WP_CLI\Package\update_directory();
			if ( is_wp_error( $ret ) )
				WP_CLI::error( $ret->get_error_message() );
			else
				WP_CLI::success( "Package directory updated." );
		}
	}

}

WP_CLI::add_command( 'package', 'Package_Command' );