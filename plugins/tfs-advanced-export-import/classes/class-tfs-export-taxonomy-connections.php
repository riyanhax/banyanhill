<?php
/**
 * Class TFS_Export_Taxonomy_Connections
 *
 * Post taxonomy connections export by date
 */
class TFS_Export_Taxonomy_Connections
{
    public function __construct()
    {
        // Add menu item for admin page
        add_action( 'admin_menu', array( $this, 'admin_menu_item' ) );

        // Do export posts if exporting
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'tfs_ra_taxonomy_export' && isset( $_GET['download'] ) ) {
            add_action('init', array( $this, 'do_taxonomy_export' ) );
        }
    }

    /**
     * Add menu item for admin page.
     *
     * @return void
     */
    public function admin_menu_item()
    {
        add_submenu_page(
            'tfs_ra_export_import',
            'Export Taxonomy Connections by date',
            'Export Taxonomy Connections by date',
            'manage_options',
            'tfs_ra_taxonomy_export',
            array( $this, 'taxonomy_export_page' )
        );
    }

    /**
     * Page for taxonomy export
     */
    public function taxonomy_export_page() {
        ?>
        <div class="wrap">
            <h2>Export Taxonomy Connections by date</h2>
            <?php
            // Check if MW auth plugins installed
            if ( class_exists( 'agora_authentication_plugin' ) ) {
                global $wp_locale;

                $months = "";

                for ( $i = 1; $i < 13; $i++ ) {
                    $months .= "\t\t\t<option value=\"" . zeroise($i, 2) . '">' .
                        $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
                }
                ?>
                <p>When you click the button below WordPress will create a .json file for you to save to your computer.</p>
                <p>This file will contain your taxonomies that have been attached to posts selected.</p>
                <p>Once you have saved the download file, you can use the 'Pubcode import' function on another WordPress blog to import the post + pubcode connections.</p>

                <form action="" method="get">
                    <input type="hidden" name="page" value="tfs_ra_taxonomy_export" />
                    <h3>Options</h3>

                    <table class="form-table">
                        <tr>
                            <th><label for="mm_start">Date Range</label></th>

                            <td><strong>Start:</strong> Month&nbsp;
                                <select name="mm_start" id="mm_start">
                                    <option value="all" selected="selected">All Dates</option>
                                    <?php echo $months; ?>
                                </select>&nbsp;Year&nbsp;
                                <input type="text" id="aa_start" name="aa_start" value="" size="4" maxlength="5" />
                            </td>

                            <td><strong>End:</strong> Month&nbsp;
                                <select name="mm_end" id="mm_end">
                                    <option value="all" selected="selected">All Dates</option>
                                    <?php echo $months; ?>
                                </select>&nbsp;Year&nbsp;
                                <input type="text" id="aa_end" name="aa_end" value="" size="4" maxlength="5" />
                            </td>
                        </tr>
                    </table>

                    <p class="submit"><input type="submit" name="submit" class="button" value="Download Post Taxonomies" />
                        <input type="hidden" name="download" value="true" />
                    </p>
                </form>
                <?php
            } else {
                ?>
                <h3>Middleware Authentication plugins needs to be active for you to perform this action.</h3>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * Do taxonomy export
     */
    public function do_taxonomy_export() {
        global $wpdb, $post_ids;

        $mm_start = isset($_GET['mm_start']) ? $_GET['mm_start'] : 'all';
        $mm_end = isset($_GET['mm_end']) ? $_GET['mm_end'] : 'all';
        $aa_start = isset($_GET['aa_start']) ? intval($_GET['aa_start']) : 0;
        $aa_end = isset($_GET['aa_end']) ? intval($_GET['aa_end']) : 0;

        if($mm_start != 'all' && $aa_start > 0) {
            $start_date = sprintf( "%04d-%02d-%02d", $aa_start, $mm_start, 1 );
        } else {
            $start_date = 'all';
        }

        if($mm_end != 'all' && $aa_end > 0) {
            if($mm_end == 12) {
                $mm_end = 1;
                $aa_end++;
            } else {
                $mm_end++;
            }
            $end_date = sprintf( "%04d-%02d-%02d", $aa_end, $mm_end, 1 );
        } else {
            $end_date = 'all';
        }

        $post_types_use = array( 'archives' );

        $how_many = count( $post_types_use );
        $placeholders = array_fill( 0, $how_many, '%s' );

        $format = implode( ', ', $placeholders );

        $cpt_query = " WHERE post_type IN($format)";

        $where = $wpdb->prepare($cpt_query, $post_types_use) ;

        if ( $start_date and $start_date != 'all' ) {
            $where .= $wpdb->prepare("AND post_date >= %s ", $start_date);
        }
        if ( $end_date and $end_date != 'all' ) {
            $where .= $wpdb->prepare("AND post_date < %s ", $end_date);
        }

        // grab a snapshot of post IDs, just in case it changes during the export
        $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts $where ORDER BY post_date_gmt ASC");

        $returned_array = array();

        if ( $post_ids ) {
            foreach ( $post_ids as $post_id ) {
                $terms = wp_get_post_terms(
                    $post_id,
                    'archives-category',
                    array("fields" => "all")
                );

                if ( $terms ) {
                    $post_cats = array();

                    foreach ( $terms as $term ) {
                        array_push( $post_cats, $term->term_id );
                    }

                    array_push( $returned_array, array( 'post_id' => $post_id, 'cats' => $post_cats ) );
                }
            }
        }

        if ( $returned_array ) {
            $json_output = json_encode($returned_array);

            if ( strlen( $start_date ) > 4 && strlen( $end_date ) > 4) {
                $filename = 'post_tax.' . $start_date . '.' . $end_date;
            } else {
                $filename = 'post_tax.' . date('Y-m-d');
            }

            header("Content-type: application/vnd.ms-excel");
            header("Content-Type: application/force-download");
            header("Content-Type: application/download");
            header("Content-disposition: csv" . date("Y-m-d") . ".json");
            header("Content-disposition: filename=" . $filename . ".json");
            print $json_output;
            exit;
        } else {
            echo 'No taxonomies found for the posts you have selected.';
        }

        die();
    }
}
