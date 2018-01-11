<?php

/**
 * Class DownloadProxy
 */
class DownloadProxy {

    function __construct() {
        $this->registerFilter();
        $this->registerHooks();
    }

    protected function registerFilter() {
        add_filter('rewrite_rules_array', array($this,'insertCustomRewriteRules'));
        add_filter('query_vars', array($this,'addQueryVars'));

    }

    protected function registerHooks() {
        add_action( 'parse_query', [$this, 'downloadFile'] );
    }

    /**
     * Checks for set 'action' param set to 'downloadProxy'.
     * Loads attachment id and filename from params in uri
     * after that checks permission and reads file to browser to enable download.
     *
     * @param $wp_query \WP_Query  current query object
     */
    public function downloadFile( $wp_query ) {
        if ( isset( $wp_query->query[ 'action' ] ) && $wp_query->query[ 'action' ] == 'downloadProxy' ) {
            $attachmentID = $wp_query->get( 'attachmentid' );
            $filename     = $wp_query->get( 'filename' );
            if ( $this->canDownloadFile( $attachmentID, $filename ) ) {
                $this->getRawFile( $attachmentID );
                exit;
            } else {
                _e("<h1>sorry you're not allowed to download this file</h1>", 'dp');
                exit;
            }
        }
    }

    /**
     * starts browser download of attachment
     *
     * @param $attachmentID
     */
    protected function getRawFile($attachmentID){
        $file = get_attached_file($attachmentID);

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

    /**
     * add custom rewrite rules
     * @param $rules
     * @return array
     */
    public function insertCustomRewriteRules($rules) {
        $rules['download/([0-9]+)/([^/]+)?$'] = 'index.php?action=downloadProxy&attachmentid=$matches[1]&filename=$matches[2]';

        return $rules;
    }

    /**
     * returns proxy download url for an attachmentID
     * which can be used as public link
     * @param $attachmentID int ID of an Wordpress attachment
     * @return bool|string
     */
    public function getPublicDownloadLink($attachmentID){
        if (!empty($attachmentID)){
            $filename = basename ( get_attached_file( $attachmentID ) );

            return '/download/'.$attachmentID.'/'.$filename;
        }
        return false;
    }

    /**
     * query_vars filter to support custom link
     * @protected
     * @param $vars
     * @return array
     */
    public function addQueryVars($vars){
        $vars[] = "action";
        $vars[] = "attachmentid";
        $vars[] = "filename";
        return $vars;
    }


    /**
     * public function to check if current user can download an specific file
     * this function can be customized for individual requirements
     *
     *
     * @param $attachmentID
     * @param $parameterFileName
     * @return bool
     */
    public function canDownloadFile($attachmentID, $parameterFileName){
        $realFileName = basename ( get_attached_file( $attachmentID ) );

        if ($parameterFileName !== $realFileName){
            return false;
        }

        return true;
    }

}