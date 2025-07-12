@@ .. @@
		$this->plugin_name = $plugin_name;
		$this->version = $version;
+		
+		// Add action to output scroll data attributes
+		add_action('wp_footer', array($this, 'add_scroll_data_attributes'));
 	}
 
@@ .. @@
 		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-bnav-public.js', array( 'jquery' ), $this->version, false );
 	}
 
+	/**
+	 * Add scroll-related data attributes to the menu wrapper
+	 */
+	public function add_scroll_data_attributes() {
+		if (!class_exists('Wp_Bnav_Settings')) {
+			require_once WP_BNAV_PATH . 'includes/class-wp-bnav-settings.php';
+		}
+		
+		$scroll_settings = Wp_Bnav_Settings::get_scroll_settings();
+		
+		// Add JavaScript to set data attributes
+		?>
+		<script type="text/javascript">
+		jQuery(document).ready(function($) {
+			var $wrapper = $('.bnav_bottom_nav_wrapper');
+			if ($wrapper.length > 0) {
+				// Set scroll-related data attributes
+				<?php if ($scroll_settings['scrollbar_enabled']): ?>
+				$wrapper.attr('data-scrollbar', '1');
+				<?php endif; ?>
+				
+				<?php if ($scroll_settings['auto_enable_scrollbar']): ?>
+				$wrapper.attr('data-auto-enable-scrollbar', '1');
+				<?php endif; ?>
+				
+				$wrapper.attr('data-scrollbar-threshold', '<?php echo esc_js($scroll_settings['scrollbar_threshold']); ?>');
+				
+				<?php if ($scroll_settings['show_scroll_indicators']): ?>
+				$wrapper.attr('data-scrollbar-indicators', '1');
+				<?php endif; ?>
+				
+				<?php if ($scroll_settings['show_scroll_dots']): ?>
+				$wrapper.attr('data-scrollbar-dots', '1');
+				<?php endif; ?>
+			}
+		});
+		</script>
+		<?php
+	}
+
 }