@@ .. @@
		$this->plugin_name = $plugin_name;
		$this->version = $version;
+		
+		// Add new settings for dot scroll indicators
+		add_action('admin_init', array($this, 'register_dot_scroll_settings'));
 	}
 
@@ .. @@
 		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-bnav-admin.js', array( 'jquery' ), $this->version, false );
 	}
 
+	/**
+	 * Register settings for dot scroll indicators
+	 */
+	public function register_dot_scroll_settings() {
+		// Show scroll dots setting
+		register_setting(
+			'bnav_settings_group',
+			'bnav_show_scroll_dots',
+			array(
+				'type' => 'boolean',
+				'default' => false,
+				'sanitize_callback' => 'rest_sanitize_boolean'
+			)
+		);
+
+		// Auto-enable scrollbar setting
+		register_setting(
+			'bnav_settings_group',
+			'bnav_auto_enable_scrollbar',
+			array(
+				'type' => 'boolean',
+				'default' => false,
+				'sanitize_callback' => 'rest_sanitize_boolean'
+			)
+		);
+
+		// Scrollbar threshold setting
+		register_setting(
+			'bnav_settings_group',
+			'bnav_scrollbar_threshold',
+			array(
+				'type' => 'integer',
+				'default' => 5,
+				'sanitize_callback' => array($this, 'sanitize_threshold')
+			)
+		);
+
+		// Show scroll indicators setting
+		register_setting(
+			'bnav_settings_group',
+			'bnav_show_scroll_indicators',
+			array(
+				'type' => 'boolean',
+				'default' => false,
+				'sanitize_callback' => 'rest_sanitize_boolean'
+			)
+		);
+	}
+
+	/**
+	 * Sanitize threshold value
+	 */
+	public function sanitize_threshold($value) {
+		$value = intval($value);
+		return max(3, min(10, $value)); // Ensure value is between 3 and 10
+	}
+
 }