<?php
class Tasks_Metabox {
    
    /**
     * Constructor.
     */
    public static function init() {
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_metabox'  )        );
        add_action( 'save_post',      array( __CLASS__, 'save_metabox' ), 10, 2 );
        
        // Tax
        add_action( 'state_add_form_fields', array( __CLASS__, 'state_add_form_fields' ), 10, 2 );
        add_action( 'state_edit_form_fields', array( __CLASS__, 'state_edit_form_fields' ), 10, 2 );
        add_action( 'edited_state', array( __CLASS__, 'save_state' ), 10, 2 );
        add_action( 'create_state', array( __CLASS__, 'save_state' ), 10, 2 );
    
        // Projects
        add_action( 'project_add_form_fields', array( __CLASS__, 'project_add_form_fields' ), 10, 2 );
        add_action( 'project_edit_form_fields', array( __CLASS__, 'project_edit_form_fields' ), 10, 2 );
        add_action( 'edited_project', array( __CLASS__, 'save_project' ), 10, 2 );
        add_action( 'create_project', array( __CLASS__, 'save_project' ), 10, 2 );
        
    }

    // STATE
    
    public static function save_state( $term_id ) {
        if ( isset( $_POST['term_meta'] ) ) {
            
            $t_id = $term_id;
            $term_meta = get_option( "taxonomy_$t_id" );
            $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ) {
                if ( isset ( $_POST['term_meta'][$key] ) ) {
                    $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
            // Save the option array.
            update_option( "taxonomy_$t_id", $term_meta );
        }
        
    } 
    
    public static function state_add_form_fields() {
        ?>
		<div class="form-field">
			<label for="term_meta[class_term_meta]"><?php _e( 'Color', 'tasks' ); ?></label>
			<input type="text" name="term_meta[color]" id="term_meta[color]" value="">
		</div>
	<?php
	}
    
	public static function state_edit_form_fields( $term ) {
	    
	    $t_id = $term->term_id;
	    $term_meta = get_option( "taxonomy_$t_id" );
	    ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[color]"><?php _e( 'Color', 'tasks' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[color]" id="term_meta[color]" value="<?php echo esc_attr( $term_meta['color'] ) ? esc_attr( $term_meta['color'] ) : ''; ?>">
			</td>
		</tr>
	<?php
	}
    
	
	// PROJECT
	
	public static function save_project( $term_id ) {
	    if ( isset( $_POST['term_meta'] ) ) {
	        /*
	        $cat_keys = array_keys( $_POST['term_meta'] );
	        foreach ( $cat_keys as $key ) {
	            if ( isset ( $_POST['term_meta'][$key] ) ) {
	                update_term_meta( $term_id, $key, sanitize_text_field( $_POST['term_meta'][$key] ) );
	            }
	        }
	        */
	        if ( isset( $_POST['term_meta']['user'] ) ) {
	            update_term_meta( $term_id, 'user', intval( $_POST['term_meta']['user'] ) );
	        }
	    }
	    
	}
	
	public static function project_add_form_fields() {
	    ?>
		<div class="form-field">
			<label for="term_meta[class_term_meta]"><?php _e( 'Client', 'tasks' ); ?></label>
			<?php wp_dropdown_users(array('name' => 'term_meta[user]'));?>
		</div>
	<?php
	}
    
	public static function project_edit_form_fields( $term ) {
	    ?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[color]"><?php _e( 'Client', 'tasks' ); ?></label></th>
			<td>
				<?php wp_dropdown_users( array( 'name' => 'term_meta[user]', 'selected' => get_term_meta( $term->term_id, 'user', true ) ) );?>
			</td>
		</tr>
	<?php
	}
	
	
	
    /**
     * Adds the metaboxes to 'tasks'
     */
    public static function add_metabox() {
        add_meta_box(
            'dates',
            __( 'Dates', TASKS_PLUGIN_DOMAIN ),
            array( __CLASS__, 'render_metabox_dates' ),
            'task',
            'normal',
            'default'
            );
        add_meta_box(
            'user_assign',
            __( 'Resources', TASKS_PLUGIN_DOMAIN ),
            array( __CLASS__, 'render_metabox_user_assign' ),
            'task',
            'normal',
            'default'
            );
        
        // Entradas de tiempo
        add_meta_box(
            'time_entries',
            __( 'Time entries', TASKS_PLUGIN_DOMAIN ),
            array( __CLASS__, 'render_metabox_time_entries' ),
            'task',
            'normal',
            'default'
            );
    }
    
    /**
     * Renders the meta box: user assign
     */
    public static function render_metabox_dates( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
        ?>
        
        <?php 
        // Start date
        $start_date = get_post_meta( $post->ID, 'start_date', true );
        ?>
        <label for="title_field" style="width:150px; display:inline-block;"><?php echo esc_html__('Start date', TASKS_PLUGIN_DOMAIN);?></label>
        <input type="date" name="start_date" id="start_date" value="<?php echo $start_date;?>"/>
		
		<?php 
        // End date
        $end_date = get_post_meta( $post->ID, 'end_date', true );
        ?>
        <label for="title_field" style="width:150px; display:inline-block;"><?php echo esc_html__('End date', TASKS_PLUGIN_DOMAIN);?></label>
        <input type="date" name="end_date" id="end_date" value="<?php echo $end_date;?>"/>
		
        <?php
        // Duration
        $end_date = get_post_meta( $post->ID, 'duration', true );
        ?>
        <label for="title_field" style="width:150px; display:inline-block;"><?php echo esc_html__('Duration in minutes', TASKS_PLUGIN_DOMAIN);?></label>
        <input type="number" name="duration" id="duration" value="<?php echo $end_date;?>"/>
		
        <?php
    }
    
    /**
     * Renders the meta box: user assign
     */
    public static function render_metabox_user_assign( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
        ?>
        
        <label for="title_field" style="width:150px; display:inline-block;"><?php echo esc_html__('User', TASKS_PLUGIN_DOMAIN);?></label>
		<select id="resource" name="resource" class="title_field">
		<option value="0">---</option>
  		<?php 
  		$post_user_id = get_post_meta( $post->ID, 'resource', true );
  		$users = get_users();
  		foreach ( $users as $user ) {
  		    $user_info = get_userdata( $user->ID );
  		    $selected = '';
  		    if ( $post_user_id == $user->ID ) {
  		        $selected = 'selected="selected"';    
  		    }
  		?>
  			<option value="<?php echo $user_info->ID;?>" <?php echo $selected;?> ><?php echo esc_html( $user_info->display_name );?></option>
  		<?php 
  		}
  		?>
  		</select>       
        
        <?php 
        // User load %
        $user_load = get_post_meta( $post->ID, 'user_load', true );
        ?>
        <label for="title_field" style="width:150px; display:inline-block;"><?php echo esc_html__('User load (%)', TASKS_PLUGIN_DOMAIN);?></label>
        <input type="number" name="user_load" id="user_load" value="<?php echo $user_load;?>"/>
		
        <?php
    }

    /**
     * Renders the meta box: time_entries
     */
    public static function render_metabox_time_entries( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );

        $time_entries_total = get_post_meta( $post->ID, 'time_entries_total', true );
        
        $time_entries = get_post_meta( $post->ID, 'time_entries', true );
  		$time_entries = maybe_unserialize( $time_entries );
  		?>
  		<h4>Total time: <?php echo $time_entries_total;?> min.</h4>
  		<div class="table-responsive">
			<table class="tasks_time_entries_table grid" id="sort">
				<thead>
					<tr>
						<th class="tasks_time_entries-table-desc" width="70%">Descripción</th>
						<th class="tasks_time_entries-table-cant" width="15%">Fecha</th>
						<th class="tasks_time_entries-table-price" width="15%">Tiempo</th>
					</tr>
				</thead>
				<tbody>
    			<?php
          		if ( is_array( $time_entries ) ) {
          		    foreach ( $time_entries as $time_entry ) {
          		        ?>
          		        <tr>
							<td><input type="text" name="time_entries_desc[]" placeholder="Descripción" value="<?php echo $time_entry['desc'];?>"/></td>
							<td class="right"><input type="date" name="time_entries_date[]" placeholder="Fecha" value="<?php echo $time_entry['date'];?>"/></td>
							<td class="right"><input type="number" name="time_entries_time[]" placeholder="Tiempo" value="<?php echo $time_entry['time'];?>"/></td>
						</tr>
          		        <?php
          		    }
          		}
          		?>
  				</tbody>
			</table>
		</div>		
		<button id="tasks_time_entries_button_add_item" class="button button-primary button-large" onclick="return false;"><?php _e( "Añadir entrada de tiempo", TASKS_PLUGIN_DOMAIN );?></button>
  		<?php
     }
    
    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
        $nonce_action = 'custom_nonce_action';
        
        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }
        
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
        
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        /*
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
        
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        */
        
        $user_id   = isset( $_POST['resource'] ) ? intval( $_POST['resource'] ) : '';
        update_post_meta( $post_id, 'resource', $user_id );
        
        $user_load   = isset( $_POST['user_load'] ) ? intval( $_POST['user_load'] ) : '';
        update_post_meta( $post_id, 'user_load', $user_load );
        
        $start_date   = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
        update_post_meta( $post_id, 'start_date', $start_date );
        
        $end_date   = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';
        update_post_meta( $post_id, 'end_date', $end_date );
        
        $duration   = isset( $_POST['duration'] ) ? intval( $_POST['duration'] ) : '0';
        update_post_meta( $post_id, 'duration', $duration );
        
        // Time entries
        if ( isset( $_POST['time_entries_desc'] ) ) {
            $descriptions = array();
            foreach ( $_POST['time_entries_desc'] as $description ) {
                $descriptions[] = sanitize_text_field( $description );
            }
            $dates = array();
            foreach ( $_POST['time_entries_date'] as $date ) {
                $dates[] = sanitize_text_field( $date );
            }
            $times = array();
            foreach ( $_POST['time_entries_time'] as $time ) {
                $times[] = sanitize_text_field( $time );
            }
            
            $time_entries = array();
            $cnt = 0;
            $time_entries_total = 0;
            foreach ( $descriptions as $description ) {
                if (( strlen($description) > 0 ) || ( strlen($dates[$cnt]) > 0 ) || ( strlen($times[$cnt]) > 0 )) {
                    $time_entries[] = array(
                        'desc' => $description,
                        'date' => $dates[$cnt],
                        'time' => $times[$cnt]
                    );
                    $time_entries_total += intval($times[$cnt]);
                }
                $cnt++;
            }
            
            $values = maybe_serialize( $time_entries );
            update_post_meta( $post->ID, 'time_entries', $values );
        
            // Por rendimiento guardamos este dato aparte, para no tener que estar calculando constantemente.
            update_post_meta( $post->ID, 'time_entries_total', $time_entries_total );
        }
    }
}

Tasks_Metabox::init();
