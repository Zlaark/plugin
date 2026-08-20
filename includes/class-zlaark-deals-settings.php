<?php
/**
 * Settings, plus JSON export/import for the deal catalogue.
 *
 * The uninstall routine deletes every deal and category. That is gated behind
 * an explicit opt-in here, defaulting to OFF, so removing the plugin from the
 * Plugins screen can never destroy hand-entered content by accident.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zlaark_Deals_Settings {

	const OPTION = 'zlaark_deals_settings';

	/** Bumped whenever the meta schema changes, so old deals can be migrated. */
	const SCHEMA = 2;

	public static function defaults() {
		return array(
			'delete_data_on_uninstall' => 0,
			'load_fonts'               => 1,
			'single_panel'             => 'side',
		);
	}

	public static function all() {
		$saved = get_option( self::OPTION, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		return wp_parse_args( $saved, self::defaults() );
	}

	public static function get( $key ) {
		$all = self::all();
		return isset( $all[ $key ] ) ? $all[ $key ] : null;
	}

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_post_zlaark_deals_export', array( __CLASS__, 'handle_export' ) );
		add_action( 'admin_post_zlaark_deals_import', array( __CLASS__, 'handle_import' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
	}

	public static function add_page() {
		add_submenu_page(
			'edit.php?post_type=' . ZLAARK_DEALS_CPT,
			__( 'Deals Settings', 'zlaark-deals-pro' ),
			__( 'Settings', 'zlaark-deals-pro' ),
			'manage_options',
			'zlaark-deals-settings',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function register_settings() {
		register_setting(
			'zlaark_deals_settings_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	public static function sanitize( $input ) {
		$out = self::defaults();
		$out['delete_data_on_uninstall'] = empty( $input['delete_data_on_uninstall'] ) ? 0 : 1;
		$out['load_fonts']               = empty( $input['load_fonts'] ) ? 0 : 1;

		$mode = isset( $input['single_panel'] ) ? sanitize_key( $input['single_panel'] ) : 'side';
		$out['single_panel'] = in_array( $mode, array( 'off', 'above', 'side' ), true ) ? $mode : 'side';

		return $out;
	}

	/* -------------------------------------------------------------------
	 * Settings screen
	 * ---------------------------------------------------------------- */

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = self::all();
		$count    = wp_count_posts( ZLAARK_DEALS_CPT );
		$total    = ( isset( $count->publish ) ? (int) $count->publish : 0 ) + ( isset( $count->draft ) ? (int) $count->draft : 0 );
		?>
		<div class="wrap zd-settings">
			<h1><?php esc_html_e( 'Deals Settings', 'zlaark-deals-pro' ); ?></h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'zlaark_deals_settings_group' ); ?>

				<h2 class="title"><?php esc_html_e( 'Typography', 'zlaark-deals-pro' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Widget fonts', 'zlaark-deals-pro' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION ); ?>[load_fonts]" value="1" <?php checked( $settings['load_fonts'], 1 ); ?>>
								<?php esc_html_e( 'Load the Zlaark webfonts (Bricolage Grotesque, Instrument Sans, IBM Plex Mono)', 'zlaark-deals-pro' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Turn this off only if your theme already loads these three families. All three are free and open-source (SIL Open Font License).', 'zlaark-deals-pro' ); ?>
							</p>
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Single deal pages', 'zlaark-deals-pro' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Offer panel', 'zlaark-deals-pro' ); ?></th>
						<td>
							<select name="<?php echo esc_attr( self::OPTION ); ?>[single_panel]">
								<option value="side" <?php selected( $settings['single_panel'], 'side' ); ?>>
									<?php esc_html_e( 'Beside the content', 'zlaark-deals-pro' ); ?>
								</option>
								<option value="above" <?php selected( $settings['single_panel'], 'above' ); ?>>
									<?php esc_html_e( 'Above the content', 'zlaark-deals-pro' ); ?>
								</option>
								<option value="off" <?php selected( $settings['single_panel'], 'off' ); ?>>
									<?php esc_html_e( 'Off — I will place the widget myself', 'zlaark-deals-pro' ); ?>
								</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Adds the offer panel — score, price, savings, coupon, renewal price and verification — to every deal page automatically. Building a single template by hand needs Elementor Pro, so this is on by default. Turn it off if you have Pro and would rather place the Deal Panel widget yourself.', 'zlaark-deals-pro' ); ?>
							</p>
						</td>
					</tr>
				</table>

				<h2 class="title"><?php esc_html_e( 'Data', 'zlaark-deals-pro' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'On uninstall', 'zlaark-deals-pro' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION ); ?>[delete_data_on_uninstall]" value="1" <?php checked( $settings['delete_data_on_uninstall'], 1 ); ?>>
								<?php
								printf(
									/* translators: %d: number of deals currently stored. */
									esc_html__( 'Permanently delete all %d deals and their categories when this plugin is deleted', 'zlaark-deals-pro' ),
									(int) $total
								);
								?>
							</label>
							<p class="description">
								<strong><?php esc_html_e( 'Leave this unchecked unless you are certain.', 'zlaark-deals-pro' ); ?></strong>
								<?php esc_html_e( 'Deleting the plugin from the Plugins screen with this on removes every deal permanently, bypassing the Trash. Deactivating the plugin never deletes anything either way.', 'zlaark-deals-pro' ); ?>
							</p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<hr>

			<h2 class="title"><?php esc_html_e( 'Export &amp; import', 'zlaark-deals-pro' ); ?></h2>
			<p class="description" style="max-width:640px">
				<?php esc_html_e( 'Export writes every deal, its meta and its categories to a JSON file. Use it as a backup before upgrades, or to move the catalogue between staging and production. Images are referenced by URL and re-imported into the media library when possible.', 'zlaark-deals-pro' ); ?>
			</p>

			<p>
				<a class="button button-secondary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=zlaark_deals_export' ), 'zlaark_deals_export' ) ); ?>">
					<?php
					printf(
						/* translators: %d: number of deals. */
						esc_html__( 'Export %d deals to JSON', 'zlaark-deals-pro' ),
						(int) $total
					);
					?>
				</a>
			</p>

			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="zlaark_deals_import">
				<?php wp_nonce_field( 'zlaark_deals_import' ); ?>
				<p>
					<input type="file" name="zlaark_import_file" accept="application/json,.json" required>
				</p>
				<p>
					<label>
						<input type="checkbox" name="zlaark_import_update" value="1" checked>
						<?php esc_html_e( 'Update deals that already exist (matched by slug) instead of creating duplicates', 'zlaark-deals-pro' ); ?>
					</label>
				</p>
				<?php submit_button( __( 'Import deals', 'zlaark-deals-pro' ), 'secondary' ); ?>
			</form>
		</div>
		<?php
	}

	/* -------------------------------------------------------------------
	 * Export
	 * ---------------------------------------------------------------- */

	public static function handle_export() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export deals.', 'zlaark-deals-pro' ) );
		}
		check_admin_referer( 'zlaark_deals_export' );

		$deals = get_posts(
			array(
				'post_type'        => ZLAARK_DEALS_CPT,
				'post_status'      => array( 'publish', 'draft', 'pending', 'private' ),
				'numberposts'      => -1,
				'orderby'          => 'menu_order title',
				'order'            => 'ASC',
				'suppress_filters' => false,
			)
		);

		$payload = array(
			'generator'  => 'zlaark-deals',
			'version'    => ZLAARK_DEALS_VERSION,
			'schema'     => self::SCHEMA,
			'exported'   => gmdate( 'c' ),
			'site'       => home_url(),
			'categories' => array(),
			'deals'      => array(),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => ZLAARK_DEALS_TAX,
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$payload['categories'][] = array(
					'slug'        => $term->slug,
					'name'        => $term->name,
					'description' => $term->description,
				);
			}
		}

		foreach ( $deals as $deal ) {
			$meta = array();
			foreach ( array_keys( Zlaark_Deals_Meta::FIELDS ) as $key ) {
				$meta[ $key ] = get_post_meta( $deal->ID, $key, true );
			}

			$image_id  = (int) get_post_meta( $deal->ID, '_zlaark_image_id', true );
			$image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';

			$payload['deals'][] = array(
				'slug'       => $deal->post_name,
				'title'      => $deal->post_title,
				'content'    => $deal->post_content,
				'excerpt'    => $deal->post_excerpt,
				'status'     => $deal->post_status,
				'menu_order' => (int) $deal->menu_order,
				'categories' => wp_get_post_terms( $deal->ID, ZLAARK_DEALS_TAX, array( 'fields' => 'slugs' ) ),
				'meta'       => $meta,
				'image_url'  => $image_url ? $image_url : '',
			);
		}

		$json = wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=zlaark-deals-' . gmdate( 'Y-m-d' ) . '.json' );
		header( 'Content-Length: ' . strlen( $json ) );
		echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON download.
		exit;
	}

	/* -------------------------------------------------------------------
	 * Import
	 * ---------------------------------------------------------------- */

	public static function handle_import() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to import deals.', 'zlaark-deals-pro' ) );
		}
		check_admin_referer( 'zlaark_deals_import' );

		$redirect = admin_url( 'edit.php?post_type=' . ZLAARK_DEALS_CPT . '&page=zlaark-deals-settings' );

		if ( empty( $_FILES['zlaark_import_file']['tmp_name'] ) ) {
			wp_safe_redirect( add_query_arg( 'zd_import', 'nofile', $redirect ) );
			exit;
		}

		$tmp = sanitize_text_field( wp_unslash( $_FILES['zlaark_import_file']['tmp_name'] ) );
		if ( ! is_uploaded_file( $tmp ) ) {
			wp_safe_redirect( add_query_arg( 'zd_import', 'nofile', $redirect ) );
			exit;
		}

		$raw     = file_get_contents( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$payload = json_decode( $raw, true );

		if ( ! is_array( $payload ) || empty( $payload['deals'] ) || ! is_array( $payload['deals'] ) ) {
			wp_safe_redirect( add_query_arg( 'zd_import', 'invalid', $redirect ) );
			exit;
		}

		$update = ! empty( $_POST['zlaark_import_update'] );

		// Categories first, so deals can be assigned to them.
		if ( ! empty( $payload['categories'] ) && is_array( $payload['categories'] ) ) {
			foreach ( $payload['categories'] as $cat ) {
				if ( empty( $cat['slug'] ) ) {
					continue;
				}
				$slug = sanitize_title( $cat['slug'] );
				if ( ! term_exists( $slug, ZLAARK_DEALS_TAX ) ) {
					wp_insert_term(
						sanitize_text_field( isset( $cat['name'] ) ? $cat['name'] : $slug ),
						ZLAARK_DEALS_TAX,
						array(
							'slug'        => $slug,
							'description' => isset( $cat['description'] ) ? sanitize_text_field( $cat['description'] ) : '',
						)
					);
				}
			}
		}

		$created = 0;
		$updated = 0;

		foreach ( $payload['deals'] as $row ) {
			if ( empty( $row['title'] ) ) {
				continue;
			}

			$slug     = isset( $row['slug'] ) ? sanitize_title( $row['slug'] ) : sanitize_title( $row['title'] );
			$existing = $update ? get_page_by_path( $slug, OBJECT, ZLAARK_DEALS_CPT ) : null;

			$postarr = array(
				'post_type'    => ZLAARK_DEALS_CPT,
				'post_title'   => sanitize_text_field( $row['title'] ),
				'post_name'    => $slug,
				'post_content' => isset( $row['content'] ) ? wp_kses_post( $row['content'] ) : '',
				'post_excerpt' => isset( $row['excerpt'] ) ? sanitize_textarea_field( $row['excerpt'] ) : '',
				'post_status'  => isset( $row['status'] ) && in_array( $row['status'], array( 'publish', 'draft', 'pending', 'private' ), true ) ? $row['status'] : 'publish',
				'menu_order'   => isset( $row['menu_order'] ) ? (int) $row['menu_order'] : 0,
			);

			if ( $existing ) {
				$postarr['ID'] = $existing->ID;
				$post_id       = wp_update_post( $postarr, true );
				$updated++;
			} else {
				$post_id = wp_insert_post( $postarr, true );
				$created++;
			}

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			if ( ! empty( $row['categories'] ) && is_array( $row['categories'] ) ) {
				wp_set_object_terms( $post_id, array_map( 'sanitize_title', $row['categories'] ), ZLAARK_DEALS_TAX, false );
			}

			if ( ! empty( $row['meta'] ) && is_array( $row['meta'] ) ) {
				foreach ( Zlaark_Deals_Meta::FIELDS as $key => $callback ) {
					if ( ! array_key_exists( $key, $row['meta'] ) ) {
						continue;
					}
					$value = call_user_func( $callback, $row['meta'][ $key ] );
					update_post_meta( $post_id, $key, $value );
				}
			}

			// Re-attach the image by URL if it already lives in this media library.
			if ( ! empty( $row['image_url'] ) ) {
				$attachment_id = attachment_url_to_postid( esc_url_raw( $row['image_url'] ) );
				if ( $attachment_id ) {
					update_post_meta( $post_id, '_zlaark_image_id', (int) $attachment_id );
				}
			}

			update_post_meta( $post_id, '_zlaark_schema', self::SCHEMA );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'zd_import'  => 'ok',
					'zd_created' => $created,
					'zd_updated' => $updated,
				),
				$redirect
			)
		);
		exit;
	}

	public static function notices() {
		if ( ! isset( $_GET['zd_import'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		$state = sanitize_key( wp_unslash( $_GET['zd_import'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( 'ok' === $state ) {
			$created = isset( $_GET['zd_created'] ) ? (int) $_GET['zd_created'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$updated = isset( $_GET['zd_updated'] ) ? (int) $_GET['zd_updated'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: 1: created count, 2: updated count. */
						__( 'Import finished — %1$d deals created, %2$d updated.', 'zlaark-deals-pro' ),
						$created,
						$updated
					)
				)
			);
			return;
		}

		$messages = array(
			'nofile'  => __( 'No file was uploaded. Choose a JSON file and try again.', 'zlaark-deals-pro' ),
			'invalid' => __( 'That file could not be read as a Zlaark Deals export. Check it is the JSON file produced by the Export button.', 'zlaark-deals-pro' ),
		);

		if ( isset( $messages[ $state ] ) ) {
			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				esc_html( $messages[ $state ] )
			);
		}
	}
}
