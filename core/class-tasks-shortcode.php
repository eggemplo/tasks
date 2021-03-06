<?php
class Tasks_Shortcode {
    public static function init()  {
    
        add_shortcode('tasks_list', array( __CLASS__, 'tasks_list' ) );
        
        // Resources
        add_shortcode('calendar_resources', array( __CLASS__, 'calendar_resources' ) );
        
    }

    public static function tasks_list( $attr ) {
        global $post;
        
        $attr = shortcode_atts( array(
            'hide_author' => 0
        ), $attr );
        
        $output = '';
        
        $user_id = get_current_user_id();
        if ( current_user_can( 'manage_options' ) ) { // admin
            $tasks = get_posts( array( 'post_type' => 'task', 'numberposts' => -1 ) );
        } else { // only his tasks
            // get his projects
            $args = array(
                'hide_empty' => false, // also retrieve terms which are not used yet
                'meta_query' => array(
                    array(
                        'key'       => 'user',
                        'value'     => $user_id
                    )
                ),
                'taxonomy'  => 'project',
            );
            $projects = get_terms( $args );
                        
            $projects_id = array();
            if ( $projects ) {
                foreach ( $projects as $project ) {
                    $projects_id[] = $project->term_id;
                }
            }
            // get tasks in these projects
            $tasks_query = new WP_Query( array(
                'post_type' => 'task',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'project',
                        'field' => 'term_id',
                        'terms' => $projects_id,
                        'operator' => 'IN'
                    )
                )
            ) );
            $temp_post = $post;
            $tasks = array();
            while( $tasks_query->have_posts() ) {
                $tasks_query->the_post();
                $tasks[] = $post;
            }
            $post = $temp_post;
        }
        
        if ( $tasks ) {
            $columna_asignada_a = ($attr['hide_author']) ? '' : '<th scope="col">Asignada a</th>';
            
            $output .= '<div class="container">';
            $output .= '<div class="row task-row">';
            $output .= '<input type="text" id="taskSearchInput" placeholder="' . __( 'Search', 'tasks' ) . '"/>';
            $output .= '
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">#</th>
                    ' . $columna_asignada_a . '
                    <th scope="col">' . __('Date', 'tasks') . '</th>
                    <th scope="col">' . __('Project', 'tasks') . '</th>
                    <th scope="col">' . __('Title', 'tasks') . '</th>
                    <th scope="col">' . __('Duration', 'tasks') . '</th>
                    <th scope="col">' . __('Time', 'tasks') . '</th>
                    <th scope="col">' . __('Status', 'tasks') . '</th>
                    </tr>
                </thead>
                <tbody id="tasksTable">';
            foreach ( $tasks as $task ) {
                $state = get_the_terms( $task, 'state' );
                if ( $state ) {
                    $state = $state[0]->term_id;
                } else {
                    $state = 0;
                }
                $project = get_the_terms( $task, 'project' );
                if ( $project ) {
                    $project_name = $project[0]->name;
                } else {
                    $project_name = '--';
                }
                $back_color = '';
                $term_meta = get_option( 'taxonomy_' . $state );
                if ( $term_meta ) {
                    $back_color = 'background-color:' . $term_meta['color'] . ';';
                }
                
                $post_user_id = get_post_meta( $task->ID, 'user', true );
                $columna_asignada_a = '';
                if ( !$attr['hide_author'] ) {
                    $columna_asignada_a = '<td>' . get_userdata( $post_user_id )->display_name . '</td>';
                }
                $output .= '
                <tr>
                  <th scope="row">#' . $task->ID . '</th>
                  ' . $columna_asignada_a . '
                  <td>' . get_post_meta($task->ID, 'start_date', true) . '</td>
                  <td>' . $project_name . '</td>
                  <td>' . get_the_title( $task->ID ) . '</td>
                  <td>' . get_post_meta($task->ID, 'duration', true) . 'min.</td>
                  <td>' . get_post_meta($task->ID, 'time_entries_total', true) . 'min.</td>
                  <td style="' . $back_color .'">' . strip_tags( get_the_term_list( $task->ID, 'state' ) ) . '</td>
                </tr>
                ';
            }
            $output .= '
              </tbody>
            </table>
            ';
        } else {
            $output .= '<h2>' . __('No tasks available yet.', 'tasks') . '</h2>';
        }
        return $output;
    }
    
    public static function calendar_resources( $attr ) {        
        $output = Calendar::get_calendar();
        return $output;
    }
}
Tasks_Shortcode::init();
